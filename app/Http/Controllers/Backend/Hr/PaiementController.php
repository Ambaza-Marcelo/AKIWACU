<?php

namespace App\Http\Controllers\Backend\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Storage;
use App\Models\HrEmploye;
use App\Models\HrPaiement;
use App\Models\HrJournalPaie;
use App\Models\HrDepartement;
use App\Models\HrService;
use App\Models\HrBanque;
use App\Models\HrReglage;
use App\Models\HrCompany;
use Carbon\Carbon;
use Validator;
use PDF;

class PaiementController extends Controller
{
    //
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            return $next($request);
        });
    }

    public function index($company_id)
    {
        if (is_null($this->user) || !$this->user->can('hr_employe.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les employés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $paiements = HrPaiement::where('employe_id','!=','')->where('company_id',$company_id)->get();
        return view('backend.pages.hr.paiement.index',compact('paiements'));
    }

    public function selectByCompany()
    {
        if (is_null($this->user) || !$this->user->can('hr_employe.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les employés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $companies = HrCompany::all();
        return view('backend.pages.hr.paiement.select_by_company',compact('companies'));
    }

    public function createByCompany()
    {
        if (is_null($this->user) || !$this->user->can('hr_employe.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les employés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $companies = HrCompany::all();
        return view('backend.pages.hr.paiement.create_by_company',compact('companies'));
    }

    public function create($company_id)
    {
        if (is_null($this->user) || !$this->user->can('hr_employe.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les employés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $journal_paie = HrJournalPaie::where('etat', 0)->first();

        $employes = HrEmploye::where('company_id',$company_id)->whereNotIn('id', function($q){
        $q->select('employe_id')->where('code','0003')->where('employe_id','!=','')->from('hr_paiements');
        })->orderBy('firstname')->get();
        

        return view('backend.pages.hr.paiement.create',compact('employes','company_id','journal_paie'));
    }

    public function fetch(Request $request)
    {
        $data['data'] = HrEmploye::where("id", $request->employe_id)->get(["id","somme_salaire_base","numero_compte","indemnite_deplacement","indemnite_logement","prime_fonction","matricule_no","cni","code_departement","code_service","code_banque"]);
  
        return response()->json($data);
    }

     public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_employe.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les employés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $rules = array(
                'date_debut' => 'required',
                'date_fin' => 'required|after:date_debut',
                'somme_salaire_base' => 'required',
                'employe_id' => 'required',
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

        $codeJournalPaieEncours = HrJournalPaie::where('etat', 0)->value('code');

        $plafond_cotisation = 450000;

        $employe_id = $request->employe_id;
        $date_debut = $request->date_debut;
        $date_fin = $request->date_fin;
        $indemnite_id = $request->indemnite_id;
        $cotisation_id = $request->cotisation_id;
        $impot_id = $request->impot_id;
        $etat = 0;
        $somme_salaire_base = $request->somme_salaire_base;
        $somme_salaire_net = $request->somme_salaire_net;
        $nbre_jours_ouvrables = $request->nbre_jours_ouvrables;
        $nbre_jours_conges = $request->nbre_jours_conges;
        $nbre_jours_conges_consomes = $request->nbre_jours_conges_consomes;
        $code_banque = $request->code_banque;
        $code_departement = $request->code_departement;
        $code_service = $request->code_service;
        $numero_compte = $request->numero_compte;
        $allocation_familiale = $request->allocation_familiale;
        $montant_ind_imposable = $request->montant_ind_imposable;
        $montant_ind_non_imposable = $request->montant_ind_non_imposable;
        $designation_prime_non_imposable = $request->designation_prime_non_imposable;
        $montant_prime_non_imposable = $request->montant_prime_non_imposable;
        $soins_medicaux = $request->soins_medicaux;
        $retenue_pret = $request->retenue_pret;
        $autre_retenue = $request->autre_retenue;
        $company_id = $request->company_id;
        $created_by = $this->user->name;
            

      $indemnite_deplacement = HrEmploye::where('id',$employe_id)->value('indemnite_deplacement');
      $indemnite_logement = HrEmploye::where('id',$employe_id)->value('indemnite_logement');
      $prime_fonction = HrEmploye::where('id',$employe_id)->value('prime_fonction');

      $salaire_brut = $somme_salaire_base + $allocation_familiale + 
       $indemnite_logement + $indemnite_deplacement + $prime_fonction;


        if ($salaire_brut < 450000) {
                $inss = ($salaire_brut * 4)/100;
                $somme_cotisation_inss = ($salaire_brut * 4)/100;
                $inss_employeur = ($salaire_brut * 4)/100;
            }else{
                $inss = (450000 * 4)/100;
                $somme_cotisation_inss = ($plafond_cotisation * 4)/100;
                $inss_employeur = ($plafond_cotisation * 6)/100;
            }

            if ($salaire_brut < 250000) {
                $assurance_maladie_employe = 0;
                $assurance_maladie_employeur = 15000;
            }else{
                $assurance_maladie_employe = 6000;
                $assurance_maladie_employeur = 9000;
            }


        $base_imposable = $salaire_brut - $indemnite_logement - $indemnite_deplacement - $inss - $assurance_maladie_employe - $allocation_familiale;
      //les impots

            if ($base_imposable >= 0 && $base_imposable <= 150000) {
                $somme_impot = 0;
            }elseif ($base_imposable > 150000 && $base_imposable <= 300000) {
                $somme_impot = (($base_imposable - 150000) * 20)/100;
            }elseif ($base_imposable > 300000) {
                $somme_impot = 30000 + (($base_imposable - 300000) * 30)/100;    
            }

        $somme_salaire_brut_imposable = $salaire_brut;
        $somme_salaire_brut_non_imposable = $salaire_brut;
        $somme_salaire_net_imposable = $salaire_brut - $somme_cotisation_inss - $somme_impot;
        $somme_salaire_net_non_imposable = $salaire_brut - $somme_cotisation_inss - $somme_impot;

        $paiement = new HrPaiement();
        $paiement->employe_id = $employe_id;
        $paiement->code = $codeJournalPaieEncours;
        $paiement->date_debut = $request->date_debut;
        $paiement->date_fin = $request->date_fin;
        $paiement->etat = 0;
        $paiement->somme_salaire_base = $somme_salaire_base;
        $paiement->somme_salaire_brut_imposable = $somme_salaire_brut_imposable;
        $paiement->somme_salaire_brut_non_imposable = $somme_salaire_brut_non_imposable;
        $paiement->somme_salaire_net_imposable = $somme_salaire_net_imposable;
        $paiement->somme_salaire_net_non_imposable = $somme_salaire_net_non_imposable;
        $paiement->nbre_jours_ouvrables = $request->nbre_jours_ouvrables;
        $paiement->code_banque = $code_banque;
        $paiement->code_departement = $code_departement;
        $paiement->code_service = $code_service;
        $paiement->numero_compte = $request->numero_compte;
        $paiement->somme_impot = $somme_impot;
        $paiement->somme_cotisation_inss = $somme_cotisation_inss;
        $paiement->inss_employeur = $inss_employeur;
        $paiement->assurance_maladie_employe = $assurance_maladie_employe;
        $paiement->assurance_maladie_employeur = $assurance_maladie_employeur;
        $paiement->indemnite_deplacement = $indemnite_deplacement;
        $paiement->indemnite_logement = $indemnite_logement;
        $paiement->allocation_familiale = $allocation_familiale;
        $paiement->soins_medicaux = $soins_medicaux;
        $paiement->prime_fonction = $prime_fonction;
        $paiement->retenue_pret = $request->retenue_pret;
        $paiement->autre_retenue = $request->autre_retenue;
        $paiement->company_id = $request->company_id;
        $paiement->created_by = $this->user->name;
        $paiement->save();

        session()->flash('success', $this->user->name.', vous avez créé une fiche de paie avec succés !!');
            return redirect()->back();
        //return redirect()->route('admin.hr-paiements.index',$company_id);
    }

    public function show($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_employe.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de voir les employés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }


        $employe_id = HrPaiement::where('id',$id)->where('employe_id','!=','')->value('employe_id');
        $code = HrPaiement::where('id',$id)->where('employe_id','!=','')->value('code');
        $data = HrPaiement::where('code',$code)->where('employe_id','!=','')->where('employe_id',$employe_id)->first();

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        return view('backend.pages.hr.paiement.show',compact('data'));
    }


     public function ficheSalaire($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_employe.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }
        

        $employe_id = HrPaiement::where('id',$id)->where('employe_id','!=','')->value('employe_id');
        $company_id = HrPaiement::where('id',$id)->where('employe_id','!=','')->value('company_id');
        $company = HrCompany::where('id',$company_id)->first();
        $code = HrPaiement::where('id',$id)->where('employe_id','!=','')->value('code');
        $matricule_no = HrEmploye::where('id',$employe_id)->value('matricule_no');
        $data = HrPaiement::where('code',$code)->where('employe_id','!=','')->where('employe_id',$employe_id)->first();

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $currentTime = Carbon::now();
        $dateT =  $currentTime->toDateTimeString();
        $dateTime = str_replace([' ',':'], '_', $dateT);

        $pdf = PDF::loadView('backend.pages.hr.document.fiche_salaire',compact('data','setting','company'));

        Storage::put('public/'.$company->name.'/hr/bulletin-salaire/'.$matricule_no.'_'.$dateTime.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('Fiche_de_paie'.'.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrEmploye  $employe
     * @return \Illuminate\Http\Response
     */
    public function edit($id,$company_id)
    {
        if (is_null($this->user) || !$this->user->can('hr_employe.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier l\'employé! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $employe_id = HrPaiement::where('id',$id)->where('company_id',$company_id)->where('employe_id','!=','')->value('employe_id');
        $code = HrPaiement::where('id',$id)->where('company_id',$company_id)->where('employe_id','!=','')->value('code');
        $data = HrPaiement::where('code',$code)->where('company_id',$company_id)->where('employe_id','!=','')->where('employe_id',$employe_id)->first();

        $employes = HrEmploye::orderBy('firstname','asc')->get();

        return view('backend.pages.hr.paiement.edit',compact('data','employes','company_id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrEmploye  $employe
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_employe.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier l\'employé! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $rules = array(
                'date_debut' => 'required',
                'date_fin' => 'required|after:date_debut',
                'somme_salaire_base' => 'required',
                'employe_id' => 'required',
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

        $codeJournalPaieEncours = HrJournalPaie::where('etat', 0)->value('code');

        $plafond_cotisation = 450000;

        $employe_id = $request->employe_id;
        $date_debut = $request->date_debut;
        $date_fin = $request->date_fin;
        $indemnite_id = $request->indemnite_id;
        $cotisation_id = $request->cotisation_id;
        $impot_id = $request->impot_id;
        $etat = 0;
        $somme_salaire_base = $request->somme_salaire_base;
        $somme_salaire_net = $request->somme_salaire_net;
        $nbre_jours_ouvrables = $request->nbre_jours_ouvrables;
        $nbre_jours_conges = $request->nbre_jours_conges;
        $nbre_jours_conges_consomes = $request->nbre_jours_conges_consomes;
        $code_banque = $request->code_banque;
        $code_departement = $request->code_departement;
        $code_service = $request->code_service;
        $numero_compte = $request->numero_compte;
        $allocation_familiale = $request->allocation_familiale;
        $montant_ind_imposable = $request->montant_ind_imposable;
        $montant_ind_non_imposable = $request->montant_ind_non_imposable;
        $designation_prime_non_imposable = $request->designation_prime_non_imposable;
        $montant_prime_non_imposable = $request->montant_prime_non_imposable;
        $soins_medicaux = $request->soins_medicaux;
        $retenue_pret = $request->retenue_pret;
        $autre_retenue = $request->autre_retenue;
        $company_id = $request->company_id;
        $created_by = $this->user->name;
            


      $indemnite_deplacement = ($somme_salaire_base * 15)/100;
      $indemnite_logement = ($somme_salaire_base * 60)/100;
      $prime_fonction = $request->prime_fonction;


       $salaire_brut = $somme_salaire_base + $allocation_familiale + 
       $indemnite_logement + $indemnite_deplacement + $prime_fonction;


        if ($salaire_brut < 450000) {
                $inss = ($salaire_brut * 4)/100;
                $somme_cotisation_inss = ($salaire_brut * 4)/100;
                $inss_employeur = ($salaire_brut * 4)/100;
            }else{
                $inss = (450000 * 4)/100;
                $somme_cotisation_inss = ($plafond_cotisation * 4)/100;
                $inss_employeur = ($plafond_cotisation * 6)/100;
            }

            if ($salaire_brut < 250000) {
                $assurance_maladie_employe = 0;
                $assurance_maladie_employeur = 15000;
            }else{
                $assurance_maladie_employe = 6000;
                $assurance_maladie_employeur = 9000;
            }


        $base_imposable = $salaire_brut - $indemnite_logement - $indemnite_deplacement - $inss - $assurance_maladie_employe - $allocation_familiale;
      //les impots

            if ($base_imposable >= 0 && $base_imposable <= 150000) {
                $somme_impot = 0;
            }elseif ($base_imposable > 150000 && $base_imposable <= 300000) {
                $somme_impot = (($base_imposable - 150000) * 20)/100;
            }elseif ($base_imposable > 300000) {
                $somme_impot = 30000 + (($base_imposable - 300000) * 30)/100;    
            }


    $somme_salaire_brut_imposable = $salaire_brut;
    $somme_salaire_brut_non_imposable = $salaire_brut;
    $somme_salaire_net_imposable = $salaire_brut - $somme_cotisation_inss - $somme_impot;
    $somme_salaire_net_non_imposable = $salaire_brut - $somme_cotisation_inss - $somme_impot;


        $employe_id = HrPaiement::where('id',$id)->where('company_id',$company_id)->where('employe_id','!=','')->value('employe_id');
        $code = HrPaiement::where('id',$id)->where('employe_id','!=','')->value('code');
        $paiement = HrPaiement::where('code',$code)->where('company_id',$company_id)->where('employe_id','!=','')->where('employe_id',$employe_id)->first();
        $paiement->employe_id = $employe_id;
        $paiement->code = $codeJournalPaieEncours;
        $paiement->date_debut = $request->date_debut;
        $paiement->date_fin = $request->date_fin;
        $paiement->etat = 0;
        $paiement->somme_salaire_base = $somme_salaire_base;
        $paiement->somme_salaire_brut_imposable = $somme_salaire_brut_imposable;
        $paiement->somme_salaire_brut_non_imposable = $somme_salaire_brut_non_imposable;
        $paiement->somme_salaire_net_imposable = $somme_salaire_net_imposable;
        $paiement->somme_salaire_net_non_imposable = $somme_salaire_net_non_imposable;
        $paiement->nbre_jours_ouvrables = $request->nbre_jours_ouvrables;
        $paiement->code_banque = $code_banque;
        $paiement->code_departement = $code_departement;
        $paiement->code_service = $code_service;
        $paiement->numero_compte = $request->numero_compte;
        $paiement->somme_impot = $somme_impot;
        $paiement->somme_cotisation_inss = $somme_cotisation_inss;
        $paiement->inss_employeur = $inss_employeur;
        $paiement->assurance_maladie_employe = $assurance_maladie_employe;
        $paiement->assurance_maladie_employeur = $assurance_maladie_employeur;
        $paiement->indemnite_deplacement = $indemnite_deplacement;
        $paiement->indemnite_logement = $indemnite_logement;
        $paiement->allocation_familiale = $allocation_familiale;
        $paiement->soins_medicaux = $soins_medicaux;
        $paiement->prime_fonction = $prime_fonction;
        $paiement->retenue_pret = $request->retenue_pret;
        $paiement->autre_retenue = $request->autre_retenue;
        $paiement->company_id = $request->company_id;
        $paiement->created_by = $this->user->name;
        $paiement->save();

        $remuneration_brute = $salaire_brut;
        $total_deductions = $paiement->somme_cotisation_inss + $paiement->assurance_maladie_employe + $paiement->somme_impot + $paiement->retenue_pret + $paiement->soins_medicaux + $paiement->autre_retenue;
        $net_a_payer = $remuneration_brute - $total_deductions;

        $employe = HrEmploye::where('company_id',$company_id)->where('id',$employe_id)->first();
        $employe->somme_salaire_base = $paiement->somme_salaire_base;
        $employe->somme_salaire_net = $net_a_payer;
        $employe->indemnite_deplacement = ($paiement->somme_salaire_base * 15)/100;
        $employe->indemnite_logement = ($paiement->somme_salaire_base*60)/100;
        $employe->prime_fonction = $paiement->prime_fonction;
        $employe->save();

        session()->flash('success', $this->user->name.', vous avez modifié le bulletin de paie!!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrPaiement  $paiement
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_employe.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer l\'employé! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $paiement = HrPaiement::findOrFail($id);
        $paiement->delete();
        session()->flash('success', 'le bulletin de paie est supprimé !!');
        return redirect()->back();
    }
}

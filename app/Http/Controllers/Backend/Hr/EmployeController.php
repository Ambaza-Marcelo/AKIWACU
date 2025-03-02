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
use App\Models\HrEmploye;
use App\Models\HrGrade;
use App\Models\HrDepartement;
use App\Models\HrFonction;
use App\Models\HrService;
use App\Models\HrBanque;
use App\Models\HrReglage;
use App\Models\HrCompany;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use App\Exports\Hr\EmployeExport;
use Excel;


class EmployeController extends Controller
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




        $employes = HrEmploye::where('company_id',$company_id)->get();
        return view('backend.pages.hr.employe.index',compact('employes','company_id'));
    }
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('hr_employe.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les employés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $departements = HrDepartement::all();
        $services = HrService::all();
        $grades = HrGrade::all();
        $fonctions = HrFonction::all();
        $banques = HrBanque::all();
        $companies = HrCompany::all();
        $reglage = HrReglage::orderBy('created_at','desc')->first();

        return view('backend.pages.hr.employe.create',compact('departements','services','grades','fonctions','banques','companies','reglage'));
    }

     public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_employe.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les employés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        //
        $request->validate([
        'firstname' => 'required|min:4|max:255',
        'lastname' => 'required|min:4|max:255',
        'cni' => 'required|min:8|max:50',
        'birthdate' => 'required',
        'quartier_residence_actuel' => 'required',
        'pays' => 'required',
        'statut_matrimonial' => 'required',
        'date_debut' => 'required',
        'somme_salaire_base' => 'required',
        'departement_id' => 'required',
        'service_id' => 'required',
        'prime_fonction' => 'required',
        'fonction_id' => 'required',
        'grade_id' => 'required',
        'banque_id' => 'required',
        'gender' => 'required',

        ]);


        $code_banque = HrBanque::where('id',$request->banque_id)->value('code');
        $code_departement = HrDepartement::where('id',$request->departement_id)->value('code');
        $code_service = HrService::where('id',$request->service_id)->value('code');

            $latest = HrEmploye::latest()->first();
            if ($latest) {
               $matricule_no = 'EGR' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $matricule_no = 'EGR' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

        $employe = new HrEmploye();
        $employe->firstname = $request->firstname;
        $employe->lastname = $request->lastname;
        $employe->phone_no = $request->phone_no;
        $employe->mail = $request->mail;
        $employe->matricule_no = $matricule_no;
        $employe->fathername = $request->fathername;
        $employe->mothername = $request->mothername;
        $employe->cni = $request->cni;
        //$employe->type_contrat = $request->type_contrat;
        $employe->birthdate = $request->birthdate;
        $employe->bloodgroup = $request->bloodgroup;
        $employe->province = $request->province;
        $employe->commune = $request->commune;
        $employe->zone = $request->zone;
        $employe->quartier = $request->quartier;
        $employe->gender = $request->gender;
        $employe->children_number = $request->children_number;
        $employe->province_residence_actuel = $request->province_residence_actuel;
        $employe->commune_residence_actuel = $request->commune_residence_actuel;
        $employe->zone_residence_actuel = $request->zone_residence_actuel;
        $employe->quartier_residence_actuel = $request->quartier_residence_actuel;
        $employe->avenue_residence_actuel = $request->avenue_residence_actuel;
        $employe->numero = $request->numero;
        $employe->image = $request->image;
        $employe->pays = $request->pays;
        $employe->statut_matrimonial = $request->statut_matrimonial;
        $employe->date_debut = $request->date_debut;
        $employe->date_fin = $request->date_fin;
        $employe->etat = 0;
        $employe->somme_salaire_base = $request->somme_salaire_base;
        $employe->somme_salaire_net = $request->somme_salaire_net;
        $employe->nbre_jours_ouvrables = $request->nbre_jours_ouvrables;
        $employe->nbre_jours_conges = $request->nbre_jours_conges;
        $employe->nbre_jours_conges_consomes = $request->nbre_jours_conges_consomes;
        $employe->code_banque = $code_banque;
        $employe->code_departement = $code_departement;
        $employe->code_service = $code_service;
        $employe->numero_compte = $request->numero_compte;
        $employe->indemnite_deplacement = ($request->somme_salaire_base * 15)/100;
        $employe->indemnite_logement = ($request->somme_salaire_base*60)/100;
        $employe->prime_fonction = $request->prime_fonction;
        $employe->taux_assurance_maladie = $request->taux_assurance_maladie;
        $employe->taux_retraite = $request->taux_retraite;
        $employe->interet_credit_logement = $request->interet_credit_logement;
        $employe->retenue_pret = $request->retenue_pret;
        $employe->autre_retenue = $request->autre_retenue;
        $employe->created_by = $this->user->name;
        $employe->departement_id = $request->departement_id;
        $employe->service_id = $request->service_id;
        $employe->fonction_id = $request->fonction_id;
        $employe->grade_id = $request->grade_id;
        $employe->banque_id = $request->banque_id;
        $employe->company_id = $request->company_id;
        $employe->save();

        session()->flash('success', $this->user->name.', vous avez créé un Employé avec succés !!');

        return redirect()->route('admin.hr-employes.index',$employe->company_id);
    }

    public function show($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_employe.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de voir les employés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $data = HrEmploye::where('id',$id)->first();
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        return view('backend.pages.hr.employe.show',compact('data','setting'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrEmploye  $employe
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_employe.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier l\'employé! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        $departements = HrDepartement::all();
        $services = HrService::all();
        $grades = HrGrade::all();
        $fonctions = HrFonction::all();
        $banques = HrBanque::all();
        $companies = HrCompany::all();
        $reglage = HrReglage::orderBy('created_at','desc')->first();

        $employe = HrEmploye::where('id',$id)->first();
        return view('backend.pages.hr.employe.edit', compact('employe','departements','services','grades','fonctions','banques','companies','reglage','employe'));
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
        $request->validate([
        'firstname' => 'required|min:4|max:255',
        'lastname' => 'required|min:4|max:255',
        'cni' => 'required|min:8|max:50',
        'birthdate' => 'required',
        'quartier_residence_actuel' => 'required',
        'pays' => 'required',
        'statut_matrimonial' => 'required',
        'date_debut' => 'required',
        'somme_salaire_base' => 'required',
        'departement_id' => 'required',
        'service_id' => 'required',
        'prime_fonction' => 'required',
        'fonction_id' => 'required',
        'grade_id' => 'required',
        'banque_id' => 'required',

        ]);


        $code_banque = HrBanque::where('id',$request->banque_id)->value('code');
        $code_departement = HrDepartement::where('id',$request->departement_id)->value('code');
        $code_service = HrService::where('id',$request->service_id)->value('code');

        $employe = HrEmploye::findOrFail($id);
        $employe->firstname = $request->firstname;
        $employe->lastname = $request->lastname;
        $employe->phone_no = $request->phone_no;
        $employe->mail = $request->mail;
        $employe->fathername = $request->fathername;
        $employe->mothername = $request->mothername;
        $employe->cni = $request->cni;
        //$employe->type_contrat = $request->type_contrat;
        $employe->birthdate = $request->birthdate;
        $employe->bloodgroup = $request->bloodgroup;
        $employe->province = $request->province;
        $employe->commune = $request->commune;
        $employe->zone = $request->zone;
        $employe->quartier = $request->quartier;
        $employe->gender = $request->gender;
        $employe->children_number = $request->children_number;
        $employe->province_residence_actuel = $request->province_residence_actuel;
        $employe->commune_residence_actuel = $request->commune_residence_actuel;
        $employe->zone_residence_actuel = $request->zone_residence_actuel;
        $employe->quartier_residence_actuel = $request->quartier_residence_actuel;
        $employe->avenue_residence_actuel = $request->avenue_residence_actuel;
        $employe->numero = $request->numero;
        $employe->image = $request->image;
        $employe->pays = $request->pays;
        $employe->statut_matrimonial = $request->statut_matrimonial;
        $employe->date_debut = $request->date_debut;
        $employe->date_fin = $request->date_fin;
        $employe->etat = 0;
        $employe->somme_salaire_base = $request->somme_salaire_base;
        $employe->somme_salaire_net = $request->somme_salaire_net;
        $employe->nbre_jours_ouvrables = $request->nbre_jours_ouvrables;
        $employe->nbre_jours_conges = $request->nbre_jours_conges;
        $employe->nbre_jours_conges_consomes = $request->nbre_jours_conges_consomes;
        $employe->code_banque = $code_banque;
        $employe->code_departement = $code_departement;
        $employe->code_service = $code_service;
        $employe->numero_compte = $request->numero_compte;
        $employe->indemnite_deplacement = ($request->somme_salaire_base * 15)/100;
        $employe->indemnite_logement = ($request->somme_salaire_base*60)/100;
        $employe->prime_fonction = $request->prime_fonction;
        $employe->taux_assurance_maladie = $request->taux_assurance_maladie;
        $employe->taux_retraite = $request->taux_retraite;
        $employe->interet_credit_logement = $request->interet_credit_logement;
        $employe->retenue_pret = $request->retenue_pret;
        $employe->autre_retenue = $request->autre_retenue;
        $employe->created_by = $this->user->name;
        $employe->departement_id = $request->departement_id;
        $employe->service_id = $request->service_id;
        $employe->fonction_id = $request->fonction_id;
        $employe->grade_id = $request->grade_id;
        $employe->banque_id = $request->banque_id;
        $employe->company_id = $request->company_id;
        $employe->save();
        session()->flash('success', $this->user->name.', vous avez modifié Employé!!');
        return redirect()->back();
    }


    public function exportToExcel(Request $request)
    {
        return Excel::download(new EmployeExport, 'employes.xlsx');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrEmploye  $employe
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_employe.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer l\'employé! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $employe = HrEmploye::findOrFail($id);
        $employe->delete();
        session()->flash('success', 'Employé est supprimé !!');
        return redirect()->back();
    }
}

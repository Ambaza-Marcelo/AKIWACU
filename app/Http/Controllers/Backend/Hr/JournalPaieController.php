<?php

namespace App\Http\Controllers\Backend\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\HrEmploye;
use App\Models\HrJournalPaie;
use App\Models\HrJournalPaieDetail;
use App\Models\HrPaiement;
use App\Models\HrCompany;
use App\Exports\Hr\JournalPaieExport;
use Carbon\Carbon;
use Validator;
use Excel;
use PDF;

class JournalPaieController extends Controller
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

    public function index()
    {
        if (is_null($this->user) || !$this->user->can('hr_employe.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les employés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $journal_paies = HrJournalPaie::all();
        $journal_paie_encours = count(HrJournalPaie::where('etat', 0)->get());
        return view('backend.pages.hr.journal_paie.index',compact('journal_paies','journal_paie_encours'));
    }

    public function selectByCompany($code)
    {
        if (is_null($this->user) || !$this->user->can('hr_employe.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les employés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $companies = HrCompany::all();
        return view('backend.pages.hr.journal_paie.select_by_company',compact('companies','code'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('hr_journal_paie.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les paies! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        return view('backend.pages.hr.journal_paie.create');
    }

     public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_journal_paie.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les paies! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'date_debut' => 'required',
            'title' => 'required',
            'date_fin' => 'required|after:date_debut'

        ]);


            $latest = HrJournalPaie::latest()->first();
            if ($latest) {
               $code = (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $code = (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }



        $journal_paie = new HrJournalPaie();
        $journal_paie->date_debut = $request->date_debut;
        $journal_paie->title = $request->title;
        $journal_paie->date_fin = $request->date_fin;
        $journal_paie->etat = 0;
        $journal_paie->code = $code;
        $journal_paie->save();

        $journal_paie_detail = new HrJournalPaieDetail();
        $journal_paie_detail->date_debut = $request->date_debut;
        //$journal_paie_detail->title = $request->title;
        $journal_paie_detail->date_fin = $request->date_fin;
        $journal_paie_detail->etat = 0;
        $journal_paie_detail->code = $code;
        $journal_paie_detail->save();

        if($latest){
        $datas = HrPaiement::where('code',$latest->code)->get();
        foreach ($datas as $data) {
            $paiement = new HrPaiement();
            $paiement->employe_id = $data->employe_id;
            $paiement->code = $code;
            $paiement->date_debut = $request->date_debut;
            $paiement->date_fin = $request->date_fin;
            $paiement->etat = 0;
            $paiement->somme_salaire_base = $data->somme_salaire_base;
            $paiement->somme_salaire_brut_imposable = $data->somme_salaire_brut_imposable;
            $paiement->somme_salaire_brut_non_imposable = $data->somme_salaire_brut_non_imposable;
            $paiement->somme_salaire_net_imposable = $data->somme_salaire_net_imposable;
            $paiement->somme_salaire_net_non_imposable = $data->somme_salaire_net_non_imposable;
            $paiement->nbre_jours_ouvrables = $data->nbre_jours_ouvrables;
            $paiement->code_banque = $data->code_banque;
            $paiement->code_departement = $data->code_departement;
            $paiement->code_service = $data->code_service;
            $paiement->numero_compte = $data->numero_compte;
            $paiement->somme_impot = $data->somme_impot;
            $paiement->somme_cotisation_inss = $data->somme_cotisation_inss;
            $paiement->inss_employeur = $data->inss_employeur;
            $paiement->assurance_maladie_employe = $data->assurance_maladie_employe;
            $paiement->assurance_maladie_employeur = $data->assurance_maladie_employeur;
            $paiement->indemnite_deplacement = $data->indemnite_deplacement;
            $paiement->indemnite_logement = $data->indemnite_logement;
            $paiement->allocation_familiale = $data->allocation_familiale;
            $paiement->prime_fonction = $data->prime_fonction;
            $paiement->company_id = $data->company_id;
            $paiement->created_by = $this->user->name;
            $paiement->save();
        }
    }
     
    
        session()->flash('success', 'Journal Paie est créé !!');

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($code,$company_id)
    {
        //
        if (is_null($this->user) || !$this->user->can('hr_journal_paie.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les paies! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        $data = HrJournalPaie::where('code',$code)->first();

        $journal_paies = HrPaiement::where('code',$code)->where('employe_id','!=','')->where('company_id',$company_id)->get();
        /*
        $journal_paies = HrPaiement::select(
                        DB::raw('employe_id,somme_salaire_base,somme_impot,somme_cotisation_inss,inss_employeur,assurance_maladie_employe,assurance_maladie_employeur,indemnite_deplacement,indemnite_logement,allocation_familiale,soins_medicaux,prime_fonction,retenue_pret,autre_retenue'))->where('code',$code)->groupBy('employe_id','somme_salaire_base')->get();
                        */
        return view('backend.pages.hr.journal_paie.show',compact('journal_paies','data'));
        
    }

    public function journalPaie($code)
    {
        if (is_null($this->user) || !$this->user->can('hr_journal_paie.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $datas = HrJournalPaieDetail::select(
                        DB::raw('employe_id,somme_salaire_base,sum(somme_cotisation) as somme_cotisation,sum(somme_indemnite) as somme_indemnite,sum(somme_prime) as somme_prime,sum(somme_impot) as somme_impot,sum(avance_sur_salaire) as avance_sur_salaire,sum(autre_retenue) as autre_retenue'))->where('code',$code)->groupBy('employe_id','somme_salaire_base')->get();

        $journal_paie = HrJournalPaie::where('code', $code)->first();
        //$somme_indemnite = HrJournalPaieDetail::where('code',$code)->sum('somme_indemnite');
        $pdf = PDF::loadView('backend.pages.document.journal_paie',compact('datas','setting','journal_paie'))->setPaper('a4', 'landscape');

        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);

        Storage::put('public/journal-paie/'.$dateTime.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download($dateTime.'.pdf');
    }

    public function cloturer($code)
    {
       if (is_null($this->user) || !$this->user->can('hr_journal_paie.cloturer')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de clôturer le journal de paye! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
            HrJournalPaie::where('code', '=', $code)
                ->update(['etat' => 1,'cloture_par' => $this->user->name]);
            HrJournalPaieDetail::where('code', '=', $code)
                ->update(['etat' => 1,'cloture_par' => $this->user->name]);

            HrPaiement::where('code', '=', $code)
                ->update(['etat' => 1,'cloture_par' => $this->user->name]);

        session()->flash('success', 'Journal Paie  est clôturé avec succés!!');
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrJournalPaie  $journal_paie
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_journal_paie.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier le journal de paye! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $journal_paie = HrJournalPaie::findOrFail($id);
        return view('backend.pages.hr.journal_paie.edit', compact('journal_paie'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrJournalPaie  $journal_paie
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_journal_paie.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier le journal de paye! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'date_debut' => 'required',
            'date_fin' => 'required',
            'title' => 'required|min:10|max:255'

        ]);

        $journal_paie = HrJournalPaie::findOrFail($id);

        $journal_paie->date_debut = $request->date_debut;
        $journal_paie->title = $request->title;
        $journal_paie->date_fin = $request->date_fin;
        $journal_paie->etat = 0;
        $journal_paie->save();
        session()->flash('success', 'Journal Paie est modifié !!');
        return redirect()->route('admin.journal-paies.index');
    }


    public function exportToExcel(Request $request)
    {
        return Excel::download(new JournalPaieExport, 'journal_paie.xlsx');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrJournalPaie  $journal_paie
     * @return \Illuminate\Http\Response
     */
    public function destroy($code)
    {
        if (is_null($this->user) || !$this->user->can('hr_journal_paie.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer le journal de paye! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $journal_paie = HrJournalPaie::where('code',$code)->first();
        $journal_paie->delete();
        session()->flash('success', 'Journal Paie est supprimé !!');
        return redirect()->back();
    }
}

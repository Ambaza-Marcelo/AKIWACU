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
use App\Models\HrTakeCongePaye;
use App\Models\HrCongePaye;
use App\Models\HrTypeConge;
use App\Models\HrJournalCongePaye;
use App\Models\HrCompany;
use Carbon\Carbon;
use PDF;

class TakeCongePayeController extends Controller
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
        if (is_null($this->user) || !$this->user->can('hr_conge_paye.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les congés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $conge_payes = DB::table('hr_conge_payes')->orderBy('created_at','desc')->get();
        $take_conge_payes = HrTakeCongePaye::with('employe')->where('company_id',$company_id)->get();
        return view('backend.pages.hr.take_conge_paye.index',compact('take_conge_payes','conge_payes','company_id'));
    }

    public function selectByCompany()
    {
        if (is_null($this->user) || !$this->user->can('hr_conge_paye.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les congés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $companies = HrCompany::all();
        return view('backend.pages.hr.take_conge_paye.select_by_company',compact('companies'));
    }

    public function createByCompany()
    {
        if (is_null($this->user) || !$this->user->can('hr_conge_paye.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les congés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $companies = HrCompany::all();
        return view('backend.pages.hr.take_conge_paye.create_by_company',compact('companies'));
    }
    
    public function create($company_id)
    {
        if (is_null($this->user) || !$this->user->can('hr_conge_paye.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les congés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        $employes = HrEmploye::where('company_id',$company_id)->get();

        return view('backend.pages.hr.take_conge_paye.create',compact('employes','company_id'));
    }

    public function fetch(Request $request)
    {
        $data['data'] = HrJournalCongePaye::where("employe_id", $request->employe_id)->get(["nbre_jours_conge_restant","nbre_jours_conge_paye","nbre_jours_conge_pris", "employe_id"]);
  
        return response()->json($data);
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_conge_paye.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les congés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $currentDate = \Carbon\Carbon::now();
        $request->validate([
            'date_heure_debut' => 'required|after:'.$currentDate,
            'date_heure_fin' => 'required|after:date_heure_debut',
            'nbre_jours_conge_sollicite' => 'required',
            'nbre_jours_conge_restant' => 'required',
            'employe_id' => 'required'

        ]);

        try {DB::beginTransaction();

        $nbre_jours_conge_paye = HrJournalCongePaye::where('employe_id',$request->employe_id)->value('nbre_jours_conge_paye');
        $nbre_jours_conge_restant = HrJournalCongePaye::where('employe_id',$request->employe_id)->value('nbre_jours_conge_restant');
        $nbre_jours_conge_pris = HrJournalCongePaye::where('employe_id',$request->employe_id)->value('nbre_jours_conge_pris');

        $code = date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);


        if ($nbre_jours_conge_restant >= 1) {

        $take_conge_paye = new HrTakeCongePaye();
        $take_conge_paye->date_heure_debut = $request->date_heure_debut;
        $take_conge_paye->date_heure_fin = $request->date_heure_fin;
        $take_conge_paye->nbre_jours_conge_sollicite = $request->nbre_jours_conge_sollicite;
        $take_conge_paye->nbre_jours_conge_pris = $request->nbre_jours_conge_pris + $request->nbre_jours_conge_sollicite;
        $take_conge_paye->nbre_jours_conge_restant = $nbre_jours_conge_restant - $take_conge_paye->nbre_jours_conge_pris;
        $take_conge_paye->created_by = $this->user->name;
        $take_conge_paye->etat = 1;
        $take_conge_paye->code = $code;
        $take_conge_paye->employe_id = $request->employe_id;
        $take_conge_paye->company_id = $request->company_id;
        $take_conge_paye->save();

        $employe = HrEmploye::where('id',$request->employe_id)->value('lastname');

        DB::commit();

        session()->flash('success', $employe.', Vous venez de solliciter le congé payé de !'.$request->nbre_jours_conge_sollicite.' jours');

        return redirect()->route('admin.hr-take-conge-payes.index',$take_conge_paye->company_id);

        }else{
            session()->flash('error', $employe.', vous avez déjà sollicité '.$nbre_jours_conge_paye.' jours de votre congé payé!');
            return redirect()->route('admin.hr-take-conge-payes.index',$take_conge_paye->company_id);
        }


        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }


    public function validerConge($id)
    {
       if (is_null($this->user) || !$this->user->can('hr_conge_paye.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any conge !');
        }
            HrTakeCongePaye::where('id', '=', $id)
                ->update(['etat' => 2,'valide_par' => $this->user->name]);

        session()->flash('success', 'congé est validé avec succés!!');
        return back();
    }

    public function rejeterConge($id)
    {
       if (is_null($this->user) || !$this->user->can('hr_conge_paye.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any conge !');
        }

        try {DB::beginTransaction();

        $employe_id = HrTakeCongePaye::where('id',$id)->value('employe_id');

        $data = HrJournalCongePaye::where('employe_id', $employe_id)->first();

        $journal_conge_paye = HrJournalCongePaye::where('id',$data->id)->first();
        $journal_conge_paye->nbre_jours_conge_sollicite = 0;
        $journal_conge_paye->nbre_jours_conge_pris = $data->nbre_jours_conge_pris - $data->nbre_jours_conge_sollicite;
        
        $journal_conge_paye->nbre_jours_conge_restant = $data->nbre_jours_conge_restant + $data->nbre_jours_conge_sollicite;
        $journal_conge_paye->etat = -1;
        $journal_conge_paye->employe_id = $data->employe_id;
        $journal_conge_paye->rejete_par = $this->user->name;
        $journal_conge_paye->save();
        

        $take_conge_paye = HrTakeCongePaye::where('code',$data->code)->first();
        $take_conge_paye->etat = -1;
        $take_conge_paye->employe_id = $data->employe_id;
        //$take_conge_paye->rejete_par = $this->user->name;
        $take_conge_paye->save();

        DB::commit();
        session()->flash('success', 'congé est rejeté !!');
        return back();

        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

    public function annulerConge($id)
    {
       if (is_null($this->user) || !$this->user->can('hr_conge_paye.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any conge !');
        }

        $employe_id = HrTakeCongePaye::where('id',$id)->value('employe_id');

        $data = HrJournalCongePaye::where('employe_id', $employe_id)->first();


        HrJournalCongePaye::where('id', '=', $data->id)
                ->update(['etat' => 1,'annule_par' => $this->user->name]);

        session()->flash('success', 'congé est annulé!!');
        return back();
    }

    public function confirmerConge($id)
    {
       if (is_null($this->user) || !$this->user->can('hr_conge_paye.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any conge !');
        }

        HrTakeCongePaye::where('id', '=', $id)
                ->update(['etat' => 3,'confirme_par' => $this->user->name]);

        session()->flash('success', 'congé est confirmé!!');
        return back();
    }

    public function approuverConge($id)
    {
       if (is_null($this->user) || !$this->user->can('hr_conge_paye.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any conge !');
        }

        try {DB::beginTransaction();

        $datas = HrTakeCongePaye::where('id',$id)->get();

        foreach($datas as $data){
        $nbre_jours_conge_paye = HrJournalCongePaye::where('employe_id',$data->employe_id)->value('nbre_jours_conge_paye');
        $nbre_jours_conge_restant = HrJournalCongePaye::where('employe_id',$data->employe_id)->value('nbre_jours_conge_restant');
        $nbre_jours_conge_pris = HrJournalCongePaye::where('employe_id',$data->employe_id)->value('nbre_jours_conge_pris');

        $code = date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);


        if ($nbre_jours_conge_restant >= 1) {

        $journal_conge_paye = HrJournalCongePaye::where('employe_id',$data->employe_id)->first();
        $journal_conge_paye->date_heure_debut = $data->date_heure_debut;
        $journal_conge_paye->date_heure_fin = $data->date_heure_fin;
        $journal_conge_paye->nbre_jours_conge_sollicite = $data->nbre_jours_conge_sollicite;
        if (!empty($nbre_jours_conge_pris))  {
            $journal_conge_paye->nbre_jours_conge_pris = $nbre_jours_conge_pris + $data->nbre_jours_conge_sollicite;
        }else{
           $journal_conge_paye->nbre_jours_conge_pris = $data->nbre_jours_conge_sollicite; 
        }
        
        $journal_conge_paye->nbre_jours_conge_restant = $nbre_jours_conge_restant - $data->nbre_jours_conge_sollicite;
        $journal_conge_paye->created_by = $this->user->name;
        $journal_conge_paye->etat = 1;
        $journal_conge_paye->code = $code;
        $journal_conge_paye->employe_id = $data->employe_id;
        $journal_conge_paye->save();

    }

        HrTakeCongePaye::where('id', '=', $id)
                ->update(['etat' => 4,'approuve_par' => $this->user->name]);

        DB::commit();
        session()->flash('success', 'Congé Payé est approuvé avec succés !!');
        return back();

        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }


    public function lettreDemandeConge($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_conge_paye.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $employe_id = HrTakeCongePaye::where('id',$id)->value('employe_id');
        $data = HrJournalCongePaye::where('employe_id', $employe_id)->first();
        $take_conge_paye = HrTakeCongePaye::where('id', $id)->first();
        $pdf = PDF::loadView('backend.pages.hr.document.lettre_demande_conge',compact('data','take_conge_paye','setting'));

        // download pdf file
        return $pdf->download('lettre_demande_conge'.'.pdf');
        
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrTakeCongePaye  $take_conge_paye
     * @return \Illuminate\Http\Response
     */
    public function show($id){

    }
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_conge_paye.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modfier le congé! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        $employes = HrEmploye::all();
        $type_conges = HrTypeConge::all();
        $conge_payes = HrCongePaye::all();
        //
        $take_conge_paye = HrTakeCongePaye::findOrFail($id);
        return view('backend.pages.hr.take_conge_paye.edit', compact('take_conge_paye','employes','type_conges','conge_payes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrTakeCongePaye  $take_conge_paye
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_conge_paye.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modfier le congé! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'date_heure_debut' => 'required',
            'date_heure_fin' => 'required',
            'nbre_jours_conge_pris' => 'required',
            'employe_id' => 'required',
            'conge_paye_id' => 'required',

        ]);

        $take_conge_paye = HrTakeCongePaye::findOrFail($id);
        $take_conge_paye->date_heure_debut = $request->date_heure_debut;
        $take_conge_paye->date_heure_fin = $request->date_heure_fin;
        $take_conge_paye->nbre_jours_conge_paye = $request->nbre_jours_conge_paye;
        $take_conge_paye->nbre_jours_conge_sollicite = $request->nbre_jours_conge_sollicite;
        $take_conge_paye->nbre_jours_conge_pris = $request->nbre_jours_conge_pris;
        $take_conge_paye->nbre_jours_conge_restant = $request->nbre_jours_conge_restant;
        $take_conge_paye->auteur = $this->user->name;
        $take_conge_paye->etat = $request->etat;
        $take_conge_paye->employe_id = $request->employe_id;
        $take_conge_paye->conge_paye_id = $request->conge_paye_id;
        $take_conge_paye->save();
        session()->flash('success', 'congé est modifié !!');
        return redirect()->route('admin.hr-take-conge-payes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrTakeCongePaye  $take_conge_paye
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_conge_paye.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer le congé! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $take_conge_paye = HrTakeCongePaye::findOrFail($id);
        $take_conge_paye->delete();
        session()->flash('success', 'congé est supprimé !!');
        return redirect()->back();
    }
}

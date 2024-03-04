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
use App\Models\HrTakeConge;
use App\Models\HrTypeConge;
use App\Models\HrStagiaire;
use App\Models\HrCompany;
use PDF;
use Carbon\Carbon;

class TakeCongeController extends Controller
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
        if (is_null($this->user) || !$this->user->can('hr_conge.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les congés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $take_conges = HrTakeConge::with('employe')->where('company_id',$company_id)->get();
        return view('backend.pages.hr.take_conge.index',compact('take_conges','company_id'));
    }

    public function selectByCompany()
    {
        if (is_null($this->user) || !$this->user->can('hr_conge.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les congés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $companies = HrCompany::all();
        return view('backend.pages.hr.take_conge.select_by_company',compact('companies'));
    }

    public function createByCompany()
    {
        if (is_null($this->user) || !$this->user->can('hr_conge.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les congés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $companies = HrCompany::all();
        return view('backend.pages.hr.take_conge.create_by_company',compact('companies'));
    }

    public function create($company_id)
    {
        if (is_null($this->user) || !$this->user->can('hr_conge.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les congés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        $employes = HrEmploye::where('company_id',$company_id)->get();
        $stagiaires = HrStagiaire::where('company_id',$company_id)->get();
        $type_conges = HrTypeConge::all();

        return view('backend.pages.hr.take_conge.create',compact('type_conges','employes','stagiaires','company_id'));
    }

     public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_conge.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les congés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'date_heure_debut' => 'required',
            'date_heure_fin' => 'required',
            //'employe_id' => 'required',
            //'type_conge_id' => 'required',

        ]);

        $take_conge = new HrTakeConge();
        $take_conge->date_heure_debut = $request->date_heure_debut;
        $take_conge->date_heure_fin = $request->date_heure_fin;
        setlocale(LC_TIME, "fr_FR");
        $time1 = strftime(strtotime($take_conge->date_heure_debut));
        $time2 = strftime(strtotime($take_conge->date_heure_fin));


        $heures = $time2 - $time1;
 
        $nbre_heures = $heures / 3600;


        //$take_conge->nbre_jours_conge_pris = $request->nbre_jours_conge_pris;
        $take_conge->nbre_heures_conge_pris = $nbre_heures;
        $take_conge->auteur = $this->user->name;
        $take_conge->etat = 0;
        $take_conge->employe_id = $request->employe_id;
        $take_conge->stagiaire_id = $request->stagiaire_id;
        $take_conge->type_conge_id = $request->type_conge_id;
        $take_conge->company_id = $request->company_id;
        $take_conge->save();

        $employe = HrEmploye::where('id',$request->employe_id)->value('lastname');

        session()->flash('success', $this->user->name.' vous avez donné '.$employe.' '.number_format($nbre_heures,0).' heure(s) de sortie');

        return redirect()->route('admin.hr-take-conges.index',$take_conge->company_id);
    }


    public function billetSortie($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_conge.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $data = HrTakeConge::where('id', $id)->first();
        $pdf = PDF::loadView('backend.pages.hr.document.billet_sortie_conge',compact('data','setting'));

        // download pdf file
        return $pdf->download('billet_de_sortie'.'.pdf');
        
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrTakeConge  $take_conge
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_conge.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modfier le congé! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        $employes = HrEmploye::orderBy('firstname','desc')->get();
        $stagiaires = HrStagiaire::orderBy('firstname','desc')->get();
        $type_conges = HrTypeConge::all();
        $conge_payes = CongePaye::all();
        //
        $take_conge = HrTakeConge::findOrFail($id);
        return view('backend.pages.hr.take_conge.edit', compact('take_conge','employes','type_conges','conge_payes','stagiaires'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrTakeConge  $take_conge
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_conge.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modfier le congé! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'date_heure_debut' => 'required',
            'date_heure_fin' => 'required',
            'nbre_jours_conge_pris' => 'required',
            //'employe_id' => 'required',
            'type_conge_id' => 'required',

        ]);

        $take_conge = HrTakeConge::findOrFail($id);
        $take_conge->date_heure_debut = $request->date_heure_debut;
        $take_conge->date_heure_fin = $request->date_heure_fin;
        $take_conge->nbre_jours_conge_pris = $request->nbre_jours_conge_pris;
        $take_conge->nbre_heures_conge_pris = $request->nbre_heures_conge_pris;
        $take_conge->auteur = $this->user->name;
        $take_conge->etat = $request->etat;
        $take_conge->employe_id = $request->employe_id;
        $take_conge->stagiaire_id = $request->stagiaire_id;
        $take_conge->type_conge_id = $request->type_conge_id;
        $take_conge->save();
        session()->flash('success', 'HrTakeConge est modifié !!');
        return redirect()->route('admin.hr-take-conges.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrTakeConge  $take_conge
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_conge.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer le congé! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $take_conge = HrTakeConge::findOrFail($id);
        $take_conge->delete();
        session()->flash('success', 'HrTakeConge est supprimé !!');
        return redirect()->back();
    }
}

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
use App\Models\HrStagiaire;
use App\Models\HrGrade;
use App\Models\HrDepartement;
use App\Models\HrFonction;
use App\Models\HrService;
use App\Models\HrEcole;
use App\Models\HrFiliere;
use App\Models\HrCompany;

class StagiaireController extends Controller
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
        if (is_null($this->user) || !$this->user->can('hr_stagiaire.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les stagiaires! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $stagiaires = HrStagiaire::with('grade','departement','service','fonction')->where('company_id',$company_id)->get();
        return view('backend.pages.hr.stagiaire.index',compact('stagiaires'));
    }

    public function selectByCompany()
    {
        if (is_null($this->user) || !$this->user->can('hr_conge.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les stagiaires! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $companies = HrCompany::all();
        return view('backend.pages.hr.stagiaire.select_by_company',compact('companies'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('hr_stagiaire.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les stagiaires! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $departements = HrDepartement::all();
        $services = HrService::all();
        $grades = hrGrade::all();
        $fonctions = HrFonction::all();
        $ecoles = HrEcole::all();
        $filieres = HrFiliere::all();

        $companies = HrCompany::all();

        return view('backend.pages.hr.stagiaire.create',compact('departements','services','grades','fonctions','ecoles','filieres','companies'));
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_stagiaire.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les stagiaires! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'firstname' => 'required|min:5|max:255',
            'lastname' => 'required|min:5|max:255',
            //'phone_no' => 'required|min:8|max:15',
            'cni' => 'required|min:10|max:30',
            'birthdate' => 'required|before:2010-01-01',
            'company_id' => 'required',
            //'province' => 'required',
            //'commune' => 'required',
            //'zone' => 'required',
            //'quartier' => 'required',
            'academique_ou_professionnel' => 'required',
            'gender' => 'required'

        ]);

        $stagiaire = new HrStagiaire();
        $stagiaire->firstname = $request->firstname;
        $stagiaire->lastname = $request->lastname;
        $stagiaire->phone_no = $request->phone_no;
        $stagiaire->company_id = $request->company_id;
        $stagiaire->mail = $request->mail;
        $stagiaire->fathername = $request->fathername;
        $stagiaire->mothername = $request->mothername;
        $stagiaire->cni = $request->cni;
        $stagiaire->birthdate = $request->birthdate;
        $stagiaire->bloodgroup = $request->bloodgroup;
        $stagiaire->province = $request->province;
        $stagiaire->commune = $request->commune;
        $stagiaire->zone = $request->zone;
        $stagiaire->quartier = $request->quartier;
        $stagiaire->gender = $request->gender;
        $stagiaire->etat = 0;
        $stagiaire->province_residence_actuel = $request->province_residence_actuel;
        $stagiaire->commune_residence_actuel = $request->commune_residence_actuel;
        $stagiaire->zone_residence_actuel = $request->zone_residence_actuel;
        $stagiaire->quartier_residence_actuel = $request->quartier_residence_actuel;
        $stagiaire->avenue_residence_actuel = $request->avenue_residence_actuel;
        $stagiaire->numero = $request->numero;
        $stagiaire->academique_ou_professionnel = $request->academique_ou_professionnel;
        $stagiaire->auteur = $this->user->name;
        $stagiaire->departement_id = $request->departement_id;
        $stagiaire->service_id = $request->service_id;
        $stagiaire->fonction_id = $request->fonction_id;
        $stagiaire->grade_id = $request->grade_id;
        $stagiaire->ecole_id = $request->ecole_id;
        $stagiaire->filiere_id = $request->filiere_id;
        $stagiaire->save();

        session()->flash('success', 'Stagiaire est créé !!');

        return redirect()->route('admin.hr-stagiaires.index',$stagiaire->company_id);
    }


    public function show($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_employe.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de voir les employés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $data = HrStagiaire::where('id',$id)->first();
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        return view('backend.pages.hr.stagiaire.show',compact('data','setting'));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrStagiaire  $stagiaire
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_stagiaire.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier l\'employé! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        $departements = HrDepartement::all();
        $services = HrService::all();
        $grades = HrGrade::all();
        $fonctions = HrFonction::all();
        $ecoles = HrEcole::all();
        $filieres = HrFiliere::all();
        $companies = HrCompany::all();
        //
        $stagiaire = HrStagiaire::findOrFail($id);
        return view('backend.pages.hr.stagiaire.edit', compact('stagiaire','departements','services','grades','fonctions','filieres','ecoles','companies'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrStagiaire  $stagiaire
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_stagiaire.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier l\'employé! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            //'phone_no' => 'required',
            'cni' => 'required|min:10|max:30',
            'birthdate' => 'required|before:2010-01-01',
            'company_id' => 'required',
            //'province' => 'required',
            //'commune' => 'required',
            //'zone' => 'required',
            //'quartier' => 'required',
            'gender' => 'required'

        ]);

        $stagiaire = HrStagiaire::findOrFail($id);
        $stagiaire->firstname = $request->firstname;
        $stagiaire->lastname = $request->lastname;
        $stagiaire->phone_no = $request->phone_no;
        $stagiaire->company_id = $request->company_id;
        $stagiaire->mail = $request->mail;
        $stagiaire->fathername = $request->fathername;
        $stagiaire->mothername = $request->mothername;
        $stagiaire->cni = $request->cni;
        $stagiaire->birthdate = $request->birthdate;
        $stagiaire->bloodgroup = $request->bloodgroup;
        $stagiaire->province = $request->province;
        $stagiaire->commune = $request->commune;
        $stagiaire->zone = $request->zone;
        $stagiaire->quartier = $request->quartier;
        $stagiaire->gender = $request->gender;
        $stagiaire->province_residence_actuel = $request->province_residence_actuel;
        $stagiaire->commune_residence_actuel = $request->commune_residence_actuel;
        $stagiaire->zone_residence_actuel = $request->zone_residence_actuel;
        $stagiaire->quartier_residence_actuel = $request->quartier_residence_actuel;
        $stagiaire->avenue_residence_actuel = $request->avenue_residence_actuel;
        $stagiaire->numero = $request->numero;
        $stagiaire->academique_ou_professionnel = $request->academique_ou_professionnel;
        $stagiaire->created_by = $this->user->name;
        $stagiaire->departement_id = $request->departement_id;
        $stagiaire->service_id = $request->service_id;
        $stagiaire->fonction_id = $request->fonction_id;
        $stagiaire->grade_id = $request->grade_id;
        $stagiaire->ecole_id = $request->ecole_id;
        $stagiaire->filiere_id = $request->filiere_id;
        $stagiaire->save();
        session()->flash('success', 'Stagiaire est modifié !!');
        return redirect()->route('admin.hr-stagiaires.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrStagiaire  $stagiaire
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_stagiaire.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer l\'employé! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $stagiaire = HrStagiaire::findOrFail($id);
        $stagiaire->delete();
        session()->flash('success', 'Stagiaire est supprimé !!');
        return redirect()->back();
    }
}

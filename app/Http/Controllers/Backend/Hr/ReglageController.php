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
use App\Models\HrReglage;
use App\Models\HrGroupeCotisation;
use App\Models\HrGroupeIndemnite;
use App\Models\HrGroupeImpot;
use App\Models\HrTypeCotisation;
use App\Models\HrTypeIndemnite;
use App\Models\HrTypePrime;
use App\Models\HrPrime;
use App\Models\HrIndemnite;
use App\Models\HrCotisation;
use App\Models\HrImpot;
use App\Models\HrTypeConge;
use App\Models\HrTypeAbsence;

class ReglageController extends Controller
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
        if (is_null($this->user) || !$this->user->can('hr_reglage.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les reglages! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $reglage = HrReglage::orderBy('created_at','desc')->first();
        $type_absences = HrTypeAbsence::all();
        return view('backend.pages.hr.reglage.index',compact('reglage','type_absences'));
    }
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('hr_reglage.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les reglages! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        return view('backend.pages.hr.reglage.create');
    }

     public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_reglage.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les reglages! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
        'nbre_jours_ouvrables' => 'required',
        //'nbre_jours_feries' => 'required',
        //'jour_anticipation_conge' => 'required',
        'jour_conge_par_mois' => 'required',
        'min_jour_conge_paye' => 'required',
        'max_jour_conge_paye' => 'required',
        //'taux_assurance_maladie' => 'required',
        //'taux_retraite' => 'required',
        //'interet_credit_logement' => 'required',
        //'retenue_pret' => 'required',
        'prafond_impot' => 'required',
        'prafond_cotisation' => 'required',

        ]);

        $reglage = new HrReglage();
        $reglage->nbre_jours_ouvrables = $request->nbre_jours_ouvrables;
        $reglage->nbre_jours_feries = $request->nbre_jours_feries;
        $reglage->jour_anticipation_conge = $request->jour_anticipation_conge;
        $reglage->jour_conge_par_mois = $request->jour_conge_par_mois;
        $reglage->min_jour_conge_paye = $request->min_jour_conge_paye;
        $reglage->max_jour_conge_paye = $request->max_jour_conge_paye;
        $reglage->taux_assurance_maladie = $request->taux_assurance_maladie;
        $reglage->taux_retraite = $request->taux_retraite;
        $reglage->interet_credit_logement = $request->interet_credit_logement;
        $reglage->retenue_pret = $request->retenue_pret;
        $reglage->prafond_impot = $request->prafond_impot;
        $reglage->prafond_cotisation = $request->prafond_cotisation;
        $reglage->save();

        session()->flash('success', $this->user->name.', vous avez enregistré avec succés !!');

        return redirect()->route('admin.hr-reglages.index');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrReglage  $reglage
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_reglage.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier reglage! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $reglage = HrReglage::findOrFail($id);
        return view('backend.pages.hr.reglage.edit', compact('reglage'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrReglage  $reglage
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_reglage.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier reglage! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $request->validate([
        'nbre_jours_ouvrables' => 'required',
        //'nbre_jours_feries' => 'required',
        //'jour_anticipation_conge' => 'required',
        'jour_conge_par_mois' => 'required',
        'min_jour_conge_paye' => 'required',
        'max_jour_conge_paye' => 'required',
        //'taux_assurance_maladie' => 'required',
        //'taux_retraite' => 'required',
        //'interet_credit_logement' => 'required',
        //'retenue_pret' => 'required',
        'prafond_impot' => 'required',
        'prafond_cotisation' => 'required',

        ]);

        $reglage = HrReglage::findOrFail($id);
        $reglage->nbre_jours_ouvrables = $request->nbre_jours_ouvrables;
        $reglage->nbre_jours_feries = $request->nbre_jours_feries;
        $reglage->jour_anticipation_conge = $request->jour_anticipation_conge;
        $reglage->jour_conge_par_mois = $request->jour_conge_par_mois;
        $reglage->min_jour_conge_paye = $request->min_jour_conge_paye;
        $reglage->max_jour_conge_paye = $request->max_jour_conge_paye;
        $reglage->taux_assurance_maladie = $request->taux_assurance_maladie;
        $reglage->taux_retraite = $request->taux_retraite;
        $reglage->interet_credit_logement = $request->interet_credit_logement;
        $reglage->retenue_pret = $request->retenue_pret;
        $reglage->prafond_impot = $request->prafond_impot;
        $reglage->prafond_cotisation = $request->prafond_cotisation;
        $reglage->save();
        session()->flash('success', $this->user->name.', vous avez modifié avec succés!!');
        return redirect()->route('admin.hr-reglages.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrReglage  $reglage
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_reglage.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer reglage! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $reglage = HrReglage::findOrFail($id);
        $reglage->delete();
        session()->flash('success', 'reglage est supprimé !!');
        return redirect()->back();
    }
}

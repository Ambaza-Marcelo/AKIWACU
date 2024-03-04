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
use App\Models\HrCongePaye;
use App\Models\HrJournalCongePaye;
use App\Models\TakeCongePaye;

use Validator;

class JournalCongePayeController extends Controller
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

    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_conge_paye.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier l\'indemnité! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $journalCongePaye = HrJournalCongePaye::where('id',$id)->first();

        return view('backend.pages.hr.journal_conge_paye.edit', compact('journalCongePaye'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\JournalPaie  $journal_paie
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_conge_paye.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier l\'indemnité! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'employe_id' => 'required',
            'nbre_jours_conge_paye' => 'required',
            'nbre_jours_conge_pris' => 'required',
            'nbre_jours_conge_restant' => 'required',
            'session' => 'required',
        ]);

        $journal_conge_paye = HrJournalCongePaye::findOrFail($id);

        $journal_conge_paye->employe_id = $request->employe_id;
        $journal_conge_paye->nbre_jours_conge_paye = $request->nbre_jours_conge_paye;
        $journal_conge_paye->nbre_jours_conge_pris = $request->nbre_jours_conge_pris;
        $journal_conge_paye->nbre_jours_conge_restant = $request->nbre_jours_conge_restant;
        $journal_conge_paye->session = $request->session;
        $journal_conge_paye->save();
        session()->flash('success', 'Journal Congé Payé est modifié !!');
        return redirect()->back();
    }
}

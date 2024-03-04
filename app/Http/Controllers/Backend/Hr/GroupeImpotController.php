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
use App\Models\HrGroupeImpot;
use Validator;

class GroupeImpotController extends Controller
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

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('hr_impot.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer l\'indemnité! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        return view('backend.pages.hr.groupe_impot.create');
    }

     public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_impot.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer l\'indemnité! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        //
        $request->validate([
            'designation' => 'required',
            'classe_inferieure' => 'required',
            'classe_superieure' => 'required',

        ]);

        $groupe_impot = new HrGroupeImpot();
        $groupe_impot->designation = $request->designation;
        $groupe_impot->classe_inferieure = $request->classe_inferieure;
        $groupe_impot->classe_superieure = $request->classe_superieure;
        $groupe_impot->save();

        session()->flash('success', 'Groupe Impot est créé !!');

        return redirect()->back();
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrGroupeImpot  $groupe_impot
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_impot.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier l\'indemnité! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $groupe_impot = HrGroupeImpot::findOrFail($id);
        return view('backend.pages.hr.groupe_impot.edit', compact('groupe_impot'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrGroupeImpot  $groupe_impot
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_impot.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier l\'indemnité! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'designation' => 'required',
            'classe_inferieure' => 'required',
            'classe_superieure' => 'required',

        ]);

        $groupe_impot = HrGroupeImpot::findOrFail($id);

        $groupe_impot->designation = $request->designation;
        $groupe_impot->classe_inferieure = $request->classe_inferieure;
        $groupe_impot->classe_superieure = $request->classe_superieure;
        $groupe_impot->save();
        session()->flash('success', 'Groupe Impot est modifié !!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrGroupeImpot  $groupe_impot
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_impot.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer l\'indemnité! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $groupe_impot = HrGroupeImpot::findOrFail($id);
        $groupe_impot->delete();
        session()->flash('success', 'Groupe Impot est supprimé !!');
        return redirect()->back();
    }
}

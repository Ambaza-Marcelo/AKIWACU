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
use App\Models\HrGroupeCotisation;
use Validator;

class GroupeCotisationController extends Controller
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
        if (is_null($this->user) || !$this->user->can('hr_cotisation.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer la cotisation! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        return view('backend.pages.hr.groupe_cotisation.create');
    }

     public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_cotisation.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer la cotisation! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        //
        $request->validate([
            'designation' => 'required',
            'classe_inferieure' => 'required',
            'classe_superieure' => 'required',

        ]);

        $groupe_cotisation = new HrGroupeCotisation();
        $groupe_cotisation->designation = $request->designation;
        $groupe_cotisation->classe_inferieure = $request->classe_inferieure;
        $groupe_cotisation->classe_superieure = $request->classe_superieure;
        $groupe_cotisation->save();

        session()->flash('success', 'Groupe Cotisation est créé !!');

        return redirect()->back();
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrGroupeCotisation  $groupe_cotisation
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_cotisation.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier la cotisation! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $groupe_cotisation = HrGroupeCotisation::findOrFail($id);
        return view('backend.pages.hr.groupe_cotisation.edit', compact('groupe_cotisation'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrGroupeCotisation  $groupe_cotisation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_cotisation.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier la cotisation! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'designation' => 'required',
            'classe_inferieure' => 'required',
            'classe_superieure' => 'required',

        ]);

        $groupe_cotisation = HrGroupeCotisation::findOrFail($id);

        $groupe_cotisation->designation = $request->designation;
        $groupe_cotisation->classe_inferieure = $request->classe_inferieure;
        $groupe_cotisation->classe_superieure = $request->classe_superieure;
        $groupe_cotisation->save();
        session()->flash('success', 'Groupe Cotisation est modifié !!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrGroupeCotisation  $groupe_cotisation
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_cotisation.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer la cotisation! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $groupe_cotisation = HrGroupeCotisation::findOrFail($id);
        $groupe_cotisation->delete();
        session()->flash('success', 'Groupe Cotisation est supprimé !!');
        return redirect()->back();
    }
}

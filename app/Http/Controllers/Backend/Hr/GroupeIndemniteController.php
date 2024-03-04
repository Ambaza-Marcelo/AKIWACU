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
use App\Models\HrGroupeIndemnite;
use Validator;

class GroupeIndemniteController extends Controller
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
        if (is_null($this->user) || !$this->user->can('hr_indemnite.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser l\'indemnité! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $groupe_indemnites = DB::table('hr_groupe_indemnites')->get();
        return view('backend.pages.hr.groupe_indemnite.index',compact('groupe_indemnites'));
    }
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('hr_indemnite.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer l\'indemnité! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        return view('backend.pages.hr.groupe_indemnite.create');
    }

     public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_indemnite.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer l\'indemnité! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        //
        $request->validate([
            'designation' => 'required',
            'classe_inferieure' => 'required',
            'classe_superieure' => 'required',

        ]);

        $groupe_indemnite = new HrGroupeIndemnite();
        $groupe_indemnite->designation = $request->designation;
        $groupe_indemnite->classe_inferieure = $request->classe_inferieure;
        $groupe_indemnite->classe_superieure = $request->classe_superieure;
        $groupe_indemnite->save();

        session()->flash('success', 'Groupe Indemnite est créé !!');

        return redirect()->back();
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrGroupeIndemnite  $groupe_indemnite
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_indemnite.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier l\'indemnité! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $groupe_indemnite = HrGroupeIndemnite::findOrFail($id);
        return view('backend.pages.hr.groupe_indemnite.edit', compact('groupe_indemnite'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrGroupeIndemnite  $groupe_indemnite
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_indemnite.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier l\'indemnité! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'designation' => 'required',
            'classe_inferieure' => 'required',
            'classe_superieure' => 'required',

        ]);

        $groupe_indemnite = HrGroupeIndemnite::findOrFail($id);

        $groupe_indemnite->designation = $request->designation;
        $groupe_indemnite->classe_inferieure = $request->classe_inferieure;
        $groupe_indemnite->classe_superieure = $request->classe_superieure;
        $groupe_indemnite->save();
        session()->flash('success', 'Groupe Indemnite est modifié !!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrGroupeIndemnite  $groupe_indemnite
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_indemnite.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer l\'indemnité! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $groupe_indemnite = HrGroupeIndemnite::findOrFail($id);
        $groupe_indemnite->delete();
        session()->flash('success', 'Groupe Indemnite est supprimé !!');
        return redirect()->back();
    }
}

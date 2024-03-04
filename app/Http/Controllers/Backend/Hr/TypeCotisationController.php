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
use App\Models\HrTypeCotisation;

class TypeCotisationController extends Controller
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

        return view('backend.pages.hr.type_cotisation.create');
    }

     public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_cotisation.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer la cotisation! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        //
        $request->validate([
            'name' => 'required',

        ]);

        $type_cotisation = new HrTypeCotisation();
        $type_cotisation->name = $request->name;
        $type_cotisation->save();

        session()->flash('success', 'Type Cotisation est créé !!');

        return redirect()->back();
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrTypeCotisation  $type_cotisation
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_cotisation.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier la cotisation! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $type_cotisation = HrTypeCotisation::findOrFail($id);
        return view('backend.pages.hr.type_cotisation.edit', compact('type_cotisation'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrTypeCotisation  $type_cotisation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_cotisation.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier la cotisation! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'name' => 'required',

        ]);

        $type_cotisation = HrTypeCotisation::findOrFail($id);

        $type_cotisation->name = $request->name;
        $type_cotisation->save();
        session()->flash('success', 'Type Cotisation est modifié !!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrTypeCotisation  $type_cotisation
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_cotisation.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer la cotisation! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $type_cotisation = HrTypeCotisation::findOrFail($id);
        $type_cotisation->delete();
        session()->flash('success', 'Type Cotisation est supprimé !!');
        return redirect()->back();
    }
}

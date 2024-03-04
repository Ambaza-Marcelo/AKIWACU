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
use App\Models\HrTypeConge;

class TypeCongeController extends Controller
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
        if (is_null($this->user) || !$this->user->can('hr_conge.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les congés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $type_conges = HrTypeConge::all();

        return view('backend.pages.hr.type_conge.create',compact('type_conges'));
    }


    public function create()
    {
        if (is_null($this->user) || !$this->user->can('hr_conge.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les congés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        return view('backend.pages.hr.type_conge.create');
    }

     public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_conge.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les congés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'libelle' => 'required',
            'description' => 'required|min:10|max:500',

        ]);

        $type_conge = new HrTypeConge();
        $type_conge->libelle = $request->libelle;
        $type_conge->description = $request->description;
        $type_conge->save();

        session()->flash('success', 'Type Congé est créé !!');

        return redirect()->back();
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrTypeConge  $type_conge
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_conge.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modfier le congé! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $type_conge = HrTypeConge::findOrFail($id);
        return view('backend.pages.hr.type_conge.edit', compact('type_conge'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrTypeConge  $type_conge
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_conge.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modfier le congé! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'libelle' => 'required',
            'description' => 'required|min:10|max:500',

        ]);

        $type_conge = HrTypeConge::findOrFail($id);
        $type_conge->libelle = $request->libelle;
        $type_conge->description = $request->description;
        $type_conge->save();
        session()->flash('success', 'Type Congé est modifié !!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrTypeConge  $type_conge
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_conge.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer le congé! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $type_conge = HrTypeConge::findOrFail($id);
        $type_conge->delete();
        session()->flash('success', 'Type Congé est supprimé !!');
        return redirect()->back();
    }
}

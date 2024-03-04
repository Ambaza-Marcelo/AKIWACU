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
use App\Models\HrEcole;

class EcoleController extends Controller
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
        if (is_null($this->user) || !$this->user->can('hr_ecole.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les ecoles! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $ecoles = DB::table('hr_ecoles')->orderBy('created_at','desc')->get();
        return view('backend.pages.hr.ecole.index',compact('ecoles'));
    }
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('hr_ecole.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer l\'école! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        return view('backend.pages.hr.ecole.create');
    }

     public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_ecole.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer l\'école! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'nom' => 'required',
            'adresse' => 'required',

        ]);

        $ecole = new HrEcole();
        $ecole->nom = $request->nom;
        $ecole->etat = $request->etat;
        $ecole->adresse = $request->adresse;
        $ecole->description = $request->description;
        $ecole->save();

        session()->flash('success', 'Ecole est créé !!');

        return redirect()->route('admin.hr-ecoles.index');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Ecole  $ecole
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_ecole.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier l\'école! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $ecole = HrEcole::findOrFail($id);
        return view('backend.pages.hr.ecole.edit', compact('ecole'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrEcole  $ecole
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_ecole.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier l\'école! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'nom' => 'required',
            'adresse' => 'required',

        ]);

        $ecole = HrEcole::findOrFail($id);

        $ecole->nom = $request->nom;
        $ecole->etat = $request->etat;
        $ecole->adresse = $request->adresse;
        $ecole->description = $request->description;
        $ecole->save();
        session()->flash('success', 'Ecole est modifié !!');
        return redirect()->route('admin.hr-ecoles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrEcole  $ecole
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_ecole.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer l\'école! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $ecole = HrEcole::findOrFail($id);
        $ecole->delete();
        session()->flash('success', 'Ecole est supprimé !!');
        return redirect()->back();
    }
}

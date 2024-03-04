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
use App\Models\HrFiliere;

class FiliereController extends Controller
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
        if (is_null($this->user) || !$this->user->can('hr_filiere.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les filieres! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $filieres = DB::table('hr_filieres')->orderBy('created_at','desc')->get();
        return view('backend.pages.hr.filiere.index',compact('filieres'));
    }
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('hr_filiere.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer le filiere! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        return view('backend.pages.hr.filiere.create');
    }

     public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_filiere.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer le filiere! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'nom' => 'required',

        ]);

        $filiere = new HrFiliere();
        $filiere->nom = $request->nom;
        $filiere->save();

        session()->flash('success', 'Filiere est créé !!');

        return redirect()->route('admin.hr-filieres.index');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrFiliere  $filiere
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_filiere.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier le filiere! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $filiere = HrFiliere::findOrFail($id);
        return view('backend.pages.hr.filiere.edit', compact('filiere'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrFiliere  $filiere
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_filiere.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier le filiere! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'nom' => 'required',

        ]);

        $filiere = HrFiliere::findOrFail($id);

        $filiere->nom = $request->nom;
        $filiere->save();
        session()->flash('success', 'Filiere est modifié !!');
        return redirect()->route('admin.hr-filieres.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrFiliere  $filiere
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_filiere.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer le filiere! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $filiere = HrFiliere::findOrFail($id);
        $filiere->delete();
        session()->flash('success', 'Filiere est supprimé !!');
        return redirect()->back();
    }
}

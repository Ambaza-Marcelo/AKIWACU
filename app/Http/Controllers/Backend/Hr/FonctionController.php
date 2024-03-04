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
use App\Models\HrFonction;


class FonctionController extends Controller
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
        if (is_null($this->user) || !$this->user->can('hr_fonction.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser la fonction! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $fonctions = HrFonction::all();

        return view('backend.pages.hr.fonction.index',compact('fonctions'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('hr_fonction.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer la fonction! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        return view('backend.pages.hr.fonction.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_fonction.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer la fonction! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'name' => 'required',

        ]);

        $fonction = new HrFonction();
        $fonction->name = $request->name;
        $fonction->save();

        session()->flash('success', 'Fonction est créé !!');

        return redirect()->back();
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrFonction  $fonction
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_fonction.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier la fonction! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $fonction = HrFonction::findOrFail($id);
        return view('backend.pages.hr.fonction.edit', compact('fonction'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrFonction  $fonction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_fonction.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier la fonction! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'name' => 'required',

        ]);

        $fonction = HrFonction::findOrFail($id);

        $fonction->name = $request->name;
        $fonction->save();
        session()->flash('success', 'Fonction est modifié !!');
        return redirect()->route('admin.hr-fonctions.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrFonction  $fonction
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_fonction.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer la fonction! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $fonction = HrFonction::findOrFail($id);
        $fonction->delete();
        session()->flash('success', 'Fonction est supprimé !!');
        return redirect()->back();
    }
}

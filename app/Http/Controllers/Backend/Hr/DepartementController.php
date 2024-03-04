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
use App\Models\HrDepartement;

class DepartementController extends Controller
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
        if (is_null($this->user) || !$this->user->can('hr_departement.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de voir le departement! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $departements = HrDepartement::all();
        return view('backend.pages.hr.departement.index',compact('departements'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('hr_departement.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer le departement! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        return view('backend.pages.hr.departement.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_departement.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer le departement! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'name' => 'required',

        ]);

            $latest = HrDepartement::latest()->first();
            if ($latest) {
               $code = 'DEP' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $code = 'DEP' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

        $departement = new HrDepartement();
        $departement->name = $request->name;
        $departement->code = $code;
        $departement->save();


        session()->flash('success', 'Departement est créé !!');

        return redirect()->back();
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrDepartement  $departement
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_departement.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier le departement! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $departement = HrDepartement::findOrFail($id);
        return view('backend.pages.hr.departement.edit', compact('departement'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrDepartement  $departement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_departement.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier le departement! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'name' => 'required',

        ]);

        $departement = HrDepartement::findOrFail($id);

        $departement->name = $request->name;
        $departement->save();

        session()->flash('success', 'Departement est modifié !!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrDepartement  $departement
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_departement.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer le departement! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $departement = HrDepartement::findOrFail($id);
        $departement->delete();
        session()->flash('success', 'Departement est supprimé !!');
        return redirect()->back();
    }
}

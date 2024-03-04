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
use App\Models\HrTypeIndemnite;

class TypeIndemniteController extends Controller
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
        if (is_null($this->user) || !$this->user->can('hr_indemnite.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les indemnités! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        return view('backend.pages.hr.type_indemnite.create');
    }

     public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_indemnite.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les indemnités! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'name' => 'required',
            'type' => 'required',

        ]);

        $type_indemnite = new HrTypeIndemnite();
        $type_indemnite->name = $request->name;
        $type_indemnite->type = $request->type;
        $type_indemnite->save();

        session()->flash('success', 'Type Indemnite est créé !!');

        return redirect()->back();
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrTypeIndemnite  $type_indemnite
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_indemnite.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier l\'indemnité! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $type_indemnite = HrTypeIndemnite::findOrFail($id);
        return view('backend.pages.hr.type_indemnite.edit', compact('type_indemnite'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrTypeIndemnite  $type_indemnite
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_indemnite.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier l\'indemnité! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'name' => 'required',
            'type' => 'required',

        ]);

        $type_indemnite = HrTypeIndemnite::findOrFail($id);

        $type_indemnite->name = $request->name;
        $type_indemnite->type = $request->type;
        $type_indemnite->save();
        session()->flash('success', 'Type Indemnite est modifié !!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrTypeIndemnite  $type_indemnite
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_indemnite.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer l\'indemnité! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $type_indemnite = HrTypeIndemnite::findOrFail($id);
        $type_indemnite->delete();
        session()->flash('success', 'TypeIndemnite est supprimé !!');
        return redirect()->back();
    }
}

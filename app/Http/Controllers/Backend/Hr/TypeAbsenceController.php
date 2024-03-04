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
use App\Models\HrTypeAbsence;

class TypeAbsenceController extends Controller
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
        if (is_null($this->user) || !$this->user->can('hr_absence.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer type absence! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        return view('backend.pages.hr.type_absence.create');
    }

     public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_absence.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer type absence! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        //
        $request->validate([
            'name' => 'required',

        ]);

        $type_absence = new HrTypeAbsence();
        $type_absence->name = $request->name;
        $type_absence->save();

        session()->flash('success', 'Type Abasence est créé !!');

        return redirect()->back();
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrTypeAbsence  $type_absence
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_absence.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier type absence! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $type_absence = HrTypeAbsence::findOrFail($id);
        return view('backend.pages.hr.type_absence.edit', compact('type_absence'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrTypeAbsence  $type_absence
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_absence.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier type absence! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'name' => 'required',

        ]);

        $type_absence = HrTypeAbsence::findOrFail($id);

        $type_absence->name = $request->name;
        $type_absence->save();
        session()->flash('success', 'Type Abasence est modifié !!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrTypeAbsence  $type_absence
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_absence.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer type absence! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $type_absence = HrTypeAbsence::findOrFail($id);
        $type_absence->delete();
        session()->flash('success', 'Type Abasence est supprimé !!');
        return redirect()->back();
    }
}

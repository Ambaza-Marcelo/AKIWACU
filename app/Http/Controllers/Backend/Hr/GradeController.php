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
use App\Models\HrGrade;

class GradeController extends Controller
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
        if (is_null($this->user) || !$this->user->can('hr_grade.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser le grade! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $grades = HrGrade::all();

        return view('backend.pages.hr.grade.index',compact('grades'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('hr_grade.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer le grade! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        return view('backend.pages.hr.grade.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_grade.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer le grade! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'name' => 'required',

        ]);

        $grade = new HrGrade();
        $grade->name = $request->name;
        $grade->save();

        session()->flash('success', 'Grade est créé !!');

        return redirect()->back();
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrGrade  $grade
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_grade.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier le grade! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $grade = HrGrade::findOrFail($id);
        return view('backend.pages.hr.grade.edit', compact('grade'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrGrade  $grade
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_grade.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier le grade! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'name' => 'required',

        ]);

        $grade = HrGrade::findOrFail($id);

        $grade->name = $request->name;
        $grade->save();
        session()->flash('success', 'Grade est modifié !!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrGrade  $grade
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_grade.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer le grade! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $grade = HrGrade::findOrFail($id);
        $grade->delete();
        session()->flash('success', 'Grade est supprimé !!');
        return redirect()->back();
    }
}

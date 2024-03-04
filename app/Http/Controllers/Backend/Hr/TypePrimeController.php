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
use App\Models\HrTypePrime;

class TypePrimeController extends Controller
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
        if (is_null($this->user) || !$this->user->can('hr_prime.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer le prime! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        return view('backend.pages.hr.type_prime.create');
    }

     public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_prime.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer le prime! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        //
        $request->validate([
            'name' => 'required',
            'type' => 'required',

        ]);

        $type_prime = new HrTypePrime();
        $type_prime->name = $request->name;
        $type_prime->type = $request->type;
        $type_prime->save();

        session()->flash('success', 'Type Prime est créé !!');

        return redirect()->back();
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrTypePrime  $type_prime
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_prime.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier le prime! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $type_prime = HrTypePrime::findOrFail($id);
        return view('backend.pages.hr.type_prime.edit', compact('type_prime'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrTypePrime  $type_prime
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_prime.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier le prime! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'name' => 'required',
            'type' => 'required',

        ]);

        $type_prime = HrTypePrime::findOrFail($id);

        $type_prime->name = $request->name;
        $type_prime->type = $request->type;
        $type_prime->save();
        session()->flash('success', 'Type Prime est modifié !!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrTypePrime  $type_prime
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_prime.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer le prime! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $type_prime = HrTypePrime::findOrFail($id);
        $type_prime->delete();
        session()->flash('success', 'Type Prime est supprimé !!');
        return redirect()->back();
    }
}

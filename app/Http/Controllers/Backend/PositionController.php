<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use \App\Models\Position;

class PositionController extends Controller
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
        if (is_null($this->user) || !$this->user->can('position.view')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        } 

        $positions = DB::table('positions')->orderBy('created_at','desc')->get();
        return view('backend.pages.position.index',compact('positions'));
    }
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('position.create')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        } 

        return view('backend.pages.position.create');
    }

    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required',

        ]);

        $position = new Position();
        $position->name = $request->name;
        $position->save();

        session()->flash('success', 'Position est créé !!');

        return redirect()->route('admin.positions.index');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Position  $position
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('position.edit')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        } 
        //
        $position = Position::findOrFail($id);
        return view('backend.pages.position.edit', compact('position'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Position  $position
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([
            'name' => 'required',

        ]);

        $position = Position::findOrFail($id);

        $position->name = $request->name;
        $position->save();
        session()->flash('success', 'Categorie est modifié !!');
        return redirect()->route('admin.positions.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Position  $position
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('position.delete')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        } 
        //
        $position = Position::findOrFail($id);
        $position->delete();
        session()->flash('success', 'Categorie est supprimé !!');
        return redirect()->back();
    }
}

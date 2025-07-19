<?php

namespace App\Http\Controllers\Backend\F;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use \App\Models\F\FTable;

class FTableController extends Controller
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
        if (is_null($this->user) || !$this->user->can('f_table.view')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        } 

        $tables = FTable::all();
        return view('backend.pages.f.table.index',compact('tables'));
    }
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('f_table.create')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        } 

        return view('backend.pages.f.table.create');
    }

    public function choose()
    {
        if (is_null($this->user) || !$this->user->can('f_table.view')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        } 

        $tables = FTable::all();
        return view('backend.pages.f.table.choose',compact('tables'));
    }

    public function chooseType($table_id)
    {
        if (is_null($this->user) || !$this->user->can('f_table.view')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        } 

            $created_by = $this->user->name;
            $waiter_name = FTable::where('id',$table_id)->value('waiter_name');

            if ($waiter_name == $created_by || $waiter_name == '' || $this->user->can('f_bill.create')) {
                return view('backend.pages.f.table.choose_type',compact('table_id'));
            }else{
                session()->flash('error', 'Tu n\'es pas '.$waiter_name.'veuillez utiliser vos comptes s\'il vous plait!!');
                return back();
            }

        
    }

    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required',

        ]);

        $table = new FTable();
        $table->name = $request->name;
        $table->save();

        session()->flash('success', 'Table est créé !!');

        return redirect()->route('admin.f-tables.index');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\FTable  $table
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('f_table.edit')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        } 
        //
        $table = FTable::findOrFail($id);
        return view('backend.pages.f.table.edit', compact('table'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\FTable  $table
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([
            'name' => 'required',

        ]);

        $table = FTable::findOrFail($id);

        $table->name = $request->name;
        $table->save();
        session()->flash('success', 'table est modifié !!');
        return redirect()->route('admin.f-tables.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\FTable  $table
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('f_table.delete')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        } 
        //
        $table = FTable::findOrFail($id);
        $table->delete();
        session()->flash('success', 'table est supprimé !!');
        return redirect()->back();
    }
}

<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use \App\Models\Table;

class TableController extends Controller
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
        if (is_null($this->user) || !$this->user->can('table.view')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        } 

        $tables = Table::all();
        return view('backend.pages.table.index',compact('tables'));
    }
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('table.create')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        } 

        return view('backend.pages.table.create');
    }

    public function choose()
    {
        if (is_null($this->user) || !$this->user->can('table.view')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        } 

        $tables = Table::all();
        return view('backend.pages.table.choose',compact('tables'));
    }

    public function chooseType($table_id)
    {
        if (is_null($this->user) || !$this->user->can('table.view')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        } 

            $created_by = $this->user->name;
            $waiter_name = Table::where('id',$table_id)->value('waiter_name');

            if ($waiter_name == $created_by || $waiter_name == '' || $this->user->can('invoice_drink.create')) {
                return view('backend.pages.table.choose_type',compact('table_id'));
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

        $table = new Table();
        $table->name = $request->name;
        $table->save();

        session()->flash('success', 'Table est créé !!');

        return redirect()->route('admin.tables.index');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Table  $table
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('table.edit')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        } 
        //
        $table = Table::findOrFail($id);
        return view('backend.pages.table.edit', compact('table'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Table  $table
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([
            'name' => 'required',

        ]);

        $table = Table::findOrFail($id);

        $table->name = $request->name;
        $table->save();
        session()->flash('success', 'table est modifié !!');
        return redirect()->route('admin.tables.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Table  $table
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('table.delete')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        } 
        //
        $table = Table::findOrFail($id);
        $table->delete();
        session()->flash('success', 'table est supprimé !!');
        return redirect()->back();
    }
}

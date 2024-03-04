<?php

namespace App\Http\Controllers\Backend\Sotb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Address;
use App\Models\SotbSupplier;
use App\Exports\SupplierExport;
use Excel;

class SupplierController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (is_null($this->user) || !$this->user->can('sotb_material.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any supplier !');
        }

        $suppliers = SotbSupplier::all();
        return view('backend.pages.sotb.supplier.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('sotb_material.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any supplier !');
        }

        $addresses  = Address::all();
        return view('backend.pages.sotb.supplier.create', compact('addresses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('sotb_material.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any supplier !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:100',
            'mail' => 'required|min:10',
            'phone_no' => 'required',
            //'category' => 'required'
        ]);

        // Create New SotbSupplier
        $supplier = new SotbSupplier();
        $supplier->name = $request->name;
        $supplier->mail = $request->mail;
        $supplier->phone_no = $request->phone_no;
        $supplier->address = $request->address;
        //$supplier->category = $request->category;
        //$supplier->vat_taxpayer = $request->vat_taxpayer;
        //$supplier->tin_number = $request->tin_number;
        $supplier->created_by = $this->user->name;
        $supplier->save();

        session()->flash('success', 'SotbSupplier has been created !!');
        return redirect()->route('admin.sotb-suppliers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('sotb_material.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any supplier !');
        }

        $supplier = SotbSupplier::find($id);
        $addresses  = Address::all();
        return view('backend.pages.sotb.supplier.edit', compact('supplier', 'addresses'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('sotb_material.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any supplier !');
        }

        $supplier = SotbSupplier::find($id);

        $request->validate([
            'name' => 'required|max:100',
            'mail' => 'required|min:10',
            'phone_no' => 'required',
            'address' => 'required'
        ]);

        // update SotbSupplier
        $supplier->name = $request->name;
        $supplier->mail = $request->mail;
        $supplier->phone_no = $request->phone_no;
        $supplier->address = $request->address;
        $supplier->category = $request->category;
        $supplier->vat_taxpayer = $request->vat_taxpayer;
        $supplier->tin_number = $request->tin_number;
        $supplier->created_by = $this->user->name;
        $supplier->save();

        session()->flash('success', 'Supplier has been updated !!');
        return back();
    }


    public function get_supplier_data()
    {
        return Excel::download(new SupplierExport, 'fournisseurs.xlsx');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('sotb_material.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any supplier !');
        }

        $supplier = SotbSupplier::find($id);
        if (!is_null($supplier)) {
            $supplier->delete();
        }

        session()->flash('success', 'Supplier has been deleted !!');
        return back();
    }
}

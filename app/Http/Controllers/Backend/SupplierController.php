<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Address;
use App\Models\Supplier;
use App\Exports\SupplierExport;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
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
        if (is_null($this->user) || !$this->user->can('supplier.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any supplier !');
        }

        $suppliers = Supplier::all();
        return view('backend.pages.supplier.index', compact('suppliers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('supplier.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any supplier !');
        }

        $addresses  = Address::all();
        return view('backend.pages.supplier.create', compact('addresses'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('supplier.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any supplier !');
        }

        // Validation Data
        $request->validate([
            'tp_type' => 'required',
            'telephone' => 'required',
        ]);

        $tp_TIN = $request->supplier_TIN;

        if (empty($tp_TIN) && $request->vat_supplier_payer == 1) {
            session()->flash('error', 'Le NIF du fournisseur est obligatoire');
            return redirect()->back();
        }elseif (!empty($tp_TIN) && strlen($tp_TIN) < 10) {
            session()->flash('error', 'Le NIF du fournisseur n\'existe pas');
            return redirect()->back();
        }

        if ($request->vat_supplier_payer == 1 && $request->tp_type == 2) {

            $theUrl = config('app.guzzle_test_url').'/ebms_api/login/';
            $response = Http::post($theUrl, [
            'username'=> config('app.obr_test_username'),
            'password'=> config('app.obr_test_pwd')

            ]);

            $data =  json_decode($response);
            $data2 = ($data->result);
        
    
            $token = $data2->token;


            $theUrl = config('app.guzzle_test_url').'/ebms_api/checkTIN/';
            $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json'])->post($theUrl, [
            'tp_TIN'=>$tp_TIN,

            ]); 

            $data =  json_decode($response);
            $data2 = ($data->result);
        
    
            $success = $data->success;
            $msg = $data->msg;

            $data3 = ($data2->taxpayer);

            $index_one = ($data3['0']);

            $tp_name = $index_one->tp_name;

            $supplier = new Supplier();
            $supplier->date = $request->date;
            $supplier->supplier_name = $tp_name;
            $supplier->tp_type = $request->tp_type;
            $supplier->telephone = $request->telephone;
            $supplier->mail = $request->mail;
            $supplier->supplier_TIN = $request->supplier_TIN;
            $supplier->supplier_address = $request->supplier_address;
            $supplier->vat_supplier_payer = $request->vat_supplier_payer;
            $supplier->company = $request->company;
            $supplier->save();
            session()->flash('success', 'Le fournisseur a été créé avec succés !!, OBR Message : '.$msg.'('.$tp_name.')');
                return redirect()->route('admin.suppliers.index');

        }else{
            $supplier = new Supplier();
            $supplier->date = $request->date;
            $supplier->supplier_name = $request->supplier_name;
            $supplier->tp_type = $request->tp_type;
            $supplier->telephone = $request->telephone;
            $supplier->mail = $request->mail;
            $supplier->supplier_TIN = $request->supplier_TIN;
            $supplier->supplier_address = $request->supplier_address;
            $supplier->vat_supplier_payer = $request->vat_supplier_payer;
            $supplier->company = $request->company;
            $supplier->save();
        }

        session()->flash('success', 'Supplier has been created !!');
        return redirect()->route('admin.suppliers.index');
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
        if (is_null($this->user) || !$this->user->can('supplier.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any supplier !');
        }

        $supplier = Supplier::find($id);
        $addresses  = Address::all();
        return view('backend.pages.supplier.edit', compact('supplier', 'addresses'));
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
        if (is_null($this->user) || !$this->user->can('supplier.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any supplier !');
        }

        $supplier = Supplier::find($id);

        // Validation Data
        $request->validate([
            'tp_type' => 'required',
            'telephone' => 'required',
        ]);

        $tp_TIN = $request->supplier_TIN;

        if (empty($tp_TIN) && $request->vat_supplier_payer == 1) {
            session()->flash('error', 'Le NIF du fournisseur est obligatoire');
            return redirect()->back();
        }elseif (!empty($tp_TIN) && strlen($tp_TIN) < 10) {
            session()->flash('error', 'Le NIF du fournisseur n\'existe pas');
            return redirect()->back();
        }

        if ($request->vat_supplier_payer == 1 && $request->tp_type == 2) {

            $theUrl = config('app.guzzle_test_url').'/ebms_api/login/';
            $response = Http::post($theUrl, [
            'username'=> config('app.obr_test_username'),
            'password'=> config('app.obr_test_pwd')

            ]);

            $data =  json_decode($response);
            $data2 = ($data->result);
        
    
            $token = $data2->token;


            $theUrl = config('app.guzzle_test_url').'/ebms_api/checkTIN/';
            $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$token,
            'Accept' => 'application/json'])->post($theUrl, [
            'tp_TIN'=>$tp_TIN,

            ]); 

            $data =  json_decode($response);
            $data2 = ($data->result);
        
    
            $success = $data->success;
            $msg = $data->msg;

            $data3 = ($data2->taxpayer);

            $index_one = ($data3['0']);

            $tp_name = $index_one->tp_name;

            $supplier->date = $request->date;
            $supplier->supplier_name = $tp_name;
            $supplier->tp_type = $request->tp_type;
            $supplier->telephone = $request->telephone;
            $supplier->mail = $request->mail;
            $supplier->supplier_TIN = $request->supplier_TIN;
            $supplier->supplier_address = $request->supplier_address;
            $supplier->vat_supplier_payer = $request->vat_supplier_payer;
            $supplier->company = $request->company;
            $supplier->save();
            session()->flash('success', 'Le fournisseur a été modifié avec succés !!, OBR Message : '.$msg.'('.$tp_name.')');
            return redirect()->route('admin.suppliers.index');

        }else{
            $supplier->date = $request->date;
            $supplier->supplier_name = $request->supplier_name;
            $supplier->tp_type = $request->tp_type;
            $supplier->telephone = $request->telephone;
            $supplier->mail = $request->mail;
            $supplier->supplier_TIN = $request->supplier_TIN;
            $supplier->supplier_address = $request->supplier_address;
            $supplier->vat_supplier_payer = $request->vat_supplier_payer;
            $supplier->company = $request->company;
            $supplier->save();
        }

        session()->flash('success', 'Supplier has been updated !!');
        return redirect()->route('admin.suppliers.index');
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
        if (is_null($this->user) || !$this->user->can('supplier.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any supplier !');
        }

        $supplier = Supplier::find($id);
        if (!is_null($supplier)) {
            $supplier->delete();
        }

        session()->flash('success', 'Supplier has been deleted !!');
        return back();
    }
}

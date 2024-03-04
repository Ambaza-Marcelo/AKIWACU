<?php

namespace App\Http\Controllers\Backend\MusumbaSteel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\MsMaterial;
use App\Models\MsMaterialCategory;
use App\Models\MsMaterialStore;
use App\Models\MsMaterialStoreDetail;
use Excel;

class MaterialController extends Controller
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_supplier.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any material !');
        }

        $materials = MsMaterial::all();
        return view('backend.pages.musumba_steel.material.index', compact('materials'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_supplier.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any material !');
        }
        $categories = MsMaterialCategory::all();
        $material_stores = MsMaterialStore::all();
        return view('backend.pages.musumba_steel.material.create', compact(
            'categories','material_stores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_supplier.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any material !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'unit' => 'required|max:20',
            'purchase_price' => 'required',
            'quantity' => 'required',
            'code_store' => 'required',
        ]);

        $code_store = $request->code_store;
        // Create New Item
        $material = new MsMaterial();
        $material->name = $request->name;
        $material->quantity = $request->quantity;
        $reference = strtoupper(substr($request->name, 0, 3));
        $material->code = $reference.date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);
        $material->unit = $request->unit;
        $material->purchase_price = $request->purchase_price;
        
        $material->specification = $request->specification;
        $material->threshold_quantity = $request->threshold_quantity;
        $material->mcategory_id = $request->mcategory_id;
        $material->code_store = $code_store;
        $material->created_by = $this->user->name;
        $material->save();

        $material_id = MsMaterial::latest()->first()->id;

            $unit = MsMaterial::where('id',$material_id)->value('unit');
            $code_store = MsMaterial::where('id',$material_id)->value('code_store');
            $quantity = MsMaterial::where('id',$material_id)->value('quantity');
            $threshold_quantity = MsMaterial::where('id',$material_id)->value('threshold_quantity');
            $purchase_price = MsMaterial::where('id',$material_id)->value('purchase_price');
            $specification = MsMaterial::where('id',$material_id)->value('specification');

            $store_code = MsMaterialStoreDetail::where('code',$code_store)->value('code');
            $material_in_store = MsMaterialStoreDetail::where('code',$code_store)->where('material_id',$material_id)->first();
            if (!empty($material_in_store)) {
                $material_in_store->material_id = $material_id;
                $material_in_store->quantity = $quantity;
                $material_in_store->threshold_quantity = $threshold_quantity;
                $material_in_store->purchase_price = $purchase_price;
                $material_in_store->cump = $purchase_price;
                $material_in_store->total_purchase_value = $quantity * $material->purchase_price;
                $material_in_store->total_cump_value = $quantity * $material->purchase_price;
                $material_in_store->unit = $unit;
                $material_in_store->code = $code_store;
                $material_in_store->created_by = $this->user->name;
                $material_in_store->save();
            }else{
                $material_in_store = new MsMaterialStoreDetail();
                $material_in_store->material_id = $material_id;
                $material_in_store->quantity = $quantity;
                $material_in_store->threshold_quantity = $threshold_quantity;
                $material_in_store->purchase_price = $purchase_price;
                $material_in_store->cump = $purchase_price;
                $material_in_store->total_purchase_value = $quantity * $material->purchase_price;
                $material_in_store->total_cump_value = $quantity * $material->purchase_price;
                $material_in_store->unit = $unit;
                $material_in_store->code = $code_store;
                $material_in_store->created_by = $this->user->name;
                $material_in_store->save();
            }

        session()->flash('success', 'Material has been created !!');
        return redirect()->route('admin.ms-materials.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $code
     * @return \Illuminate\Http\Response
     */
    public function show($code)
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_supplier.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any material !');
        }

        $material = MsMaterial::find($id);
        $categories = MsMaterialCategory::all();
        $material_stores = MsMaterialStore::all();
        return view('backend.pages.musumba_steel.material.edit', compact(
            'material', 
            'categories',
            'material_stores'));
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_supplier.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any material !');
        }

        // Create New MsMaterial

        $request->validate([
            'name' => 'required|max:255',
            'unit' => 'required|max:20',
            'purchase_price' => 'required',
            'quantity' => 'required',
            'code_store' => 'required',
        ]);

        $code_store = $request->code_store;

        $material = MsMaterial::where('id',$id)->first();

        $material->name = $request->name;
        $material->quantity = $request->quantity;
        $material->unit = $request->unit;
        $material->purchase_price = $request->purchase_price;
        
        $material->specification = $request->specification;
        $material->selling_price = $request->selling_price;
        $material->threshold_quantity = $request->threshold_quantity;
        $material->mcategory_id = $request->mcategory_id;
        $material->code_store = $code_store;
        $material->created_by = $this->user->name;
        $material->save();

        //$material_id = MsMaterial::latest()->first()->id;

            $unit = MsMaterial::where('id',$id)->value('unit');
            $code_store = MsMaterial::where('id',$id)->value('code_store');
            $quantity = MsMaterial::where('id',$id)->value('quantity');
            $threshold_quantity = MsMaterial::where('id',$id)->value('threshold_quantity');
            $purchase_price = MsMaterial::where('id',$id)->value('purchase_price');
            $specification = MsMaterial::where('id',$id)->value('specification');

            $material_in_store = MsMaterialStoreDetail::where('code',$code_store)->where('material_id',$id)->first();

        if (!empty($material_in_store)) {
                $material_in_store->material_id = $id;
                $material_in_store->quantity = $quantity;
                $material_in_store->threshold_quantity = $threshold_quantity;
                $material_in_store->purchase_price = $purchase_price;
                $material_in_store->cump = $purchase_price;
                $material_in_store->total_purchase_value = $quantity * $material->purchase_price;
                $material_in_store->total_cump_value = $quantity * $material->purchase_price;
                $material_in_store->unit = $unit;
                $material_in_store->code = $code_store;
                $material_in_store->created_by = $this->user->name;
                $material_in_store->save();
            }else{
                $material_in_store = new MsMaterialStoreDetail();
                $material_in_store->material_id = $id;
                $material_in_store->quantity = $quantity;
                $material_in_store->threshold_quantity = $threshold_quantity;
                $material_in_store->purchase_price = $purchase_price;
                $material_in_store->cump = $purchase_price;
                $material_in_store->total_purchase_value = $quantity * $material->purchase_price;
                $material_in_store->total_cump_value = $quantity * $material->purchase_price;
                $material_in_store->unit = $unit;
                $material_in_store->code = $code_store;
                $material_in_store->created_by = $this->user->name;
                $material_in_store->save();
            }

        session()->flash('success', 'Material has been updated !!');
        return redirect()->route('admin.ms-materials.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_supplier.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any material !');
        }

        $material = MsMaterial::find($id);
        if (!is_null($material)) {
            $material->delete();
        }

        session()->flash('success', 'Material has been deleted !!');
        return back();
    }
}

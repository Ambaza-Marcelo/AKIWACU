<?php

namespace App\Http\Controllers\Backend\Sotb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\SotbMaterial;
use App\Models\SotbMaterialCategory;
use App\Models\SotbMaterialBgStore;
use App\Models\SotbMaterialSmStore;
use App\Models\SotbMaterialBgStoreDetail;
use App\Models\SotbMaterialSmStoreDetail;
use App\Models\SotbMaterialMdStore;
use App\Models\SotbMaterialMdStoreDetail;
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
        if (is_null($this->user) || !$this->user->can('sotb_material.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any material !');
        }

        $materials = SotbMaterial::all();
        return view('backend.pages.sotb.material.index', compact('materials'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('sotb_material.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any material !');
        }
        $categories = SotbMaterialCategory::all();
        $material_bg_stores = SotbMaterialBgStore::all();
        $material_md_stores = SotbMaterialMdStore::all();
        $material_sm_stores = SotbMaterialSmStore::all();
        return view('backend.pages.sotb.material.create', compact(
            'categories','material_md_stores','material_sm_stores','material_bg_stores'));
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
            abort(403, 'Sorry !! You are Unauthorized to create any material !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'unit' => 'required|max:20',
            'purchase_price' => 'required',
            'quantity' => 'required',
            'store_type' => 'required',
            'code_store' => 'required',
        ]);

        $store_type = $request->store_type;
        $code_store = $request->code_store;
        // Create New Item
        $material = new SotbMaterial();
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
        $material->store_type = $store_type;
        $material->created_by = $this->user->name;
        $material->save();

        $material_id = SotbMaterial::latest()->first()->id;

            $unit = SotbMaterial::where('id',$material_id)->value('unit');
            $code_store = SotbMaterial::where('id',$material_id)->value('code_store');
            $quantity = SotbMaterial::where('id',$material_id)->value('quantity');
            $threshold_quantity = SotbMaterial::where('id',$material_id)->value('threshold_quantity');
            $purchase_price = SotbMaterial::where('id',$material_id)->value('purchase_price');
            $specification = SotbMaterial::where('id',$material_id)->value('specification');

        if ($store_type == '1') {
            $store_code = SotbMaterialMdStoreDetail::where('code',$code_store)->value('code');
            $material_in_md_store = SotbMaterialMdStoreDetail::where('code',$code_store)->where('material_id',$material_id)->first();
            if (!empty($material_in_md_store)) {
                $material_in_md_store->material_id = $material_id;
                $material_in_md_store->quantity = $quantity;
                $material_in_md_store->threshold_quantity = $threshold_quantity;
                $material_in_md_store->purchase_price = $purchase_price;
                $material_in_md_store->cump = $purchase_price;
                $material_in_md_store->total_purchase_value = $quantity * $material->purchase_price;
                $material_in_md_store->total_cump_value = $quantity * $material->purchase_price;
                $material_in_md_store->unit = $unit;
                $material_in_md_store->code = $code_store;
                $material_in_md_store->created_by = $this->user->name;
                $material_in_md_store->save();
            }else{
                $material_in_md_store = new SotbMaterialMdStoreDetail();
                $material_in_md_store->material_id = $material_id;
                $material_in_md_store->quantity = $quantity;
                $material_in_md_store->threshold_quantity = $threshold_quantity;
                $material_in_md_store->purchase_price = $purchase_price;
                $material_in_md_store->cump = $purchase_price;
                $material_in_md_store->total_purchase_value = $quantity * $material->purchase_price;
                $material_in_md_store->total_cump_value = $quantity * $material->purchase_price;
                $material_in_md_store->unit = $unit;
                $material_in_md_store->code = $code_store;
                $material_in_md_store->created_by = $this->user->name;
                $material_in_md_store->save();
            }
        }elseif($store_type == '2'){
            $drink_sm_store_code = SotbMaterialSmStoreDetail::where('code',$code_store)->value('code');
            $material_in_sm_store = SotbMaterialSmStoreDetail::where('code',$code_store)->where('material_id',$material_id)->first();
            if (!empty($material_in_sm_store)) {
                $material_in_sm_store->material_id = $material_id;
                $material_in_sm_store->quantity = $quantity;
                $material_in_sm_store->threshold_quantity = $threshold_quantity;
                $material_in_sm_store->purchase_price = $purchase_price;
                $material_in_sm_store->cump = $purchase_price;
                $material_in_sm_store->total_purchase_value = $quantity * $material->purchase_price;
                $material_in_sm_store->total_cump_value = $quantity * $material->purchase_price;
                $material_in_sm_store->unit = $unit;
                $material_in_sm_store->code = $code_store;
                $material_in_sm_store->created_by = $this->user->name;
                $material_in_sm_store->save();
            }else{
                $material_in_sm_store = new SotbMaterialSmStoreDetail();
                $material_in_sm_store->material_id = $material_id;
                $material_in_sm_store->quantity = $quantity;
                $material_in_sm_store->threshold_quantity = $threshold_quantity;
                $material_in_sm_store->purchase_price = $purchase_price;
                $material_in_sm_store->cump = $purchase_price;
                $material_in_sm_store->total_purchase_value = $quantity * $material->purchase_price;
                $material_in_sm_store->total_cump_value = $quantity * $material->purchase_price;
                $material_in_sm_store->unit = $unit;
                $material_in_sm_store->code = $code_store;
                $material_in_sm_store->created_by = $this->user->name;
                $material_in_sm_store->save();
            }
        }else{
            $store_code = SotbMaterialBgStoreDetail::where('code',$code_store)->value('code');
            $material_in_bg_store = SotbMaterialBgStoreDetail::where('code',$code_store)->where('material_id',$material_id)->first();
            if (!empty($material_in_bg_store)) {
                $material_in_bg_store->material_id = $material_id;
                $material_in_bg_store->quantity = $quantity;
                $material_in_bg_store->threshold_quantity = $threshold_quantity;
                $material_in_bg_store->purchase_price = $purchase_price;
                $material_in_bg_store->cump = $purchase_price;
                $material_in_bg_store->total_purchase_value = $quantity * $material->purchase_price;
                $material_in_bg_store->total_cump_value = $quantity * $material->purchase_price;
                $material_in_bg_store->unit = $unit;
                $material_in_bg_store->code = $code_store;
                $material_in_bg_store->created_by = $this->user->name;
                $material_in_bg_store->save();
            }else{
                $material_in_bg_store = new SotbMaterialBgStoreDetail();
                $material_in_bg_store->material_id = $material_id;
                $material_in_bg_store->quantity = $quantity;
                $material_in_bg_store->threshold_quantity = $threshold_quantity;
                $material_in_bg_store->purchase_price = $purchase_price;
                $material_in_bg_store->cump = $purchase_price;
                $material_in_bg_store->total_purchase_value = $quantity * $material->purchase_price;
                $material_in_bg_store->total_cump_value = $quantity * $material->purchase_price;
                $material_in_bg_store->unit = $unit;
                $material_in_bg_store->code = $code_store;
                $material_in_bg_store->created_by = $this->user->name;
                $material_in_bg_store->save();
            }
        }

        session()->flash('success', 'Material has been created !!');
        return redirect()->route('admin.sotb-materials.index');
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
        if (is_null($this->user) || !$this->user->can('sotb_material.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any material !');
        }

        $material = SotbMaterial::find($id);
        $categories = SotbMaterialCategory::all();
        $material_bg_stores = SotbMaterialBgStore::all();
        $material_md_stores = SotbMaterialMdStore::all();
        $material_sm_stores = SotbMaterialSmStore::all();
        return view('backend.pages.sotb.material.edit', compact(
            'material', 
            'categories',
            'material_md_stores',
            'material_sm_stores',
            'material_bg_stores'));
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
            abort(403, 'Sorry !! You are Unauthorized to edit any material !');
        }

        // Create New SotbMaterial

        $request->validate([
            'name' => 'required|max:255',
            'unit' => 'required|max:20',
            'purchase_price' => 'required',
            'quantity' => 'required',
            'store_type' => 'required',
            'code_store' => 'required',
        ]);

        $store_type = $request->store_type;
        $code_store = $request->code_store;

        $material = SotbMaterial::where('id',$id)->first();

        $material->name = $request->name;
        $material->quantity = $request->quantity;
        $material->unit = $request->unit;
        $material->purchase_price = $request->purchase_price;
        
        $material->specification = $request->specification;
        $material->selling_price = $request->selling_price;
        $material->threshold_quantity = $request->threshold_quantity;
        $material->mcategory_id = $request->mcategory_id;
        $material->code_store = $code_store;
        $material->store_type = $store_type;
        $material->created_by = $this->user->name;
        $material->save();

        //$material_id = SotbMaterial::latest()->first()->id;

            $unit = SotbMaterial::where('id',$id)->value('unit');
            $code_store = SotbMaterial::where('id',$id)->value('code_store');
            $quantity = SotbMaterial::where('id',$id)->value('quantity');
            $threshold_quantity = SotbMaterial::where('id',$id)->value('threshold_quantity');
            $purchase_price = SotbMaterial::where('id',$id)->value('purchase_price');
            $specification = SotbMaterial::where('id',$id)->value('specification');

        if ($store_type == '1') {
            $store_code = SotbMaterialMdStoreDetail::where('code',$code_store)->value('code');
            $material_in_md_store = SotbMaterialMdStoreDetail::where('code',$code_store)->where('material_id',$id)->first();
            if (!empty($material_in_md_store)) {
                $material_in_md_store->material_id = $id;
                $material_in_md_store->quantity = $quantity;
                $material_in_md_store->threshold_quantity = $threshold_quantity;
                $material_in_md_store->purchase_price = $purchase_price;
                $material_in_md_store->cump = $purchase_price;
                $material_in_md_store->total_purchase_value = $quantity * $material->purchase_price;
                $material_in_md_store->total_cump_value = $quantity * $material->purchase_price;
                $material_in_md_store->unit = $unit;
                $material_in_md_store->code = $code_store;
                $material_in_md_store->created_by = $this->user->name;
                $material_in_md_store->save();
            }else{
                $material_in_md_store = new SotbMaterialMdStoreDetail();
                $material_in_md_store->material_id = $id;
                $material_in_md_store->quantity = $quantity;
                $material_in_md_store->threshold_quantity = $threshold_quantity;
                $material_in_md_store->purchase_price = $purchase_price;
                $material_in_md_store->cump = $purchase_price;
                $material_in_md_store->total_purchase_value = $quantity * $material->purchase_price;
                $material_in_md_store->total_cump_value = $quantity * $material->purchase_price;
                $material_in_md_store->unit = $unit;
                $material_in_md_store->code = $code_store;
                $material_in_md_store->created_by = $this->user->name;
                $material_in_md_store->save();
            }
        }elseif($store_type == '2'){
            $drink_sm_store_code = SotbMaterialSmStoreDetail::where('code',$code_store)->value('code');
            $material_in_sm_store = SotbMaterialSmStoreDetail::where('code',$code_store)->where('material_id',$id)->first();
            if (!empty($material_in_sm_store)) {
                $material_in_sm_store->material_id = $id;
                $material_in_sm_store->quantity = $quantity;
                $material_in_sm_store->threshold_quantity = $threshold_quantity;
                $material_in_sm_store->purchase_price = $purchase_price;
                $material_in_sm_store->cump = $purchase_price;
                $material_in_sm_store->total_purchase_value = $quantity * $material->purchase_price;
                $material_in_sm_store->total_cump_value = $quantity * $material->purchase_price;
                $material_in_sm_store->unit = $unit;
                $material_in_sm_store->code = $code_store;
                $material_in_sm_store->created_by = $this->user->name;
                $material_in_sm_store->save();
            }else{
                $material_in_sm_store = new SotbMaterialSmStoreDetail();
                $material_in_sm_store->material_id = $id;
                $material_in_sm_store->quantity = $quantity;
                $material_in_sm_store->threshold_quantity = $threshold_quantity;
                $material_in_sm_store->purchase_price = $purchase_price;
                $material_in_sm_store->cump = $purchase_price;
                $material_in_sm_store->total_purchase_value = $quantity * $material->purchase_price;
                $material_in_sm_store->total_cump_value = $quantity * $material->purchase_price;
                $material_in_sm_store->unit = $unit;
                $material_in_sm_store->code = $code_store;
                $material_in_sm_store->created_by = $this->user->name;
                $material_in_sm_store->save();
            }
        }else{
            $store_code = SotbMaterialBgStoreDetail::where('code',$code_store)->value('code');
            $material_in_bg_store = SotbMaterialBgStoreDetail::where('code',$code_store)->where('material_id',$id)->first();
            if (!empty($material_in_bg_store)) {
                $material_in_bg_store->material_id = $id;
                $material_in_bg_store->quantity = $quantity;
                $material_in_bg_store->threshold_quantity = $threshold_quantity;
                $material_in_bg_store->purchase_price = $purchase_price;
                $material_in_bg_store->cump = $purchase_price;
                $material_in_bg_store->total_purchase_value = $quantity * $material->purchase_price;
                $material_in_bg_store->total_cump_value = $quantity * $material->purchase_price;
                $material_in_bg_store->unit = $unit;
                $material_in_bg_store->code = $code_store;
                $material_in_bg_store->created_by = $this->user->name;
                $material_in_bg_store->save();
            }else{
                $material_in_bg_store = new SotbMaterialBgStoreDetail();
                $material_in_bg_store->material_id = $id;
                $material_in_bg_store->quantity = $quantity;
                $material_in_bg_store->threshold_quantity = $threshold_quantity;
                $material_in_bg_store->purchase_price = $purchase_price;
                $material_in_bg_store->cump = $purchase_price;
                $material_in_bg_store->total_purchase_value = $quantity * $material->purchase_price;
                $material_in_bg_store->total_cump_value = $quantity * $material->purchase_price;
                $material_in_bg_store->unit = $unit;
                $material_in_bg_store->code = $code_store;
                $material_in_bg_store->created_by = $this->user->name;
                $material_in_bg_store->save();
            }
        }

        session()->flash('success', 'Material has been updated !!');
        return redirect()->route('admin.sotb-materials.index');
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
            abort(403, 'Sorry !! You are Unauthorized to delete any material !');
        }

        $material = SotbMaterial::find($id);
        if (!is_null($material)) {
            $material->delete();
        }

        session()->flash('success', 'Material has been deleted !!');
        return back();
    }
}

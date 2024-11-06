<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Drink;
use App\Models\DrinkCategory;
use App\Models\DrinkMeasurement;
use App\Models\DrinkBigStore;
use App\Models\DrinkSmallStore;
use App\Models\DrinkBigStoreDetail;
use App\Models\DrinkSmallStoreDetail;
use App\Models\DrinkExtraBigStore;
use App\Models\DrinkExtraBigStoreDetail;
use Excel;

class DrinkController extends Controller
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
        if (is_null($this->user) || !$this->user->can('drink.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any drink !');
        }

        $drinks = Drink::all();
        return view('backend.pages.drink.index', compact('drinks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('drink.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any drink !');
        }
        $categories = DrinkCategory::all();
        $drink_measurements = DrinkMeasurement::all();
        $drink_extra_big_stores = DrinkExtraBigStore::all();
        $drink_big_stores = DrinkBigStore::all();
        $drink_small_stores = DrinkSmallStore::all();
        return view('backend.pages.drink.create', compact(
            'categories','drink_measurements','drink_big_stores','drink_extra_big_stores','drink_small_stores'));
    }

    public function autocomplete(Request $request)
    {
        $data = Drink::select("name as value", "id")
                    ->where('name', 'LIKE', '%'. $request->get('search'). '%')
                    ->get();
    
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('drink.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any drink !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'drink_measurement_id' => 'required',
            'purchase_price' => 'required',
            'selling_price' => 'required',
            'dcategory_id' => 'required',
            'store_type' => 'required',
            'code_store' => 'required',
        ]);

        $store_type = $request->store_type;
        $code_store = $request->code_store;
        // Create New Item

        try {DB::beginTransaction();
            
        $drink = new Drink();
        $drink->name = $request->name;
        $drink->quantity_bottle = 0;
        $drink->quantity_ml = $request->quantity_ml;
        $reference = strtoupper(substr($request->name, 0, 3));
        $drink->code = $reference.date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);
        $drink->drink_measurement_id = $request->drink_measurement_id;
        $drink->purchase_price = $request->purchase_price;
        $drink->cump = $request->purchase_price;
        $drink->quantity_ml = $request->quantity_ml;

        $drink->vat = $request->vat;
        $drink->item_ct = $request->item_ct;
        $drink->item_tl = $request->item_tl;
        $drink->brarudi_price = $request->brarudi_price;

        //$drink->taux_marge = $request->taux_marge;
        //$drink->taux_majoration = $request->taux_majoration;
        //$drink->taux_reduction = $request->taux_reduction;
        
        $drink->specification = $request->specification;
        $drink->selling_price = $request->selling_price;
        $drink->threshold_quantity = $request->threshold_quantity;
        $drink->dcategory_id = $request->dcategory_id;
        $drink->code_store = $code_store;
        $drink->store_type = $store_type;
        $drink->created_by = $this->user->name;
        $drink->save();
        $drink_id = Drink::latest()->first()->id;

            $unit = Drink::where('id',$drink_id)->value('unit');
            $code_store = Drink::where('id',$drink_id)->value('code_store');
            $quantity_bottle = Drink::where('id',$drink_id)->value('quantity_bottle');
            $quantity_ml = Drink::where('id',$drink_id)->value('quantity_ml');
            $threshold_quantity = Drink::where('id',$drink_id)->value('threshold_quantity');
            $purchase_price = Drink::where('id',$drink_id)->value('purchase_price');
            $selling_price = Drink::where('id',$drink_id)->value('selling_price');
            $specification = Drink::where('id',$drink_id)->value('specification');
            $vat = Drink::where('id',$drink_id)->value('vat');
            $brarudi_price = Drink::where('id',$drink_id)->value('brarudi_price');

        if ($store_type == '1') {
            $drink_big_store_code = DrinkBigStoreDetail::where('code',$code_store)->value('code');
            $drink_in_big_store = DrinkBigStoreDetail::where('code',$code_store)->where('drink_id',$drink_id)->first();
            if (!empty($drink_in_big_store)) {
                $drink_in_big_store->drink_id = $drink_id;
                $drink_in_big_store->quantity_bottle = $quantity_bottle;
                $drink_in_big_store->quantity_ml = $quantity_ml;
                $drink_in_big_store->threshold_quantity = $threshold_quantity;
                $drink_in_big_store->specification = $specification;
                $drink_in_big_store->vat = $vat;
                $drink_in_big_store->brarudi_price = $brarudi_price;
                $drink_in_big_store->purchase_price = $purchase_price;
                $drink_in_big_store->cump = $purchase_price;
                $drink_in_big_store->selling_price = $selling_price;
                $drink_in_big_store->total_value_bottle = $quantity_bottle * $drink->purchase_price;
                $drink_in_big_store->total_purchase_value = $quantity_bottle * $drink->purchase_price;
                $drink_in_big_store->total_selling_value = $quantity_bottle * $drink->selling_price;
                $drink_in_big_store->unit = $unit;
                $drink_in_big_store->code = $code_store;
                $drink_in_big_store->created_by = $this->user->name;
                $drink_in_big_store->save();
            }else{
                $drink_in_big_store = new DrinkBigStoreDetail();
                $drink_in_big_store->drink_id = $drink_id;
                $drink_in_big_store->quantity_bottle = $quantity_bottle;
                $drink_in_big_store->quantity_ml = $quantity_ml;
                $drink_in_big_store->threshold_quantity = $threshold_quantity;
                $drink_in_big_store->specification = $specification;
                $drink_in_big_store->vat = $vat;
                $drink_in_big_store->brarudi_price = $brarudi_price;
                $drink_in_big_store->purchase_price = $purchase_price;
                $drink_in_big_store->cump = $purchase_price;
                $drink_in_big_store->selling_price = $selling_price;
                $drink_in_big_store->total_value_bottle = $quantity_bottle * $drink->purchase_price;
                $drink_in_big_store->total_purchase_value = $quantity_bottle * $drink->purchase_price;
                $drink_in_big_store->total_selling_value = $quantity_bottle * $drink->selling_price;
                $drink_in_big_store->unit = $unit;
                $drink_in_big_store->code = $code_store;
                $drink_in_big_store->created_by = $this->user->name;
                $drink_in_big_store->save();
                /*
                $drink_in_small_store = new DrinkSmallStoreDetail();
                $drink_in_small_store->drink_id = $drink_id;
                $drink_in_small_store->threshold_quantity = $threshold_quantity;
                $drink_in_small_store->purchase_price = $purchase_price;
                $drink_in_small_store->selling_price = $selling_price;
                $drink_in_small_store->unit = $unit;
                $drink_in_small_store->code = $code_store;
                $drink_in_small_store->created_by = $this->user->name;
                $drink_in_small_store->save();
                */
            }
        }elseif($store_type == '2'){
            $drink_small_store_code = DrinkSmallStoreDetail::where('code',$code_store)->value('code');
            $drink_in_small_store = DrinkSmallStoreDetail::where('code',$code_store)->where('drink_id',$drink_id)->first();
            if (!empty($drink_in_small_store)) {
                $drink_in_small_store->drink_id = $drink_id;
                $drink_in_small_store->quantity_bottle = $quantity_bottle;
                $drink_in_small_store->quantity_ml = $quantity_ml;
                $drink_in_small_store->threshold_quantity = $threshold_quantity;
                $drink_in_small_store->specification = $specification;
                $drink_in_small_store->vat = $vat;
                $drink_in_small_store->brarudi_price = $brarudi_price;
                $drink_in_small_store->purchase_price = $purchase_price;
                $drink_in_small_store->cump = $purchase_price;
                $drink_in_small_store->selling_price = $selling_price;
                $drink_in_small_store->total_value_bottle = $quantity_bottle * $drink->purchase_price;
                $drink_in_small_store->total_purchase_value = $quantity_bottle * $drink->purchase_price;
                $drink_in_small_store->total_selling_value = $quantity_bottle * $drink->selling_price;
                $drink_in_small_store->unit = $unit;
                $drink_in_small_store->code = $code_store;
                $drink_in_small_store->created_by = $this->user->name;
                $drink_in_small_store->save();
            }else{
                $drink_in_small_store = new DrinkSmallStoreDetail();
                $drink_in_small_store->drink_id = $drink_id;
                $drink_in_small_store->quantity_bottle = $quantity_bottle;
                $drink_in_small_store->quantity_ml = $quantity_ml;
                $drink_in_small_store->threshold_quantity = $threshold_quantity;
                $drink_in_small_store->specification = $specification;
                $drink_in_small_store->vat = $vat;
                $drink_in_small_store->brarudi_price = $brarudi_price;
                $drink_in_small_store->cump = $purchase_price;
                $drink_in_small_store->purchase_price = $purchase_price;
                $drink_in_small_store->selling_price = $selling_price;
                $drink_in_small_store->total_value_bottle = $quantity_bottle * $drink->purchase_price;
                $drink_in_small_store->total_purchase_value = $quantity_bottle * $drink->purchase_price;
                $drink_in_small_store->total_selling_value = $quantity_bottle * $drink->selling_price;
                $drink_in_small_store->unit = $unit;
                $drink_in_small_store->code = $code_store;
                $drink_in_small_store->created_by = $this->user->name;
                $drink_in_small_store->save();
            }
        }else{
            $drink_extra_big_store_code = DrinkExtraBigStoreDetail::where('code',$code_store)->value('code');
            $drink_in_extra_big_store = DrinkExtraBigStoreDetail::where('code',$code_store)->where('drink_id',$drink_id)->first();
            if (!empty($drink_in_extra_big_store)) {
                $drink_in_extra_big_store->drink_id = $drink_id;
                $drink_in_extra_big_store->quantity = $quantity_bottle;
                $drink_in_extra_big_store->quantity_ml = $quantity_ml;
                $drink_in_extra_big_store->threshold_quantity = $threshold_quantity;
                $drink_in_extra_big_store->specification = $specification;
                $drink_in_extra_big_store->vat = $vat;
                $drink_in_extra_big_store->brarudi_price = $brarudi_price;
                $drink_in_extra_big_store->purchase_price = $purchase_price;
                $drink_in_extra_big_store->cump = $purchase_price;
                $drink_in_extra_big_store->selling_price = $selling_price;
                $drink_in_extra_big_store->total_value_bottle = $quantity_bottle * $drink->purchase_price;
                $drink_in_extra_big_store->total_purchase_value = $quantity_bottle * $drink->purchase_price;
                $drink_in_extra_big_store->total_selling_value = $quantity_bottle * $drink->selling_price;
                $drink_in_extra_big_store->unit = $unit;
                $drink_in_extra_big_store->code = $code_store;
                $drink_in_extra_big_store->created_by = $this->user->name;
                $drink_in_extra_big_store->save();
            }else{
                $drink_in_extra_big_store = new DrinkExtraBigStoreDetail();
                $drink_in_extra_big_store->drink_id = $drink_id;
                $drink_in_extra_big_store->quantity = $quantity_bottle;
                $drink_in_extra_big_store->quantity_ml = $quantity_ml;
                $drink_in_extra_big_store->threshold_quantity = $threshold_quantity;
                $drink_in_extra_big_store->specification = $specification;
                $drink_in_extra_big_store->vat = $vat;
                $drink_in_extra_big_store->brarudi_price = $brarudi_price;
                $drink_in_extra_big_store->purchase_price = $purchase_price;
                $drink_in_extra_big_store->cump = $purchase_price;
                $drink_in_extra_big_store->selling_price = $selling_price;
                $drink_in_extra_big_store->total_value_bottle = $quantity_bottle * $drink->purchase_price;
                $drink_in_extra_big_store->total_purchase_value = $quantity_bottle * $drink->purchase_price;
                $drink_in_extra_big_store->total_selling_value = $quantity_bottle * $drink->selling_price;
                $drink_in_extra_big_store->unit = $unit;
                $drink_in_extra_big_store->code = $code_store;
                $drink_in_extra_big_store->created_by = $this->user->name;
                $drink_in_extra_big_store->save();
            }
        }
            DB::commit();
            session()->flash('success', 'Drink has been created !!');
            return redirect()->route('admin.drinks.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

        
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
        if (is_null($this->user) || !$this->user->can('drink.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any drink !');
        }

        $drink = Drink::find($id);
        $categories = DrinkCategory::all();
        $drink_measurements = DrinkMeasurement::all();
        $drink_extra_big_stores = DrinkExtraBigStore::all();
        $drink_big_stores = DrinkBigStore::all();
        $drink_small_stores = DrinkSmallStore::all();
        return view('backend.pages.drink.edit', compact(
            'drink', 
            'categories',
            'drink_measurements',
            'drink_big_stores',
            'drink_small_stores',
            'drink_extra_big_stores'));
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
        if (is_null($this->user) || !$this->user->can('drink.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any drink !');
        }

        // Create New Drink

        $request->validate([
            'name' => 'required|max:255',
            'drink_measurement_id' => 'required',
            'purchase_price' => 'required',
            'dcategory_id' => 'required',
            'store_type' => 'required',
            'code_store' => 'required',
        ]);

        try {DB::beginTransaction();

        $store_type = $request->store_type;
        $code_store = $request->code_store;

        $drink = Drink::where('id',$id)->first();

        $drink->name = $request->name;
        //$drink->quantity_bottle = $request->quantity_bottle;
        $drink->quantity_ml = $request->quantity_ml;
        $drink->drink_measurement_id = $request->drink_measurement_id;
        $drink->purchase_price = $request->purchase_price;
        $drink->cump = $request->purchase_price;
        $drink->quantity_ml = $request->quantity_ml;

        $drink->vat = $request->vat;
        $drink->item_ct = $request->item_ct;
        $drink->item_tl = $request->item_tl;
        $drink->brarudi_price = $request->brarudi_price;

        //$drink->taux_marge = $request->taux_marge;
        //$drink->taux_majoration = $request->taux_majoration;
        //$drink->taux_reduction = $request->taux_reduction;
        
        
        $drink->specification = $request->specification;
        $drink->selling_price = $request->selling_price;
        $drink->threshold_quantity = $request->threshold_quantity;
        $drink->dcategory_id = $request->dcategory_id;
        $drink->code_store = $code_store;
        $drink->store_type = $store_type;
        $drink->created_by = $this->user->name;
        $drink->save();

        $drink_id = $id;

            $unit = Drink::where('id',$drink_id)->value('unit');
            $quantity_bottle = Drink::where('id',$drink_id)->value('quantity_bottle');
            $quantity_ml = Drink::where('id',$drink_id)->value('quantity_ml');
            $threshold_quantity = Drink::where('id',$drink_id)->value('threshold_quantity');
            $purchase_price = Drink::where('id',$drink_id)->value('purchase_price');
            $selling_price = Drink::where('id',$drink_id)->value('selling_price');
            $specification = Drink::where('id',$drink_id)->value('specification');
            $brarudi_price = Drink::where('id',$drink_id)->value('brarudi_price');
            $vat = Drink::where('id',$drink_id)->value('vat');

        if ($store_type == '1') {
            $drink_big_store_code = DrinkBigStoreDetail::where('code',$code_store)->value('code');
            $drink_in_big_store = DrinkBigStoreDetail::where('code',$code_store)->where('drink_id',$drink_id)->first();
            if (!empty($drink_in_big_store)) {
                $drink_in_big_store->drink_id = $drink_id;
                $drink_in_big_store->quantity_bottle = $quantity_bottle;
                $drink_in_big_store->quantity_ml = $quantity_ml;
                $drink_in_big_store->threshold_quantity = $threshold_quantity;
                $drink_in_big_store->purchase_price = $purchase_price;
                $drink_in_big_store->cump = $purchase_price;
                $drink_in_big_store->selling_price = $selling_price;
                $drink_in_big_store->vat = $vat;
                $drink_in_big_store->brarudi_price = $brarudi_price;
                $drink_in_big_store->total_value_bottle = $quantity_bottle * $drink->purchase_price;
                $drink_in_big_store->total_purchase_value = $quantity_bottle * $drink->purchase_price;
                $drink_in_big_store->total_selling_value = $quantity_bottle * $drink->selling_price;
                $drink_in_big_store->unit = $unit;
                $drink_in_big_store->code = $code_store;
                $drink_in_big_store->created_by = $this->user->name;
                $drink_in_big_store->save();
            }else{
                $drink_in_big_store = new DrinkBigStoreDetail();
                $drink_in_big_store->drink_id = $drink_id;
                $drink_in_big_store->quantity_bottle = $quantity_bottle;
                $drink_in_big_store->quantity_ml = $quantity_ml;
                $drink_in_big_store->threshold_quantity = $threshold_quantity;
                $drink_in_big_store->cump = $purchase_price;
                $drink_in_big_store->purchase_price = $purchase_price;
                $drink_in_big_store->selling_price = $selling_price;
                $drink_in_big_store->vat = $vat;
                $drink_in_big_store->brarudi_price = $brarudi_price;
                $drink_in_big_store->total_value_bottle = $quantity_bottle * $drink->purchase_price;
                $drink_in_big_store->total_purchase_value = $quantity_bottle * $drink->purchase_price;
                $drink_in_big_store->total_selling_value = $quantity_bottle * $drink->selling_price;
                $drink_in_big_store->unit = $unit;
                $drink_in_big_store->code = $code_store;
                $drink_in_big_store->created_by = $this->user->name;
                $drink_in_big_store->save();
            }
        }elseif($store_type == '2'){
            $drink_small_store_code = DrinkSmallStoreDetail::where('code',$code_store)->value('code');
            $drink_in_small_store = DrinkSmallStoreDetail::where('code',$code_store)->where('drink_id',$drink_id)->first();
            if (!empty($drink_in_small_store)) {
                $drink_in_small_store->drink_id = $drink_id;
                $drink_in_small_store->quantity_bottle = $quantity_bottle;
                $drink_in_small_store->quantity_ml = $quantity_ml;
                $drink_in_small_store->threshold_quantity = $threshold_quantity;
                $drink_in_small_store->cump = $purchase_price;
                $drink_in_small_store->purchase_price = $purchase_price;
                $drink_in_small_store->selling_price = $selling_price;
                $drink_in_small_store->vat = $vat;
                $drink_in_small_store->brarudi_price = $brarudi_price;
                $drink_in_small_store->total_value_bottle = $quantity_bottle * $drink->purchase_price;
                $drink_in_small_store->total_purchase_value = $quantity_bottle * $drink->purchase_price;
                $drink_in_small_store->total_selling_value = $quantity_bottle * $drink->selling_price;
                $drink_in_small_store->unit = $unit;
                $drink_in_small_store->code = $code_store;
                $drink_in_small_store->created_by = $this->user->name;
                $drink_in_small_store->save();
            }else{
                $drink_in_small_store = new DrinkSmallStoreDetail();
                $drink_in_small_store->drink_id = $drink_id;
                $drink_in_small_store->quantity_bottle = $quantity_bottle;
                $drink_in_small_store->quantity_ml = $quantity_ml;
                $drink_in_small_store->threshold_quantity = $threshold_quantity;
                $drink_in_small_store->cump = $purchase_price;
                $drink_in_small_store->purchase_price = $purchase_price;
                $drink_in_small_store->selling_price = $selling_price;
                $drink_in_small_store->vat = $vat;
                $drink_in_small_store->brarudi_price = $brarudi_price;
                $drink_in_small_store->total_value_bottle = $quantity_bottle * $drink->purchase_price;
                $drink_in_small_store->total_purchase_value = $quantity_bottle * $drink->purchase_price;
                $drink_in_small_store->total_selling_value = $quantity_bottle * $drink->selling_price;
                $drink_in_small_store->unit = $unit;
                $drink_in_small_store->code = $code_store;
                $drink_in_small_store->created_by = $this->user->name;
                $drink_in_small_store->save();
            }
        }else{
            $drink_extra_big_store_code = DrinkExtraBigStoreDetail::where('code',$code_store)->value('code');
            $drink_in_extra_big_store = DrinkExtraBigStoreDetail::where('code',$code_store)->where('drink_id',$drink_id)->first();
            if (!empty($drink_in_extra_big_store)) {
                $drink_in_extra_big_store->drink_id = $drink_id;
                $drink_in_extra_big_store->quantity = $quantity_bottle;
                $drink_in_extra_big_store->quantity_ml = $quantity_ml;
                $drink_in_extra_big_store->threshold_quantity = $threshold_quantity;
                $drink_in_extra_big_store->specification = $specification;
                $drink_in_extra_big_store->cump = $purchase_price;
                $drink_in_extra_big_store->purchase_price = $purchase_price;
                $drink_in_extra_big_store->selling_price = $selling_price;
                $drink_in_extra_big_store->vat = $vat;
                $drink_in_extra_big_store->brarudi_price = $brarudi_price;
                $drink_in_extra_big_store->total_value_bottle = $quantity_bottle * $drink->purchase_price;
                $drink_in_extra_big_store->total_purchase_value = $quantity_bottle * $drink->purchase_price;
                $drink_in_extra_big_store->total_selling_value = $quantity_bottle * $drink->selling_price;
                $drink_in_extra_big_store->unit = $unit;
                $drink_in_extra_big_store->code = $code_store;
                $drink_in_extra_big_store->created_by = $this->user->name;
                $drink_in_extra_big_store->save();
            }else{
                $drink_in_extra_big_store = new DrinkExtraBigStoreDetail();
                $drink_in_extra_big_store->drink_id = $drink_id;
                $drink_in_extra_big_store->quantity = $quantity_bottle;
                $drink_in_extra_big_store->quantity_ml = $quantity_ml;
                $drink_in_extra_big_store->threshold_quantity = $threshold_quantity;
                $drink_in_extra_big_store->specification = $specification;
                $drink_in_extra_big_store->cump = $purchase_price;
                $drink_in_extra_big_store->purchase_price = $purchase_price;
                $drink_in_extra_big_store->selling_price = $selling_price;
                $drink_in_extra_big_store->vat = $vat;
                $drink_in_extra_big_store->brarudi_price = $brarudi_price;
                $drink_in_extra_big_store->total_value_bottle = $quantity_bottle * $drink->purchase_price;
                $drink_in_extra_big_store->total_purchase_value = $quantity_bottle * $drink->purchase_price;
                $drink_in_extra_big_store->total_selling_value = $quantity_bottle * $drink->selling_price;
                $drink_in_extra_big_store->unit = $unit;
                $drink_in_extra_big_store->code = $code_store;
                $drink_in_extra_big_store->created_by = $this->user->name;
                $drink_in_extra_big_store->save();
            }
        }
            DB::commit();
            session()->flash('success', 'Drink has been updated succesfuly !!');
            return redirect()->route('admin.drinks.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('drink.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any drink !');
        }
        
        $drink = Drink::find($id);
        if (!is_null($drink)) {
            $drink->delete();
        }

        session()->flash('success', 'Drink has been deleted !!');
        return back();
    }
}

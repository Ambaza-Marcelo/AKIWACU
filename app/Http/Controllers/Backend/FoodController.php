<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Food;
use App\Models\FoodCategory;
use App\Models\FoodBigStore;
use App\Models\FoodSmallStore;
use App\Models\FoodBigStoreDetail;
use App\Models\FoodSmallStoreDetail;
use App\Models\FoodExtraBigStore;
use App\Models\FoodExtraBigStoreDetail;
use Excel;

class FoodController extends Controller
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
        if (is_null($this->user) || !$this->user->can('food.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any food !');
        }

        $foods = DB::table('foods')->get();
        return view('backend.pages.food.index', compact('foods'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('food.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any food !');
        }
        $categories = FoodCategory::all();
        $food_extra_big_stores = FoodExtraBigStore::all();
        $food_big_stores = FoodBigStore::all();
        $food_small_stores = FoodSmallStore::all();
        return view('backend.pages.food.create', compact(
            'categories','food_big_stores','food_small_stores','food_extra_big_stores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('food.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any food !');
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

        try {DB::beginTransaction();

        $store_type = $request->store_type;
        $code_store = $request->code_store;
        // Create New Item
        $food = new Food();
        $food->name = $request->name;
        $food->quantity = $request->quantity;
        $reference = strtoupper(substr($request->name, 0, 3));
        $food->code = $reference.date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);
        $food->unit = $request->unit;
        $food->purchase_price = $request->purchase_price;
        $food->cump = $request->purchase_price;
        $food->specification = $request->specification;
        $food->selling_price = $request->selling_price;
        $food->threshold_quantity = $request->threshold_quantity;
        $food->fcategory_id = $request->fcategory_id;
        $food->code_store = $code_store;
        $food->store_type = $store_type;
        $food->created_by = $this->user->name;
        $food->save();

        $food_id = Food::latest()->first()->id;

            $unit = Food::where('id',$food_id)->value('unit');
            $code_store = Food::where('id',$food_id)->value('code_store');
            $quantity = Food::where('id',$food_id)->value('quantity');
            $threshold_quantity = Food::where('id',$food_id)->value('threshold_quantity');
            $purchase_price = Food::where('id',$food_id)->value('purchase_price');
            $selling_price = Food::where('id',$food_id)->value('selling_price');
            $specification = Food::where('id',$food_id)->value('specification');


        if ($store_type == '1') {
            $food_big_store_code = FoodBigStoreDetail::where('code',$code_store)->value('code');
            $food_in_big_store = FoodBigStoreDetail::where('code',$code_store)->where('food_id',$food_id)->first();
            if (!empty($food_in_big_store)) {
                $food_in_big_store->food_id = $food_id;
                $food_in_big_store->quantity = $quantity;
                $food_in_big_store->threshold_quantity = $threshold_quantity;
                $food_in_big_store->purchase_price = $purchase_price;
                $food_in_big_store->cum = $purchase_price;
                $food_in_big_store->selling_price = $selling_price;
                //$food_in_big_store->total_value = $quantity * $food->purchase_price;
                $food_in_big_store->total_purchase_value = $quantity * $food->purchase_price;
                $food_in_big_store->total_selling_value = $quantity * $food->selling_price;
                $food_in_big_store->unit = $unit;
                $food_in_big_store->code = $code_store;
                $food_in_big_store->created_by = $this->user->name;
                $food_in_big_store->save();
            }else{
                $food_in_big_store = new FoodBigStoreDetail();
                $food_in_big_store->food_id = $food_id;
                $food_in_big_store->quantity = $quantity;
                $food_in_big_store->threshold_quantity = $threshold_quantity;
                $food_in_big_store->purchase_price = $purchase_price;
                $food_in_big_store->cump = $purchase_price;
                $food_in_big_store->selling_price = $selling_price;
                //$food_in_big_store->total_value = $quantity * $food->purchase_price;
                $food_in_big_store->total_purchase_value = $quantity * $food->purchase_price;
                $food_in_big_store->total_selling_value = $quantity * $food->selling_price;
                $food_in_big_store->unit = $unit;
                $food_in_big_store->code = $code_store;
                $food_in_big_store->created_by = $this->user->name;
                $food_in_big_store->save();
            }
        }elseif($store_type == '2'){
            $drink_small_store_code = FoodSmallStoreDetail::where('code',$code_store)->value('code');
            $food_in_small_store = FoodSmallStoreDetail::where('code',$code_store)->where('food_id',$food_id)->first();
            if (!empty($food_in_small_store)) {
                $food_in_small_store->food_id = $food_id;
                $food_in_small_store->quantity = $quantity;
                $food_in_small_store->threshold_quantity = $threshold_quantity;
                $food_in_small_store->purchase_price = $purchase_price;
                $food_in_small_store->cump = $purchase_price;
                $food_in_small_store->selling_price = $selling_price;
                //$food_in_small_store->total_value = $quantity * $food->purchase_price;
                $food_in_small_store->total_purchase_value = $quantity * $food->purchase_price;
                $food_in_small_store->total_selling_value = $quantity * $food->selling_price;
                $food_in_small_store->unit = $unit;
                $food_in_small_store->code = $code_store;
                $food_in_small_store->created_by = $this->user->name;
                $food_in_small_store->save();
            }else{
                $food_in_small_store = new FoodSmallStoreDetail();
                $food_in_small_store->food_id = $food_id;
                $food_in_small_store->quantity = $quantity;
                $food_in_small_store->threshold_quantity = $threshold_quantity;
                $food_in_small_store->purchase_price = $purchase_price;
                $food_in_small_store->cump = $purchase_price;
                $food_in_small_store->selling_price = $selling_price;
                //$food_in_small_store->total_value = $quantity * $food->purchase_price;
                $food_in_small_store->total_purchase_value = $quantity * $food->purchase_price;
                $food_in_small_store->total_selling_value = $quantity * $food->selling_price;
                $food_in_small_store->unit = $unit;
                $food_in_small_store->code = $code_store;
                $food_in_small_store->created_by = $this->user->name;
                $food_in_small_store->save();
            }
        }else{
            $food_big_store_code = FoodExtraBigStoreDetail::where('code',$code_store)->value('code');
            $food_in_big_store = FoodExtraBigStoreDetail::where('code',$code_store)->where('food_id',$food_id)->first();
            if (!empty($food_in_big_store)) {
                $food_in_big_store->food_id = $food_id;
                $food_in_big_store->quantity = $quantity;
                $food_in_big_store->threshold_quantity = $threshold_quantity;
                $food_in_big_store->purchase_price = $purchase_price;
                $food_in_big_store->cump = $purchase_price;
                $food_in_big_store->selling_price = $selling_price;
                //$food_in_big_store->total_value = $quantity * $food->purchase_price;
                $food_in_big_store->total_purchase_value = $quantity * $food->purchase_price;
                $food_in_big_store->total_selling_value = $quantity * $food->selling_price;
                $food_in_big_store->unit = $unit;
                $food_in_big_store->code = $code_store;
                $food_in_big_store->created_by = $this->user->name;
                $food_in_big_store->save();
            }else{
                $food_in_big_store = new FoodExtraBigStoreDetail();
                $food_in_big_store->food_id = $food_id;
                $food_in_big_store->quantity = $quantity;
                $food_in_big_store->threshold_quantity = $threshold_quantity;
                $food_in_big_store->purchase_price = $purchase_price;
                $food_in_big_store->cump = $purchase_price;
                $food_in_big_store->selling_price = $selling_price;
                //$food_in_big_store->total_value = $quantity * $food->purchase_price;
                $food_in_big_store->total_purchase_value = $quantity * $food->purchase_price;
                $food_in_big_store->total_selling_value = $quantity * $food->selling_price;
                $food_in_big_store->unit = $unit;
                $food_in_big_store->code = $code_store;
                $food_in_big_store->created_by = $this->user->name;
                $food_in_big_store->save();
            }
        }

        DB::commit();
            session()->flash('success', 'Food has been created !!');
            return redirect()->route('admin.foods.index');
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
     * @param  int  $code
     * @return \Illuminate\Http\Response
     */
    public function show($code)
    {
        $food_big_store = FoodBigStore::where('code',$code)->first();
        $food_big_stores = FoodBigStoreDetail::where('code',$code)->where('food_id','!=','')->get();
        return view('backend.pages.food_big_store.show', compact(
            'food_big_stores','food_big_store'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('food.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any food !');
        }

        $food = Food::find($id);
        $categories = FoodCategory::all();
        $food_extra_big_stores = FoodExtraBigStore::all();
        $food_big_stores = FoodBigStore::all();
        $food_small_stores = FoodSmallStore::all();
        return view('backend.pages.food.edit', compact(
            'food', 
            'categories',
            'food_big_stores',
            'food_small_stores',
            'food_extra_big_stores'));
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
        if (is_null($this->user) || !$this->user->can('food.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any food !');
        }

        // Create New Food

        $request->validate([
            'name' => 'required|max:255',
            'unit' => 'required|max:20',
            'purchase_price' => 'required',
            'quantity' => 'required',
            'store_type' => 'required',
            'code_store' => 'required',
        ]);

        try {DB::beginTransaction();

        $store_type = $request->store_type;
        $code_store = $request->code_store;

        $food = Food::where('id',$id)->first();

        $food->name = $request->name;
        $food->quantity = $request->quantity;
        $food->unit = $request->unit;
        $food->purchase_price = $request->purchase_price;
        
        $food->specification = $request->specification;
        $food->selling_price = $request->selling_price;
        $food->threshold_quantity = $request->threshold_quantity;
        $food->fcategory_id = $request->fcategory_id;
        $food->code_store = $code_store;
        $food->store_type = $store_type;
        $food->created_by = $this->user->name;
        $food->save();

        $food_id = $id;

            $unit = Food::where('id',$food_id)->value('unit');
            $quantity = Food::where('id',$food_id)->value('quantity');
            $threshold_quantity = Food::where('id',$food_id)->value('threshold_quantity');
            $purchase_price = Food::where('id',$food_id)->value('purchase_price');
            $selling_price = Food::where('id',$food_id)->value('selling_price');
            $specification = Food::where('id',$food_id)->value('specification');

        if ($store_type == '1') {
            $food_big_store_code = FoodBigStoreDetail::where('code',$code_store)->value('code');
            $food_in_big_store = FoodBigStoreDetail::where('code',$code_store)->where('food_id',$food_id)->first();
            if (!empty($food_in_big_store)) {
                $food_in_big_store->food_id = $food_id;
                $food_in_big_store->quantity = $quantity;
                $food_in_big_store->threshold_quantity = $threshold_quantity;
                $food_in_big_store->purchase_price = $purchase_price;
                $food_in_big_store->cump = $purchase_price;
                $food_in_big_store->selling_price = $selling_price;
                $food_in_big_store->total_value_bottle = $quantity * $food->purchase_price;
                $food_in_big_store->total_purchase_value = $quantity * $food->purchase_price;
                $food_in_big_store->total_selling_value = $quantity * $food->selling_price;
                $food_in_big_store->unit = $unit;
                $food_in_big_store->code = $code_store;
                $food_in_big_store->created_by = $this->user->name;
                $food_in_big_store->save();
            }else{
                $food_in_big_store = new FoodBigStoreDetail();
                $food_in_big_store->food_id = $food_id;
                $food_in_big_store->quantity = $quantity;
                $food_in_big_store->threshold_quantity = $threshold_quantity;
                $food_in_big_store->purchase_price = $purchase_price;
                $food_in_big_store->cump = $purchase_price;
                $food_in_big_store->selling_price = $selling_price;
                $food_in_big_store->total_value_bottle = $quantity * $food->purchase_price;
                $food_in_big_store->total_purchase_value = $quantity * $food->purchase_price;
                $food_in_big_store->total_selling_value = $quantity * $food->selling_price;
                $food_in_big_store->unit = $unit;
                $food_in_big_store->code = $code_store;
                $food_in_big_store->created_by = $this->user->name;
                $food_in_big_store->save();
            }
        }elseif($store_type == '2'){
            $drink_small_store_code = FoodSmallStoreDetail::where('code',$code_store)->value('code');
            $food_in_small_store = FoodSmallStoreDetail::where('code',$code_store)->where('food_id',$food_id)->first();
            if (!empty($food_in_small_store)) {
                $food_in_small_store->food_id = $food_id;
                $food_in_small_store->quantity = $quantity;
                $food_in_small_store->threshold_quantity = $threshold_quantity;
                $food_in_small_store->purchase_price = $purchase_price;
                $food_in_small_store->cump = $purchase_price;
                $food_in_small_store->selling_price = $selling_price;
                $food_in_small_store->total_value_bottle = $quantity * $food->purchase_price;
                $food_in_small_store->total_purchase_value = $quantity * $food->purchase_price;
                $food_in_small_store->total_selling_value = $quantity * $food->selling_price;
                $food_in_small_store->unit = $unit;
                $food_in_small_store->code = $code_store;
                $food_in_small_store->created_by = $this->user->name;
                $food_in_small_store->save();
            }else{
                $food_in_small_store = new FoodSmallStoreDetail();
                $food_in_small_store->food_id = $food_id;
                $food_in_small_store->quantity = $quantity;
                $food_in_small_store->threshold_quantity = $threshold_quantity;
                $food_in_small_store->purchase_price = $purchase_price;
                $food_in_small_store->cump = $purchase_price;
                $food_in_small_store->selling_price = $selling_price;
                $food_in_small_store->total_value_bottle = $quantity * $food->purchase_price;
                $food_in_small_store->total_purchase_value = $quantity * $food->purchase_price;
                $food_in_small_store->total_selling_value = $quantity * $food->selling_price;
                $food_in_small_store->unit = $unit;
                $food_in_small_store->code = $code_store;
                $food_in_small_store->created_by = $this->user->name;
                $food_in_small_store->save();
            }
        }else{
            $food_big_store_code = FoodExtraBigStoreDetail::where('code',$code_store)->value('code');
            $food_in_big_store = FoodExtraBigStoreDetail::where('code',$code_store)->where('food_id',$food_id)->first();
            if (!empty($food_in_big_store)) {
                $food_in_big_store->food_id = $food_id;
                $food_in_big_store->quantity = $quantity;
                $food_in_big_store->threshold_quantity = $threshold_quantity;
                $food_in_big_store->purchase_price = $purchase_price;
                $food_in_big_store->cump = $purchase_price;
                $food_in_big_store->selling_price = $selling_price;
                $food_in_big_store->total_value_bottle = $quantity * $food->purchase_price;
                $food_in_big_store->total_purchase_value = $quantity * $food->purchase_price;
                $food_in_big_store->total_selling_value = $quantity * $food->selling_price;
                $food_in_big_store->unit = $unit;
                $food_in_big_store->code = $code_store;
                $food_in_big_store->created_by = $this->user->name;
                $food_in_big_store->save();
            }else{
                $food_in_big_store = new FoodExtraBigStoreDetail();
                $food_in_big_store->food_id = $food_id;
                $food_in_big_store->quantity = $quantity;
                $food_in_big_store->threshold_quantity = $threshold_quantity;
                $food_in_big_store->purchase_price = $purchase_price;
                $food_in_big_store->cump = $purchase_price;
                $food_in_big_store->selling_price = $selling_price;
                $food_in_big_store->total_value_bottle = $quantity * $food->purchase_price;
                $food_in_big_store->total_purchase_value = $quantity * $food->purchase_price;
                $food_in_big_store->total_selling_value = $quantity * $food->selling_price;
                $food_in_big_store->unit = $unit;
                $food_in_big_store->code = $code_store;
                $food_in_big_store->created_by = $this->user->name;
                $food_in_big_store->save();
            }
        }

        DB::commit();
            session()->flash('success', 'Food has been updated !!');
            return redirect()->route('admin.foods.index');
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
        if (is_null($this->user) || !$this->user->can('food.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any food !');
        }

        $food = Food::find($id);
        if (!is_null($food)) {
            $food->delete();
        }

        session()->flash('success', 'Food has been deleted !!');
        return back();
    }
}

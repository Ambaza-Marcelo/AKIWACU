<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Storage;
use App\Models\Food;
use App\Models\FoodStockin;
use App\Models\FoodStockinDetail;
use App\Models\FoodBigStoreDetail;
use App\Models\FoodBigStore;
use App\Models\FoodExtraBigStore;
use App\Models\FoodExtraBigStoreDetail;
use App\Models\FoodSmallStore;
use App\Models\FoodSmallStoreDetail;
use App\Models\FoodBigReport;
use App\Models\FoodExtraBigReport;
use App\Models\FoodSmallReport;
use Carbon\Carbon;
use PDF;
use Validator;
use Excel;
use Mail;

class FoodStockinController extends Controller
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
        if (is_null($this->user) || !$this->user->can('food_stockin.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stockin !');
        }elseif ($this->user->can('food_stockin.view') && $this->user->can('food_small_inventory.view') && $this->user->can('food_big_inventory.view')) {
            $stockins = FoodStockin::orderBy('id','desc')->take(1000)->get();
            return view('backend.pages.food_stockin.index', compact('stockins'));
        }elseif ($this->user->can('food_stockin.view') && $this->user->can('food_big_inventory.view')) {
            $stockins = FoodStockin::where('destination_bg_store_id','!=','')->orderBy('id','desc')->take(200)->get();
            return view('backend.pages.food_stockin.index', compact('stockins'));
        }elseif ($this->user->can('food_stockin.view') && $this->user->can('food_small_inventory.view')) {
            $stockins = FoodStockin::where('destination_sm_store_id','!=','')->orderBy('id','desc')->take(200)->get();
            return view('backend.pages.food_stockin.index', compact('stockins'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('food_stockin.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockin !');
        }

        $foods  = Food::orderBy('name','asc')->get();
        $food_big_stores = FoodBigStore::all();
        $food_extra_big_stores = FoodExtraBigStore::all();
        $food_small_stores = FoodSmallStore::all();
        return view('backend.pages.food_stockin.create', compact('foods','food_big_stores','food_extra_big_stores','food_small_stores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('food_stockin.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockin !');
        }

        $rules = array(
                'food_id.*'  => 'required',
                'date'  => 'required',
                //'unit.*'  => 'required',
                'quantity.*'  => 'required',
                'purchase_price.*'  => 'required',
                'handingover'  => 'required',
                'origin'  => 'required',
                'receptionist'  => 'required',
                'item_movement_type'  => 'required',
                'store_type'  => 'required',
                'description'  => 'required|max:490'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            try {DB::beginTransaction();

            $food_id = $request->food_id;
            $date = $request->date;
            $invoice_currency = $request->invoice_currency;
            $handingover = $request->handingover;
            $receptionist = $request->receptionist;
            $origin = $request->origin;
            $store_type = $request->store_type;
            $description =$request->description; 
            $item_movement_type =$request->item_movement_type; 
            $destination_extra_store_id = $request->destination_extra_store_id;
            $destination_bg_store_id = $request->destination_bg_store_id;
            $destination_sm_store_id = $request->destination_sm_store_id;
            $unit = $request->unit;
            $quantity = $request->quantity;
            $purchase_price = $request->purchase_price;
            

            $latest = FoodStockin::latest()->first();
            if ($latest) {
               $stockin_no = 'BE' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $stockin_no = 'BE' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            if (!empty($destination_sm_store_id)) {
                $destination_sm_store_id = $destination_sm_store_id;
            }elseif(!empty($destination_bg_store_id )){
                $destination_bg_store_id = $destination_bg_store_id ;
            }elseif(!empty($destination_extra_store_id)){
                $destination_extra_store_id = $destination_extra_store_id;
            }else{
                abort(403, 'Sorry !! You have to choose a store ! more information contact Marcellin');
            }

            $created_by = $this->user->name;

            $stockin_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$stockin_no;


            for( $count = 0; $count < count($food_id); $count++ ){
                $total_amount_purchase = $quantity[$count] * $purchase_price[$count];

                $data = array(
                    'food_id' => $food_id[$count],
                    'date' => $date,
                    'store_type' => $store_type,
                    'quantity' => $quantity[$count],
                    //'unit' => $unit[$count],
                    'purchase_price' => $purchase_price[$count],
                    'total_amount_purchase' => $total_amount_purchase,
                    'receptionist' => $receptionist,
                    'handingover' => $handingover,
                    'origin' => $origin,
                    'destination_extra_store_id' => $destination_extra_store_id,
                    'destination_bg_store_id' => $destination_bg_store_id,
                    'destination_sm_store_id' => $destination_sm_store_id,
                    'stockin_no' => $stockin_no,
                    'stockin_signature' => $stockin_signature,
                    'created_by' => $created_by,
                    'description' => $description,
                    'item_movement_type' => $item_movement_type,
                    'status' => 1,
                    'created_at' => \Carbon\Carbon::now()

                );
                $insert_data[] = $data;

                
            }
            FoodStockinDetail::insert($insert_data);


            //create stockin
            $stockin = new FoodStockin();
            $stockin->date = $date;
            $stockin->stockin_no = $stockin_no;
            $stockin->stockin_signature = $stockin_signature;
            $stockin->receptionist = $receptionist;
            $stockin->handingover = $handingover;
            $stockin->origin = $origin;
            $stockin->destination_extra_store_id = $destination_extra_store_id;
            $stockin->destination_bg_store_id = $destination_bg_store_id;
            $stockin->destination_sm_store_id = $destination_sm_store_id;
            $stockin->created_by = $created_by;
            $stockin->store_type = $store_type;
            $stockin->status = 1;
            $stockin->item_movement_type = $item_movement_type;
            $stockin->description = $description;
            $stockin->created_at = \Carbon\Carbon::now();
            $stockin->save();

            DB::commit();
            session()->flash('success', 'stockin has been created !!');
            return redirect()->route('admin.food-stockins.index');
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
    public function show($stockin_no)
    {
        //
        $code = FoodStockinDetail::where('stockin_no', $stockin_no)->value('stockin_no');
        $stockins = FoodStockinDetail::where('stockin_no', $stockin_no)->get();
        return view('backend.pages.food_stockin.show', compact('stockins','code'));
         
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($stockin_no)
    {
        if (is_null($this->user) || !$this->user->can('food_stockin.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any stockin !');
        }


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $stockin_no)
    {
        if (is_null($this->user) || !$this->user->can('food_stockin.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any stockin !');
        }

        
    }

    public function bonEntree($stockin_no)
    {
        if (is_null($this->user) || !$this->user->can('food_stockin.create')) {
            abort(403, 'Sorry !! You are Unauthorized to print handover document!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $stockin_no = FoodStockin::where('stockin_no', $stockin_no)->value('stockin_no');
        $datas = FoodStockinDetail::where('stockin_no', $stockin_no)->get();
        $data = FoodStockinDetail::where('stockin_no', $stockin_no)->first();
        $receptionist = FoodStockin::where('stockin_no', $stockin_no)->value('receptionist');
        $description = FoodStockin::where('stockin_no', $stockin_no)->value('description');
        $origin = FoodStockin::where('stockin_no', $stockin_no)->value('origin');
        $handingover = FoodStockin::where('stockin_no', $stockin_no)->value('handingover');
        $stockin_signature = FoodStockin::where('stockin_no', $stockin_no)->value('stockin_signature');
        $date = FoodStockin::where('stockin_no', $stockin_no)->value('date');
        $totalValue = DB::table('food_stockin_details')
            ->where('stockin_no', '=', $stockin_no)
            ->sum('total_amount_purchase');
        $pdf = PDF::loadView('backend.pages.document.food_stockin',compact('datas','stockin_no','totalValue','receptionist','description','origin','handingover','setting','date','data','stockin_signature'));

        Storage::put('public/pdf/food_stockin/'.$stockin_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('BON_ENTREE_'.$stockin_no.'.pdf');
        
    }

    public function validateStockin($stockin_no)
    {
       if (is_null($this->user) || !$this->user->can('food_stockin.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any stockin !');
        }

        try {DB::beginTransaction();

            FoodStockin::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            FoodStockinDetail::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

            DB::commit();
            session()->flash('success', 'stockin has been validated !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function reject($stockin_no)
    {
       if (is_null($this->user) || !$this->user->can('food_stockin.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any stockin !');
        }

        try {DB::beginTransaction();

        FoodStockin::where('stockin_no', '=', $stockin_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
        FoodStockinDetail::where('stockin_no', '=', $stockin_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'Stockin has been rejected !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

    public function reset($stockin_no)
    {
       if (is_null($this->user) || !$this->user->can('food_stockin.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any stockin !');
        }

        try {DB::beginTransaction();

        FoodStockin::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
        FoodStockinDetail::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'Stockin has been reseted !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function confirm($stockin_no)
    {
       if (is_null($this->user) || !$this->user->can('food_stockin.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any stockin !');
        }

        try {DB::beginTransaction();

        FoodStockin::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            FoodStockinDetail::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'Stockin has been confirmed !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function approuve($stockin_no)
    {
       if (is_null($this->user) || !$this->user->can('food_stockin.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any stockin !');
        }

        try {DB::beginTransaction();

        $datas = FoodStockinDetail::where('stockin_no', $stockin_no)->get();

        foreach($datas as $data){

                if ($data->store_type == '2') {
                    $code_store_destination = FoodSmallStore::where('id',$data->destination_sm_store_id)->value('code');

                $valeurStockInitialDestination = FoodSmallStoreDetail::where('code',$code_store_destination)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('total_cump_value');
                $quantityStockInitialDestination = FoodSmallStoreDetail::where('code',$code_store_destination)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('quantity_portion');
                $quantityTotalBigStore = $quantityStockInitialDestination + $data->quantity;


                $valeurAcquisition = $data->quantity * $data->purchase_price;

                $valeurTotalUnite = $data->quantity + $quantityStockInitialDestination;
                $cump = ($valeurStockInitialDestination + $valeurAcquisition) / $valeurTotalUnite;

                $reportSmallStore = array(
                    'food_id' => $data->food_id,
                    'quantity_stock_initial_portion' => $quantityStockInitialDestination,
                    'value_stock_initial_portion' => $valeurStockInitialDestination,
                    'code_store' => $code_store_destination,
                    'stockin_no' => $data->stockin_no,
                    'date' => $data->date,
                    'quantity_stockin' => $data->quantity,
                    'value_stockin' => $data->total_amount_purchase,
                    'quantity_stock_final_portion' => $quantityStockInitialDestination + $data->quantity,
                    'value_stock_final_portion' => $valeurStockInitialDestination + $data->total_amount_purchase,
                    'cump' => $cump,
                    'created_by' => $this->user->name,
                    'type_transaction' => $data->item_movement_type,
                    'document_no' => $data->stockin_no,
                    'description' => $data->description,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportSmallStoreData[] = $reportSmallStore;

                    $smallStore = array(
                        'food_id' => $data->food_id,
                        'quantity_portion' => $quantityTotalBigStore,
                        'purchase_price' => $data->purchase_price,
                        'total_purchase_value' => $quantityTotalBigStore * $data->purchase_price,
                        'cump' => $cump,
                        'total_cump_value' => $quantityTotalBigStore * $cump,
                        'created_by' => $this->user->name,
                        'verified' => false
                    );

                    $bigStoreData[] = $smallStore;

                    $foodData = array(
                        'id' => $data->food_id,
                        'quantity' => $quantityTotalBigStore,
                        'cump' => $cump,
                        'purchase_price' => $data->purchase_price,
                    );

                    Food::where('id',$data->food_id)
                        ->update($foodData);

                        $food = FoodSmallStoreDetail::where('code',$code_store_destination)->where("food_id",$data->food_id)->value('food_id');

                        if (!empty($food)) {
                            FoodSmallStoreDetail::where('code',$code_store_destination)->where('food_id',$data->food_id)
                        ->update($smallStore);
                        $flag = 1;
                        }else{
                            $flag = 0;
                            session()->flash('error', 'this item is not saved in the stock');
                            return back();
                        }
  
                }elseif ($data->store_type == '1') {
                    $code_store_destination = FoodBigStore::where('id',$data->destination_bg_store_id)->value('code');

                $valeurStockInitialDestination = FoodBigStoreDetail::where('code',$code_store_destination)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('total_cump_value');
                $quantityStockInitialDestination = FoodBigStoreDetail::where('code',$code_store_destination)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('quantity');
                $quantityTotalBigStore = $quantityStockInitialDestination + $data->quantity;


                $valeurAcquisition = $data->quantity * $data->purchase_price;

                $valeurTotalUnite = $data->quantity + $quantityStockInitialDestination;
                $cump = ($valeurStockInitialDestination + $valeurAcquisition) / $valeurTotalUnite;

                $reportBigStore = array(
                    'food_id' => $data->food_id,
                    'quantity_stock_initial' => $quantityStockInitialDestination,
                    'value_stock_initial' => $valeurStockInitialDestination,
                    'code_store' => $code_store_destination,
                    'stockin_no' => $data->stockin_no,
                    'date' => $data->date,
                    'quantity_stockin' => $data->quantity,
                    'value_stockin' => $data->total_amount_purchase,
                    'quantity_stock_final' => $quantityStockInitialDestination + $data->quantity,
                    'value_stock_final' => $valeurStockInitialDestination + $data->total_amount_purchase,
                    'cump' => $cump,
                    'created_by' => $this->user->name,
                    'type_transaction' => $data->item_movement_type,
                    'document_no' => $data->stockin_no,
                    'description' => $data->description,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportBigStoreData[] = $reportBigStore;

                    $bigStore = array(
                        'food_id' => $data->food_id,
                        'quantity' => $quantityTotalBigStore,
                        'purchase_price' => $data->purchase_price,
                        'total_purchase_value' => $quantityTotalBigStore * $data->purchase_price,
                        'cump' => $cump,
                        'total_cump_value' => $quantityTotalBigStore * $cump,
                        'created_by' => $this->user->name,
                        'verified' => false
                    );

                    $bigStoreData[] = $bigStore;

                    $foodData = array(
                        'id' => $data->food_id,
                        'quantity' => $quantityTotalBigStore,
                        'cump' => $cump,
                        'purchase_price' => $data->purchase_price,
                    );

                    Food::where('id',$data->food_id)
                        ->update($foodData);

                        $food = FoodBigStoreDetail::where('code',$code_store_destination)->where("food_id",$data->food_id)->value('food_id');

                        if (!empty($food)) {
                            FoodBigStoreDetail::where('code',$code_store_destination)->where('food_id',$data->food_id)
                        ->update($bigStore);
                        $flag = 1;
                        }else{
                            $flag = 0;
                            session()->flash('error', 'this item is not saved in the stock');
                            return back();
                        }
  
                }else{
                    $code_store_destination = FoodExtraBigStore::where('id',$data->destination_extra_store_id)->value('code');

                $valeurStockInitialDestination = FoodExtraBigStoreDetail::where('code',$code_store_destination)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('total_cump_value');
                $quantityStockInitialDestination = FoodExtraBigStoreDetail::where('code',$code_store_destination)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('quantity');
                $quantityTotalBigStore = $quantityStockInitialDestination + $data->quantity;


                $valeurAcquisition = $data->quantity * $data->purchase_price;

                $valeurTotalUnite = $data->quantity + $quantityStockInitialDestination;
                $cump = ($valeurStockInitialDestination + $valeurAcquisition) / $valeurTotalUnite;

                $reportExtraBigStore = array(
                    'food_id' => $data->food_id,
                    'quantity_stock_initial' => $quantityStockInitialDestination,
                    'value_stock_initial' => $valeurStockInitialDestination,
                    'code_store' => $code_store_destination,
                    'stockin_no' => $data->stockin_no,
                    'date' => $data->date,
                    'quantity_stockin' => $data->quantity,
                    'value_stockin' => $data->total_amount_purchase,
                    'quantity_stock_final' => $quantityStockInitialDestination + $data->quantity,
                    'value_stock_final' => $valeurStockInitialDestination + $data->total_amount_purchase,
                    'cump' => $cump,
                    'created_by' => $this->user->name,
                    'type_transaction' => $data->item_movement_type,
                    'document_no' => $data->stockin_no,
                    'description' => $data->description,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportExtraBigStoreData[] = $reportExtraBigStore;

                    $bigStore = array(
                        'food_id' => $data->food_id,
                        'quantity' => $quantityTotalBigStore,
                        'purchase_price' => $data->purchase_price,
                        'total_purchase_value' => $quantityTotalBigStore * $data->purchase_price,
                        'cump' => $cump,
                        'total_cump_value' => $quantityTotalBigStore * $cump,
                        'created_by' => $this->user->name,
                        'verified' => false
                    );

                    $bigStoreData[] = $bigStore;

                    $foodData = array(
                        'id' => $data->food_id,
                        'quantity' => $quantityTotalBigStore,
                        'cump' => $cump,
                        'purchase_price' => $data->purchase_price,
                    );

                    Food::where('id',$data->food_id)
                        ->update($foodData);

                        $food = FoodExtraBigStoreDetail::where('code',$code_store_destination)->where("food_id",$data->food_id)->value('food_id');

                        if (!empty($food)) {
                            FoodExtraBigStoreDetail::where('code',$code_store_destination)->where('food_id',$data->food_id)
                        ->update($bigStore);
                        $flag = 1;
                        }else{
                            $flag = 0;
                            session()->flash('error', 'this item is not saved in the stock');
                            return back();
                        }
  
                }
        }

        if(!empty($data->destination_bg_store_id) && $flag != 0){
            FoodBigReport::insert($reportBigStoreData);
        }elseif (!empty($data->destination_sm_store_id) && $flag != 0) {
            FoodSmallReport::insert($reportSmallStoreData);
        }else{
            FoodExtraBigReport::insert($reportExtraBigStoreData);
        }
            FoodStockin::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);
            FoodStockinDetail::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'Stockin has been done successfuly !, to '.$code_store_destination);
            return back();
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
     * @param  int  $stockin_no
     * @return \Illuminate\Http\Response
     */
    public function destroy($stockin_no)
    {
        if (is_null($this->user) || !$this->user->can('food_stockin.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any stockin !');
        }

        try {DB::beginTransaction();

        $stockin = FoodStockin::where('stockin_no',$stockin_no)->first();
        if (!is_null($stockin)) {
            $stockin->delete();
            FoodStockinDetail::where('stockin_no',$stockin_no)->delete();
        }

        DB::commit();
            session()->flash('success', 'Stockin has been deleted !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }
}

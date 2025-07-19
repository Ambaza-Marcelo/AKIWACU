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
use App\Models\FoodStockout;
use App\Models\FoodStockoutDetail;
use App\Models\FoodBigStoreDetail;
use App\Models\FoodSmallStoreDetail;
use App\Models\FoodBigStore;
use App\Models\FoodSmallStore;
use App\Models\FoodBigReport;
use App\Models\FoodExtraBigStoreDetail;
use App\Models\FoodExtraBigStore;
use App\Models\FoodExtraBigReport;
use App\Models\FoodSmallReport;
use Carbon\Carbon;
use PDF;
use Validator;
use Excel;
use Mail;

class FoodStockoutController extends Controller
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
        if (is_null($this->user) || !$this->user->can('food_stockout.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stockout !');
        }elseif ($this->user->can('food_stockout.view') && $this->user->can('food_small_inventory.view') && $this->user->can('food_big_inventory.view')) {
            $stockouts = FoodStockout::orderBy('id','desc')->take(500)->get();
            return view('backend.pages.food_stockout.index', compact('stockouts'));
        }elseif ($this->user->can('food_stockout.view') && $this->user->can('food_big_inventory.view')) {
            $stockouts = FoodStockout::where('origin_bg_store_id','!=','')->orderBy('id','desc')->take(200)->get();
            return view('backend.pages.food_stockout.index', compact('stockouts'));
        }elseif ($this->user->can('food_stockout.view') && $this->user->can('food_small_inventory.view')) {
            $stockouts = FoodStockout::where('origin_sm_store_id','!=','')->orderBy('id','desc')->take(200)->get();
            return view('backend.pages.food_stockout.index', compact('stockouts'));
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('food_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $foods  = Food::orderBy('name','asc')->get();
        $food_extra_big_stores = FoodExtraBigStore::all();
        $food_big_stores = FoodBigStore::all();
        $food_small_stores = FoodSmallStore::all();
        return view('backend.pages.food_stockout.create', compact('foods','food_big_stores','food_small_stores','food_extra_big_stores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('food_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $rules = array(
                'food_id.*'  => 'required',
                'date'  => 'required',
                //'unit.*'  => 'required',
                'quantity.*'  => 'required',
                'asker'  => 'required',
                'destination'  => 'required',
                'item_movement_type' => 'required',
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
            $asker = $request->asker;
            $destination = $request->destination;
            $origin_sm_store_id = $request->origin_sm_store_id;
            $description =$request->description; 
            $origin_bg_store_id = $request->origin_bg_store_id;
            $origin_extra_store_id = $request->origin_extra_store_id;
            //$unit = $request->unit;
            $quantity = $request->quantity;
            $store_type = $request->store_type;
            $item_movement_type = $request->item_movement_type;
            

            $latest = FoodStockout::orderBy('id','desc')->first();
            if ($latest) {
               $stockout_no = 'BS' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $stockout_no = 'BS' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            if (!empty($origin_sm_store_id)) {
                $origin_sm_store_id = $origin_sm_store_id;
            }elseif(!empty($origin_bg_store_id )){
                $origin_bg_store_id = $origin_bg_store_id ;
            }elseif(!empty($origin_extra_store_id)){
                $origin_extra_store_id = $origin_extra_store_id;
            }else{
                abort(403, 'Sorry !! You have to choose a store ! more information contact Marcellin');
            }

            $created_by = $this->user->name;

            $stockout_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$stockout_no;


            for( $count = 0; $count < count($food_id); $count++ ){
                if ($store_type == 1) {
                    $purchase_price = Food::where('id', $food_id[$count])->value('cump');

                    $total_value = $quantity[$count] * $purchase_price;
                    $total_purchase_value = $quantity[$count] * $purchase_price;

                    $data = array(
                        'food_id' => $food_id[$count],
                        'date' => $date,
                        'quantity' => $quantity[$count],
                        //'unit' => $unit[$count],
                        'purchase_price' => $purchase_price,
                        'total_purchase_value' => $total_purchase_value,
                        'asker' => $asker,
                        'destination' => $destination,
                        'origin_sm_store_id' => $origin_sm_store_id,
                        'origin_bg_store_id' => $origin_bg_store_id,
                        'origin_extra_store_id' => $origin_extra_store_id,
                        'stockout_no' => $stockout_no,
                        'stockout_signature' => $stockout_signature,
                        'store_type' => $store_type,
                        'item_movement_type' => $item_movement_type,
                        'created_by' => $created_by,
                        'description' => $description,
                        'status' => 1,
                        'created_at' => \Carbon\Carbon::now()

                    );
                    $insert_data[] = $data;
                }elseif($store_type == 2){
                    $cump = Food::where('id', $food_id[$count])->value('cump');

                    $food = Food::where('id', $food_id[$count])->first();

                    $purchase_price = $cump / $food->foodMeasurement->equivalent;

                    //$quantityEquivalent = $quantity[$count] / $food->foodMeasurement->sub_equivalent;

                    $total_purchase_value = $quantity[$count] * $purchase_price;


                    $data = array(
                        'food_id' => $food_id[$count],
                        'date' => $date,
                        'quantity_portion' => $quantity[$count],
                        //'unit_portion' => $unit[$count],
                        'purchase_price' => $purchase_price,
                        'total_purchase_value' => $total_purchase_value,
                        'asker' => $asker,
                        'destination' => $destination,
                        'origin_sm_store_id' => $origin_sm_store_id,
                        'origin_bg_store_id' => $origin_bg_store_id,
                        'stockout_no' => $stockout_no,
                        'stockout_signature' => $stockout_signature,
                        'store_type' => $store_type,
                        'item_movement_type' => $item_movement_type,
                        'created_by' => $created_by,
                        'description' => $description,
                        'status' => 1,
                        'created_at' => \Carbon\Carbon::now()

                    );
                    $insert_data[] = $data;
                }else{
                    $cump = Food::where('id', $food_id[$count])->value('cump');
                    $purchase_price = Food::where('id', $food_id[$count])->value('cump');

                    $total_purchase_value = $quantity[$count] * $purchase_price;

                    $data = array(
                        'food_id' => $food_id[$count],
                        'date' => $date,
                        'quantity' => $quantity[$count],
                        //'unit' => $unit[$count],
                        'purchase_price' => $purchase_price,
                        'total_purchase_value' => $total_purchase_value,
                        'asker' => $asker,
                        'destination' => $destination,
                        'origin_sm_store_id' => $origin_sm_store_id,
                        'origin_bg_store_id' => $origin_bg_store_id,
                        'origin_extra_store_id' => $origin_extra_store_id,
                        'stockout_no' => $stockout_no,
                        'stockout_signature' => $stockout_signature,
                        'store_type' => $store_type,
                        'item_movement_type' => $item_movement_type,
                        'created_by' => $created_by,
                        'description' => $description,
                        'status' => 1,
                        'created_at' => \Carbon\Carbon::now()

                    );
                    $insert_data[] = $data;
                }

                
            }
            FoodStockoutDetail::insert($insert_data);


            //create stockout
            $stockout = new FoodStockout();
            $stockout->date = $date;
            $stockout->stockout_no = $stockout_no;
            $stockout->stockout_signature = $stockout_signature;
            $stockout->store_type = $store_type;
            $stockout->asker = $asker;
            $stockout->destination = $destination;
            $stockout->origin_sm_store_id = $origin_sm_store_id;
            $stockout->origin_bg_store_id = $origin_bg_store_id;
            $stockout->origin_extra_store_id = $origin_extra_store_id;
            $stockout->created_by = $created_by;
            $stockout->status = 1;
            $stockout->description = $description;
            $stockout->created_at = \Carbon\Carbon::now();
            $stockout->save();

            DB::commit();
            session()->flash('success', 'stockout has been created !!');
            return redirect()->route('admin.food-stockouts.index');
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
    public function show($stockout_no)
    {
        //
        $code = FoodStockoutDetail::where('stockout_no', $stockout_no)->value('stockout_no');
        $stockouts = FoodStockoutDetail::where('stockout_no', $stockout_no)->get();
        return view('backend.pages.food_stockout.show', compact('stockouts','code'));
         
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($stockout_no)
    {
        if (is_null($this->user) || !$this->user->can('food_stockout.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any stockout !');
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $stockout_no)
    {
        if (is_null($this->user) || !$this->user->can('food_stockout.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any stockout !');
        }
                
    }

    public function bonSortie($stockout_no)
    {
        if (is_null($this->user) || !$this->user->can('food_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to print handover document!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $code = FoodStockout::where('stockout_no', $stockout_no)->value('stockout_no');
        $datas = FoodStockoutDetail::where('stockout_no', $stockout_no)->get();
        $description = FoodStockout::where('stockout_no', $stockout_no)->value('description');
        $stockout_signature = FoodStockout::where('stockout_no', $stockout_no)->value('stockout_signature');
        $date = FoodStockout::where('stockout_no', $stockout_no)->value('date');
        $totalValue = DB::table('food_stockout_details')
            ->where('stockout_no', '=', $stockout_no)
            ->sum('total_purchase_value');
        $pdf = PDF::loadView('backend.pages.document.food_stockout',compact('datas','code','totalValue','description','setting','date','stockout_signature','stockout_no'));

        Storage::put('public/pdf/food_stockout/'.$stockout_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('BON_SORTIE_'.$stockout_no.'.pdf');
        
    }

    public function validateStockout($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('food_stockout.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any stockout !');
        }

        try{DB::beginTransaction();
            FoodStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            FoodStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

            DB::commit();
            session()->flash('success', 'stockout has been validated !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

    public function reject($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('food_stockout.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any stockout !');
        }

        try{DB::beginTransaction();

        FoodStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
        FoodStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'Stockout has been rejected !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

    public function reset($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('food_stockout.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any stockout !');
        }

        FoodStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
        FoodStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        session()->flash('success', 'Stockout has been reseted !!');
        return back();
    }

    public function confirm($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('food_stockout.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any stockout !');
        }

        try{DB::beginTransaction();

        FoodStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            FoodStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'Stockout has been confirmed !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

    public function approuve($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('food_stockout.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any stockout !');
        }

        try{DB::beginTransaction();

        $datas = FoodStockoutDetail::where('stockout_no', $stockout_no)->get();


        $stockout = FoodStockoutDetail::where('stockout_no', $stockout_no)->first();

        foreach($datas as $data){

            $code_store_origin = FoodBigStore::where('id',$data->origin_bg_store_id)->value('code');

            $cump = Food::where('id', $data->food_id)->value('cump');
            $purchase_price = Food::where('id', $data->food_id)->value('purchase_price');
            if ($cump <= 0) {
                $cump = $purchase_price;
            }else{
                $cump = $cump;
            }

            if ($data->store_type === '1') {

                $valeurStockInitial = FoodBigStoreDetail::where('code',$code_store_origin)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('total_cump_value');
                $quantityStockInitial = FoodBigStoreDetail::where('code',$code_store_origin)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('quantity');
                $quantityRestantBigStore = $quantityStockInitial - $data->quantity;

                $reportBigStore = array(
                    'food_id' => $data->food_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'code_store' => $code_store_origin,
                    'code_store_origin' => $code_store_origin,
                    'stockout_no' => $data->stockout_no,
                    'date' => $data->date,
                    'cump' => $cump,
                    'quantity_stockout' => $data->quantity,
                    'value_stockout' => $data->value_stockout,
                    'quantity_stock_final' => $quantityStockInitial - $data->quantity,
                    'value_stock_final' => $valeurStockInitial - $data->total_selling_value,
                    'created_by' => $this->user->name,
                    'type_transaction' => $data->item_movement_type,
                    'document_no' => $data->stockout_no,
                    'description' => $data->description,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportBigStoreData[] = $reportBigStore;

                    $bigStore = array(
                        'food_id' => $data->food_id,
                        'quantity' => $quantityRestantBigStore,
                        'total_selling_value' => $quantityRestantBigStore * $data->purchase_price,
                        'total_purchase_value' => $quantityRestantBigStore * $data->purchase_price,
                        'total_cump_value' => $quantityRestantBigStore * $data->purchase_price,
                        'created_by' => $this->user->name,
                        'verified' => true,
                        'created_at' => \Carbon\Carbon::now()
                    );


                    if ($data->quantity <= $quantityStockInitial) {
                        
                        FoodBigStoreDetail::where('code',$code_store_origin)->where('food_id',$data->food_id)
                        ->update($bigStore);

                        $flag = 0;

                        
                    }else{
                        foreach ($datas as $data) {
                            $code_store_origin = FoodBigStore::where('id',$data->origin_bg_store_id)->value('code');

                            $valeurStockInitial = FoodBigStoreDetail::where('code',$code_store_origin)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('total_cump_value');
                            $quantityStockInitial = FoodBigStoreDetail::where('code',$code_store_origin)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('quantity');
                            $quantityTotalBigStore = $quantityStockInitial + $data->quantity;

                            $returnDataBigStore = array(
                                'food_id' => $data->food_id,
                                'quantity' => $quantityTotalBigStore,
                                'total_selling_value' => $quantityTotalBigStore * $data->purchase_price,
                                'total_purchase_value' => $quantityTotalBigStore * $data->purchase_price,
                                'total_cump_value' => $quantityTotalBigStore * $data->purchase_price,
                                'created_by' => $this->user->name,
                                'verified' => false,
                                'created_at' => \Carbon\Carbon::now()
                            );

                            $statusBigStore = FoodBigStoreDetail::where('code',$code_store_origin)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('verified');
                    
                            if ($statusBigStore == true) {
                        
                                FoodBigStoreDetail::where('code',$code_store_origin)->where('food_id',$data->food_id)
                                ->update($returnDataBigStore);

                                $flag = 1;
                            }
                        }

                        FoodBigStoreDetail::where('food_id','!=','')->update(['verified' => false]);
                        FoodSmallStoreDetail::where('food_id','!=','')->update(['verified' => false]);
                        FoodExtraBigStoreDetail::where('food_id','!=','')->update(['verified' => false]);
                        
                        session()->flash('error', $this->user->name.' ,Why do you want to stockout a quantity you do not have in your store?please rewrite a valid quantity!');
                        return redirect()->back();
                    }
            }elseif ($data->store_type === '2'){
                $code_store_origin = FoodSmallStore::where('id',$data->origin_sm_store_id)->value('code');

                $valeurStockInitial = FoodSmallStoreDetail::where('code',$code_store_origin)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('total_cump_value');
                $quantityStockInitial = FoodSmallStoreDetail::where('code',$code_store_origin)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('quantity_portion');
                $quantityRestantSmallStore = $quantityStockInitial - $data->quantity_portion;

                $cump = $cump/$data->food->foodMeasurement->equivalent;
                $reportSmallStore = array(
                    'food_id' => $data->food_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'quantity_stock_initial_portion' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'code_store' => $code_store_origin,
                    'code_store_origin' => $code_store_origin,
                    'stockout_no' => $data->stockout_no,
                    'date' => $data->date,
                    'cump' => $cump,
                    'quantity_stockout' => $data->quantity_portion,
                    'value_stockout' => $data->value_stockout,
                    'quantity_stock_final' => $quantityStockInitial - $data->quantity_portion,
                    'value_stock_final' => $valeurStockInitial - $data->total_selling_value,
                    'created_by' => $this->user->name,
                    'type_transaction' => $data->item_movement_type,
                    'document_no' => $data->stockout_no,
                    'description' => $data->description,
                    'created_at' => \Carbon\Carbon::now()
                );

                $reportSmallStoreData[] = $reportSmallStore;

                    $smallStore = array(
                        'food_id' => $data->food_id,
                        'quantity_portion' => $quantityRestantSmallStore,
                        'total_purchase_value' => $quantityRestantSmallStore * $cump,
                        'total_cump_value' => $quantityRestantSmallStore * $cump,
                        'created_by' => $this->user->name,
                        'verified' => true,
                        'created_at' => \Carbon\Carbon::now()
                    );


                    if ($data->quantity_portion <= $quantityStockInitial) {

                        
                        
                        FoodSmallStoreDetail::where('code',$code_store_origin)->where('food_id',$data->food_id)
                        ->update($smallStore);

                        $flag = 0;

                        
                    }else{
                        foreach ($datas as $data) {
                            $code_store_origin = FoodSmallStore::where('id',$data->origin_sm_store_id)->value('code');

                            $valeurStockInitial = FoodSmallStoreDetail::where('code',$code_store_origin)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('total_cump_value');
                            $quantityStockInitial = FoodSmallStoreDetail::where('code',$code_store_origin)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('quantity_portion');
                            $quantityTotalSmallStore = $quantityStockInitial + $data->quantity_portion;

                            $returnDataSmallStore = array(
                                'food_id' => $data->food_id,
                                'quantity_portion' => $quantityTotalSmallStore,
                                'total_selling_value' => $quantityTotalSmallStore * $data->purchase_price,
                                'total_purchase_value' => $quantityTotalSmallStore * $data->purchase_price,
                                'total_cump_value' => $quantityTotalSmallStore * $data->purchase_price,
                                'created_by' => $this->user->name,
                                'verified' => true,
                                'created_at' => \Carbon\Carbon::now()
                            );

                            $statusSmallStore = FoodSmallStoreDetail::where('code',$code_store_origin)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('verified');
                    
                            if ($statusSmallStore == true) {
                        
                                FoodSmallStoreDetail::where('code',$code_store_origin)->where('food_id',$data->food_id)
                                ->update($returnDataSmallStore);

                                $flag = 1;
                            }
                        }

                        FoodBigStoreDetail::where('food_id','!=','')->update(['verified' => false]);
                        FoodSmallStoreDetail::where('food_id','!=','')->update(['verified' => false]);
                        FoodExtraBigStoreDetail::where('food_id','!=','')->update(['verified' => false]);

                        session()->flash('error', $this->user->name.' ,Why do you want to stockout a quantity you do not have in your store?please rewrite a valid quantity!');
                        return redirect()->back();
                    }
            }else{
                $code_store_origin = FoodExtraBigStore::where('id',$data->origin_extra_store_id)->value('code');

                $valeurStockInitial = FoodExtraBigStoreDetail::where('code',$code_store_origin)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('total_selling_value');
                $quantityStockInitial = FoodExtraBigStoreDetail::where('code',$code_store_origin)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('quantity');
                $quantityRestantSmallStore = $quantityStockInitial - $data->quantity;

                $reportextraBigStore = array(
                    'food_id' => $data->food_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'code_store' => $code_store_origin,
                    'code_store_origin' => $code_store_origin,
                    'stockout_no' => $data->stockout_no,
                    'date' => $data->date,
                    'quantity_stockout' => $data->quantity,
                    'value_stockout' => $data->value_stockout,
                    'quantity_stock_final' => $quantityStockInitial - $data->quantity,
                    'value_stock_final' => $valeurStockInitial - $data->total_selling_value,
                    'created_by' => $this->user->name,
                    'type_transaction' => $data->item_movement_type,
                    'document_no' => $data->stockout_no,
                    'description' => $data->description,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportExtraBigStoreData[] = $reportextraBigStore;

                    $extraBigStore = array(
                        'food_id' => $data->food_id,
                        'quantity' => $quantityRestantSmallStore,
                        'total_selling_value' => $quantityRestantSmallStore * $data->selling_price,
                        'total_purchase_value' => $quantityRestantSmallStore * $data->selling_price,
                        'total_cump_value' => $quantityRestantSmallStore * $data->selling_price,
                        'created_by' => $this->user->name,
                        'verified' => false,
                        'created_at' => \Carbon\Carbon::now()
                    );


                    if ($data->quantity <= $quantityStockInitial) {

                        
                        
                        FoodExtraBigStoreDetail::where('code',$code_store_origin)->where('food_id',$data->food_id)
                        ->update($extraBigStore);

                        $flag = 0;

                        
                    }else{
                        foreach ($datas as $data) {
                            $code_store_origin = FoodExtraBigStore::where('id',$data->origin_extra_store_id)->value('code');

                            $valeurStockInitial = FoodExtraBigStoreDetail::where('code',$code_store_origin)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('total_cump_value');
                            $quantityStockInitial = FoodExtraBigStoreDetail::where('code',$code_store_origin)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('quantity');
                            $quantityTotalExtraStore = $quantityStockInitial + $data->quantity;

                            $returnDataExtraStore = array(
                                'food_id' => $data->food_id,
                                'quantity_portion' => $quantityTotalExtraStore,
                                'total_selling_value' => $quantityTotalExtraStore * $data->purchase_price,
                                'total_purchase_value' => $quantityTotalExtraStore * $data->purchase_price,
                                'total_cump_value' => $quantityTotalExtraStore * $data->purchase_price,
                                'created_by' => $this->user->name,
                                'verified' => true,
                                'created_at' => \Carbon\Carbon::now()
                            );

                            $statusExtraStore = FoodSmallStoreDetail::where('code',$code_store_origin)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('verified');
                    
                            if ($statusExtraStore == true) {
                        
                                FoodExtraBigStoreDetail::where('code',$code_store_origin)->where('food_id',$data->food_id)
                                ->update($returnDataExtraStore);

                                $flag = 1;
                            }
                        }

                        FoodBigStoreDetail::where('food_id','!=','')->update(['verified' => false]);
                        FoodSmallStoreDetail::where('food_id','!=','')->update(['verified' => false]);
                        FoodExtraBigStoreDetail::where('food_id','!=','')->update(['verified' => false]);

                        session()->flash('error', $this->user->name.' ,Why do you want to stockout a quantity you do not have in your store?please rewrite a valid quantity!');
                        return redirect()->back();
                    }
            }
                
  
        }


        if($stockout->store_type == 1 && $flag != 1){
            FoodBigReport::insert($reportBigStoreData);
        }elseif ($stockout->store_type == 2 && $flag != 1) {
            FoodSmallReport::insert($reportSmallStoreData);
        }else{
            FoodExtraBigReport::insert($reportExtraBigStoreData);
        }

        FoodBigStoreDetail::where('food_id','!=','')->update(['verified' => false]);
        FoodSmallStoreDetail::where('food_id','!=','')->update(['verified' => false]);
        FoodExtraBigStoreDetail::where('food_id','!=','')->update(['verified' => false]);

        FoodStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);
        FoodStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);

        DB::commit();
        session()->flash('success', 'Stockout has been done successfuly !, from '.$code_store_origin);
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
     * @param  int  $stockout_no
     * @return \Illuminate\Http\Response
     */
    public function destroy($stockout_no)
    {
        if (is_null($this->user) || !$this->user->can('food_stockout.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any stockout !');
        }

        $stockout = FoodStockout::where('stockout_no',$stockout_no)->first();
        if (!is_null($stockout)) {
            $stockout->delete();
            FoodStockoutDetail::where('stockout_no',$stockout_no)->delete();
        }

        session()->flash('success', 'Stockout has been deleted !!');
        return back();
    }
}

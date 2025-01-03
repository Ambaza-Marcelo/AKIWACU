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
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use App\Models\Drink;
use App\Models\DrinkStockout;
use App\Models\DrinkStockoutDetail;
use App\Models\DrinkBigStoreDetail;
use App\Models\DrinkSmallStoreDetail;
use App\Models\DrinkBigStore;
use App\Models\DrinkSmallStore;
use App\Models\DrinkExtraBigStoreDetail;
use App\Models\DrinkExtraBigStore;
use App\Models\DrinkBigReport;
use App\Models\DrinkSmallReport;
use App\Models\DrinkExtraBigReport;
use App\Exports\DrinkStockoutExport;
use Carbon\Carbon;
use PDF;
use Validator;
use Excel;
use Mail;

class DrinkStockoutController extends Controller
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
        if (is_null($this->user) || !$this->user->can('drink_stockout.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stockout !');
        }elseif ($this->user->can('drink_stockout.view') && $this->user->can('drink_small_inventory.view') && $this->user->can('drink_big_inventory.view')) {
            $stockouts = DrinkStockout::orderBy('id','desc')->take(1000)->get();
            return view('backend.pages.drink_stockout.index', compact('stockouts'));
        }elseif ($this->user->can('drink_stockout.view') && $this->user->can('drink_big_inventory.view')) {
            $stockouts = DrinkStockout::where('origin_bg_store_id','!=','')->orderBy('id','desc')->take(200)->get();
            return view('backend.pages.drink_stockout.index', compact('stockouts'));
        }elseif ($this->user->can('drink_stockout.view') && $this->user->can('drink_small_inventory.view')) {
            $stockouts = DrinkStockout::where('origin_sm_store_id','!=','')->orderBy('id','desc')->take(200)->get();
            return view('backend.pages.drink_stockout.index', compact('stockouts'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('drink_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $drinks  = Drink::orderBy('name','asc')->get();
        $drink_extra_big_stores = DrinkExtraBigStore::all();
        $drink_big_stores = DrinkBigStore::all();
        $drink_small_stores = DrinkSmallStore::all();
        return view('backend.pages.drink_stockout.create', compact('drinks','drink_big_stores','drink_small_stores','drink_extra_big_stores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('drink_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $rules = array(
                'drink_id.*'  => 'required',
                'date'  => 'required',
                //'unit.*'  => 'required',
                'quantity.*'  => 'required',
                'asker'  => 'required',
                'destination'  => 'required',
                'item_movement_type'  => 'required',
                'description'  => 'required|max:490'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            try {DB::beginTransaction();

            $drink_id = $request->drink_id;
            $date = Carbon::now();
            $invoice_currency = $request->invoice_currency;
            $asker = $request->asker;
            $destination = $request->destination;
            $origin_sm_store_id = $request->origin_sm_store_id;
            $description =$request->description; 
            $origin_bg_store_id = $request->origin_bg_store_id;
            $origin_extra_store_id = $request->origin_extra_store_id;
            $item_movement_type = $request->item_movement_type;
            //$unit = $request->unit;
            $quantity = $request->quantity;
            $store_type = $request->store_type;
            

            $latest = DrinkStockout::latest()->first();
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


            for( $count = 0; $count < count($drink_id); $count++ ){

                $selling_price = Drink::where('id', $drink_id[$count])->value('selling_price');
                $purchase_price = Drink::where('id', $drink_id[$count])->value('purchase_price');
                $cump = Drink::where('id', $drink_id[$count])->value('cump');

                $total_value = $quantity[$count] * $purchase_price;
                $total_purchase_value = $quantity[$count] * $cump;
                $total_selling_value = $quantity[$count] * $selling_price;

                $data = array(
                    'drink_id' => $drink_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    //'unit' => $unit[$count],
                    'purchase_price' => $purchase_price,
                    'price' => $cump,
                    'selling_price' => $selling_price,
                    'total_purchase_value' => $total_purchase_value,
                    'total_selling_value' => $total_selling_value,
                    'asker' => $asker,
                    'destination' => $destination,
                    'origin_sm_store_id' => $origin_sm_store_id,
                    'origin_bg_store_id' => $origin_bg_store_id,
                    'origin_extra_store_id' => $origin_extra_store_id,
                    'item_movement_type' => $item_movement_type,
                    'stockout_no' => $stockout_no,
                    'stockout_signature' => $stockout_signature,
                    'store_type' => $store_type,
                    'created_by' => $created_by,
                    'description' => $description,
                    'status' => 1,
                    'created_at' => \Carbon\Carbon::now()

                );
                $insert_data[] = $data;

                
            }
            DrinkStockoutDetail::insert($insert_data);


            //create stockout
            $stockout = new DrinkStockout();
            $stockout->date = $date;
            $stockout->stockout_no = $stockout_no;
            $stockout->stockout_signature = $stockout_signature;
            $stockout->store_type = $store_type;
            $stockout->asker = $asker;
            $stockout->destination = $destination;
            $stockout->origin_sm_store_id = $origin_sm_store_id;
            $stockout->origin_bg_store_id = $origin_bg_store_id;
            $stockout->origin_extra_store_id = $origin_extra_store_id;
            $stockout->item_movement_type = $item_movement_type;
            $stockout->created_by = $created_by;
            $stockout->status = 1;
            $stockout->description = $description;
            $stockout->created_at = \Carbon\Carbon::now();
            $stockout->save();


            DB::commit();
            session()->flash('success', 'stockout has been created !!');
            return redirect()->route('admin.drink-stockouts.index');
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
        $code = DrinkStockoutDetail::where('stockout_no', $stockout_no)->value('stockout_no');
        $stockouts = DrinkStockoutDetail::where('stockout_no', $stockout_no)->get();
        return view('backend.pages.drink_stockout.show', compact('stockouts','code'));
         
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($stockout_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_stockout.edit')) {
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
        if (is_null($this->user) || !$this->user->can('drink_stockout.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any stockout !');
        }

        session()->flash('success', 'stockout has been updated !!');
        return redirect()->route('admin.stockouts.index');
        
    }

    public function bonSortie($stockout_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to print handover document!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $datas = DrinkStockoutDetail::where('stockout_no', $stockout_no)->get();
        $data = DrinkStockout::where('stockout_no', $stockout_no)->first();
        $description = DrinkStockout::where('stockout_no', $stockout_no)->value('description');
        $stockout_signature = DrinkStockout::where('stockout_no', $stockout_no)->value('stockout_signature');
        $date = DrinkStockout::where('stockout_no', $stockout_no)->value('date');
        $totalValue = DB::table('drink_stockout_details')
            ->where('stockout_no', '=', $stockout_no)
            ->sum('total_purchase_value');
        $pdf = PDF::loadView('backend.pages.document.drink_stockout',compact('datas','totalValue','data','description','stockout_no','setting','date','stockout_signature'));

        Storage::put('public/pdf/drink_stockout/'.$stockout_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('BON_SORTIE_'.$stockout_no.'.pdf');
        
    }

    public function validateStockout($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_stockout.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any stockout !');
        }

        try {DB::beginTransaction();
            DrinkStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            DrinkStockoutDetail::where('stockout_no', '=', $stockout_no)
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
       if (is_null($this->user) || !$this->user->can('drink_stockout.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any stockout !');
        }

        try {DB::beginTransaction();

        DrinkStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
        DrinkStockoutDetail::where('stockout_no', '=', $stockout_no)
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
       if (is_null($this->user) || !$this->user->can('drink_stockout.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any stockout !');
        }

        try {DB::beginTransaction();

        DrinkStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
        DrinkStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

                DB::commit();
            session()->flash('success', 'Stockout has been reseted !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function confirm($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_stockout.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any stockout !');
        }

        try {DB::beginTransaction();

        DrinkStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            DrinkStockoutDetail::where('stockout_no', '=', $stockout_no)
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
       if (is_null($this->user) || !$this->user->can('drink_stockout.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any stockout !');
        }

        try {DB::beginTransaction();

        $datas = DrinkStockoutDetail::where('stockout_no', $stockout_no)->get();

        $data = DrinkStockoutDetail::where('stockout_no', $stockout_no)->first();

        foreach($datas as $data){

            if ($data->store_type == 1) {
                $code_store_origin = DrinkBigStore::where('id',$data->origin_bg_store_id)->value('code');

                $valeurStockInitial = DrinkBigStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('total_cump_value');
                $quantityStockInitial = DrinkBigStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('quantity_bottle');
                $quantityRestantBigStore = $quantityStockInitial - $data->quantity;
                $cump = DrinkBigStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('cump');

                $reportBigStore = array(
                    'drink_id' => $data->drink_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'code_store' => $code_store_origin,
                    'code_store_origin' => $code_store_origin,
                    'stockout_no' => $data->stockout_no,
                    'date' => $data->date,
                    'quantity_stockout' => $data->quantity,
                    'value_stockout' => $data->value_stockout,
                    'quantity_stock_final' => $quantityStockInitial - $data->quantity,
                    'value_stock_final' => $valeurStockInitial - $data->total_purchase_value,
                    'type_transaction' => $data->item_movement_type,
                    'cump' => $cump,
                    'document_no' => $data->stockout_no,
                    'created_by' => $this->user->name,
                    'description' => $data->description,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportBigStoreData[] = $reportBigStore;

                    $bigStore = array(
                        'drink_id' => $data->drink_id,
                        'quantity_bottle' => $quantityRestantBigStore,
                        'total_selling_value' => $quantityRestantBigStore * $data->price,
                        'total_purchase_value' => $quantityRestantBigStore * $data->price,
                        'total_cump_value' => $quantityRestantBigStore * $cump,
                        'created_by' => $this->user->name,
                        'verified' => true,
                        'created_at' => \Carbon\Carbon::now()
                    );

                        $drink = DrinkBigStoreDetail::where('code',$code_store_origin)->where("drink_id",$data->drink_id)->value('drink_id');


                    if ($data->quantity <= $quantityStockInitial) {

                        
                        DrinkBigStoreDetail::where('code',$code_store_origin)->where('drink_id',$data->drink_id)
                        ->update($bigStore);
                        $flag = 0;

                        /*

                        $theUrl = config('app.guzzle_test_url').'/ebms_api/login/';
                        $response = Http::post($theUrl, [
                            'username'=> config('app.obr_test_username'),
                            'password'=> config('app.obr_test_pwd')

                        ]);
                        $data1 =  json_decode($response);
                        $data2 = ($data1->result);       
    
                        $token = $data2->token;

                        $theUrl = config('app.guzzle_test_url').'/ebms_api/AddStockMovement';  
                        $response = Http::withHeaders([
                        'Authorization' => 'Bearer '.$token,
                        'Accept' => 'application/json'])->post($theUrl, [
                            'system_or_device_id'=> "wsconfig('app.tin_number_company')00565",
                            'item_code'=> $data->drink->code,
                            'item_designation'=>$data->drink->name,
                            'item_quantity'=>$data->quantity,
                            'item_measurement_unit'=>$data->unit,
                            'item_purchase_or_sale_price'=>$data->purchase_price,
                            'item_purchase_or_sale_currency'=> "BIF",
                            'item_movement_type'=> $data->item_movement_type,
                            'item_movement_invoice_ref'=> "",
                            'item_movement_description'=>$data->description,
                            'item_movement_date'=> Carbon::parse($data->updated_at)->format('Y-m-d H:i:s'),

                        ]);
                        */
                    }else{

                        foreach ($datas as $data) {
                            $code_store_origin = DrinkBigStore::where('id',$data->origin_bg_store_id)->value('code');

                            $valeurStockInitial = DrinkBigStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('total_cump_value');
                            $quantityStockInitial = DrinkBigStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('quantity_bottle');
                            $quantityTotalBigStore = $quantityStockInitial + $data->quantity;

                            $cump = DrinkBigStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('cump');

                            $returnDataBigStore = array(
                                'drink_id' => $data->drink_id,
                                'quantity_bottle' => $quantityTotalBigStore,
                                'total_selling_value' => $quantityTotalBigStore * $data->price,
                                'total_purchase_value' => $quantityTotalBigStore * $data->price,
                                'total_cump_value' => $quantityTotalBigStore * $cump,
                                'created_by' => $this->user->name,
                                'verified' => false,
                                'created_at' => \Carbon\Carbon::now()
                            );

                            $statusBigStore = DrinkBigStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('verified');
                    
                            if ($statusBigStore == true) {
                        
                                DrinkBigStoreDetail::where('code',$code_store_origin)->where('drink_id',$data->drink_id)
                                ->update($returnDataBigStore);

                                $flag = 1;
                            }
                        }

                        DrinkBigStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
                        DrinkSmallStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
                        DrinkExtraBigStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
                        
                        session()->flash('error', $this->user->name.' ,Why do you want to stockout a quantity you do not have in your store?please rewrite a valid quantity!');
                        return redirect()->back();
                    }
            }elseif ($data->store_type == 2) {
                $code_store_origin = DrinkSmallStore::where('id',$data->origin_sm_store_id)->value('code');

                $valeurStockInitial = DrinkSmallStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('total_cump_value');
                $quantityStockInitial = DrinkSmallStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('quantity_bottle');

                $cump = DrinkBigStoreDetail::where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('cump');

                $quantityRestantSmallStore = $quantityStockInitial - $data->quantity;

                $reportSmallStore = array(
                    'drink_id' => $data->drink_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'code_store' => $code_store_origin,
                    'code_store_origin' => $code_store_origin,
                    'stockout_no' => $data->stockout_no,
                    'date' => $data->date,
                    'quantity_stockout' => $data->quantity,
                    'value_stockout' => $data->value_stockout,
                    'quantity_stock_final' => $quantityStockInitial - $data->quantity,
                    'value_stock_final' => $valeurStockInitial - $data->total_purchase_value,
                    'type_transaction' => $data->item_movement_type,
                    'cump' => $cump,
                    'document_no' => $data->stockout_no,
                    'created_by' => $this->user->name,
                    'description' => $data->description,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportSmallStoreData[] = $reportSmallStore;

                    $smallStore = array(
                        'drink_id' => $data->drink_id,
                        'quantity_bottle' => $quantityRestantSmallStore,
                        'total_selling_value' => $quantityRestantSmallStore * $data->selling_price,
                        'total_purchase_value' => $quantityRestantSmallStore * $data->price,
                        'total_cump_value' => $quantityRestantSmallStore * $data->price,
                        'created_by' => $this->user->name,
                        'verified' => true,
                        'created_at' => \Carbon\Carbon::now()
                    );

                        $drink = DrinkSmallStoreDetail::where('code',$code_store_origin)->where("drink_id",$data->drink_id)->value('drink_id');


                    if ($data->quantity <= $quantityStockInitial) {
                        
                        DrinkSmallStoreDetail::where('code',$code_store_origin)->where('drink_id',$data->drink_id)
                        ->update($smallStore);

                        $flag = 0;

                        
                        $theUrl = config('app.guzzle_test_url').'/ebms_api/login/';
                        $response = Http::post($theUrl, [
                            'username'=> config('app.obr_test_username'),
                            'password'=> config('app.obr_test_pwd')

                        ]);
                        $data1 =  json_decode($response);
                        $data2 = ($data1->result);       
    
                        $token = $data2->token;

                        $theUrl = config('app.guzzle_test_url').'/ebms_api/AddStockMovement';  
                        $response = Http::withHeaders([
                        'Authorization' => 'Bearer '.$token,
                        'Accept' => 'application/json'])->post($theUrl, [
                            'system_or_device_id'=> config('app.obr_test_username'),
                            'item_code'=> $data->drink->code,
                            'item_designation'=>$data->drink->name,
                            'item_quantity'=>$data->quantity,
                            'item_measurement_unit'=>$data->drink->drinkMeasurement->purchase_unit,
                            'item_purchase_or_sale_price'=>$cump,
                            'item_purchase_or_sale_currency'=> "BIF",
                            'item_movement_type'=> $data->item_movement_type,
                            'item_movement_invoice_ref'=> "",
                            'item_movement_description'=>$data->description,
                            'item_movement_date'=> $data->date,

                        ]);
                        
                        $dataObr =  json_decode($response);
                        
                        
                    }else{
                        foreach ($datas as $data) {
                            $code_store_origin = DrinkSmallStore::where('id',$data->origin_sm_store_id)->value('code');

                            $valeurStockInitial = DrinkSmallStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('total_cump_value');
                            $quantityStockInitial = DrinkSmallStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('quantity_bottle');
                            $quantityTotalSmallStore = $quantityStockInitial + $data->quantity;

                            $returnDataSmallStore = array(
                                'drink_id' => $data->drink_id,
                                'quantity_bottle' => $quantityTotalSmallStore,
                                'total_selling_value' => $quantityTotalSmallStore * $data->selling_price,
                                'total_purchase_value' => $quantityTotalSmallStore * $data->price,
                                'total_cump_value' => $quantityTotalSmallStore * $data->price,
                                'created_by' => $this->user->name,
                                'verified' => false,
                                'created_at' => \Carbon\Carbon::now()
                            );

                            $statusSmallStore = DrinkSmallStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('verified');
                    
                            if ($statusSmallStore == true) {
                        
                                DrinkSmallStoreDetail::where('code',$code_store_origin)->where('drink_id',$data->drink_id)
                                ->update($returnDataSmallStore);

                                $flag = 1;
                            }
                        }

                        DrinkBigStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
                        DrinkSmallStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
                        DrinkExtraBigStoreDetail::where('drink_id','!=','')->update(['verified' => false]);

                        session()->flash('error', $this->user->name.' ,Why do you want to stockout a quantity you do not have in your store?please rewrite a valid quantity!');
                        return redirect()->back();
                    }
            }else{
                $code_store_origin = DrinkExtraBigStore::where('id',$data->origin_extra_store_id)->value('code');

                $valeurStockInitial = DrinkExtraBigStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('total_cump_value');
                $quantityStockInitial = DrinkExtraBigStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('quantity');
                $quantityRestantSmallStore = $quantityStockInitial - $data->quantity;

                $cump = DrinkExtraBigStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('cump');

                $reportSmallStore = array(
                    'drink_id' => $data->drink_id,
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
                    'type_transaction' => $data->item_movement_type,
                    'cump' => $cump,
                    'document_no' => $data->stockout_no,
                    'created_by' => $this->user->name,
                    'description' => $data->description,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportExtraStoreData[] = $reportSmallStore;

                    $extraStore = array(
                        'drink_id' => $data->drink_id,
                        'quantity' => $quantityRestantSmallStore,
                        'total_selling_value' => $quantityRestantSmallStore * $data->selling_price,
                        'total_purchase_value' => $quantityRestantSmallStore * $data->price,
                        'total_cump_value' => $quantityRestantSmallStore * $data->price,
                        'created_by' => $this->user->name,
                        'verified' => true,
                        'created_at' => \Carbon\Carbon::now()
                    );

                        $drink = DrinkExtraBigStoreDetail::where('code',$code_store_origin)->where("drink_id",$data->drink_id)->value('drink_id');


                    if ($data->quantity <= $quantityStockInitial) {
                        
                        DrinkExtraBigStoreDetail::where('code',$code_store_origin)->where('drink_id',$data->drink_id)
                        ->update($extraStore);

                        $flag = 0;

                        /*

                        $theUrl = config('app.guzzle_test_url').'/ebms_api/login/';
                        $response = Http::post($theUrl, [
                            'username'=> "wsconfig('app.tin_number_company')00565",
                            'password'=> "5VS(GO:p"

                        ]);
                        $data1 =  json_decode($response);
                        $data2 = ($data1->result);       
    
                        $token = $data2->token;

                        $theUrl = config('app.guzzle_test_url').'/ebms_api/AddStockMovement';  
                        $response = Http::withHeaders([
                        'Authorization' => 'Bearer '.$token,
                        'Accept' => 'application/json'])->post($theUrl, [
                            'system_or_device_id'=> "wsconfig('app.tin_number_company')00565",
                            'item_code'=> $data->drink->code,
                            'item_designation'=>$data->drink->name,
                            'item_quantity'=>$data->quantity,
                            'item_measurement_unit'=>$data->unit,
                            'item_purchase_or_sale_price'=>$data->purchase_price,
                            'item_purchase_or_sale_currency'=> "BIF",
                            'item_movement_type'=> $data->item_movement_type,
                            'item_movement_invoice_ref'=> "",
                            'item_movement_description'=>$data->description,
                            'item_movement_date'=> Carbon::parse($data->updated_at)->format('Y-m-d H:i:s'),

                        ]);

                        */
                        
                    }else{
                        foreach ($datas as $data) {
                            $code_store_origin = DrinkExtraBigStore::where('id',$data->origin_extra_store_id)->value('code');

                            $valeurStockInitial = DrinkExtraBigStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('total_cump_value');
                            $quantityStockInitial = DrinkExtraBigStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('quantity');
                            $quantityRestantSmallStore = $quantityStockInitial - $data->quantity;

                            $returnDataExtraStore = array(
                                'drink_id' => $data->drink_id,
                                'quantity' => $quantityRestantSmallStore,
                                'total_selling_value' => $quantityRestantSmallStore * $data->selling_price,
                                'total_purchase_value' => $quantityRestantSmallStore * $data->price,
                                'total_cump_value' => $quantityRestantSmallStore * $data->price,
                                'created_by' => $this->user->name,
                                'verified' => false,
                                'created_at' => \Carbon\Carbon::now()
                            );

                            $statusExtraStore = DrinkExtraBigStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('verified');
                    
                            if ($statusExtraStore == true) {
                        
                                DrinkExtraBigStoreDetail::where('code',$code_store_origin)->where('drink_id',$data->drink_id)
                                ->update($returnDataExtraStore);

                                $flag = 1;
                            }
                        }

                        DrinkBigStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
                        DrinkSmallStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
                        DrinkExtraBigStoreDetail::where('drink_id','!=','')->update(['verified' => false]);

                        session()->flash('error', $this->user->name.' ,Why do you want to stockout a quantity you do not have in your store?please rewrite a valid quantity!');
                        return redirect()->back();
                    }
            }
                
  
        }
        if($data->store_type == 1 && $flag != 1){
            DrinkBigReport::insert($reportBigStoreData);
        }elseif ($data->store_type == 2 && $flag != 1) {
            DrinkSmallReport::insert($reportSmallStoreData);
        }else{
            DrinkExtraBigReport::insert($reportExtraStoreData);
        }
        
        DrinkBigStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
        DrinkSmallStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
        DrinkExtraBigStoreDetail::where('drink_id','!=','')->update(['verified' => false]);

        DrinkStockout::where('stockout_no', '=', $stockout_no)
                            ->update(['status' => 4,'approuved_by' => $this->user->name]);
        DrinkStockoutDetail::where('stockout_no', '=', $stockout_no)
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

    public function exportToExcel(Request $request)
    {
        return Excel::download(new DrinkStockoutExport, 'RAPPORT DES SORTIES DES BOISSONS.xlsx');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $stockout_no
     * @return \Illuminate\Http\Response
     */
    public function destroy($stockout_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_stockout.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any stockout !');
        }

        try {DB::beginTransaction();

        $stockout = DrinkStockout::where('stockout_no',$stockout_no)->first();
        if (!is_null($stockout)) {
            $stockout->delete();
            DrinkStockoutDetail::where('stockout_no',$stockout_no)->delete();
        }

        DB::commit();
            session()->flash('success', 'Stockout has been deleted !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

}

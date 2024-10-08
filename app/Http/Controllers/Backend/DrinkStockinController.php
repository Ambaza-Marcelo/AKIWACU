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
use App\Models\DrinkStockin;
use App\Models\DrinkStockinDetail;
use App\Models\DrinkBigStoreDetail;
use App\Models\DrinkBigStore;
use App\Models\DrinkBigReport;
use App\Models\DrinkSmallStoreDetail;
use App\Models\DrinkSmallStore;
use App\Models\DrinkExtraBigStoreDetail;
use App\Models\DrinkExtraBigStore;
use App\Models\DrinkSmallReport;
use App\Models\DrinkExtraBigReport;
use App\Exports\DrinkStockinExport;
use Carbon\Carbon;
use PDF;
use Validator;
use Excel;
use Mail;

class DrinkStockinController extends Controller
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
        if (is_null($this->user) || !$this->user->can('drink_stockin.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stockin !');
        }

        $stockins = DrinkStockin::orderBy('id','desc')->take(200)->get();
        return view('backend.pages.drink_stockin.index', compact('stockins'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('drink_stockin.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockin !');
        }

        $drinks  = Drink::orderBy('name','asc')->get();
        $drink_extra_big_stores = DrinkExtraBigStore::all();
        $drink_big_stores = DrinkBigStore::all();
        $drink_small_stores = DrinkSmallStore::all();
        return view('backend.pages.drink_stockin.create', compact('drinks','drink_extra_big_stores','drink_big_stores','drink_small_stores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('drink_stockin.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockin !');
        }

        $rules = array(
                'drink_id.*'  => 'required',
                'date'  => 'required',
                'unit.*'  => 'required',
                'quantity.*'  => 'required',
                'purchase_price.*'  => 'required',
                'handingover'  => 'required',
                'origin'  => 'required',
                'store_type'  => 'required',
                'receptionist'  => 'required',
                //'destination_bg_store_id'  => 'required',
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
            $date = $request->date;
            $invoice_currency = $request->invoice_currency;
            $handingover = $request->handingover;
            $receptionist = $request->receptionist;
            $origin = $request->origin;
            $store_type = $request->store_type;
            $description =$request->description; 
            $destination_bg_store_id = $request->destination_bg_store_id;
            $destination_sm_store_id = $request->destination_sm_store_id;
            $destination_extra_store_id = $request->destination_extra_store_id;
            $item_movement_type = $request->item_movement_type;
            $unit = $request->unit;
            $quantity = $request->quantity;
            $purchase_price = $request->purchase_price;
            

            $latest = DrinkStockin::latest()->first();
            if ($latest) {
               $stockin_no = 'BE' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $stockin_no = 'BE' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $stockin_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$stockin_no;


            for( $count = 0; $count < count($drink_id); $count++ ){
                $total_amount_purchase = $quantity[$count] * $purchase_price[$count];

                $data = array(
                    'drink_id' => $drink_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'unit' => $unit[$count],
                    'purchase_price' => $purchase_price[$count],
                    'total_amount_purchase' => $total_amount_purchase,
                    'receptionist' => $receptionist,
                    'handingover' => $handingover,
                    'origin' => $origin,
                    'store_type' => $store_type,
                    'destination_bg_store_id' => $destination_bg_store_id,
                    'destination_sm_store_id' => $destination_sm_store_id,
                    'destination_extra_store_id' => $destination_extra_store_id,
                    'item_movement_type' => $item_movement_type,
                    'stockin_no' => $stockin_no,
                    'stockin_signature' => $stockin_signature,
                    'created_by' => $created_by,
                    'description' => $description,
                    'status' => 1,
                    'created_at' => \Carbon\Carbon::now()

                );
                $insert_data[] = $data;

                
            }
            DrinkStockinDetail::insert($insert_data);


            //create stockin
            $stockin = new DrinkStockin();
            $stockin->date = $date;
            $stockin->stockin_no = $stockin_no;
            $stockin->stockin_signature = $stockin_signature;
            $stockin->receptionist = $receptionist;
            $stockin->handingover = $handingover;
            $stockin->origin = $origin;
            $stockin->store_type = $store_type;
            $stockin->destination_bg_store_id = $destination_bg_store_id;
            $stockin->destination_sm_store_id = $destination_sm_store_id;
            $stockin->destination_extra_store_id = $destination_extra_store_id;
            $stockin->item_movement_type = $item_movement_type;
            $stockin->created_by = $created_by;
            $stockin->status = 1;
            $stockin->description = $description;
            $stockin->created_at = \Carbon\Carbon::now();
            $stockin->save();

            DB::commit();
            session()->flash('success', 'stockin has been created !!');
            return redirect()->route('admin.drink-stockins.index');
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
        $code = DrinkStockinDetail::where('stockin_no', $stockin_no)->value('stockin_no');
        $stockins = DrinkStockinDetail::where('stockin_no', $stockin_no)->get();
        return view('backend.pages.drink_stockin.show', compact('stockins','code'));
         
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($stockin_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_stockin.edit')) {
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
        if (is_null($this->user) || !$this->user->can('drink_stockin.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any stockin !');
        }
        
    }

    public function bonEntree($stockin_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_stockin.create')) {
            abort(403, 'Sorry !! You are Unauthorized to print handover document!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $code = DrinkStockin::where('stockin_no', $stockin_no)->value('stockin_no');
        $datas = DrinkStockinDetail::where('stockin_no', $stockin_no)->get();
        $receptionniste = DrinkStockin::where('stockin_no', $stockin_no)->value('receptionist');
        $description = DrinkStockin::where('stockin_no', $stockin_no)->value('description');
        $handingover = DrinkStockin::where('stockin_no', $stockin_no)->value('handingover');
        $stockin_signature = DrinkStockin::where('stockin_no', $stockin_no)->value('stockin_signature');
        $date = DrinkStockin::where('stockin_no', $stockin_no)->value('date');
        $totalValue = DB::table('drink_stockin_details')
            ->where('stockin_no', '=', $stockin_no)
            ->sum('total_amount_purchase');
        $pdf = PDF::loadView('backend.pages.document.drink_stockin',compact('datas','code','totalValue','receptionniste','description','handingover','setting','date','stockin_signature','stockin_no'));

        Storage::put('public/pdf/drink_stockin/'.$stockin_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('BON_ENTREE_'.$stockin_no.'.pdf');
        
    }

    public function validateStockin($stockin_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_stockin.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any stockin !');
        }
        try {DB::beginTransaction();

            DrinkStockin::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            DrinkStockinDetail::where('stockin_no', '=', $stockin_no)
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
       if (is_null($this->user) || !$this->user->can('drink_stockin.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any stockin !');
        }

        try {DB::beginTransaction();

        DrinkStockin::where('stockin_no', '=', $stockin_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
        DrinkStockinDetail::where('stockin_no', '=', $stockin_no)
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
       if (is_null($this->user) || !$this->user->can('drink_stockin.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any stockin !');
        }

        try {DB::beginTransaction();

        DrinkStockin::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
        DrinkStockinDetail::where('stockin_no', '=', $stockin_no)
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
       if (is_null($this->user) || !$this->user->can('drink_stockin.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any stockin !');
        }

        try {DB::beginTransaction();

        DrinkStockin::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            DrinkStockinDetail::where('stockin_no', '=', $stockin_no)
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
       if (is_null($this->user) || !$this->user->can('drink_stockin.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any stockin !');
        }

        try {DB::beginTransaction();


        $datas = DrinkStockinDetail::where('stockin_no', $stockin_no)->get();

        $data = DrinkStockinDetail::where('stockin_no', $stockin_no)->first();

        foreach($datas as $data){

            if (!empty($data->destination_bg_store_id)) {
                $code_store_destination = DrinkBigStore::where('id',$data->destination_bg_store_id)->value('code');

                $valeurStockInitialDestination = DrinkBigStoreDetail::where('code',$code_store_destination)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('total_cump_value');
                $quantityStockInitialDestination = DrinkBigStoreDetail::where('code',$code_store_destination)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('quantity_bottle');
                $quantityTotalBigStore = $quantityStockInitialDestination + $data->quantity;


                $valeurAcquisition = $data->quantity * $data->purchase_price;

                $valeurTotalUnite = $data->quantity + $quantityStockInitialDestination;
                $cump = ($valeurStockInitialDestination + $valeurAcquisition) / $valeurTotalUnite;

                $reportBigStore = array(
                    'drink_id' => $data->drink_id,
                    'quantity_stock_initial' => $quantityStockInitialDestination,
                    'value_stock_initial' => $valeurStockInitialDestination,
                    'code_store' => $code_store_destination,
                    'stockin_no' => $data->stockin_no,
                    'date' => $data->date,
                    'quantity_stockin' => $data->quantity,
                    'value_stockin' => $data->total_amount_purchase,
                    'quantity_stock_final' => $quantityStockInitialDestination + $data->quantity,
                    'value_stock_final' => $valeurStockInitialDestination + $data->total_amount_purchase,
                    'type_transaction' => $data->item_movement_type,
                    'cump' => $cump,
                    'purchase_price' => $data->purchase_price,
                    'document_no' => $data->stockin_no,
                    'created_by' => $this->user->name,
                    'description' => $data->description,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportBigStoreData[] = $reportBigStore;

                    $bigStore = array(
                        'drink_id' => $data->drink_id,
                        'quantity_bottle' => $quantityTotalBigStore,
                        'purchase_price' => $data->purchase_price,
                        'total_purchase_value' => $quantityTotalBigStore * $data->purchase_price,
                        'cump' => $cump,
                        'total_cump_value' => $quantityTotalBigStore * $cump,
                        'created_by' => $this->user->name,
                        'verified' => false
                    );

                    $drinkData = array(
                        'id' => $data->drink_id,
                        'quantity_bottle' => $quantityTotalBigStore,
                        'cump' => $cump,
                        'purchase_price' => $data->purchase_price,
                    );

                        Drink::where('id',$data->drink_id)
                        ->update($drinkData);

                    $bigStoreData[] = $bigStore;

                        $drink = DrinkBigStoreDetail::where('code',$code_store_destination)->where("drink_id",$data->drink_id)->value('drink_id');

                        if (!empty($drink)) {
                            DrinkBigStoreDetail::where('code',$code_store_destination)->where('drink_id',$data->drink_id)
                        ->update($bigStore);
                        $flag = 1;
                        }else{
                            $code_store_destination = DrinkBigStore::where('id',$data->destination_sm_store_id)->value('code');

                            $valeurStockInitialDestination = DrinkBigStoreDetail::where('code',$code_store_destination)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('total_cump_value');
                            $quantityStockInitialDestination = DrinkBigStoreDetail::where('code',$code_store_destination)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('quantity_bottle');
                            $quantityRestantSmallStore = $quantityStockInitialDestination - $data->quantity;


                            $valeurAcquisition = $data->quantity * $data->purchase_price;

                            $valeurTotalUnite = $data->quantity + $quantityStockInitialDestination;
                            $cump = ($valeurStockInitialDestination + $valeurAcquisition) / $valeurTotalUnite;

                            $returnBigStore = array(
                                'drink_id' => $data->drink_id,
                                'quantity_bottle' => $quantityRestantSmallStore,
                                'purchase_price' => $data->purchase_price,
                                'total_purchase_value' => $quantityRestantSmallStore * $data->purchase_price,
                                'cump' => $cump,
                                'total_cump_value' => $quantityRestantSmallStore * $cump,
                                'created_by' => $this->user->name,
                                'verified' => false
                            );

                            DrinkBigStoreDetail::where('code',$code_store_destination)->where('drink_id',$data->drink_id)->where('verified',true)
                            ->update($returnBigStore);

                            DrinkBigStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
                            DrinkSmallStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
                            DrinkExtraBigStoreDetail::where('drink_id','!=','')->update(['verified' => false]);

                            $flag = 0;
                            session()->flash('error', 'this item is not saved in the stock');
                            return back();
                        }
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
            }elseif (!empty($data->destination_sm_store_id)) {
                $code_store_destination = DrinkSmallStore::where('id',$data->destination_sm_store_id)->value('code');

                $valeurStockInitialDestination = DrinkSmallStoreDetail::where('code',$code_store_destination)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('total_cump_value');
                $quantityStockInitialDestination = DrinkSmallStoreDetail::where('code',$code_store_destination)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('quantity_bottle');
                $quantityTotalSmallStore = $quantityStockInitialDestination + $data->quantity;


                $valeurAcquisition = $data->quantity * $data->purchase_price;

                $valeurTotalUnite = $data->quantity + $quantityStockInitialDestination;
                $cump = ($valeurStockInitialDestination + $valeurAcquisition) / $valeurTotalUnite;

                $reportSmallStore = array(
                    'drink_id' => $data->drink_id,
                    'quantity_stock_initial' => $quantityStockInitialDestination,
                    'value_stock_initial' => $valeurStockInitialDestination,
                    'code_store' => $code_store_destination,
                    'stockin_no' => $data->stockin_no,
                    'date' => $data->date,
                    'quantity_stockin' => $data->quantity,
                    'value_stockin' => $data->total_amount_purchase,
                    'quantity_stock_final' => $quantityStockInitialDestination + $data->quantity,
                    'value_stock_final' => $valeurStockInitialDestination + $data->total_amount_purchase,
                    'type_transaction' => $data->item_movement_type,
                    //'cump' => $cump,
                    //'purchase_price' => $data->purchase_price,
                    'document_no' => $data->stockin_no,
                    'created_by' => $this->user->name,
                    'description' => $data->description,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportSmallStoreData[] = $reportSmallStore;

                    $smallStore = array(
                        'drink_id' => $data->drink_id,
                        'quantity_bottle' => $quantityTotalSmallStore,
                        //'purchase_price' => $data->purchase_price,
                        'total_purchase_value' => $quantityTotalSmallStore * $data->purchase_price,
                        //'cump' => $cump,
                        'total_cump_value' => $quantityTotalSmallStore * $cump,
                        'created_by' => $this->user->name,
                        'verified' => true
                    );

                    $drinkData = array(
                        'id' => $data->drink_id,
                        'quantity_bottle' => $quantityTotalSmallStore,
                        //'cump' => $cump,
                        //'purchase_price' => $data->purchase_price,
                    );

                        Drink::where('id',$data->drink_id)
                        ->update($drinkData);

                    $smallStoreData[] = $smallStore;

                        $drink = DrinkSmallStoreDetail::where('code',$code_store_destination)->where("drink_id",$data->drink_id)->value('drink_id');

                        if (!empty($drink)) {
                            DrinkSmallStoreDetail::where('code',$code_store_destination)->where('drink_id',$data->drink_id)
                        ->update($smallStore);
                        $flag = 1;
                        }else{

                            $code_store_destination = DrinkSmallStore::where('id',$data->destination_sm_store_id)->value('code');

                            $valeurStockInitialDestination = DrinkSmallStoreDetail::where('code',$code_store_destination)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('total_cump_value');
                            $quantityStockInitialDestination = DrinkSmallStoreDetail::where('code',$code_store_destination)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('quantity_bottle');
                            $quantityRestantSmallStore = $quantityStockInitialDestination - $data->quantity;


                            $valeurAcquisition = $data->quantity * $data->purchase_price;

                            $valeurTotalUnite = $data->quantity + $quantityStockInitialDestination;
                            $cump = ($valeurStockInitialDestination + $valeurAcquisition) / $valeurTotalUnite;

                            $returnSmallStore = array(
                                'drink_id' => $data->drink_id,
                                'quantity_bottle' => $quantityRestantSmallStore,
                                //'purchase_price' => $data->purchase_price,
                                //'total_purchase_value' => $quantityRestantSmallStore * $data->purchase_price,
                                //'cump' => $cump,
                                //'total_cump_value' => $quantityRestantSmallStore * $cump,
                                'created_by' => $this->user->name,
                                'verified' => false
                            );

                            DrinkSmallStoreDetail::where('code',$code_store_destination)->where('drink_id',$data->drink_id)->where('verified',true)
                            ->update($returnSmallStore);

                            DrinkBigStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
                            DrinkSmallStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
                            DrinkExtraBigStoreDetail::where('drink_id','!=','')->update(['verified' => false]);

                            $flag = 0;
                            session()->flash('error', 'this item is not saved in the stock');
                            return back();
                        }
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
                $code_store_destination = DrinkExtraBigStore::where('id',$data->destination_extra_store_id)->value('code');

                $valeurStockInitialDestination = DrinkExtraBigStoreDetail::where('code',$code_store_destination)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('total_cump_value');
                $quantityStockInitialDestination = DrinkExtraBigStoreDetail::where('code',$code_store_destination)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('quantity_bottle');
                $quantityTotalBigStore = $quantityStockInitialDestination + $data->quantity;


                $valeurAcquisition = $data->quantity * $data->purchase_price;

                $valeurTotalUnite = $data->quantity + $quantityStockInitialDestination;
                $cump = ($valeurStockInitialDestination + $valeurAcquisition) / $valeurTotalUnite;

                $reportExtraBigStore = array(
                    'drink_id' => $data->drink_id,
                    'quantity_stock_initial' => $quantityStockInitialDestination,
                    'value_stock_initial' => $valeurStockInitialDestination,
                    'code_store' => $code_store_destination,
                    'stockin_no' => $data->stockin_no,
                    'date' => $data->date,
                    'quantity_stockin' => $data->quantity,
                    'value_stockin' => $data->total_amount_purchase,
                    'quantity_stock_final' => $quantityStockInitialDestination + $data->quantity,
                    'value_stock_final' => $valeurStockInitialDestination + $data->total_amount_purchase,
                    'type_transaction' => $data->item_movement_type,
                    'cump' => $cump,
                    'purchase_price' => $data->purchase_price,
                    'document_no' => $data->stockin_no,
                    'created_by' => $this->user->name,
                    'description' => $data->description,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportExtraBigStoreData[] = $reportExtraBigStore;

                    $extrabigStore = array(
                        'drink_id' => $data->drink_id,
                        'quantity_bottle' => $quantityTotalBigStore,
                        'purchase_price' => $data->purchase_price,
                        'total_purchase_value' => $quantityTotalBigStore * $data->purchase_price,
                        'cump' => $cump,
                        'total_cump_value' => $quantityTotalBigStore * $cump,
                        'created_by' => $this->user->name,
                        'verified' => false
                    );

                    $drinkData = array(
                        'id' => $data->drink_id,
                        'quantity_bottle' => $quantityTotalBigStore,
                        'cump' => $cump,
                        'purchase_price' => $data->purchase_price,
                    );

                        Drink::where('id',$data->drink_id)
                        ->update($drinkData);

                    $extrabigStoreData[] = $extrabigStore;

                        $drink = DrinkExtraBigStoreDetail::where('code',$code_store_destination)->where("drink_id",$data->drink_id)->value('drink_id');

                        if (!empty($drink)) {
                            DrinkExtraBigStoreDetail::where('code',$code_store_destination)->where('drink_id',$data->drink_id)
                        ->update($extrabigStore);
                        $flag = 1;
                        }else{
                            $code_store_destination = DrinkExtraBigStore::where('id',$data->destination_sm_store_id)->value('code');

                            $valeurStockInitialDestination = DrinkExtraBigStoreDetail::where('code',$code_store_destination)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('total_cump_value');
                            $quantityStockInitialDestination = DrinkExtraBigStoreDetail::where('code',$code_store_destination)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('quantity_bottle');
                            $quantityRestantSmallStore = $quantityStockInitialDestination - $data->quantity;


                            $valeurAcquisition = $data->quantity * $data->purchase_price;

                            $valeurTotalUnite = $data->quantity + $quantityStockInitialDestination;
                            $cump = ($valeurStockInitialDestination + $valeurAcquisition) / $valeurTotalUnite;

                            $returnExtraBigStore = array(
                                'drink_id' => $data->drink_id,
                                'quantity_bottle' => $quantityRestantSmallStore,
                                'purchase_price' => $data->purchase_price,
                                'total_purchase_value' => $quantityRestantSmallStore * $data->purchase_price,
                                'cump' => $cump,
                                'total_cump_value' => $quantityRestantSmallStore * $cump,
                                'created_by' => $this->user->name,
                                'verified' => false
                            );

                            DrinkExtraBigStoreDetail::where('code',$code_store_destination)->where('drink_id',$data->drink_id)->where('verified',true)
                            ->update($returnExtraBigStore);

                            DrinkBigStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
                            DrinkSmallStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
                            DrinkExtraBigStoreDetail::where('drink_id','!=','')->update(['verified' => false]);

                            $flag = 0;
                            session()->flash('error', 'this item is not saved in the stock');
                            return back();
                        }
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
            }
                
        }


        if(!empty($data->destination_bg_store_id) && $flag != 0){
            DrinkBigReport::insert($reportBigStoreData);
        }elseif (!empty($data->destination_sm_store_id) && $flag != 0) {
            DrinkSmallReport::insert($reportSmallStoreData);
        }else{
            DrinkExtraBigReport::insert($reportExtraBigStoreData);
        }

            DrinkBigStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
            DrinkSmallStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
            DrinkExtraBigStoreDetail::where('drink_id','!=','')->update(['verified' => false]);

            DrinkStockin::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);
            DrinkStockinDetail::where('stockin_no', '=', $stockin_no)
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

    public function exportToExcel(Request $request)
    {
        return Excel::download(new DrinkStockinExport, 'RAPPORT_ENTEES.xlsx');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $stockin_no
     * @return \Illuminate\Http\Response
     */
    public function destroy($stockin_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_stockin.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any stockin !');
        }

        try {DB::beginTransaction();

        $stockin = DrinkStockin::where('stockin_no',$stockin_no)->first();
        if (!is_null($stockin)) {
            $stockin->delete();
            DrinkStockinDetail::where('stockin_no',$stockin_no)->delete();
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

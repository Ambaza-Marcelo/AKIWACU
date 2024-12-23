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
use App\Models\DrinkTransfer;
use App\Models\DrinkTransferDetail;
use App\Models\DrinkExtraBigStoreDetail;
use App\Models\DrinkBigStoreDetail;
use App\Models\DrinkSmallStoreDetail;
use App\Models\DrinkExtraBigStore;
use App\Models\DrinkBigStore;
use App\Models\DrinkSmallStore;
use App\Models\DrinkRequisitionDetail;
use App\Models\DrinkRequisition;
use App\Models\DrinkExtraBigReport;
use App\Models\DrinkBigReport;
use App\Models\DrinkSmallReport;
use App\Exports\DrinkTransfertExport;
use Carbon\Carbon;
use PDF;
use Validator;
use Excel;
use Mail;

class DrinkTransferController extends Controller
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
        if (is_null($this->user) || !$this->user->can('drink_transfer.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any transfer !');
        }

        $transfers = DrinkTransfer::orderBy('id','desc')->take(200)->get();
        return view('backend.pages.drink_transfer.index', compact('transfers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($requisition_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_transfer.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any transfer !');
        }

        $drinks  = Drink::orderBy('name','asc')->get();
        $origin_stores = DrinkBigStore::all();
        $destination_stores = DrinkSmallStore::all();
        $datas = DrinkRequisitionDetail::where('requisition_no', $requisition_no)->get();
        return view('backend.pages.drink_transfer.create', compact('drinks','requisition_no','datas','origin_stores','destination_stores'));
    }

    public function createFromBig($requisition_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_transfer.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any transfer !');
        }

        $drinks  = Drink::where('store_type','!=',2)->orderBy('name','asc')->get();
        $origin_stores = DrinkExtraBigStore::all();
        $destination_stores = DrinkBigStore::all();
        $datas = DrinkRequisitionDetail::where('requisition_no', $requisition_no)->get();
        return view('backend.pages.drink_transfer.create_from_big', compact('drinks','requisition_no','datas','origin_stores','destination_stores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('drink_transfer.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any transfer !');
        }

        $rules = array(
                'drink_id.*'  => 'required',
                'date'  => 'required',
                //'unit.*'  => 'required',
                'quantity_requisitioned.*'  => 'required',
                'quantity_transfered.*'  => 'required',
                //'origin_store_id'  => 'required',
                'requisition_no'  => 'required',
                //'destination_store_id'  => 'required',
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
            $origin_store_id = $request->origin_store_id;
            $origin_extra_store_id = $request->origin_extra_store_id;
            $requisition_no = $request->requisition_no;
            $type_store = $request->type_store;
            $description =$request->description; 
            $destination_store_id = $request->destination_store_id;
            $destination_bg_store_id = $request->destination_bg_store_id;
            //$unit = $request->unit;
            $quantity_requisitioned = $request->quantity_requisitioned;
            $quantity_transfered = $request->quantity_transfered;
            

            $latest = DrinkTransfer::latest()->first();
            if ($latest) {
               $transfer_no = 'BT' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $transfer_no = 'BT' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $transfer_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$transfer_no;


            for( $count = 0; $count < count($drink_id); $count++ ){

                $price = DrinkBigStoreDetail::where('drink_id', $drink_id[$count])->value('cump');
                $total_value_requisitioned = $quantity_requisitioned[$count] * $price;
                $total_value_transfered = $quantity_transfered[$count] * $price;
                $data = array(
                    'drink_id' => $drink_id[$count],
                    'date' => $date,
                    'quantity_requisitioned' => $quantity_requisitioned[$count],
                    'quantity_transfered' => $quantity_transfered[$count],
                    //'unit' => $unit[$count],
                    'price' => $price,
                    'total_value_requisitioned' => $total_value_requisitioned,
                    'total_value_transfered' => $total_value_transfered,
                    'requisition_no' => $requisition_no,
                    'type_store' => $type_store,
                    'origin_extra_store_id' => $origin_extra_store_id,
                    'origin_store_id' => $origin_store_id,
                    'destination_store_id' => $destination_store_id,
                    'destination_bg_store_id' => $destination_bg_store_id,
                    'transfer_no' => $transfer_no,
                    'transfer_signature' => $transfer_signature,
                    'created_by' => $created_by,
                    'description' => $description,
                    'status' => 1,
                    'created_at' => \Carbon\Carbon::now()

                );
                $insert_data[] = $data;

                
            }
            DrinkTransferDetail::insert($insert_data);


            //create transfer
            $transfer = new DrinkTransfer();
            $transfer->date = $date;
            $transfer->transfer_no = $transfer_no;
            $transfer->transfer_signature = $transfer_signature;
            $transfer->requisition_no = $requisition_no;
            $transfer->type_store = $type_store;
            $transfer->origin_extra_store_id = $origin_extra_store_id;
            $transfer->destination_store_id = $destination_store_id;
            $transfer->origin_store_id = $origin_store_id;
            $transfer->destination_bg_store_id = $destination_bg_store_id;
            $transfer->created_by = $created_by;
            $transfer->status = 1;
            $transfer->description = $description;
            $transfer->created_at = \Carbon\Carbon::now();
            $transfer->save();

            DB::commit();
            session()->flash('success', 'transfer has been created !!');
            return redirect()->route('admin.drink-transfers.index');
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
    public function show($transfer_no)
    {
        //
        $code = DrinkTransferDetail::where('transfer_no', $transfer_no)->value('transfer_no');
        $transfers = DrinkTransferDetail::where('transfer_no', $transfer_no)->get();
        return view('backend.pages.drink_transfer.show', compact('transfers','code'));
         
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($transfer_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_transfer.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any transfer !');
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $transfer_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_transfer.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any transfer !');
        }
        
    }

    public function bonTransfert($transfer_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_transfer.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $transfer_no = DrinkTransfer::where('transfer_no', $transfer_no)->value('transfer_no');
        $datas = DrinkTransferDetail::where('transfer_no', $transfer_no)->get();
        $data = DrinkTransfer::where('transfer_no', $transfer_no)->first();
        $description = DrinkTransfer::where('transfer_no', $transfer_no)->value('description');
        $requisition_no = DrinkTransfer::where('transfer_no', $transfer_no)->value('requisition_no');
        $transfer_signature = DrinkTransfer::where('transfer_no', $transfer_no)->value('transfer_signature');
        $date = DrinkTransfer::where('transfer_no', $transfer_no)->value('date');
        $totalValueTransfered = DB::table('drink_transfer_details')
            ->where('transfer_no', '=', $transfer_no)
            ->sum('total_value_transfered');
        $totalValueRequisitioned = DB::table('drink_transfer_details')
            ->where('transfer_no', '=', $transfer_no)
            ->sum('total_value_requisitioned');
        $pdf = PDF::loadView('backend.pages.document.drink_transfert',compact('datas','transfer_no','totalValueTransfered','totalValueRequisitioned','data','description','requisition_no','setting','date','transfer_signature'));

        Storage::put('public/pdf/drink_transfert/'.$transfer_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('bon_transfert'.$transfer_no.'.pdf');
        
    }

    public function validateTransfer($transfer_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_transfer.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any transfer !');
        }

        try {DB::beginTransaction();

            DrinkTransfer::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            DrinkTransferDetail::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

            DB::commit();
            session()->flash('success', 'transfer has been validated !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function reject($transfer_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_transfer.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any transfer !');
        }

        try {DB::beginTransaction();

        $data = DrinkTransfer::where('transfer_no',$transfer_no)->first();
        DrinkTransfer::where('transfer_no', '=', $transfer_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
        DrinkTransferDetail::where('transfer_no', '=', $transfer_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        DrinkRequisition::where('requisition_no', '=', $data->requisition_no)
                        ->update(['status' => -1]);
        DrinkRequisitionDetail::where('requisition_no', '=', $data->requisition_no)
                        ->update(['status' => -1]);

                DB::commit();
            session()->flash('success', 'Transfer has been rejected !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

    public function reset($transfer_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_transfer.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any transfer !');
        }

        try {DB::beginTransaction();

        DrinkTransfer::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
        DrinkTransferDetail::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

            DB::commit();
            session()->flash('success', 'Transfer has been reseted !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function confirm($transfer_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_transfer.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any transfer !');
        }

        try {DB::beginTransaction();

            DrinkTransfer::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            DrinkTransferDetail::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'Transfer has been confirmed !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function approuve($transfer_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_transfer.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any transfer !');
        }

        try {DB::beginTransaction();

        $datas = DrinkTransferDetail::where('transfer_no', $transfer_no)->get();

        foreach($datas as $data){

                if ($data->type_store == '') {
                $code_store_origin = DrinkBigStore::where('id',$data->origin_store_id)->value('code');
                $code_store_destination = DrinkSmallStore::where('id',$data->destination_store_id)->value('code');

                $valeurStockInitialOrigine = DrinkBigStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('total_cump_value');
                $quantityStockInitialOrigine = DrinkBigStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('quantity_bottle');
                $quantityRestantBigStore = $quantityStockInitialOrigine - $data->quantity_transfered;

                $valeurStockInitialDestination = DrinkSmallStoreDetail::where('code',$code_store_destination)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('total_cump_value');
                $quantityStockInitialDestination = DrinkSmallStoreDetail::where('code',$code_store_destination)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('quantity_bottle');
                $quantityRestantSmallStore = $quantityStockInitialDestination + $data->quantity_transfered;

                $cump = DrinkBigStoreDetail::where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('cump');

                $valeurAcquisition = $data->quantity_transfered * $data->price;

                $valeurTotalUnite = $data->quantity_transfered + $quantityStockInitialDestination;

                $reportBigStore = array(
                    'drink_id' => $data->drink_id,
                    'quantity_stock_initial' => $quantityStockInitialOrigine,
                    'value_stock_initial' => $valeurStockInitialOrigine,
                    'code_store' => $code_store_origin,
                    'code_store_origin' => $code_store_origin,
                    'code_store_destination' => $code_store_destination,
                    'transfer_no' => $data->transfer_no,
                    'date' => $data->date,
                    'quantity_transfer' => $data->quantity_transfered,
                    'value_transfer' => $data->total_value_transfered,
                    'quantity_stock_final' => $quantityStockInitialOrigine - $data->quantity_transfered,
                    'value_stock_final' => $valeurStockInitialOrigine - $data->total_value_transfered,
                    'type_transaction' => 'SORTIE TRANSFERT',
                    'cump' => $cump,
                    'document_no' => $data->transfer_no,
                    'created_by' => $this->user->name,
                    'description' => $data->description,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportBigStoreData[] = $reportBigStore;


                $reportSmallStore = array(
                    'drink_id' => $data->drink_id,
                    'quantity_stock_initial' => $quantityStockInitialDestination,
                    'value_stock_initial' => $valeurStockInitialDestination,
                    'code_store' => $code_store_destination,
                    'code_store_origin' => $code_store_origin,
                    'code_store_destination' => $code_store_destination,
                    'transfer_no' => $data->transfer_no,
                    'date' => $data->date,
                    'quantity_transfer' => $data->quantity_transfered,
                    'value_transfer' => $data->total_value_transfered,
                    'quantity_stock_final' => $quantityStockInitialDestination + $data->quantity_transfered,
                    'value_stock_final' => $valeurStockInitialDestination + $data->total_value_transfered,
                    'type_transaction' => 'ENTREE TRANSFERT',
                    'cump' => $cump,
                    'document_no' => $data->transfer_no,
                    'created_by' => $this->user->name,
                    'description' => $data->description,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportSmallStoreData[] = $reportSmallStore;

                    $bigStore = array(
                        'drink_id' => $data->drink_id,
                        'quantity_bottle' => $quantityRestantBigStore,
                        'total_selling_value' => $quantityRestantBigStore * $data->price,
                        'total_purchase_value' => $quantityRestantBigStore * $data->price,
                        'total_cump_value' => $quantityRestantBigStore * $data->price,
                        'created_by' => $this->user->name,
                        'verified' => true,
                        'created_at' => \Carbon\Carbon::now()
                    );


                    $smallStoreData = array(
                            'drink_id' => $data->drink_id,
                            'quantity_bottle' => $data->quantity_transfered,
                            'cump' => $cump,
                            'unit' => $data->unit,
                            'code' => $code_store_destination,
                            'purchase_price' => $data->price,
                            'selling_price' => $data->price,
                            'total_cump_value' => $cump * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'total_purchase_value' => $data->price * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'total_selling_value' => $data->price * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'verified' => true,
                            'created_by' => $this->user->name,
                            'description' => $data->description,
                            'created_at' => \Carbon\Carbon::now()
                        );
                        $smallStore[] = $smallStoreData;

                        $smStore = array(
                            'drink_id' => $data->drink_id,
                            'quantity_bottle' => $quantityStockInitialDestination + $data->quantity_transfered,
                            'cump' => $cump,
                            //'purchase_price' => $data->price,
                            //'selling_price' => $data->price,
                            'total_cump_value' => $cump * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'total_purchase_value' => $data->price * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'total_selling_value' => $data->price * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'verified' => true,
                            'created_by' => $this->user->name,
                            'created_at' => \Carbon\Carbon::now()
                        );

                        $drink = DrinkSmallStoreDetail::where('code',$code_store_destination)->where("drink_id",$data->drink_id)->value('drink_id');


                    if ($data->quantity_transfered <= $quantityStockInitialOrigine) {

                        
                        
                        DrinkBigStoreDetail::where('code',$code_store_origin)->where('drink_id',$data->drink_id)
                        ->update($bigStore);

                        if (!empty($drink)) {
                            DrinkSmallStoreDetail::where('code',$code_store_destination)->where('drink_id',$data->drink_id)
                        ->update($smStore);
                        }else{
                            DrinkSmallStoreDetail::insert($smallStore);
                        }

                        DrinkRequisition::where('requisition_no', '=', $data->requisition_no)
                        ->update(['status' => 5]);
                        DrinkRequisitionDetail::where('requisition_no', '=', $data->requisition_no)
                        ->update(['status' => 5]);

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
                            'item_quantity'=>$data->quantity_transfered,
                            'item_measurement_unit'=>$data->drink->drinkMeasurement->purchase_unit,
                            'item_purchase_or_sale_price'=>$cump,
                            'item_purchase_or_sale_currency'=> "BIF",
                            'item_movement_type'=>"ET",
                            'item_movement_invoice_ref'=>"",
                            'item_movement_description'=>$data->description,
                            'item_movement_date'=> $data->date,

                        ]); 

                        
                    }else{

                        foreach ($datas as $data) {
                            $code_store_origin = DrinkBigStore::where('id',$data->origin_store_id)->value('code');
                            $code_store_destination = DrinkSmallStore::where('id',$data->destination_store_id)->value('code');

                            $valeurStockInitialOrigine = DrinkBigStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('total_cump_value');
                            $quantityStockInitialOrigine = DrinkBigStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('quantity_bottle');
                            $quantityTotalBigStore = $quantityStockInitialOrigine + $data->quantity_transfered;

                            $valeurStockInitialDestination = DrinkSmallStoreDetail::where('code',$code_store_destination)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('total_cump_value');
                            $quantityStockInitialDestination = DrinkSmallStoreDetail::where('code',$code_store_destination)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('quantity_bottle');
                            $quantityRestantSmallStore = $quantityStockInitialDestination - $data->quantity_transfered;

                            $cump = DrinkBigStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('cump');

                            $valeurAcquisition = $data->quantity_transfered * $data->price;

                            $valeurTotalUnite = $data->quantity_transfered + $quantityStockInitialDestination;
                            
                      
                
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
                            $returnDataSmStore = array(
                                'drink_id' => $data->drink_id,
                                'quantity_bottle' => $quantityRestantSmallStore,
                                'cump' => $cump,
                                'total_cump_value' => $cump * $quantityRestantSmallStore,
                                'total_purchase_value' => $data->price * $quantityRestantSmallStore,
                                'total_selling_value' => $data->price * $quantityRestantSmallStore,
                                'verified' => false,
                                'created_by' => $this->user->name,
                                'created_at' => \Carbon\Carbon::now()
                            );

                            $statusBigStore = DrinkBigStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('verified');
                            $statusSmallStore = DrinkSmallStoreDetail::where('code',$code_store_destination)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('verified');
                    
                            if ($statusBigStore == true) {
                        
                                DrinkBigStoreDetail::where('code',$code_store_origin)->where('drink_id',$data->drink_id)
                                 ->update($returnDataBigStore);
                                DrinkSmallStoreDetail::where('code',$code_store_destination)->where('drink_id',$data->drink_id)
                                ->update($returnDataSmStore);

                                $flag = 1;

                                DrinkRequisition::where('requisition_no', '=', $data->requisition_no)
                                ->update(['status' => 4]);
                                DrinkRequisitionDetail::where('requisition_no', '=', $data->requisition_no)
                                ->update(['status' => 4]);
                            }
                        }

                        DrinkBigStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
                        DrinkSmallStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
                        DrinkExtraBigStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
                        
                        session()->flash('error', $this->user->name.' ,Why do you want transfering a quantity you do not have in your store?please rewrite a valid quantity!');
                        return redirect()->back();
                    }
                }else{
                    $code_store_origin = DrinkExtraBigStore::where('id',$data->origin_extra_store_id)->value('code');
                $code_store_destination = DrinkBigStore::where('id',$data->destination_bg_store_id)->value('code');

                $valeurStockInitialOrigine = DrinkExtraBigStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('total_cump_value');
                $quantityStockInitialOrigine = DrinkExtraBigStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('quantity');
                $quantityRestantBigStore = $quantityStockInitialOrigine - $data->quantity_transfered;

                $valeurStockInitialDestination = DrinkBigStoreDetail::where('code',$code_store_destination)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('total_cump_value');
                $quantityStockInitialDestination = DrinkBigStoreDetail::where('code',$code_store_destination)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('quantity_bottle');
                $quantityRestantSmallStore = $quantityStockInitialDestination - $data->quantity_transfered;

                $cump = DrinkBigStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('cump');

                $valeurAcquisition = $data->quantity_transfered * $data->price;

                $valeurTotalUnite = $data->quantity_transfered + $quantityStockInitialDestination;

                $reportBigStore = array(
                    'drink_id' => $data->drink_id,
                    'quantity_stock_initial' => $quantityStockInitialOrigine,
                    'value_stock_initial' => $valeurStockInitialOrigine,
                    'code_store' => $code_store_origin,
                    'code_store_origin' => $code_store_origin,
                    'code_store_destination' => $code_store_destination,
                    'transfer_no' => $data->transfer_no,
                    'date' => $data->date,
                    'quantity_transfer' => $data->quantity_transfered,
                    'value_transfer' => $data->total_value_transfered,
                    'quantity_stock_final' => $quantityStockInitialOrigine - $data->quantity_transfered,
                    'value_stock_final' => $valeurStockInitialOrigine - $data->total_value_transfered,
                    'type_transaction' => 'SORTIE TRANSFERT',
                    'cump' => $cump,
                    'document_no' => $data->transfer_no,
                    'created_by' => $this->user->name,
                    'description' => $data->description,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportBigStoreData[] = $reportBigStore;


                $reportMediumStore = array(
                    'drink_id' => $data->drink_id,
                    'quantity_stock_initial' => $quantityStockInitialDestination,
                    'value_stock_initial' => $valeurStockInitialDestination,
                    'code_store' => $code_store_destination,
                    'code_store_origin' => $code_store_origin,
                    'code_store_destination' => $code_store_destination,
                    'transfer_no' => $data->transfer_no,
                    'date' => $data->date,
                    'quantity_transfer' => $data->quantity_transfered,
                    'value_transfer' => $data->total_value_transfered,
                    'quantity_stock_final' => $quantityStockInitialDestination + $data->quantity_transfered,
                    'value_stock_final' => $valeurStockInitialDestination + $data->total_value_transfered,
                    'type_transaction' => 'ENTREE TRANSFERT',
                    'cump' => $cump,
                    'document_no' => $data->transfer_no,
                    'created_by' => $this->user->name,
                    'description' => $data->description,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportMediumStoreData[] = $reportMediumStore;

                    $bigStore = array(
                        'drink_id' => $data->drink_id,
                        'quantity' => $quantityRestantBigStore,
                        'total_selling_value' => $quantityRestantBigStore * $data->price,
                        'total_purchase_value' => $quantityRestantBigStore * $data->price,
                        'total_cump_value' => $quantityRestantBigStore * $cump,
                        'created_by' => $this->user->name,
                        'verified' => false,
                        'created_at' => \Carbon\Carbon::now()
                    );


                    $mediumStoreData = array(
                            'drink_id' => $data->drink_id,
                            'quantity_bottle' => $data->quantity_transfered,
                            'cump' => $cump,
                            'unit' => $data->unit,
                            'code' => $code_store_destination,
                            'purchase_price' => $data->price,
                            'selling_price' => $data->price,
                            'total_cump_value' => $cump * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'total_purchase_value' => $data->price * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'total_selling_value' => $data->price * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'verified' => false,
                            'created_by' => $this->user->name,
                            'created_at' => \Carbon\Carbon::now()
                        );
                        $mediumStore[] = $mediumStoreData;

                        $mdStore = array(
                            'drink_id' => $data->drink_id,
                            'quantity_bottle' => $quantityStockInitialDestination + $data->quantity_transfered,
                            'cump' => $cump,
                            //'purchase_price' => $data->price,
                            //'selling_price' => $data->price,
                            'total_cump_value' => $cump * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'total_purchase_value' => $data->price * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'total_selling_value' => $data->price * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'verified' => false,
                            'created_by' => $this->user->name,
                            'created_at' => \Carbon\Carbon::now()
                        );

                        $drink = DrinkBigStoreDetail::where('code',$code_store_destination)->where("drink_id",$data->drink_id)->value('drink_id');


                    if ($data->quantity_transfered <= $quantityStockInitialOrigine) {

                        DrinkExtraBigReport::insert($reportBigStoreData);
                        
                        DrinkExtraBigStoreDetail::where('code',$code_store_origin)->where('drink_id',$data->drink_id)
                        ->update($bigStore);

                        if (!empty($drink)) {
                            DrinkExtraBigReport::insert($reportMediumStoreData);
                            DrinkBigStoreDetail::where('code',$code_store_destination)->where('drink_id',$data->drink_id)
                        ->update($mdStore);
                        }else{
                            DrinkExtraBigReport::insert($reportMediumStoreData);
                            DrinkBigStoreDetail::insert($mediumStore);
                        }

                        DrinkRequisition::where('requisition_no', '=', $data->requisition_no)
                        ->update(['status' => 5]);
                        DrinkRequisitionDetail::where('requisition_no', '=', $data->requisition_no)
                        ->update(['status' => 5]);
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
                            'item_quantity'=>$data->quantity_transfered,
                            'item_measurement_unit'=>$data->unit,
                            'item_purchase_or_sale_price'=>$cump,
                            'item_purchase_or_sale_currency'=> "BIF",
                            'item_movement_type'=>"ET",
                            'item_movement_invoice_ref'=>"",
                            'item_movement_description'=>$data->description,
                            'item_movement_date'=> Carbon::parse($data->updated_at)->format('Y-m-d H:i:s'),

                        ]); 
                        */
                        
                    }else{

                        DrinkBigStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
                        DrinkSmallStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
                        DrinkExtraBigStoreDetail::where('drink_id','!=','')->update(['verified' => false]);

                        session()->flash('error', $this->user->name.' ,Why do you want transfering a quantity you do not have in your store?please rewrite a valid quantity!');
                        return redirect()->back();
                    }
                }
  
        }

        if ($flag != 1) {
            DrinkBigReport::insert($reportBigStoreData);
            DrinkSmallReport::insert($reportSmallStoreData);
        }

        DrinkBigStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
        DrinkSmallStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
        DrinkExtraBigStoreDetail::where('drink_id','!=','')->update(['verified' => false]);

        DrinkTransfer::where('transfer_no', '=', $transfer_no)
             ->update(['status' => 4,'approuved_by' => $this->user->name]);
        DrinkTransferDetail::where('transfer_no', '=', $transfer_no)
            ->update(['status' => 4,'approuved_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'Transfer has been done successfuly !,from store '.$code_store_origin.' to '.$code_store_destination);
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function exportToExcel()
    {
        return Excel::download(new DrinkTransfertExport, 'transfert_boissons.xlsx');
    }

    public function rapportBoisson(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('drink_transfer.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stock !');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        $datas = DrinkTransferDetail::select(
                        DB::raw('id,drink_id,transfer_no,date,quantity_transfered,price,approuved_by,total_value_transfered'))->where('status','4')->whereBetween('date',[$start_date,$end_date])->groupBy('id','drink_id','date','transfer_no','quantity_transfered','price','approuved_by','total_value_transfered')->get();
        $total_amount = DB::table('drink_transfer_details')->where('status','4')->whereBetween('date',[$start_date,$end_date])->sum('total_value_transfered');


        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.rapport_transfert_boisson',compact('datas','dateTime','setting','end_date','start_date','total_amount'))->setPaper('a4', 'landscape');

        //Storage::put('public/journal_general/'.$d1.'_'.$d2.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download("rapport_transfert_boisson_".$dateTime.'.pdf');

        
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $transfer_no
     * @return \Illuminate\Http\Response
     */
    public function destroy($transfer_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_transfer.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any transfer !');
        }

        try {DB::beginTransaction();

        $transfer = DrinkTransfer::where('transfer_no',$transfer_no)->first();
        if (!is_null($transfer)) {
            $transfer->delete();
            DrinkTransferDetail::where('transfer_no',$transfer_no)->delete();
        }

        DB::commit();
            session()->flash('success', 'Transfer has been deleted !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

}

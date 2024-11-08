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
use App\Models\DrinkReception;
use App\Models\DrinkReceptionDetail;
use App\Models\DrinkBigStoreDetail;
use App\Models\DrinkBigStore;
use App\Models\DrinkSupplierOrderDetail;
use App\Models\DrinkSupplierOrder;
use App\Models\DrinkPurchaseDetail;
use App\Models\DrinkBigReport;
use App\Models\Supplier;
use Carbon\Carbon;
use App\Exports\DrinkReceptionExport;
use PDF;
use Validator;
use Excel;
use Mail;

class DrinkReceptionController extends Controller
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
        if (is_null($this->user) || !$this->user->can('drink_reception.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any reception !');
        }

        $receptions = DrinkReception::orderBy('id','desc')->take(200)->get();
        return view('backend.pages.drink_reception.index', compact('receptions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($order_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_reception.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any reception !');
        }

        $drinks  = Drink::orderBy('name','asc')->get();
        $destination_stores = DrinkBigStore::all();
        $suppliers = Supplier::all();
        $datas = DrinkSupplierOrderDetail::where('order_no', $order_no)->get();
        return view('backend.pages.drink_reception.create', compact('drinks','order_no','datas','destination_stores','suppliers'));
    }

    public function createWithoutOrder($purchase_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_reception.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any reception !');
        }

        $drinks  = Drink::where('store_type','!=',2)->orderBy('name','asc')->get();
        $destination_stores = DrinkBigStore::all();
        $suppliers = Supplier::all();
        $datas = DrinkPurchaseDetail::where('purchase_no', $purchase_no)->get();
        return view('backend.pages.drink_reception.create_without', compact('drinks','purchase_no','datas','destination_stores','suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('drink_reception.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any reception !');
        }

        $rules = array(
                'drink_id.*'  => 'required',
                'date'  => 'required',
                //'unit.*'  => 'required',
                'quantity_ordered.*'  => 'required',
                'purchase_price.*'  => 'required',
                //'selling_price.*'  => 'required',
                'quantity_received.*'  => 'required',
                'order_no'  => 'required',
                'invoice_no'  => 'required',
                'receptionist'  => 'required',
                'vat_supplier_payer'  => 'required',
                'invoice_currency'  => 'required',
                'destination_store_id'  => 'required',
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
            $vat_supplier_payer = $request->vat_supplier_payer;
            $origin_store_id = $request->origin_store_id;
            $invoice_currency = $request->invoice_currency;
            $handingover = $request->handingover;
            $receptionist = $request->receptionist;
            $order_no = $request->order_no;
            $invoice_no = $request->invoice_no;
            $description =$request->description; 
            $destination_store_id = $request->destination_store_id;
            //$unit = $request->unit;
            $quantity_ordered = $request->quantity_ordered;
            $purchase_price = $request->purchase_price;
            $selling_price = $request->selling_price;
            $quantity_received = $request->quantity_received;
            $supplier_id = $request->supplier_id;
            

            $latest = DrinkReception::latest()->first();
            if ($latest) {
               $reception_no = 'REC' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $reception_no = 'REC' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $reception_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$reception_no;


            for( $count = 0; $count < count($drink_id); $count++ ){
                if($vat_supplier_payer == 1){
                    $price_nvat = ($purchase_price[$count]*$quantity_received[$count]);
                    $vat = ($price_nvat* 18)/100;
                    $price_wvat = $price_nvat + $vat;
                    $total_amount_purchase = $price_wvat; 

                }else{
                    $price_nvat = ($purchase_price[$count]*$quantity_received[$count]);
                    $vat = 0;
                    $price_wvat = $price_nvat + $vat;
                    $total_amount_purchase = $price_wvat;
                }
                
                $total_amount_ordered = $quantity_ordered[$count] * $purchase_price[$count];
                $total_amount_received = $quantity_received[$count] * $purchase_price[$count];

                $data = array(
                    'drink_id' => $drink_id[$count],
                    'date' => $date,
                    'quantity_ordered' => $quantity_ordered[$count],
                    'quantity_received' => $quantity_received[$count],
                    'quantity_remaining' => $quantity_received[$count] - $quantity_ordered[$count],
                    //'unit' => $unit[$count],
                    'purchase_price' => $purchase_price[$count],
                    //'selling_price' => $selling_price[$count],
                    'total_amount_ordered' => $total_amount_ordered,
                    'total_amount_received' => $total_amount_received,
                    'total_amount_purchase' => $total_amount_purchase,
                    'order_no' => $order_no,
                    'invoice_no' => $invoice_no,
                    'invoice_currency' => $invoice_currency,
                    'vat' => $vat,
                    'price_nvat' => $price_nvat,
                    'price_wvat' => $price_wvat,
                    'supplier_id' => $supplier_id,
                    'vat_supplier_payer' => $vat_supplier_payer,
                    'receptionist' => $receptionist,
                    'handingover' => $handingover,
                    'destination_store_id' => $destination_store_id,
                    'reception_no' => $reception_no,
                    'reception_signature' => $reception_signature,
                    'created_by' => $created_by,
                    'description' => $description,
                    'status' => 1,
                    'created_at' => \Carbon\Carbon::now()

                );
                $insert_data[] = $data;

                
            }
            DrinkReceptionDetail::insert($insert_data);


            //create reception
            $reception = new DrinkReception();
            $reception->date = $date;
            $reception->reception_no = $reception_no;
            $reception->reception_signature = $reception_signature;
            $reception->order_no = $order_no;
            $reception->vat_supplier_payer = $vat_supplier_payer;
            $reception->invoice_no = $invoice_no;
            $reception->invoice_currency = $invoice_currency;
            $reception->receptionist = $receptionist;
            $reception->handingover = $handingover;
            $reception->supplier_id = $supplier_id;
            $reception->destination_store_id = $destination_store_id;
            $reception->created_by = $created_by;
            $reception->status = 1;
            $reception->description = $description;
            $reception->created_at = \Carbon\Carbon::now();
            $reception->save();

            DB::commit();
            session()->flash('success', 'reception has been created !!');
            return redirect()->route('admin.drink-receptions.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
            
        
    }

    public function storeWithoutOrder(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('drink_reception.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any reception !');
        }

        $rules = array(
                'drink_id.*'  => 'required',
                'date'  => 'required',
                //'unit.*'  => 'required',
                'quantity_ordered.*'  => 'required',
                'purchase_price.*'  => 'required',
               // 'selling_price.*'  => 'required',
                'quantity_received.*'  => 'required',
                'purchase_no'  => 'required',
                //'invoice_no'  => 'required',
                'receptionist'  => 'required',
                'vat_supplier_payer'  => 'required',
                //'invoice_currency'  => 'required',
                'destination_store_id'  => 'required',
                'description'  => 'required|min:10|max:500'
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
            $vat_supplier_payer = $request->vat_supplier_payer;
            $origin_store_id = $request->origin_store_id;
            $invoice_currency = $request->invoice_currency;
            $handingover = $request->handingover;
            $receptionist = $request->receptionist;
            $purchase_no = $request->purchase_no;
            $invoice_no = $request->invoice_no;
            $description =$request->description; 
            $destination_store_id = $request->destination_store_id;
            //$unit = $request->unit;
            $quantity_ordered = $request->quantity_ordered;
            $purchase_price = $request->purchase_price;
            $selling_price = $request->selling_price;
            $quantity_received = $request->quantity_received;
            $supplier_id = $request->supplier_id;
            

            $latest = DrinkReception::latest()->first();
            if ($latest) {
               $reception_no = 'REC' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $reception_no = 'REC' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $reception_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$reception_no;


            for( $count = 0; $count < count($drink_id); $count++ ){
                if($vat_supplier_payer == 1){
                    $price_nvat = ($purchase_price[$count]*$quantity_received[$count]);
                    $vat = ($price_nvat* 18)/100;
                    $price_wvat = $price_nvat + $vat;
                    $total_amount_purchase = $price_wvat; 

                }else{
                    $price_nvat = ($purchase_price[$count]*$quantity_received[$count]);
                    $vat = 0;
                    $price_wvat = $price_nvat + $vat;
                    $total_amount_purchase = $price_wvat;
                }
                $total_amount_ordered = $quantity_ordered[$count] * $purchase_price[$count];
                $total_amount_received = $quantity_received[$count] * $purchase_price[$count];

                $data = array(
                    'drink_id' => $drink_id[$count],
                    'date' => $date,
                    'quantity_ordered' => $quantity_ordered[$count],
                    'quantity_received' => $quantity_received[$count],
                    'quantity_remaining' => $quantity_received[$count] - $quantity_ordered[$count],
                    //'unit' => $unit[$count],
                    'purchase_price' => $purchase_price[$count],
                    //'selling_price' => $selling_price[$count],
                    'total_amount_ordered' => $total_amount_ordered,
                    'total_amount_received' => $total_amount_received,
                    'total_amount_purchase' => $total_amount_purchase,
                    'purchase_no' => $purchase_no,
                    'invoice_no' => $invoice_no,
                    'invoice_currency' => $invoice_currency,
                    'vat' => $vat,
                    'price_nvat' => $price_nvat,
                    'price_wvat' => $total_amount_purchase,
                    'supplier_id' => $supplier_id,
                    'vat_supplier_payer' => $vat_supplier_payer,
                    'receptionist' => $receptionist,
                    'handingover' => $handingover,
                    'destination_store_id' => $destination_store_id,
                    'reception_no' => $reception_no,
                    'reception_signature' => $reception_signature,
                    'created_by' => $created_by,
                    'description' => $description,
                    'status' => 1,
                    'created_at' => \Carbon\Carbon::now()

                );
                $insert_data[] = $data;

                
            }
            DrinkReceptionDetail::insert($insert_data);


            //create reception
            $reception = new DrinkReception();
            $reception->date = $date;
            $reception->reception_no = $reception_no;
            $reception->reception_signature = $reception_signature;
            $reception->purchase_no = $purchase_no;
            $reception->vat_supplier_payer = $vat_supplier_payer;
            $reception->invoice_no = $invoice_no;
            $reception->invoice_currency = $invoice_currency;
            $reception->receptionist = $receptionist;
            $reception->handingover = $handingover;
            $reception->supplier_id = $supplier_id;
            $reception->destination_store_id = $destination_store_id;
            $reception->created_by = $created_by;
            $reception->status = 1;
            $reception->description = $description;
            $reception->save();

            DB::commit();
            session()->flash('success', 'reception has been created !!');
            return redirect()->route('admin.drink-receptions.index');
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
    public function show($reception_no)
    {
        //
        $code = DrinkReceptionDetail::where('reception_no', $reception_no)->value('reception_no');
        $receptions = DrinkReceptionDetail::where('reception_no', $reception_no)->get();
        return view('backend.pages.drink_reception.show', compact('receptions','code'));
         
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($reception_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_reception.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any reception !');
        }

        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $reception_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_reception.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any reception !');
        }

        
    }

    public function fiche_reception($reception_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_reception.create')) {
            abort(403, 'Sorry !! You are Unauthorized to print handover document!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $code = DrinkReception::where('reception_no', $reception_no)->value('reception_no');
        $datas = DrinkReceptionDetail::where('reception_no', $reception_no)->get();
        $receptionniste = DrinkReception::where('reception_no', $reception_no)->value('receptionist');
        $description = DrinkReception::where('reception_no', $reception_no)->value('description');
        $supplier = DrinkReception::where('reception_no', $reception_no)->first();
        $data = DrinkReception::where('reception_no', $reception_no)->first();
        $invoice_no = DrinkReception::where('reception_no', $reception_no)->value('invoice_no');
        $invoice_currency = DrinkReception::where('reception_no', $reception_no)->value('invoice_currency');
        $reception_signature = DrinkReception::where('reception_no', $reception_no)->value('reception_signature');
        $date = DrinkReception::where('reception_no', $reception_no)->value('date');
        $totalValue = DB::table('drink_reception_details')
            ->where('reception_no', '=', $reception_no)
            ->sum('total_amount_purchase');
        $total_wvat = DB::table('drink_reception_details')
            ->where('reception_no', '=', $reception_no)
            ->sum('price_wvat');
        $pdf = PDF::loadView('backend.pages.document.fiche_reception_boisson',compact('datas','code','totalValue','receptionniste','description','supplier','data','invoice_no','setting','date','reception_signature','invoice_currency','total_wvat'));

        Storage::put('public/pdf/fiche_reception_boisson/'.$reception_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('fiche_reception_'.$reception_no.'.pdf');
        
    }

    public function validateReception($reception_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_reception.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any reception !');
        }

        try {DB::beginTransaction();

            DrinkReception::where('reception_no', '=', $reception_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            DrinkReceptionDetail::where('reception_no', '=', $reception_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

                DB::commit();
            session()->flash('success', 'reception has been validated !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function reject($reception_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_reception.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any reception !');
        }

        try {DB::beginTransaction(); 

        DrinkReception::where('reception_no', '=', $reception_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
        DrinkReceptionDetail::where('reception_no', '=', $reception_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

                DB::commit();
            session()->flash('success', 'Reception has been rejected !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function reset($reception_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_reception.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any reception !');
        }

        try {DB::beginTransaction();

        DrinkReception::where('reception_no', '=', $reception_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
        DrinkReceptionDetail::where('reception_no', '=', $reception_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

                DB::commit();
            session()->flash('success', 'Reception has been reseted !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function confirm($reception_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_reception.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any reception !');
        }

        try {DB::beginTransaction();

            DrinkReception::where('reception_no', '=', $reception_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            DrinkReceptionDetail::where('reception_no', '=', $reception_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

            DB::commit();
            session()->flash('success', 'Reception has been confirmed !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function approuve($reception_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_reception.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any reception !');
        }

        try {DB::beginTransaction();

        $datas = DrinkReceptionDetail::where('reception_no', $reception_no)->get();

        foreach($datas as $data){

                $code_store_destination = DrinkBigStore::where('id',$data->destination_store_id)->value('code');

                $valeurStockInitialDestination = DrinkBigStoreDetail::where('code',$code_store_destination)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('total_cump_value');
                $quantityStockInitialDestination = DrinkBigStoreDetail::where('code',$code_store_destination)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('quantity_bottle');
                $quantityTotalBigStore = $quantityStockInitialDestination + $data->quantity_received;


                $valeurAcquisition = $data->quantity_received * $data->purchase_price;

                $valeurTotalUnite = $data->quantity_received + $quantityStockInitialDestination;
                $cump = ($valeurStockInitialDestination + $valeurAcquisition) / $valeurTotalUnite;

                $reportBigStore = array(
                    'drink_id' => $data->drink_id,
                    'quantity_stock_initial' => $quantityStockInitialDestination,
                    'value_stock_initial' => $valeurStockInitialDestination,
                    'code_store' => $code_store_destination,
                    'reception_no' => $data->reception_no,
                    'date' => $data->date,
                    'quantity_reception' => $data->quantity_received,
                    'value_reception' => $data->total_amount_received,
                    'quantity_stock_final' => $quantityStockInitialDestination + $data->quantity_received,
                    'value_stock_final' => $valeurStockInitialDestination + $data->total_amount_received,
                    'type_transaction' => 'ACHAT',
                    'cump' => $cump,
                    'purchase_price' => $data->purchase_price,
                    'document_no' => $data->reception_no,
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

                        $drink = DrinkBigStoreDetail::where('code',$code_store_destination)->where("drink_id",$data->drink_id)->value('drink_id');

                        if (!empty($drink)) {
                            DrinkBigStoreDetail::where('code',$code_store_destination)->where('drink_id',$data->drink_id)
                        ->update($bigStore);
                        $flag = 1;
                        }else{
                            $flag = 0;
                            session()->flash('error', 'this item is not saved in the stock');
                            return back();
                        }


                        DrinkSupplierOrder::where('order_no', '=', $data->order_no)
                        ->update(['status' => 5]);
                        DrinkSupplierOrderDetail::where('order_no', '=', $data->order_no)
                        ->update(['status' => 5]);

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
                            'item_quantity'=>$data->quantity_received,
                            'item_measurement_unit'=>$data->unit,
                            'item_purchase_or_sale_price'=>$data->purchase_price,
                            'item_purchase_or_sale_currency'=> "BIF",
                            'item_movement_type'=>"EN",
                            'item_movement_invoice_ref'=>"",
                            'item_movement_description'=>$data->description,
                            'item_movement_date'=> Carbon::parse($data->updated_at)->format('Y-m-d H:i:s'),

                        ]);

                        */
  
        }

        if ($flag != 0) {
            DrinkBigReport::insert($reportBigStoreData);
        }

        DrinkReception::where('reception_no', '=', $reception_no)
            ->update(['status' => 4,'approuved_by' => $this->user->name]);
        DrinkReceptionDetail::where('reception_no', '=', $reception_no)
             ->update(['status' => 4,'approuved_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'Reception has been done successfuly !, to '.$code_store_destination);
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

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

        $datas = DrinkReceptionDetail::select(
                        DB::raw('id,drink_id,reception_no,date,quantity_received,purchase_price,supplier_id,total_amount_purchase'))->where('status','4')->whereBetween('date',[$start_date,$end_date])->groupBy('id','drink_id','date','reception_no','quantity_received','purchase_price','supplier_id','total_amount_purchase')->orderBy('id','asc')->get();
        $total_amount = DB::table('drink_reception_details')->where('status','4')->whereBetween('date',[$start_date,$end_date])->sum('total_amount_purchase');


        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.rapport_reception_boisson',compact('datas','dateTime','setting','end_date','start_date','total_amount'))->setPaper('a4', 'landscape');

        //Storage::put('public/journal_general/'.$d1.'_'.$d2.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download("rapport_reception_boisson_".$dateTime.'.pdf');

        
    }

    public function exportToExcel(Request $request)
    {
        return Excel::download(new DrinkReceptionExport, 'RAPPORT DES ACHATS DES BOISSONS.xlsx');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $reception_no
     * @return \Illuminate\Http\Response
     */
    public function destroy($reception_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_reception.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any reception !');
        }

        try {DB::beginTransaction();

        $reception = DrinkReception::where('reception_no',$reception_no)->first();
        if (!is_null($reception)) {
            $reception->delete();
            DrinkReceptionDetail::where('reception_no',$reception_no)->delete();
        }

        DB::commit();
            session()->flash('success', 'Reception has been deleted !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

}

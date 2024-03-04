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
use App\Models\Drink;
use App\Models\Food;
use App\Models\BarristTransfer;
use App\Models\BarristTransferDetail;
use App\Models\DrinkBigStoreDetail;
use App\Models\FoodBigStoreDetail;
use App\Models\DrinkBigStore;
use App\Models\FoodBigStore;
use App\Models\BarristStore;
use App\Models\BarristRequisitionDetail;
use App\Models\BarristRequisition;
use App\Models\DrinkBigReport;
use App\Models\FoodBigReport;
use App\Models\BarristBigReport;
use App\Models\BarristSmallReport;
use Carbon\Carbon;
use PDF;
use Validator;
use Excel;
use Mail;

class BarristTransferController extends Controller
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
        if (is_null($this->user) || !$this->user->can('barrist_transfer.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any transfer !');
        }

        $transfers = BarristTransfer::all();
        return view('backend.pages.barrist_transfer.index', compact('transfers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createDrink($requisition_no)
    {
        if (is_null($this->user) || !$this->user->can('barrist_transfer.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any transfer !');
        }

        $drinks  = Drink::where('store_type','!=',2)->orderBy('name','asc')->get();
        $origin_stores = DrinkBigStore::all();
        $barrist_stores = BarristStore::all();
        $datas = BarristRequisitionDetail::where('requisition_no', $requisition_no)->get();
        return view('backend.pages.barrist_transfer.create_drink', compact('drinks','requisition_no','datas','origin_stores','barrist_stores'));
    }

    public function createFood($requisition_no)
    {
        if (is_null($this->user) || !$this->user->can('barrist_transfer.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any transfer !');
        }

        $foods  = Food::where('store_type','!=',2)->orderBy('name','asc')->get();
        $origin_stores = FoodBigStore::all();
        $barrist_stores = BarristStore::all();
        $datas = BarristRequisitionDetail::where('requisition_no', $requisition_no)->get();
        return view('backend.pages.barrist_transfer.create_food', compact('foods','requisition_no','datas','origin_stores','barrist_stores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function storeDrink(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('barrist_transfer.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any transfer !');
        }

        $rules = array(
                'drink_id.*'  => 'required',
                'date'  => 'required',
                'unit.*'  => 'required',
                'quantity_requisitioned.*'  => 'required',
                'price.*'  => 'required',
                'quantity_transfered.*'  => 'required',
                'origin_dstore_id'  => 'required',
                'requisition_no'  => 'required',
                'description'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $drink_id = $request->drink_id;
            $date = $request->date;
            $origin_dstore_id = $request->origin_dstore_id;
            $requisition_no = $request->requisition_no;
            $description =$request->description; 
            $unit = $request->unit;
            $quantity_requisitioned = $request->quantity_requisitioned;
            $price = $request->price;
            $quantity_transfered = $request->quantity_transfered;
            

            $latest = BarristTransfer::latest()->first();
            if ($latest) {
               $transfer_no = 'BT' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $transfer_no = 'BT' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $transfer_signature = "4001711615".Carbon::parse(Carbon::now())->format('YmdHis')."/".$transfer_no;


            for( $count = 0; $count < count($drink_id); $count++ ){
                $total_value_requisitioned = $quantity_requisitioned[$count] * $price[$count];
                $total_value_transfered = $quantity_transfered[$count] * $price[$count];
                $data = array(
                    'drink_id' => $drink_id[$count],
                    'date' => $date,
                    'quantity_requisitioned' => $quantity_requisitioned[$count],
                    'quantity_transfered' => $quantity_transfered[$count],
                    'unit' => $unit[$count],
                    'price' => $price[$count],
                    'total_value_requisitioned' => $total_value_requisitioned,
                    'total_value_transfered' => $total_value_transfered,
                    'requisition_no' => $requisition_no,
                    'origin_dstore_id' => $origin_dstore_id,
                    'transfer_no' => $transfer_no,
                    'transfer_signature' => $transfer_signature,
                    'created_by' => $created_by,
                    'description' => $description,
                    'status' => 1,
                    'created_at' => \Carbon\Carbon::now()

                );
                $insert_data[] = $data;

                
            }
            BarristTransferDetail::insert($insert_data);


            //create transfer
            $transfer = new BarristTransfer();
            $transfer->date = $date;
            $transfer->transfer_no = $transfer_no;
            $transfer->transfer_signature = $transfer_signature;
            $transfer->requisition_no = $requisition_no;
            $transfer->origin_dstore_id = $origin_dstore_id;
            $transfer->created_by = $created_by;
            $transfer->status = 1;
            $transfer->description = $description;
            $transfer->save();
            
        session()->flash('success', 'transfer has been created !!');
        return redirect()->route('admin.barrist-transfers.index');
    }

    public function storeFood(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('barrist_transfer.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any transfer !');
        }

        $rules = array(
                'food_id.*'  => 'required',
                'date'  => 'required',
                'unit.*'  => 'required',
                'quantity_requisitioned.*'  => 'required',
                'price.*'  => 'required',
                'quantity_transfered.*'  => 'required',
                'origin_fstore_id'  => 'required',
                'requisition_no'  => 'required',
                'description'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $food_id = $request->food_id;
            $date = $request->date;
            $origin_fstore_id = $request->origin_fstore_id;
            $requisition_no = $request->requisition_no;
            $description =$request->description; 
            $unit = $request->unit;
            $quantity_requisitioned = $request->quantity_requisitioned;
            $price = $request->price;
            $quantity_transfered = $request->quantity_transfered;
            

            $latest = BarristTransfer::latest()->first();
            if ($latest) {
               $transfer_no = 'BT' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $transfer_no = 'BT' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $transfer_signature = "4001711615".Carbon::parse(Carbon::now())->format('YmdHis')."/".$transfer_no;


            for( $count = 0; $count < count($food_id); $count++ ){
                $total_value_requisitioned = $quantity_requisitioned[$count] * $price[$count];
                $total_value_transfered = $quantity_transfered[$count] * $price[$count];
                $data = array(
                    'food_id' => $food_id[$count],
                    'date' => $date,
                    'quantity_requisitioned' => $quantity_requisitioned[$count],
                    'quantity_transfered' => $quantity_transfered[$count],
                    'unit' => $unit[$count],
                    'price' => $price[$count],
                    'total_value_requisitioned' => $total_value_requisitioned,
                    'total_value_transfered' => $total_value_transfered,
                    'requisition_no' => $requisition_no,
                    'origin_fstore_id' => $origin_fstore_id,
                    'transfer_no' => $transfer_no,
                    'transfer_signature' => $transfer_signature,
                    'created_by' => $created_by,
                    'description' => $description,
                    'status' => 1,
                    'created_at' => \Carbon\Carbon::now()

                );
                $insert_data[] = $data;

                
            }
            BarristTransferDetail::insert($insert_data);


            //create transfer
            $transfer = new BarristTransfer();
            $transfer->date = $date;
            $transfer->transfer_no = $transfer_no;
            $transfer->transfer_signature = $transfer_signature;
            $transfer->requisition_no = $requisition_no;
            $transfer->origin_fstore_id = $origin_fstore_id;
            $transfer->created_by = $created_by;
            $transfer->status = 1;
            $transfer->description = $description;
            $transfer->save();
            
        session()->flash('success', 'transfer has been created !!');
        return redirect()->route('admin.barrist-transfers.index');
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
        $code = BarristTransferDetail::where('transfer_no', $transfer_no)->value('transfer_no');
        $transfers = BarristTransferDetail::where('transfer_no', $transfer_no)->get();
        return view('backend.pages.barrist_transfer.show', compact('transfers','code'));
         
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($transfer_no)
    {
        if (is_null($this->user) || !$this->user->can('barrist_transfer.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any transfer !');
        }

        $drinks  = Drink::all();
        $transfer = BarristTransfer::where('transfer_no', $transfer_no)->first();
        $datas = BarristTransferDetail::where('transfer_no', $transfer_no)->get();
        return view('backend.pages.barrist_transfer.edit', compact('drinks','datas','transfer'));
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
        if (is_null($this->user) || !$this->user->can('barrist_transfer.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any transfer !');
        }

        $rules = array(
                'drink_id.*'  => 'required',
                'date'  => 'required',
                'unit.*'  => 'required',
                'quantity.*'  => 'required',
                'unit_price.*'  => 'required',
                'total_value.*'  => 'required',
                'invoice_no'  => 'required',
                'requisition_no'  => 'required',
                'supplier'  => 'required',
                //'remaining_quantity'  => 'required',
                'receptionist'  => 'required',
                'description'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $drink_id = $request->drink_id;
            $date = $request->date;
            $invoice_no = $request->invoice_no;
            $requisition_no = $request->requisition_no;
            $description =$request->description; 
            $supplier = $request->supplier;
            $unit = $request->unit;
            $quantity = $request->quantity;
            $unit_price = $request->unit_price;
            //$remaining_quantity = $request->remaining_quantity;
            $receptionist =$request->receptionist; 
            $created_by = $this->user->name;


            for( $count = 0; $count < count($drink_id); $count++ ){
                $total_value = $quantity[$count] * $unit_price[$count];
                $order_quantity = OrderDetail::where("requisition_no",$requisition_no)->where("article_id",$drink_id[$count])->value('quantity');
                $remaining_quantity = $order_quantity - $quantity[$count];

                $status = 0;
                if ($remaining_quantity == 0) {
                    $status = 2;
                }else{
                    $status = 1;
                }
                $data = array(
                    'drink_id' => $drink_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'unit' => $unit[$count],
                    'unit_price' => $unit_price[$count],
                    'total_value' => $total_value,
                    'invoice_no' => $invoice_no,
                    'requisition_no' => $requisition_no,
                    'supplier' => $supplier,
                    'remaining_quantity' => $remaining_quantity,
                    'receptionist' => $receptionist,
                    //'reception_no' => $bon_no,
                    'created_by' => $created_by,
                    'description' => $description,
                    'status' => $status,
                    'created_at' => \Carbon\Carbon::now()

                );
                //$insert_data[] = $data;
                BarristTransferDetail::where('drink_id',$drink_id[$count])
                        ->update($data);
                
            }
            //BarristTransferDetail::insert($insert_data);

            //update transfer
            $transfer = BarristTransfer::where('reception_no', $bon_no)->first();
            $transfer->date = $date;
            $transfer->invoice_no = $invoice_no;
            $transfer->requisition_no = $requisition_no;
            $transfer->receptionist = $receptionist;
            $transfer->supplier = $supplier;
            $transfer->created_by = $created_by;
            $transfer->description = $description;
            $transfer->save();

            session()->flash('success', 'transfer has been updated !!');
        return redirect()->route('admin.receptions.index');
        
    }

    public function bonTransfert($transfer_no)
    {
        if (is_null($this->user) || !$this->user->can('barrist_transfer.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $transfer_no = BarristTransfer::where('transfer_no', $transfer_no)->value('transfer_no');
        
        $data = BarristTransfer::where('transfer_no', $transfer_no)->first();
        $description = BarristTransfer::where('transfer_no', $transfer_no)->value('description');
        $requisition_no = BarristTransfer::where('transfer_no', $transfer_no)->value('requisition_no');
        $transfer_signature = BarristTransfer::where('transfer_no', $transfer_no)->value('transfer_signature');
        $date = BarristTransfer::where('transfer_no', $transfer_no)->value('date');
        $drink_id = BarristTransferDetail::where('transfer_no', $transfer_no)->value('drink_id');
        if (!empty($drink_id)) {
            $datas = BarristTransferDetail::where('transfer_no', $transfer_no)->where('drink_id','!=','')->get();
            $totalValueTransfered = DB::table('barrist_transfer_details')
                ->where('transfer_no', '=', $transfer_no)->where('drink_id','!=','')
                ->sum('total_value_transfered');
            $totalValueRequisitioned = DB::table('barrist_transfer_details')
                ->where('transfer_no', '=', $transfer_no)->where('drink_id','!=','')
                ->sum('total_value_requisitioned');
        }else{
            $datas = BarristTransferDetail::where('transfer_no', $transfer_no)->where('food_id','!=','')->get();
            $totalValueTransfered = DB::table('barrist_transfer_details')
                ->where('transfer_no', '=', $transfer_no)->where('food_id','!=','')
                ->sum('total_value_transfered');
            $totalValueRequisitioned = DB::table('barrist_transfer_details')
                ->where('transfer_no', '=', $transfer_no)->where('food_id','!=','')
                ->sum('total_value_requisitioned');
        }
        $pdf = PDF::loadView('backend.pages.document.barrist_transfert',compact('datas','transfer_no','totalValueTransfered','totalValueRequisitioned','data','description','requisition_no','setting','date','transfer_signature'));

        Storage::put('public/pdf/barrist_transfert/'.$transfer_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('bon_transfert'.$transfer_no.'.pdf');
        
    }

    public function validateTransfer($transfer_no)
    {
       if (is_null($this->user) || !$this->user->can('barrist_transfer.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any transfer !');
        }
            BarristTransfer::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            BarristTransferDetail::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        session()->flash('success', 'transfer has been validated !!');
        return back();
    }

    public function reject($transfer_no)
    {
       if (is_null($this->user) || !$this->user->can('barrist_transfer.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any transfer !');
        }

        BarristTransfer::where('transfer_no', '=', $transfer_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
        BarristTransferDetail::where('transfer_no', '=', $transfer_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        session()->flash('success', 'Transfer has been rejected !!');
        return back();
    }

    public function reset($transfer_no)
    {
       if (is_null($this->user) || !$this->user->can('barrist_transfer.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any transfer !');
        }

        BarristTransfer::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
        BarristTransferDetail::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        session()->flash('success', 'Transfer has been reseted !!');
        return back();
    }

    public function confirm($transfer_no)
    {
       if (is_null($this->user) || !$this->user->can('barrist_transfer.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any transfer !');
        }

        BarristTransfer::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            BarristTransferDetail::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        session()->flash('success', 'Transfer has been confirmed !!');
        return back();
    }

    public function approuveDrink($transfer_no)
    {
       if (is_null($this->user) || !$this->user->can('barrist_transfer.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any transfer !');
        }


        $datas = BarristTransferDetail::where('transfer_no', $transfer_no)->get();

        foreach($datas as $data){

                $code_store_origin = DrinkBigStore::where('id',$data->origin_dstore_id)->value('code');

                $valeurStockInitialOrigine = DrinkBigStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('total_selling_value');
                $quantityStockInitialOrigine = DrinkBigStoreDetail::where('code',$code_store_origin)->where('drink_id','!=', '')->where('drink_id', $data->drink_id)->value('quantity');
                $quantityRestantBigStore = $quantityStockInitialOrigine - $data->quantity_transfered;

                $valeurStockInitialDestination = BarristStore::where('drink_id', $data->drink_id)->value('total_selling_value');
                $quantityStockInitialDestination = BarristStore::where('drink_id', $data->drink_id)->value('quantity');
                $quantityTotal = $quantityStockInitialDestination + $data->quantity_transfered;


                $valeurAcquisition = $data->quantity_transfered * $data->price;

                $valeurTotalUnite = $data->quantity_transfered + $quantityStockInitialDestination;
                $cump = ($valeurStockInitialDestination + $valeurAcquisition) / $valeurTotalUnite;

                $reportBigStore = array(
                    'drink_id' => $data->drink_id,
                    'quantity_stock_initial' => $quantityStockInitialOrigine,
                    'value_stock_initial' => $valeurStockInitialOrigine,
                    'code_store' => $code_store_origin,
                    'code_store_origin' => $code_store_origin,
                    'transfer_no' => $data->transfer_no,
                    'quantity_transfer' => $data->quantity_transfered,
                    'value_transfer' => $data->total_value_transfered,
                    'quantity_stock_final' => $quantityStockInitialOrigine - $data->quantity_transfered,
                    'value_stock_final' => $valeurStockInitialOrigine - $data->total_value_transfered,
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportBigStoreData[] = $reportBigStore;


                $reportSmallStore = array(
                    'drink_id' => $data->drink_id,
                    'quantity_stock_initial' => $quantityStockInitialDestination,
                    'value_stock_initial' => $valeurStockInitialDestination,
                    'code_store' => $code_store_origin,
                    'code_store_origin' => $code_store_origin,
                    'transfer_no' => $data->transfer_no,
                    'quantity_transfer' => $data->quantity_transfered,
                    'value_transfer' => $data->total_value_transfered,
                    'quantity_stock_final' => $quantityStockInitialDestination + $data->quantity_transfered,
                    'value_stock_final' => $valeurStockInitialDestination + $data->total_value_transfered,
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportSmallStoreData[] = $reportSmallStore;

                    $bigStore = array(
                        'drink_id' => $data->drink_id,
                        'quantity' => $quantityRestantBigStore,
                        'total_selling_value' => $quantityRestantBigStore * $data->price,
                        'total_purchase_value' => $quantityRestantBigStore * $data->price,
                        'total_cump_value' => $quantityRestantBigStore * $data->price,
                        'created_by' => $this->user->name,
                        'verified' => false,
                        'created_at' => \Carbon\Carbon::now()
                    );


                    $smallStoreData = array(
                            'drink_id' => $data->drink_id,
                            'quantity' => $data->quantity_transfered,
                            'cump' => $cump,
                            'unit' => $data->unit,
                            'purchase_price' => $data->price,
                            'selling_price' => $data->price,
                            'total_cump_value' => $cump * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'total_purchase_value' => $data->price * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'total_selling_value' => $data->price * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'verified' => false,
                            'created_by' => $this->user->name,
                            'created_at' => \Carbon\Carbon::now()
                        );
                        $smallStore[] = $smallStoreData;


                        $drink = BarristStore::where("drink_id",$data->drink_id)->value('drink_id');


                    if ($data->quantity_transfered <= $quantityStockInitialOrigine) {

                        BarristBigReport::insert($reportBigStoreData);
                        
                        BarristStore::where('drink_id',$data->drink_id)
                        ->update($bigStore);

                        if (!empty($drink)) {
                            BarristSmallReport::insert($reportSmallStoreData);
                            BarristStore::where('drink_id',$data->drink_id)
                        ->update($smallStoreData);
                        }else{
                            BarristSmallReport::insert($reportSmallStoreData);
                            BarristStore::insert($smallStore);
                        }

                        BarristRequisition::where('requisition_no', '=', $data->requisition_no)
                        ->update(['status' => 5]);
                        BarristRequisitionDetail::where('requisition_no', '=', $data->requisition_no)
                        ->update(['status' => 5]);

                        
                    }else{
                        session()->flash('error', $this->user->name.' ,Why do you want transfering a quantity you do not have in your store?please rewrite a valid quantity!');
                        return redirect()->back();
                    }
  
        }


            BarristTransfer::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);
            BarristTransferDetail::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);

        session()->flash('success', 'Transfer has been done successfuly !,from store '.$code_store_origin.' to BARRIST STORE');
        return back();
    }

    public function approuveFood($transfer_no)
    {
       if (is_null($this->user) || !$this->user->can('barrist_transfer.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any transfer !');
        }


        $datas = BarristTransferDetail::where('transfer_no', $transfer_no)->get();

        foreach($datas as $data){

                $code_store_origin = FoodBigStore::where('id',$data->origin_fstore_id)->value('code');

                $valeurStockInitialOrigine = FoodBigStoreDetail::where('code',$code_store_origin)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('total_selling_value');
                $quantityStockInitialOrigine = FoodBigStoreDetail::where('code',$code_store_origin)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('quantity');
                $quantityRestantBigStore = $quantityStockInitialOrigine - $data->quantity_transfered;

                $valeurStockInitialDestination = BarristStore::where('food_id', $data->food_id)->value('total_selling_value');
                $quantityStockInitialDestination = BarristStore::where('food_id', $data->food_id)->value('quantity');
                $quantityTotal = $quantityStockInitialDestination + $data->quantity_transfered;


                $valeurAcquisition = $data->quantity_transfered * $data->price;

                $valeurTotalUnite = $data->quantity_transfered + $quantityStockInitialDestination;
                $cump = ($valeurStockInitialDestination + $valeurAcquisition) / $valeurTotalUnite;

                $reportBigStore = array(
                    'food_id' => $data->food_id,
                    'quantity_stock_initial' => $quantityStockInitialOrigine,
                    'value_stock_initial' => $valeurStockInitialOrigine,
                    'code_store' => $code_store_origin,
                    'code_store_origin' => $code_store_origin,
                    'transfer_no' => $data->transfer_no,
                    'quantity_transfer' => $data->quantity_transfered,
                    'value_transfer' => $data->total_value_transfered,
                    'quantity_stock_final' => $quantityStockInitialOrigine - $data->quantity_transfered,
                    'value_stock_final' => $valeurStockInitialOrigine - $data->total_value_transfered,
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportBigStoreData[] = $reportBigStore;


                $reportSmallStore = array(
                    'food_id' => $data->food_id,
                    'quantity_stock_initial' => $quantityStockInitialDestination,
                    'value_stock_initial' => $valeurStockInitialDestination,
                    'code_store' => $code_store_origin,
                    'code_store_origin' => $code_store_origin,
                    'transfer_no' => $data->transfer_no,
                    'quantity_transfer' => $data->quantity_transfered,
                    'value_transfer' => $data->total_value_transfered,
                    'quantity_stock_final' => $quantityStockInitialDestination + $data->quantity_transfered,
                    'value_stock_final' => $valeurStockInitialDestination + $data->total_value_transfered,
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportSmallStoreData[] = $reportSmallStore;

                    $bigStore = array(
                        'food_id' => $data->food_id,
                        'quantity' => $quantityRestantBigStore,
                        'total_selling_value' => $quantityRestantBigStore * $data->price,
                        'total_purchase_value' => $quantityRestantBigStore * $data->price,
                        'total_cump_value' => $quantityRestantBigStore * $data->price,
                        'created_by' => $this->user->name,
                        'verified' => false,
                        'created_at' => \Carbon\Carbon::now()
                    );


                    $smallStoreData = array(
                            'food_id' => $data->food_id,
                            'quantity' => $quantityTotal,
                            'cump' => $cump,
                            'unit' => $data->unit,
                            'purchase_price' => $data->price,
                            'selling_price' => $data->price,
                            'total_cump_value' => $cump * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'total_purchase_value' => $data->price * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'total_selling_value' => $data->price * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'verified' => false,
                            'created_by' => $this->user->name,
                            'created_at' => \Carbon\Carbon::now()
                        );
                        $smallStore[] = $smallStoreData;


                        $drink = BarristStore::where("food_id",$data->food_id)->value('food_id');


                    if ($data->quantity_transfered <= $quantityStockInitialOrigine) {

                        BarristBigReport::insert($reportBigStoreData);
                        
                        FoodBigStoreDetail::where('food_id',$data->food_id)
                        ->update($bigStore);

                        if (!empty($drink)) {
                            BarristSmallReport::insert($reportSmallStoreData);
                            BarristStore::where('food_id',$data->food_id)
                        ->update($smallStoreData);
                        }else{
                            BarristSmallReport::insert($reportSmallStoreData);
                            BarristStore::insert($smallStore);
                        }

                        BarristRequisition::where('requisition_no', '=', $data->requisition_no)
                        ->update(['status' => 5]);
                        BarristRequisitionDetail::where('requisition_no', '=', $data->requisition_no)
                        ->update(['status' => 5]);

                        
                    }else{
                        session()->flash('error', $this->user->name.' ,Why do you want transfering a quantity you do not have in your store?please rewrite a valid quantity!');
                        return redirect()->back();
                    }
  
        }


            BarristTransfer::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);
            BarristTransferDetail::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);

        session()->flash('success', 'Transfer has been done successfuly !,from store '.$code_store_origin.' to BARRIST STORE');
        return back();
    }

    public function get_reception_data()
    {
        return Excel::download(new ReceptionExport, 'receptions.xlsx');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $transfer_no
     * @return \Illuminate\Http\Response
     */
    public function destroy($transfer_no)
    {
        if (is_null($this->user) || !$this->user->can('barrist_transfer.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any transfer !');
        }

        $transfer = BarristTransfer::where('transfer_no',$transfer_no)->first();
        if (!is_null($transfer)) {
            $transfer->delete();
            BarristTransferDetail::where('transfer_no',$transfer_no)->delete();
        }

        session()->flash('success', 'Transfer has been deleted !!');
        return back();
    }
}

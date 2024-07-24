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
use App\Models\Material;
use App\Models\MaterialTransfer;
use App\Models\MaterialTransferDetail;
use App\Models\MaterialBigStoreDetail;
use App\Models\MaterialSmallStoreDetail;
use App\Models\MaterialBigStore;
use App\Models\MaterialExtraBigStore;
use App\Models\MaterialExtraBigStoreDetail;
use App\Models\MaterialExtraBigReport;
use App\Models\MaterialSmallStore;
use App\Models\MaterialRequisitionDetail;
use App\Models\MaterialRequisition;
use App\Models\MaterialBigReport;
use App\Models\MaterialSmallReport;
use Carbon\Carbon;
use PDF;
use Validator;
use Excel;
use Mail;

class MaterialTransferController extends Controller
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
        if (is_null($this->user) || !$this->user->can('material_transfer.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any transfer !');
        }

        $transfers = MaterialTransfer::all();
        return view('backend.pages.material_transfer.index', compact('transfers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($requisition_no)
    {
        if (is_null($this->user) || !$this->user->can('material_transfer.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any transfer !');
        }

        $materials  = Material::orderBy('name','asc')->get();
        $origin_stores = MaterialBigStore::all();
        $destination_stores = MaterialSmallStore::all();
        $datas = MaterialRequisitionDetail::where('requisition_no', $requisition_no)->get();
        return view('backend.pages.material_transfer.create', compact('materials','requisition_no','datas','origin_stores','destination_stores'));
    }

    public function createFromBig($requisition_no)
    {
        if (is_null($this->user) || !$this->user->can('material_transfer.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any transfer !');
        }

        $materials  = Material::orderBy('name','asc')->get();
        $origin_stores = MaterialExtraBigStore::all();
        $destination_stores = MaterialBigStore::all();
        $datas = MaterialRequisitionDetail::where('requisition_no', $requisition_no)->get();
        return view('backend.pages.material_transfer.create_from_big', compact('materials','requisition_no','datas','origin_stores','destination_stores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('material_transfer.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any transfer !');
        }

        $rules = array(
                'material_id.*'  => 'required',
                'date'  => 'required',
                'unit.*'  => 'required',
                'quantity_requisitioned.*'  => 'required',
                'price.*'  => 'required',
                'quantity_transfered.*'  => 'required',
                'origin_store_id'  => 'required',
                'requisition_no'  => 'required',
                'destination_store_id'  => 'required',
                'description'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $material_id = $request->material_id;
            $date = $request->date;
            $origin_store_id = $request->origin_store_id;
            $requisition_no = $request->requisition_no;
            $type_store = $request->type_store;
            $description =$request->description; 
            $destination_store_id = $request->destination_store_id;
            $unit = $request->unit;
            $quantity_requisitioned = $request->quantity_requisitioned;
            $price = $request->price;
            $quantity_transfered = $request->quantity_transfered;
            

            $latest = MaterialTransfer::latest()->first();
            if ($latest) {
               $transfer_no = 'BT' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $transfer_no = 'BT' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $transfer_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$transfer_no;


            for( $count = 0; $count < count($material_id); $count++ ){
                $total_value_requisitioned = $quantity_requisitioned[$count] * $price[$count];
                $total_value_transfered = $quantity_transfered[$count] * $price[$count];
                $data = array(
                    'material_id' => $material_id[$count],
                    'date' => $date,
                    'quantity_requisitioned' => $quantity_requisitioned[$count],
                    'quantity_transfered' => $quantity_transfered[$count],
                    'unit' => $unit[$count],
                    'price' => $price[$count],
                    'total_value_requisitioned' => $total_value_requisitioned,
                    'total_value_transfered' => $total_value_transfered,
                    'requisition_no' => $requisition_no,
                    'type_store' => $type_store,
                    'origin_store_id' => $origin_store_id,
                    'destination_store_id' => $destination_store_id,
                    'transfer_no' => $transfer_no,
                    'transfer_signature' => $transfer_signature,
                    'created_by' => $created_by,
                    'description' => $description,
                    'status' => 1,
                    'created_at' => \Carbon\Carbon::now()

                );
                $insert_data[] = $data;

                
            }
            MaterialTransferDetail::insert($insert_data);


            //create transfer
            $transfer = new MaterialTransfer();
            $transfer->date = $date;
            $transfer->transfer_no = $transfer_no;
            $transfer->transfer_signature = $transfer_signature;
            $transfer->requisition_no = $requisition_no;
            $transfer->type_store = $type_store;
            $transfer->origin_store_id = $origin_store_id;
            $transfer->destination_store_id = $destination_store_id;
            $transfer->created_by = $created_by;
            $transfer->status = 1;
            $transfer->description = $description;
            $transfer->save();
            
        session()->flash('success', 'transfer has been created !!');
        return redirect()->route('admin.material-transfers.index');
    }


    public function storeFromBig(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('material_transfer.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any transfer !');
        }

        $rules = array(
                'material_id.*'  => 'required',
                'date'  => 'required',
                'unit.*'  => 'required',
                'quantity_requisitioned.*'  => 'required',
                'price.*'  => 'required',
                'quantity_transfered.*'  => 'required',
                'origin_extra_store_id'  => 'required',
                'requisition_no'  => 'required',
                'destination_bg_store_id'  => 'required',
                'description'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $material_id = $request->material_id;
            $date = $request->date;
            $origin_extra_store_id = $request->origin_extra_store_id;
            $requisition_no = $request->requisition_no;
            $type_store = $request->type_store;
            $description =$request->description; 
            $destination_bg_store_id = $request->destination_bg_store_id;
            $unit = $request->unit;
            $quantity_requisitioned = $request->quantity_requisitioned;
            $price = $request->price;
            $quantity_transfered = $request->quantity_transfered;
            

            $latest = MaterialTransfer::latest()->first();
            if ($latest) {
               $transfer_no = 'BT' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $transfer_no = 'BT' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $transfer_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$transfer_no;


            for( $count = 0; $count < count($material_id); $count++ ){
                $total_value_requisitioned = $quantity_requisitioned[$count] * $price[$count];
                $total_value_transfered = $quantity_transfered[$count] * $price[$count];
                $data = array(
                    'material_id' => $material_id[$count],
                    'date' => $date,
                    'quantity_requisitioned' => $quantity_requisitioned[$count],
                    'quantity_transfered' => $quantity_transfered[$count],
                    'unit' => $unit[$count],
                    'price' => $price[$count],
                    'total_value_requisitioned' => $total_value_requisitioned,
                    'total_value_transfered' => $total_value_transfered,
                    'requisition_no' => $requisition_no,
                    'type_store' => $type_store,
                    'origin_extra_store_id' => $origin_extra_store_id,
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
            MaterialTransferDetail::insert($insert_data);


            //create transfer
            $transfer = new MaterialTransfer();
            $transfer->date = $date;
            $transfer->transfer_no = $transfer_no;
            $transfer->transfer_signature = $transfer_signature;
            $transfer->requisition_no = $requisition_no;
            $transfer->type_store = $type_store;
            $transfer->origin_extra_store_id = $origin_extra_store_id;
            $transfer->destination_bg_store_id = $destination_bg_store_id;
            $transfer->created_by = $created_by;
            $transfer->status = 1;
            $transfer->description = $description;
            $transfer->save();
            
        session()->flash('success', 'transfer has been created !!');
        return redirect()->route('admin.material-transfers.index');
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
        $code = MaterialTransferDetail::where('transfer_no', $transfer_no)->value('transfer_no');
        $transfers = MaterialTransferDetail::where('transfer_no', $transfer_no)->get();
        return view('backend.pages.material_transfer.show', compact('transfers','code'));
         
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($transfer_no)
    {
        if (is_null($this->user) || !$this->user->can('material_transfer.edit')) {
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
        if (is_null($this->user) || !$this->user->can('material_transfer.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any transfer !');
        }

        
    }

    public function bonTransfert($transfer_no)
    {
        if (is_null($this->user) || !$this->user->can('material_transfer.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $transfer_no = MaterialTransfer::where('transfer_no', $transfer_no)->value('transfer_no');
        $datas = MaterialTransferDetail::where('transfer_no', $transfer_no)->get();
        $data = MaterialTransfer::where('transfer_no', $transfer_no)->first();
        $description = MaterialTransfer::where('transfer_no', $transfer_no)->value('description');
        $requisition_no = MaterialTransfer::where('transfer_no', $transfer_no)->value('requisition_no');
        $transfer_signature = MaterialTransfer::where('transfer_no', $transfer_no)->value('transfer_signature');
        $date = MaterialTransfer::where('transfer_no', $transfer_no)->value('date');
        $totalValueTransfered = DB::table('material_transfer_details')
            ->where('transfer_no', '=', $transfer_no)
            ->sum('total_value_transfered');
        $totalValueRequisitioned = DB::table('material_transfer_details')
            ->where('transfer_no', '=', $transfer_no)
            ->sum('total_value_requisitioned');
        $pdf = PDF::loadView('backend.pages.document.material_transfert',compact('datas','transfer_no','totalValueTransfered','totalValueRequisitioned','data','description','requisition_no','setting','date','transfer_signature'));

        Storage::put('public/pdf/material_transfert/'.$transfer_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('bon_transfert'.$transfer_no.'.pdf');
        
    }

    public function validateTransfer($transfer_no)
    {
       if (is_null($this->user) || !$this->user->can('material_transfer.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any transfer !');
        }
            MaterialTransfer::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            MaterialTransferDetail::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        session()->flash('success', 'transfer has been validated !!');
        return back();
    }

    public function reject($transfer_no)
    {
       if (is_null($this->user) || !$this->user->can('material_transfer.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any transfer !');
        }

        MaterialTransfer::where('transfer_no', '=', $transfer_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
        MaterialTransferDetail::where('transfer_no', '=', $transfer_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        session()->flash('success', 'Transfer has been rejected !!');
        return back();
    }

    public function reset($transfer_no)
    {
       if (is_null($this->user) || !$this->user->can('material_transfer.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any transfer !');
        }

        MaterialTransfer::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
        MaterialTransferDetail::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        session()->flash('success', 'Transfer has been reseted !!');
        return back();
    }

    public function confirm($transfer_no)
    {
       if (is_null($this->user) || !$this->user->can('material_transfer.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any transfer !');
        }

        MaterialTransfer::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            MaterialTransferDetail::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        session()->flash('success', 'Transfer has been confirmed !!');
        return back();
    }

    public function approuve($transfer_no)
    {
       if (is_null($this->user) || !$this->user->can('material_transfer.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any transfer !');
        }


        $datas = MaterialTransferDetail::where('transfer_no', $transfer_no)->get();

        foreach($datas as $data){

                if ($data->type_store == 'md') {
                    $code_store_origin = MaterialBigStore::where('id',$data->origin_store_id)->value('code');
                $code_store_destination = MaterialSmallStore::where('id',$data->destination_store_id)->value('code');

                $valeurStockInitialOrigine = MaterialBigStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('total_cump_value');
                $quantityStockInitialOrigine = MaterialBigStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('quantity');
                $quantityRestantBigStore = $quantityStockInitialOrigine - $data->quantity_transfered;

                $valeurStockInitialDestination = MaterialSmallStoreDetail::where('code',$code_store_destination)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('total_cump_value');
                $quantityStockInitialDestination = MaterialSmallStoreDetail::where('code',$code_store_destination)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('quantity');
                $quantityRestantSmallStore = $quantityStockInitialDestination - $data->quantity_transfered;


                $valeurAcquisition = $data->quantity_transfered * $data->price;

                $valeurTotalUnite = $data->quantity_transfered + $quantityStockInitialDestination;
                $cump = ($valeurStockInitialDestination + $valeurAcquisition) / $valeurTotalUnite;

                $reportBigStore = array(
                    'material_id' => $data->material_id,
                    'quantity_stock_initial' => $quantityStockInitialOrigine,
                    'value_stock_initial' => $valeurStockInitialOrigine,
                    'code_store' => $code_store_destination,
                    'code_store_origin' => $code_store_origin,
                    'code_store_destination' => $code_store_destination,
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
                    'material_id' => $data->material_id,
                    'quantity_stock_initial' => $quantityStockInitialDestination,
                    'value_stock_initial' => $valeurStockInitialDestination,
                    'code_store' => $code_store_destination,
                    'code_store_origin' => $code_store_origin,
                    'code_store_destination' => $code_store_destination,
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
                        'material_id' => $data->material_id,
                        'quantity' => $quantityRestantBigStore,
                        'total_selling_value' => $quantityRestantBigStore * $data->price,
                        'total_purchase_value' => $quantityRestantBigStore * $data->price,
                        'total_cump_value' => $quantityRestantBigStore * $data->price,
                        'created_by' => $this->user->name,
                        'verified' => false,
                        'created_at' => \Carbon\Carbon::now()
                    );


                    $smallStoreData = array(
                            'material_id' => $data->material_id,
                            'quantity' => $data->quantity_transfered,
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
                        $smallStore[] = $smallStoreData;

                        $smStore = array(
                            'material_id' => $data->material_id,
                            'quantity' => $quantityStockInitialDestination + $data->quantity_transfered,
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

                        $material = MaterialSmallStoreDetail::where('code',$code_store_destination)->where("material_id",$data->material_id)->value('material_id');


                    if ($data->quantity_transfered <= $quantityStockInitialOrigine) {

                        MaterialBigReport::insert($reportBigStoreData);
                        
                        MaterialBigStoreDetail::where('code',$code_store_origin)->where('material_id',$data->material_id)
                        ->update($bigStore);

                        if (!empty($material)) {
                            MaterialSmallReport::insert($reportSmallStoreData);
                            MaterialSmallStoreDetail::where('code',$code_store_destination)->where('material_id',$data->material_id)
                        ->update($smStore);
                        }else{
                            MaterialSmallReport::insert($reportSmallStoreData);
                            MaterialSmallStoreDetail::insert($smallStore);
                        }

                        MaterialRequisition::where('requisition_no', '=', $data->requisition_no)
                        ->update(['status' => 5]);
                        MaterialRequisitionDetail::where('requisition_no', '=', $data->requisition_no)
                        ->update(['status' => 5]);

                        
                    }else{
                        session()->flash('error', 'Why do you want transfering quantity you do not have in your store?please rewrite a valid quantity!');
                        return redirect()->back();
                    }


                }elseif ($data->type_store == 'bg') {
                    $code_store_origin = MaterialExtraBigStore::where('id',$data->origin_extra_store_id)->value('code');
                $code_store_destination = MaterialBigStore::where('id',$data->destination_bg_store_id)->value('code');

                $valeurStockInitialOrigine = MaterialExtraBigStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('total_cump_value');
                $quantityStockInitialOrigine = MaterialExtraBigStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('quantity');
                $quantityRestantBigStore = $quantityStockInitialOrigine - $data->quantity_transfered;

                $valeurStockInitialDestination = MaterialBigStoreDetail::where('code',$code_store_destination)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('total_cump_value');
                $quantityStockInitialDestination = MaterialBigStoreDetail::where('code',$code_store_destination)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('quantity');
                $quantityRestantSmallStore = $quantityStockInitialDestination - $data->quantity_transfered;


                $valeurAcquisition = $data->quantity_transfered * $data->price;

                $valeurTotalUnite = $data->quantity_transfered + $quantityStockInitialDestination;
                $cump = ($valeurStockInitialDestination + $valeurAcquisition) / $valeurTotalUnite;

                $reportBigStore = array(
                    'material_id' => $data->material_id,
                    'quantity_stock_initial' => $quantityStockInitialOrigine,
                    'value_stock_initial' => $valeurStockInitialOrigine,
                    'code_store' => $code_store_destination,
                    'code_store_origin' => $code_store_origin,
                    'code_store_destination' => $code_store_destination,
                    'transfer_no' => $data->transfer_no,
                    'quantity_transfer' => $data->quantity_transfered,
                    'value_transfer' => $data->total_value_transfered,
                    'quantity_stock_final' => $quantityStockInitialOrigine - $data->quantity_transfered,
                    'value_stock_final' => $valeurStockInitialOrigine - $data->total_value_transfered,
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportBigStoreData[] = $reportBigStore;


                $reportMediumStore = array(
                    'material_id' => $data->material_id,
                    'quantity_stock_initial' => $quantityStockInitialDestination,
                    'value_stock_initial' => $valeurStockInitialDestination,
                    'code_store' => $code_store_destination,
                    'code_store_origin' => $code_store_origin,
                    'code_store_destination' => $code_store_destination,
                    'transfer_no' => $data->transfer_no,
                    'quantity_transfer' => $data->quantity_transfered,
                    'value_transfer' => $data->total_value_transfered,
                    'quantity_stock_final' => $quantityStockInitialDestination + $data->quantity_transfered,
                    'value_stock_final' => $valeurStockInitialDestination + $data->total_value_transfered,
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportMediumStoreData[] = $reportMediumStore;

                    $bigStore = array(
                        'material_id' => $data->material_id,
                        'quantity' => $quantityRestantBigStore,
                        'total_selling_value' => $quantityRestantBigStore * $data->price,
                        'total_purchase_value' => $quantityRestantBigStore * $data->price,
                        'total_cump_value' => $quantityRestantBigStore * $data->price,
                        'created_by' => $this->user->name,
                        'verified' => false,
                        'created_at' => \Carbon\Carbon::now()
                    );


                    $mediumStoreData = array(
                            'material_id' => $data->material_id,
                            'quantity' => $quantityStockInitialDestination + $data->quantity_transfered,
                            'cump' => $cump,
                            'unit' => $data->unit,
                            'code' => $code_store_destination,
                            'purchase_price' => $data->price,
                            'total_cump_value' => $cump * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'total_purchase_value' => $data->price * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'total_selling_value' => $data->price * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'verified' => false,
                            'created_by' => $this->user->name,
                            'created_at' => \Carbon\Carbon::now()
                        );
                        $mediumStore[] = $mediumStoreData;

                        $mdStore = array(
                            'material_id' => $data->material_id,
                            'quantity' => $quantityStockInitialDestination + $data->quantity_transfered,
                            'cump' => $cump,
                            'total_cump_value' => $cump * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'total_purchase_value' => $data->price * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'total_selling_value' => $data->price * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'verified' => false,
                            'created_by' => $this->user->name,
                            'created_at' => \Carbon\Carbon::now()
                        );

                        $material = MaterialBigStoreDetail::where('code',$code_store_destination)->where("material_id",$data->material_id)->value('material_id');


                    if ($data->quantity_transfered <= $quantityStockInitialOrigine) {

                        MaterialExtraBigReport::insert($reportBigStoreData);
                        
                        MaterialExtraBigStoreDetail::where('code',$code_store_origin)->where('material_id',$data->material_id)
                        ->update($bigStore);

                        if (!empty($material)) {
                            MaterialBigReport::insert($reportMediumStoreData);
                            MaterialBigStoreDetail::where('code',$code_store_destination)->where('material_id',$data->material_id)
                        ->update($mdStore);
                        }else{
                            MaterialBigReport::insert($reportMediumStoreData);
                            MaterialBigStoreDetail::insert($mediumStore);
                        }

                        MaterialRequisition::where('requisition_no', '=', $data->requisition_no)
                        ->update(['status' => 5]);
                        MaterialRequisitionDetail::where('requisition_no', '=', $data->requisition_no)
                        ->update(['status' => 5]);

                        
                    }else{
                        session()->flash('error', 'Why do you want transfering quantity you do not have in your store?please rewrite a valid quantity!');
                        return redirect()->back();
                    }

                }
  
        }

                    MaterialTransfer::where('transfer_no', '=', $transfer_no)
                        ->update(['status' => 4,'approuved_by' => $this->user->name]);
                    MaterialTransferDetail::where('transfer_no', '=', $transfer_no)
                        ->update(['status' => 4,'approuved_by' => $this->user->name]);

                    session()->flash('success', 'Transfer has been done successfuly !,from store '.$code_store_origin.' to '.$code_store_destination);
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
        if (is_null($this->user) || !$this->user->can('material_transfer.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any transfer !');
        }

        $transfer = MaterialTransfer::where('transfer_no',$transfer_no)->first();
        if (!is_null($transfer)) {
            $transfer->delete();
            MaterialTransferDetail::where('transfer_no',$transfer_no)->delete();
        }

        session()->flash('success', 'Transfer has been deleted !!');
        return back();
    }
}

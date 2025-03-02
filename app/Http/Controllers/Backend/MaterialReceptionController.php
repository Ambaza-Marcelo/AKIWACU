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
use App\Models\MaterialReception;
use App\Models\MaterialReceptionDetail;
use App\Models\MaterialBigStoreDetail;
use App\Models\MaterialBigStore;
use App\Models\MaterialExtraBigStore;
use App\Models\MaterialExtraBigStoreDetail;
use App\Models\MaterialSupplierOrderDetail;
use App\Models\MaterialSupplierOrder;
use App\Models\MaterialPurchase;
use App\Models\MaterialPurchaseDetail;
use App\Models\MaterialBigReport;
use App\Models\MaterialExtraBigReport;
use App\Models\Supplier;
use App\Exports\MaterialReceptionExport;
use Carbon\Carbon;
use PDF;
use Validator;
use Excel;
use Mail;

class MaterialReceptionController extends Controller
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
        if (is_null($this->user) || !$this->user->can('material_reception.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any reception !');
        }

        $receptions = MaterialReception::orderBy('id','desc')->take(200)->get();
        return view('backend.pages.material_reception.index', compact('receptions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($order_no)
    {
        if (is_null($this->user) || !$this->user->can('material_reception.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any reception !');
        }

        $materials  = Material::orderBy('name','asc')->get();
        $destination_stores = materialBigStore::all();
        $suppliers = Supplier::all();
        $data = MaterialSupplierOrder::where('order_no', $order_no)->first();
        if ($data->status == -5) {
            $reception = MaterialReception::where('order_no',$order_no)->orderBy('reception_no','desc')->first();
            $datas = MaterialReceptionDetail::where('reception_no', $reception->reception_no)->get();
        }elseif ($data->status == 4) {
            $datas = MaterialSupplierOrderDetail::where('order_no', $order_no)->get();
        }else{
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        return view('backend.pages.material_reception.create', compact('materials','order_no','datas','destination_stores','suppliers','data'));
    }

    public function createWithoutRemaining($order_no)
    {
        if (is_null($this->user) || !$this->user->can('material_reception.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any reception !');
        }

        $materials  = Material::where('store_type','!=',2)->orderBy('name','asc')->get();
        $destination_stores = materialBigStore::all();
        $suppliers = Supplier::all();
        $datas = MaterialSupplierOrderDetail::where('order_no', $order_no)->get();
        return view('backend.pages.material_reception.create_without', compact('materials','order_no','datas','destination_stores','suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('material_reception.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any reception !');
        }

        $rules = array(
                'material_id.*'  => 'required',
                'date'  => 'required',
                'quantity_ordered.*'  => 'required',
                'purchase_price.*'  => 'required',
                'quantity_received.*'  => 'required',
                'order_no'  => 'required',
                'invoice_no'  => 'required',
                'receptionist'  => 'required',
                'vat_supplier_payer'  => 'required',
                'type_reception'  => 'required',
                'vat_rate'  => 'required',
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

            $material_id = $request->material_id;
            $date = Carbon::now();
            $vat_supplier_payer = $request->vat_supplier_payer;
            $origin_store_id = $request->origin_store_id;
            $invoice_currency = $request->invoice_currency;
            $handingover = $request->handingover;
            $receptionist = $request->receptionist;
            $vat_rate = $request->vat_rate;
            $type_reception = $request->type_reception;
            $order_no = $request->order_no;
            $invoice_no = $request->invoice_no;
            $description =$request->description; 
            $destination_store_id = $request->destination_store_id;
            $quantity_ordered = $request->quantity_ordered;
            $purchase_price = $request->purchase_price;
            $selling_price = $request->selling_price;
            $quantity_received = $request->quantity_received;
            $supplier_id = $request->supplier_id;


            $order = MaterialSupplierOrder::where('order_no',$order_no)->first();
            

            $latest = MaterialReception::orderBy('id','desc')->first();
            if ($latest) {
               $reception_no = 'REC' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $reception_no = 'REC' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            if (!empty($vat_rate)) {
                $vat_rate = $vat_rate;
            }else{
                $vat_rate = 0;
            }

            $created_by = $this->user->name;

            $reception_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$reception_no;


            for( $count = 0; $count < count($material_id); $count++ ){
                if($vat_supplier_payer == 1){
                    $price_nvat = ($purchase_price[$count]*$quantity_received[$count]);
                    $vat = ($price_nvat* $vat_rate)/100;
                    $price_wvat = $price_nvat + $vat;
                    $total_amount_purchase = $price_wvat; 

                }else{
                    $price_nvat = ($purchase_price[$count]*$quantity_received[$count]);
                    $vat = 0;
                    $price_wvat = $price_nvat + $vat;
                    $total_amount_purchase = $price_wvat;
                }
                
                $total_amount_ordered = $quantity_ordered[$count] * $purchase_price[$count];
                $total_amount_received = $total_amount_purchase;


                if ($order->status == -5) {
                    $total_quantity_received = DB::table('material_reception_details')
                    ->where('order_no', '=', $order_no)
                    ->sum('quantity_received');

                    $quantity_remaining = $quantity_ordered[$count] - ($total_quantity_received + $quantity_received[$count]);

                }else{
                    $quantity_remaining = $quantity_ordered[$count] - $quantity_received[$count];
                }

                if ($quantity_remaining > 0) {
                    $type_reception = 0;
                }else{
                    $type_reception = 1;
                }

                $data = array(
                    'material_id' => $material_id[$count],
                    'date' => $date,
                    'quantity_ordered' => $quantity_ordered[$count],
                    'quantity_received' => $quantity_received[$count],
                    'quantity_remaining' => $quantity_remaining,
                    'vat_rate' => $vat_rate,
                    'purchase_price' => $purchase_price[$count],
                    'type_reception' => $type_reception,
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
            MaterialReceptionDetail::insert($insert_data);


            //create reception
            $reception = new MaterialReception();
            $reception->date = $date;
            $reception->reception_no = $reception_no;
            $reception->reception_signature = $reception_signature;
            $reception->order_no = $order_no;
            $reception->vat_supplier_payer = $vat_supplier_payer;
            $reception->invoice_no = $invoice_no;
            $reception->invoice_currency = $invoice_currency;
            $reception->receptionist = $receptionist;
            $reception->vat_rate = $vat_rate;
            $reception->type_reception = $type_reception;
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
            return redirect()->route('admin.material-receptions.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
            
    }

    public function storeWithoutRemaining(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('material_reception.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any reception !');
        }

        $rules = array(
                'material_id.*'  => 'required',
                'date'  => 'required',
                'quantity_ordered.*'  => 'required',
                'purchase_price.*'  => 'required',
                'quantity_received.*'  => 'required',
                'order_no'  => 'required',
                'invoice_no'  => 'required',
                'receptionist'  => 'required',
                'vat_supplier_payer'  => 'required',
                'type_reception'  => 'required',
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

            $material_id = $request->material_id;
            $date = Carbon::now();
            $vat_supplier_payer = $request->vat_supplier_payer;
            $origin_store_id = $request->origin_store_id;
            $invoice_currency = $request->invoice_currency;
            $handingover = $request->handingover;
            $receptionist = $request->receptionist;
            $vat_rate = $request->vat_rate;
            $type_reception = $request->type_reception;
            $order_no = $request->order_no;
            $invoice_no = $request->invoice_no;
            $description =$request->description; 
            $destination_store_id = $request->destination_store_id;
            $quantity_ordered = $request->quantity_ordered;
            $purchase_price = $request->purchase_price;
            $selling_price = $request->selling_price;
            $quantity_received = $request->quantity_received;
            $supplier_id = $request->supplier_id;
            

            $latest = MaterialReception::orderBy('id','desc')->first();
            if ($latest) {
               $reception_no = 'REC' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $reception_no = 'REC' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            if (!empty($vat_rate)) {
                $vat_rate = $vat_rate;
            }else{
                $vat_rate = 0;
            }

            $created_by = $this->user->name;

            $reception_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$reception_no;


            for( $count = 0; $count < count($material_id); $count++ ){
                if($vat_supplier_payer == 1){
                    $price_nvat = ($purchase_price[$count]*$quantity_received[$count]);
                    $vat = ($price_nvat* $vat_rate)/100;
                    $price_wvat = $price_nvat + $vat;
                    $total_amount_purchase = $price_wvat; 

                }else{
                    $price_nvat = ($purchase_price[$count]*$quantity_received[$count]);
                    $vat = 0;
                    $price_wvat = $price_nvat + $vat;
                    $total_amount_purchase = $price_wvat;
                }

                $total_amount_ordered = $quantity_ordered[$count] * $purchase_price[$count];
                $total_amount_received = $total_amount_purchase;

                $data = array(
                    'material_id' => $material_id[$count],
                    'date' => $date,
                    'quantity_ordered' => $quantity_ordered[$count],
                    'quantity_received' => $quantity_received[$count],
                    'quantity_remaining' => $quantity_received[$count] - $quantity_ordered[$count],
                    'vat_rate' => $vat_rate,
                    'purchase_price' => $purchase_price[$count],
                    'type_reception' => $type_reception,
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
            MaterialReceptionDetail::insert($insert_data);


            //create reception
            $reception = new MaterialReception();
            $reception->date = $date;
            $reception->reception_no = $reception_no;
            $reception->reception_signature = $reception_signature;
            $reception->order_no = $order_no;
            $reception->vat_supplier_payer = $vat_supplier_payer;
            $reception->invoice_no = $invoice_no;
            $reception->invoice_currency = $invoice_currency;
            $reception->receptionist = $receptionist;
            $reception->vat_rate = $vat_rate;
            $reception->type_reception = $type_reception;
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
            return redirect()->route('admin.material-receptions.index');
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
        $code = MaterialReceptionDetail::where('reception_no', $reception_no)->value('reception_no');
        $receptions = MaterialReceptionDetail::where('reception_no', $reception_no)->get();
        return view('backend.pages.material_reception.show', compact('receptions','code'));
         
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($reception_no)
    {
        if (is_null($this->user) || !$this->user->can('material_reception.edit')) {
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
        if (is_null($this->user) || !$this->user->can('material_reception.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any reception !');
        }

        
    }

    public function fiche_reception($reception_no)
    {
        if (is_null($this->user) || !$this->user->can('material_reception.create')) {
            abort(403, 'Sorry !! You are Unauthorized to print handover document!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $code = MaterialReception::where('reception_no', $reception_no)->value('reception_no');
        $datas = MaterialReceptionDetail::where('reception_no', $reception_no)->get();
        $receptionniste = MaterialReception::where('reception_no', $reception_no)->value('receptionist');
        $description = MaterialReception::where('reception_no', $reception_no)->value('description');
        $supplier = MaterialReception::where('reception_no', $reception_no)->first();
        $data = MaterialReception::where('reception_no', $reception_no)->first();
        $invoice_no = MaterialReception::where('reception_no', $reception_no)->value('invoice_no');
        $invoice_currency = MaterialReception::where('reception_no', $reception_no)->value('invoice_currency');
        $reception_signature = MaterialReception::where('reception_no', $reception_no)->value('reception_signature');
        $date = MaterialReception::where('reception_no', $reception_no)->value('date');
        $totalValue = DB::table('material_reception_details')
            ->where('reception_no', '=', $reception_no)
            ->sum('total_amount_purchase');
        $total_wvat = DB::table('material_reception_details')
            ->where('reception_no', '=', $reception_no)
            ->sum('price_wvat');
        $pdf = PDF::loadView('backend.pages.document.fiche_reception_materiel',compact('datas','code','totalValue','receptionniste','description','supplier','data','invoice_no','setting','date','reception_signature','total_wvat','invoice_currency'));

        Storage::put('public/pdf/fiche_reception_materiel/'.$reception_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('fiche_reception_'.$reception_no.'.pdf');
        
    }

    public function validateReception($reception_no)
    {
       if (is_null($this->user) || !$this->user->can('material_reception.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any reception !');
        }

        try {DB::beginTransaction();

            MaterialReception::where('reception_no', '=', $reception_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            MaterialReceptionDetail::where('reception_no', '=', $reception_no)
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
       if (is_null($this->user) || !$this->user->can('material_reception.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any reception !');
        }

        try {DB::beginTransaction();

        MaterialReception::where('reception_no', '=', $reception_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
        MaterialReceptionDetail::where('reception_no', '=', $reception_no)
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
       if (is_null($this->user) || !$this->user->can('material_reception.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any reception !');
        }

        try {DB::beginTransaction();

        MaterialReception::where('reception_no', '=', $reception_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
        MaterialReceptionDetail::where('reception_no', '=', $reception_no)
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
       if (is_null($this->user) || !$this->user->can('material_reception.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any reception !');
        }

        try {DB::beginTransaction();

        MaterialReception::where('reception_no', '=', $reception_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            MaterialReceptionDetail::where('reception_no', '=', $reception_no)
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
       if (is_null($this->user) || !$this->user->can('material_reception.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any reception !');
        }


        try {DB::beginTransaction();


        $datas = MaterialReceptionDetail::where('reception_no', $reception_no)->get();

        $reception = MaterialReception::where('reception_no',$reception_no)->first();

        foreach($datas as $data){

                if (!empty($data->destination_store_id)) {
                    $code_store_destination = MaterialBigStore::where('id',$data->destination_store_id)->value('code');

                $valeurStockInitialDestination = MaterialBigStoreDetail::where('code',$code_store_destination)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('total_cump_value');
                $quantityStockInitialDestination = MaterialBigStoreDetail::where('code',$code_store_destination)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('quantity');
                $quantityTotalBigStore = $quantityStockInitialDestination + $data->quantity_received;


                $valeurAcquisition = $data->quantity_received * $data->purchase_price;

                $valeurTotalUnite = $data->quantity_received + $quantityStockInitialDestination;
                if ($valeurTotalUnite == 0) {
                    $cump = $data->purchase_price;
                }else{
                    $cump = ($valeurStockInitialDestination + $valeurAcquisition) / $valeurTotalUnite;
                }

                $reportBigStore = array(
                    'material_id' => $data->material_id,
                    'quantity_stock_initial' => $quantityStockInitialDestination,
                    'value_stock_initial' => $valeurStockInitialDestination,
                    'code_store' => $code_store_destination,
                    'reception_no' => $data->reception_no,
                    'document_no' => $data->reception_no,
                    'date' => $data->date,
                    'quantity_reception' => $data->quantity_received,
                    'value_reception' => $data->total_amount_received,
                    'quantity_stock_final' => $quantityStockInitialDestination - $data->quantity_received,
                    'value_stock_final' => $valeurStockInitialDestination - $data->total_amount_received,
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportBigStoreData[] = $reportBigStore;

                    $bigStore = array(
                        'material_id' => $data->material_id,
                        'quantity' => $quantityTotalBigStore,
                        'total_purchase_value' => $quantityTotalBigStore * $data->purchase_price,
                        'cump' => $cump,
                        'total_cump_value' => $quantityTotalBigStore * $cump,
                        'created_by' => $this->user->name,
                        'verified' => false
                    );

                    $bigStoreData[] = $bigStore;

                    $materialData = array(
                        'id' => $data->material_id,
                        'quantity' => $quantityTotalBigStore,
                        'cump' => $cump
                    );

                    Material::where('id',$data->material_id)
                        ->update($materialData);

                        $material = MaterialBigStoreDetail::where('code',$code_store_destination)->where("material_id",$data->material_id)->value('material_id');

                        if (!empty($material)) {
                            MaterialBigStoreDetail::where('code',$code_store_destination)->where('material_id',$data->material_id)
                        ->update($bigStore);
                        $flag_md = 1;
                        }else{
                            $flag_md = 0;
                            session()->flash('error', 'this item is not saved in the stock');
                            return back();
                        }


                        if ($data->purchase_no) {
                            MaterialPurchase::where('purchase_no', '=', $data->purchase_no)
                            ->update(['status' => 6]);
                            MaterialPurchaseDetail::where('purchase_no', '=', $data->purchase_no)
                            ->update(['status' => 6]);

                        }else{
                            MaterialSupplierOrder::where('order_no', '=', $data->order_no)
                            ->update(['status' => 5]);
                            MaterialSupplierOrderDetail::where('order_no', '=', $data->order_no)
                            ->update(['status' => 5]);
                        }


                }elseif(!empty($data->destination_extra_store_id)){
                    $code_store_destination = MaterialExtraBigStore::where('id',$data->destination_extra_store_id)->value('code');

                $valeurStockInitialDestination = MaterialExtraBigStoreDetail::where('code',$code_store_destination)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('total_cump_value');
                $quantityStockInitialDestination = MaterialExtraBigStoreDetail::where('code',$code_store_destination)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('quantity');
                $quantityTotalBigStore = $quantityStockInitialDestination + $data->quantity_received;


                $valeurAcquisition = $data->quantity_received * $data->purchase_price;

                $valeurTotalUnite = $data->quantity_received + $quantityStockInitialDestination;
                if ($valeurTotalUnite == 0) {
                    $cump = $data->purchase_price;
                }else{
                    $cump = ($valeurStockInitialDestination + $valeurAcquisition) / $valeurTotalUnite;
                }
                

                $reportBigStore = array(
                    'material_id' => $data->material_id,
                    'quantity_stock_initial' => $quantityStockInitialDestination,
                    'value_stock_initial' => $valeurStockInitialDestination,
                    'code_store' => $code_store_destination,
                    'reception_no' => $data->reception_no,
                    'date' => $data->date,
                    'quantity_reception' => $data->quantity_received,
                    'value_reception' => $data->total_amount_received,
                    'quantity_stock_final' => $quantityStockInitialDestination - $data->quantity_received,
                    'document_no' => $reception_no,
                    'type_transaction' => 'ACHATS',
                    'description' => $data->description,
                    'value_stock_final' => $valeurStockInitialDestination - $data->total_amount_received,
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportBigStoreData[] = $reportBigStore;

                    $bigStore = array(
                        'material_id' => $data->material_id,
                        'quantity' => $quantityTotalBigStore,
                        'total_purchase_value' => $quantityTotalBigStore * $data->purchase_price,
                        'cump' => $cump,
                        'total_cump_value' => $quantityTotalBigStore * $cump,
                        'created_by' => $this->user->name,
                        'verified' => false
                    );

                    $bigStoreData[] = $bigStore;

                    $materialData = array(
                        'id' => $data->material_id,
                        'quantity' => $quantityTotalBigStore,
                        'cump' => $cump
                    );

                    Material::where('id',$data->material_id)
                        ->update($materialData);

                        $material = MaterialExtraBigStoreDetail::where('code',$code_store_destination)->where("material_id",$data->material_id)->value('material_id');

                        if (!empty($material)) {
                            MaterialExtraBigStoreDetail::where('code',$code_store_destination)->where('material_id',$data->material_id)
                        ->update($bigStore);
                        $flag_md = 1;
                        }else{
                            $flag_md = 0;
                            session()->flash('error', 'this item is not saved in the stock');
                            return back();
                        }


                        if ($data->purchase_no) {
                            MaterialPurchase::where('purchase_no', '=', $data->purchase_no)
                            ->update(['status' => 6]);
                            MaterialPurchaseDetail::where('purchase_no', '=', $data->purchase_no)
                            ->update(['status' => 6]);

                        }else{
                            MaterialSupplierOrder::where('order_no', '=', $data->order_no)
                            ->update(['status' => 5]);
                            MaterialSupplierOrderDetail::where('order_no', '=', $data->order_no)
                            ->update(['status' => 5]);
                        }

                }else{
                    session()->flash('error', 'oops!Something wrong');
                    return back();
                }
  
        }


        if ($flag_md != 0) {
            MaterialBigReport::insert($reportBigStoreData);
        }
        
            if ($reception->type_reception == 0) {
                MaterialSupplierOrder::where('order_no', '=', $reception->order_no)
                ->update(['status' => -5]);
                MaterialSupplierOrderDetail::where('order_no', '=', $reception->order_no)
                ->update(['status' => -5]);
            }else{
                MaterialSupplierOrder::where('order_no', '=', $reception->order_no)
                ->update(['status' => 5]);
                MaterialSupplierOrderDetail::where('order_no', '=', $reception->order_no)
                ->update(['status' => 5]);
            }


            MaterialReception::where('reception_no', '=', $reception_no)
                            ->update(['status' => 4,'approuved_by' => $this->user->name]);
            MaterialReceptionDetail::where('reception_no', '=', $reception_no)
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

    public function exportToExcel(Request $request)
    {
        return Excel::download(new MaterialReceptionExport, 'RAPPORT_ACHAT_RECEPTION.xlsx');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $reception_no
     * @return \Illuminate\Http\Response
     */
    public function destroy($reception_no)
    {
        if (is_null($this->user) || !$this->user->can('material_reception.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any reception !');
        }

        try {DB::beginTransaction();

        $reception = MaterialReception::where('reception_no',$reception_no)->first();
        if (!is_null($reception)) {
            $reception->delete();
            MaterialReceptionDetail::where('reception_no',$reception_no)->delete();
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

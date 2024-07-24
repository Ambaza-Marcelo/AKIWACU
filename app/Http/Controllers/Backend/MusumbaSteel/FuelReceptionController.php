<?php

namespace App\Http\Controllers\Backend\MusumbaSteel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Storage;
use App\Models\MsFuel;
use App\Models\MsFuelReception;
use App\Models\MsFuelReceptionDetail;
use App\Models\MsFuelPump;
use App\Models\MsFuelSupplierOrderDetail;
use App\Models\MsFuelSupplierOrder;
use App\Models\MsFuelPurchase;
use App\Models\MsFuelPurchaseDetail;
use App\Models\MsFuelReport;
use App\Models\MsFuelSupplier;
use Carbon\Carbon;
use PDF;
use Validator;
use Excel;
use Mail;

class FuelReceptionController extends Controller
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_reception.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any reception !');
        }

        $receptions = MsFuelReception::all();
        return view('backend.pages.musumba_steel.fuel.reception.index', compact('receptions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($order_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_reception.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any reception !');
        }

        $fuels  = MsFuel::orderBy('name','asc')->get();
        $pumps = MsFuelPump::all();
        $suppliers = MsFuelSupplier::all();
        $data = MsFuelSupplierOrderDetail::where('order_no', $order_no)->first();
        $datas = MsFuelSupplierOrderDetail::where('order_no', $order_no)->get();
        return view('backend.pages.musumba_steel.fuel.reception.create', compact('fuels','order_no','datas','pumps','suppliers','data'));
    }

    public function createWithoutOrder($purchase_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_reception.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any reception !');
        }

        $fuels  = MsFuel::orderBy('name','asc')->get();
        $pumps = MsFuelPump::all();
        $suppliers = MsFuelSupplier::all();
        $datas = MsFuelPurchaseDetail::where('purchase_no', $purchase_no)->get();
        return view('backend.pages.musumba_steel.fuel.reception.create_without', compact('fuels','purchase_no','datas','pumps','suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_reception.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any reception !');
        }

        $rules = array(
                'fuel_id.*'  => 'required',
                'date'  => 'required',
                'quantity_ordered.*'  => 'required',
                'purchase_price.*'  => 'required',
                'quantity_received.*'  => 'required',
                'order_no'  => 'required',
                'invoice_no'  => 'required',
                'pump_id'  => 'required',
                'receptionist'  => 'required',
                'vat_supplier_payer'  => 'required',
                'invoice_currency'  => 'required',
                'description'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $fuel_id = $request->fuel_id;
            $date = $request->date;
            $vat_supplier_payer = $request->vat_supplier_payer;
            $origin_store_id = $request->origin_store_id;
            $invoice_currency = $request->invoice_currency;
            $handingover = $request->handingover;
            $receptionist = $request->receptionist;
            $order_no = $request->order_no;
            $invoice_no = $request->invoice_no;
            $description =$request->description; 
            $pump_id = $request->pump_id;
            $unit = $request->unit;
            $quantity_ordered = $request->quantity_ordered;
            $purchase_price = $request->purchase_price;
            $quantity_received = $request->quantity_received;
            $supplier_id = $request->supplier_id;
            

            $latest = MsFuelReception::latest()->first();
            if ($latest) {
               $reception_no = 'REC' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $reception_no = 'REC' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $reception_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$reception_no;


            for( $count = 0; $count < count($fuel_id); $count++ ){
                if($vat_supplier_payer == 1){
                    $price_nvat = ($purchase_price[$count]*$quantity_received[$count]);
                    $vat = ($price_nvat* 18)/100;
                    $price_wvat = $price_nvat + $vat;
                    $total_amount_purchase = $price_nvat; 

                }else{
                    $price_nvat = ($purchase_price[$count]*$quantity_received[$count]);
                    $vat = 0;
                    $price_wvat = $price_nvat + $vat;
                    $total_amount_purchase = $price_nvat;
                }
                $total_amount_ordered = $quantity_ordered[$count] * $purchase_price[$count];
                $total_amount_received = $quantity_received[$count] * $purchase_price[$count];

                $data = array(
                    'fuel_id' => $fuel_id[$count],
                    'date' => $date,
                    'quantity_ordered' => $quantity_ordered[$count],
                    'quantity_received' => $quantity_received[$count],
                    'quantity_remaining' => $quantity_received[$count] - $quantity_ordered[$count],
                    'purchase_price' => $purchase_price[$count],
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
                    'pump_id' => $pump_id,
                    'reception_no' => $reception_no,
                    'reception_signature' => $reception_signature,
                    'created_by' => $created_by,
                    'description' => $description,
                    'status' => 1,
                    'created_at' => \Carbon\Carbon::now()

                );
                $insert_data[] = $data;

                
            }
            MsFuelReceptionDetail::insert($insert_data);


            //create reception
            $reception = new MsFuelReception();
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
            $reception->pump_id = $pump_id;
            $reception->created_by = $created_by;
            $reception->status = 1;
            $reception->description = $description;
            $reception->save();
            
        session()->flash('success', 'reception has been created !!');
        return redirect()->route('admin.ms-fuel-receptions.index');
    }

    public function storeWithoutOrder(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_reception.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any reception !');
        }

        $rules = array(
                'fuel_id.*'  => 'required',
                'date'  => 'required',
                'quantity_ordered.*'  => 'required',
                'purchase_price.*'  => 'required',
                'quantity_received.*'  => 'required',
                'receptionist'  => 'required',
                'description'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $fuel_id = $request->fuel_id;
            $date = $request->date;
            $vat_supplier_payer = $request->vat_supplier_payer;
            $origin_store_id = $request->origin_store_id;
            $invoice_currency = $request->invoice_currency;
            $handingover = $request->handingover;
            $receptionist = $request->receptionist;
            $purchase_no = $request->purchase_no;
            $invoice_no = $request->invoice_no;
            $description =$request->description; 
            $pump_id = $request->pump_id;
            $quantity_ordered = $request->quantity_ordered;
            $purchase_price = $request->purchase_price;
            $selling_price = $request->selling_price;
            $quantity_received = $request->quantity_received;
            $supplier_id = $request->supplier_id;
            

            $latest = MsFuelReception::latest()->first();
            if ($latest) {
               $reception_no = 'REC' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $reception_no = 'REC' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $reception_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$reception_no;


            for( $count = 0; $count < count($fuel_id); $count++ ){
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
                    'fuel_id' => $fuel_id[$count],
                    'date' => $date,
                    'quantity_ordered' => $quantity_ordered[$count],
                    'quantity_received' => $quantity_received[$count],
                    'quantity_remaining' => $quantity_received[$count] - $quantity_ordered[$count],
                    'purchase_price' => $purchase_price[$count],
                    'total_amount_ordered' => $total_amount_ordered,
                    'total_amount_received' => $total_amount_received,
                    'total_amount_purchase' => $total_amount_purchase,
                    'purchase_no' => $purchase_no,
                    'invoice_no' => $invoice_no,
                    'invoice_currency' => $invoice_currency,
                    'vat' => $vat,
                    'price_nvat' => $price_nvat,
                    'price_wvat' => $price_wvat,
                    'supplier_id' => $supplier_id,
                    'vat_supplier_payer' => $vat_supplier_payer,
                    'receptionist' => $receptionist,
                    'handingover' => $handingover,
                    'pump_id' => $pump_id,
                    'reception_no' => $reception_no,
                    'reception_signature' => $reception_signature,
                    'created_by' => $created_by,
                    'description' => $description,
                    'status' => 1,
                    'created_at' => \Carbon\Carbon::now()

                );
                $insert_data[] = $data;

                
            }
            MsFuelReceptionDetail::insert($insert_data);


            //create reception
            $reception = new MsFuelReception();
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
            $reception->pump_id = $pump_id;
            $reception->created_by = $created_by;
            $reception->status = 1;
            $reception->description = $description;
            $reception->save();
            
        session()->flash('success', 'reception has been created !!');
        return redirect()->route('admin.ms-fuel-receptions.index');
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
        $code = MsFuelReceptionDetail::where('reception_no', $reception_no)->value('reception_no');
        $receptions = MsFuelReceptionDetail::where('reception_no', $reception_no)->get();
        return view('backend.pages.musumba_steel.fuel.reception.show', compact('receptions','code'));
         
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($reception_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_reception.edit')) {
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_reception.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any reception !');
        }

        
    }

    public function fiche_reception($reception_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_reception.create')) {
            abort(403, 'Sorry !! You are Unauthorized to print handover document!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $code = MsFuelReception::where('reception_no', $reception_no)->value('reception_no');
        $datas = MsFuelReceptionDetail::where('reception_no', $reception_no)->get();
        $receptionniste = MsFuelReception::where('reception_no', $reception_no)->value('receptionist');
        $description = MsFuelReception::where('reception_no', $reception_no)->value('description');
        $supplier = MsFuelReception::where('reception_no', $reception_no)->first();
        $data = MsFuelReception::where('reception_no', $reception_no)->first();
        $invoice_no = MsFuelReception::where('reception_no', $reception_no)->value('invoice_no');
        $invoice_currency = MsFuelReception::where('reception_no', $reception_no)->value('invoice_currency');
        $reception_signature = MsFuelReception::where('reception_no', $reception_no)->value('reception_signature');
        $date = MsFuelReception::where('reception_no', $reception_no)->value('date');
        $totalValue = DB::table('ms_fuel_reception_details')
            ->where('reception_no', '=', $reception_no)
            ->sum('total_amount_purchase');
        $total_wvat = DB::table('ms_fuel_reception_details')
            ->where('reception_no', '=', $reception_no)
            ->sum('price_wvat');
        $pdf = PDF::loadView('backend.pages.musumba_steel.document.fiche_reception',compact('datas','code','totalValue','receptionniste','description','supplier','data','invoice_no','setting','date','reception_signature','total_wvat','invoice_currency'));

        Storage::put('public/musumba_steel/fuel/fiche_reception/'.$reception_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('fiche_reception_'.$reception_no.'.pdf');
        
    }

    public function validateReception($reception_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_reception.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any reception !');
        }
            MsFuelReception::where('reception_no', '=', $reception_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            MsFuelReceptionDetail::where('reception_no', '=', $reception_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        session()->flash('success', 'reception has been validated !!');
        return back();
    }

    public function reject($reception_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_reception.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any reception !');
        }

        MsFuelReception::where('reception_no', '=', $reception_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
        MsFuelReceptionDetail::where('reception_no', '=', $reception_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        session()->flash('success', 'Reception has been rejected !!');
        return back();
    }

    public function reset($reception_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_reception.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any reception !');
        }

        MsFuelReception::where('reception_no', '=', $reception_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
        MsFuelReceptionDetail::where('reception_no', '=', $reception_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        session()->flash('success', 'Reception has been reseted !!');
        return back();
    }

    public function confirm($reception_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_reception.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any reception !');
        }

        MsFuelReception::where('reception_no', '=', $reception_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            MsFuelReceptionDetail::where('reception_no', '=', $reception_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        session()->flash('success', 'Reception has been confirmed !!');
        return back();
    }

    public function approuve($reception_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_reception.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any reception !');
        }


        $datas = MsFuelReceptionDetail::where('reception_no', $reception_no)->get();

        foreach($datas as $data){

                $valeurStockInitialDestination = MsFuelPump::where('id', $data->pump_id)->value('total_purchase_value');
                $quantityStockInitialDestination = MsFuelPump::where('id', $data->pump_id)->value('quantity');
                $quantityTotalStore = $quantityStockInitialDestination + $data->quantity_received;


                $valeurAcquisition = $data->quantity_received * $data->purchase_price;

                $valeurTotalUnite = $data->quantity_received + $quantityStockInitialDestination;
                $cump = ($valeurStockInitialDestination + $valeurAcquisition) / $valeurTotalUnite;

                $reportStore = array(
                    'fuel_id' => $data->fuel_id,
                    'pump_id' => $data->pump_id,
                    'quantity_stock_initial' => $quantityStockInitialDestination,
                    'value_stock_initial' => $valeurStockInitialDestination,
                    'reception_no' => $data->reception_no,
                    'quantity_reception' => $data->quantity_received,
                    'value_reception' => $data->total_amount_received,
                    'quantity_stock_final' => $quantityStockInitialDestination - $data->quantity_received,
                    'value_stock_final' => $valeurStockInitialDestination - $data->total_amount_received,
                    'created_by' => $this->user->name,
                    'description' => $data->description,
                    'date' => $data->date,
                    'type_transaction' => "ACHATS",
                    'document_no' => $reception_no,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportStoreData[] = $reportStore;

                    $pumpStore = array(
                        'fuel_id' => $data->fuel_id,
                        'quantity' => $quantityTotalStore,
                        'total_purchase_value' => $quantityTotalStore * $data->purchase_price,
                        'cump' => $cump,
                        'auteur' => $this->user->name,
                        'verified' => false
                    );

                    $pumpStoreData[] = $pumpStore;

                    $fuelData = array(
                        'id' => $data->fuel_id,
                        'quantity' => $quantityTotalStore,
                        'cump' => $cump
                    );

                        $fuel = MsFuelPump::where("id",$data->pump_id)->value('fuel_id');

                        if (!empty($fuel)) {
                            $flag = 1;
                            MsFuelPump::where('fuel_id',$data->fuel_id)
                        ->update($pumpStore);
                        MsFuel::where('id',$data->fuel_id)
                        ->update($fuelData);
                        }else{
                            $flag = 0;
                            session()->flash('error', 'this type of fuel is not linkded to the pump');
                            return back();
                        }


                        if ($data->purchase_no) {
                            MsFuelPurchase::where('purchase_no', '=', $data->purchase_no)
                            ->update(['status' => 6]);
                            MsFuelPurchaseDetail::where('purchase_no', '=', $data->purchase_no)
                            ->update(['status' => 6]);

                        }else{
                            MsFuelSupplierOrder::where('order_no', '=', $data->order_no)
                            ->update(['status' => 5]);
                            MsFuelSupplierOrderDetail::where('order_no', '=', $data->order_no)
                            ->update(['status' => 5]);
                        }

            }

            if ($flag != 0) {
                MsFuelReport::insert($reportStoreData);
            }

            MsFuelReception::where('reception_no', '=', $reception_no)
                            ->update(['status' => 4,'approuved_by' => $this->user->name]);
            MsFuelReceptionDetail::where('reception_no', '=', $reception_no)
                            ->update(['status' => 4,'approuved_by' => $this->user->name]);

            session()->flash('success', 'Reception has been done successfuly !');
                        return back();

    }

    public function get_reception_data()
    {
        return Excel::download(new ReceptionExport, 'receptions.xlsx');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $reception_no
     * @return \Illuminate\Http\Response
     */
    public function destroy($reception_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_reception.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any reception !');
        }

        $reception = MsFuelReception::where('reception_no',$reception_no)->first();
        if (!is_null($reception)) {
            $reception->delete();
            MsFuelReceptionDetail::where('reception_no',$reception_no)->delete();
        }

        session()->flash('success', 'Reception has been deleted !!');
        return back();
    }
}

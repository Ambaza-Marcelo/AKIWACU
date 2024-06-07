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
use App\Models\FoodReception;
use App\Models\FoodReceptionDetail;
use App\Models\FoodBigStoreDetail;
use App\Models\FoodBigStore;
use App\Models\FoodSupplierOrderDetail;
use App\Models\FoodSupplierOrder;
use App\Models\FoodPurchaseDetail;
use App\Models\FoodBigReport;
use App\Models\Supplier;
use Carbon\Carbon;
use App\Exports\FoodReceptionExport;
use PDF;
use Validator;
use Excel;
use Mail;

class FoodReceptionController extends Controller
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
        if (is_null($this->user) || !$this->user->can('food_reception.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any reception !');
        }

        $receptions = FoodReception::orderBy('id','desc')->take(200)->get();
        return view('backend.pages.food_reception.index', compact('receptions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($order_no)
    {
        if (is_null($this->user) || !$this->user->can('food_reception.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any reception !');
        }

        $foods  = Food::orderBy('name','asc')->get();
        $destination_stores = FoodBigStore::all();
        $suppliers = Supplier::all();
        $datas = FoodSupplierOrderDetail::where('order_no', $order_no)->get();
        return view('backend.pages.food_reception.create', compact('foods','order_no','datas','destination_stores','suppliers'));
    }

    public function createWithoutOrder($purchase_no)
    {
        if (is_null($this->user) || !$this->user->can('food_reception.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any reception !');
        }

        $foods  = Food::where('store_type','!=',2)->orderBy('name','asc')->get();
        $destination_stores = FoodBigStore::all();
        $suppliers = Supplier::all();
        $datas = FoodPurchaseDetail::where('purchase_no', $purchase_no)->get();
        return view('backend.pages.food_reception.create_without', compact('foods','purchase_no','datas','destination_stores','suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('food_reception.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any reception !');
        }

        $rules = array(
                'food_id.*'  => 'required',
                'date'  => 'required',
                'unit.*'  => 'required',
                'quantity_ordered.*'  => 'required',
                'purchase_price.*'  => 'required',
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

            $food_id = $request->food_id;
            $date = $request->date;
            $vat_supplier_payer = $request->vat_supplier_payer;
            $invoice_currency = $request->invoice_currency;
            $handingover = $request->handingover;
            $receptionist = $request->receptionist;
            $order_no = $request->order_no;
            $invoice_no = $request->invoice_no;
            $description =$request->description; 
            $destination_store_id = $request->destination_store_id;
            $unit = $request->unit;
            $quantity_ordered = $request->quantity_ordered;
            $purchase_price = $request->purchase_price;
            $quantity_received = $request->quantity_received;
            $supplier_id = $request->supplier_id;
            

            $latest = FoodReception::latest()->first();
            if ($latest) {
               $reception_no = 'REC' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $reception_no = 'REC' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $reception_signature = "4001711615".Carbon::parse(Carbon::now())->format('YmdHis')."/".$reception_no;


            for( $count = 0; $count < count($food_id); $count++ ){
                if($vat_supplier_payer == '1'){
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
                    'food_id' => $food_id[$count],
                    'date' => $date,
                    'quantity_ordered' => $quantity_ordered[$count],
                    'quantity_received' => $quantity_received[$count],
                    'quantity_remaining' => $quantity_ordered[$count] - $quantity_received[$count],
                    'unit' => $unit[$count],
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
            FoodReceptionDetail::insert($insert_data);


            //create reception
            $reception = new FoodReception();
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
            $reception->save();
            
        session()->flash('success', 'reception has been created !!');
        return redirect()->route('admin.food-receptions.index');
    }

    public function storeWithoutOrder(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('food_reception.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any reception !');
        }

        $rules = array(
                'food_id.*'  => 'required',
                'date'  => 'required',
                'unit.*'  => 'required',
                'quantity_ordered.*'  => 'required',
                'purchase_price.*'  => 'required',
                'quantity_received.*'  => 'required',
                //'order_no'  => 'required',
                //'invoice_no'  => 'required',
                'receptionist'  => 'required',
                'vat_supplier_payer'  => 'required',
                'invoice_currency'  => 'required',
                'destination_store_id'  => 'required',
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
            $vat_supplier_payer = $request->vat_supplier_payer;
            $invoice_currency = $request->invoice_currency;
            $handingover = $request->handingover;
            $receptionist = $request->receptionist;
            $purchase_no = $request->purchase_no;
            $invoice_no = $request->invoice_no;
            $description =$request->description; 
            $destination_store_id = $request->destination_store_id;
            $unit = $request->unit;
            $quantity_ordered = $request->quantity_ordered;
            $purchase_price = $request->purchase_price;
            $quantity_received = $request->quantity_received;
            $supplier_id = $request->supplier_id;
            

            $latest = FoodReception::latest()->first();
            if ($latest) {
               $reception_no = 'REC' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $reception_no = 'REC' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $reception_signature = "4001711615".Carbon::parse(Carbon::now())->format('YmdHis')."/".$reception_no;


            for( $count = 0; $count < count($food_id); $count++ ){
                if($vat_supplier_payer == '1'){
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
                    'food_id' => $food_id[$count],
                    'date' => $date,
                    'quantity_ordered' => $quantity_ordered[$count],
                    'quantity_received' => $quantity_received[$count],
                    'quantity_remaining' => $quantity_ordered[$count] - $quantity_received[$count],
                    'unit' => $unit[$count],
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
            FoodReceptionDetail::insert($insert_data);


            //create reception
            $reception = new FoodReception();
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
            
        session()->flash('success', 'reception has been created !!');
        return redirect()->route('admin.food-receptions.index');
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
        $code = FoodReceptionDetail::where('reception_no', $reception_no)->value('reception_no');
        $receptions = FoodReceptionDetail::where('reception_no', $reception_no)->get();
        return view('backend.pages.food_reception.show', compact('receptions','code'));
         
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($reception_no)
    {
        if (is_null($this->user) || !$this->user->can('food_reception.edit')) {
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
        if (is_null($this->user) || !$this->user->can('food_reception.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any reception !');
        }
        
    }

    public function fiche_reception($reception_no)
    {
        if (is_null($this->user) || !$this->user->can('food_reception.create')) {
            abort(403, 'Sorry !! You are Unauthorized to print handover document!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $code = FoodReception::where('reception_no', $reception_no)->value('reception_no');
        $datas = FoodReceptionDetail::where('reception_no', $reception_no)->get();
        $receptionniste = FoodReception::where('reception_no', $reception_no)->value('receptionist');
        $description = FoodReception::where('reception_no', $reception_no)->value('description');
        $supplier = FoodReception::where('reception_no', $reception_no)->first();
        $data = FoodReception::where('reception_no', $reception_no)->first();
        $invoice_no = FoodReception::where('reception_no', $reception_no)->value('invoice_no');
        $invoice_currency = FoodReception::where('reception_no', $reception_no)->value('invoice_currency');
        $reception_signature = FoodReception::where('reception_no', $reception_no)->value('reception_signature');
        $date = FoodReception::where('reception_no', $reception_no)->value('date');
        $totalValue = DB::table('food_reception_details')
            ->where('reception_no', $reception_no)
            ->sum('total_amount_received');
        $price_wvat  = DB::table('food_reception_details')
            ->where('reception_no', $reception_no)
            ->sum('price_wvat');
        $pdf = PDF::loadView('backend.pages.document.fiche_reception_nourriture',compact('datas','code','totalValue','receptionniste','description','supplier','data','invoice_no','setting','date','reception_signature','invoice_currency','price_wvat'));

        Storage::put('public/pdf/fiche_reception_nourriture/'.$reception_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('fiche_reception_'.$reception_no.'.pdf');
        
    }

    public function validateReception($reception_no)
    {
       if (is_null($this->user) || !$this->user->can('food_reception.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any reception !');
        }
            FoodReception::where('reception_no', '=', $reception_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            FoodReceptionDetail::where('reception_no', '=', $reception_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        session()->flash('success', 'reception has been validated !!');
        return back();
    }

    public function reject($reception_no)
    {
       if (is_null($this->user) || !$this->user->can('food_reception.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any reception !');
        }

        FoodReception::where('reception_no', '=', $reception_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
        FoodReceptionDetail::where('reception_no', '=', $reception_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        session()->flash('success', 'Reception has been rejected !!');
        return back();
    }

    public function reset($reception_no)
    {
       if (is_null($this->user) || !$this->user->can('food_reception.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any reception !');
        }

        FoodReception::where('reception_no', '=', $reception_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
        FoodReceptionDetail::where('reception_no', '=', $reception_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        session()->flash('success', 'Reception has been reseted !!');
        return back();
    }

    public function confirm($reception_no)
    {
       if (is_null($this->user) || !$this->user->can('food_reception.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any reception !');
        }

        FoodReception::where('reception_no', '=', $reception_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            FoodReceptionDetail::where('reception_no', '=', $reception_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        session()->flash('success', 'Reception has been confirmed !!');
        return back();
    }

    public function approuve($reception_no)
    {
       if (is_null($this->user) || !$this->user->can('food_reception.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any reception !');
        }


        $datas = FoodReceptionDetail::where('reception_no', $reception_no)->get();

        foreach($datas as $data){

                $code_store_destination = FoodBigStore::where('id',$data->destination_store_id)->value('code');

                $valeurStockInitialDestination = FoodBigStoreDetail::where('code',$code_store_destination)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('total_cump_value');
                $quantityStockInitialDestination = FoodBigStoreDetail::where('code',$code_store_destination)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('quantity');
                $quantityTotalBigStore = $quantityStockInitialDestination + $data->quantity_received;


                $valeurAcquisition = $data->quantity_received * $data->purchase_price;

                $valeurTotalUnite = $data->quantity_received + $quantityStockInitialDestination;
                $cump = ($valeurStockInitialDestination + $valeurAcquisition) / $valeurTotalUnite;

                $reportBigStore = array(
                    'food_id' => $data->food_id,
                    'quantity_stock_initial' => $quantityStockInitialDestination,
                    'value_stock_initial' => $valeurStockInitialDestination,
                    'code_store' => $code_store_destination,
                    'reception_no' => $data->reception_no,
                    'date' => $data->date,
                    'quantity_reception' => $data->quantity_received,
                    'value_reception' => $data->total_amount_received,
                    'quantity_stock_final' => $quantityStockInitialDestination - $data->quantity_received,
                    'value_stock_final' => $valeurStockInitialDestination - $data->total_amount_received,
                    'type_transaction' => 'ACHAT',
                    'document_no' => $data->reception_no,
                    'created_by' => $this->user->name,
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


                        FoodSupplierOrder::where('order_no', '=', $data->order_no)
                        ->update(['status' => 5]);
                        FoodSupplierOrderDetail::where('order_no', '=', $data->order_no)
                        ->update(['status' => 5]);
  
        }

            if ($flag != 0) {
                FoodBigReport::insert($reportBigStoreData);
            }

            FoodReception::where('reception_no', '=', $reception_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);
            FoodReceptionDetail::where('reception_no', '=', $reception_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);

        session()->flash('success', 'Reception has been done successfuly !, to '.$code_store_destination);
        return back();
    }

    public function exportToExcel(Request $request)
    {
        return Excel::download(new FoodReceptionExport, 'RAPPORT_ACHATS.xlsx');
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $reception_no
     * @return \Illuminate\Http\Response
     */
    public function destroy($reception_no)
    {
        if (is_null($this->user) || !$this->user->can('food_reception.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any reception !');
        }

        $reception = FoodReception::where('reception_no',$reception_no)->first();
        if (!is_null($reception)) {
            $reception->delete();
            FoodReceptionDetail::where('reception_no',$reception_no)->delete();
        }

        session()->flash('success', 'Reception has been deleted !!');
        return back();
    }
}

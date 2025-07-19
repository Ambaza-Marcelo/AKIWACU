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
use App\Models\FoodPurchase;
use App\Models\FoodPurchaseDetail;
use App\Models\FoodSupplierOrder;
use App\Models\FoodSupplierOrderDetail;
use App\Models\FoodReception;
use App\Models\Supplier;
use Validator;
use PDF;
use Mail;
use Carbon\Carbon;

class FoodSupplierOrderController extends Controller
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
        if (is_null($this->user) || !$this->user->can('food_supplier_order.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any order !');
        }

        $orders = FoodSupplierOrder::orderBy('id','desc')->take(200)->get();
        return view('backend.pages.food_supplier_order.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($purchase_no)
    {
        if (is_null($this->user) || !$this->user->can('food_supplier_order.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any order !');
        }

        $foods  = Food::orderBy('name','asc')->get();
        $suppliers  = Supplier::orderBy('supplier_name','asc')->get();
        $data = FoodPurchaseDetail::where('purchase_no', $purchase_no)->first();
        $datas = FoodPurchaseDetail::where('purchase_no', $purchase_no)->get();
        return view('backend.pages.food_supplier_order.create', compact('foods','purchase_no','datas','data','suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('food_supplier_order.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any order !');
        }

        $rules = array(
                'food_id.*'  => 'required',
                'date'  => 'required',
                'vat_supplier_payer'  => 'required',
                //'vat_rate'  => 'required',
                //'invoice_currency'  => 'required',
                'supplier_id'  => 'required',
                'quantity.*'  => 'required',
                'purchase_price.*'  => 'required',
                'purchase_no'  => 'required',
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
            $date = Carbon::now();
            $purchase_no = $request->purchase_no;
            $description =$request->description; 
            $vat_supplier_payer = $request->vat_supplier_payer;
            $vat_rate = $request->vat_rate;
            $invoice_currency = $request->invoice_currency;
            $supplier_id = $request->supplier_id;
            $quantity = $request->quantity;
            $purchase_price = $request->purchase_price;

            if (!empty($vat_rate)) {
                $vat_rate = $vat_rate;
            }else{
                $vat_rate = 0;
            }
            

            $latest = FoodSupplierOrder::orderBy('id','desc')->first();
            if ($latest) {
               $order_no = 'BCF' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $order_no = 'BCF' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $order_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$order_no;


            for( $count = 0; $count < count($food_id); $count++ ){

                if($vat_supplier_payer == 1){
                    $price_nvat = ($purchase_price[$count]*$quantity[$count]);
                    $vat = ($price_nvat* $vat_rate)/100;
                    $price_wvat = $price_nvat + $vat;

                }else{
                    $price_nvat = ($purchase_price[$count]*$quantity[$count]);
                    $vat = 0;
                    $price_wvat = $price_nvat + $vat;
                }

                $total_value = $price_wvat;
                $data = array(
                    'food_id' => $food_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'vat_supplier_payer' => $vat_supplier_payer,
                    'vat_rate' => $vat_rate,
                    'invoice_currency' => $invoice_currency,
                    'supplier_id' => $supplier_id,
                    'purchase_price' => $purchase_price[$count],
                    'price_nvat' => $price_nvat,
                    'vat' => $vat,
                    'price_wvat' => $price_wvat,
                    'total_value' => $total_value,
                    'purchase_no' => $purchase_no,
                    'order_no' => $order_no,
                    'order_signature' => $order_signature,
                    'created_by' => $created_by,
                    'description' => $description,
                    'status' => 1,
                    'created_at' => \Carbon\Carbon::now()

                );
                $insert_data[] = $data;

                
            }
            FoodSupplierOrderDetail::insert($insert_data);


            //create order
            $order = new FoodSupplierOrder();
            $order->date = $date;
            $order->order_no = $order_no;
            $order->order_signature = $order_signature;
            $order->purchase_no = $purchase_no;
            $order->vat_supplier_payer = $vat_supplier_payer;
            $order->vat_rate = $vat_rate;
            $order->invoice_currency = $invoice_currency;
            $order->supplier_id = $supplier_id;
            $order->created_by = $created_by;
            $order->status = 1;
            $order->description = $description;
            $order->created_at = \Carbon\Carbon::now();
            $order->save();

            $purchase_no = FoodSupplierOrder::where('order_no',$order_no)->value('purchase_no');

            FoodPurchase::where('purchase_no', '=', $purchase_no)
                ->update(['status' => -3]);
            FoodPurchaseDetail::where('purchase_no', '=', $purchase_no)
                ->update(['status' => -3]);

            DB::commit();
            session()->flash('success', 'order has been created !!');
            return redirect()->route('admin.food-supplier-orders.index');
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
    public function show($order_no)
    {
        //
        $code = FoodSupplierOrderDetail::where('order_no', $order_no)->value('order_no');
        $orders = FoodSupplierOrderDetail::where('order_no', $order_no)->get();
        return view('backend.pages.food_supplier_order.show', compact('orders','code'));
         
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($purchase_no)
    {
        if (is_null($this->user) || !$this->user->can('food_supplier_order.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any order !');
        }

        $foods  = Food::orderBy('name','asc')->get();
        $suppliers  = Supplier::orderBy('supplier_name','asc')->get();
        $data = FoodPurchaseDetail::where('purchase_no', $purchase_no)->first();
        $datas = FoodPurchaseDetail::where('purchase_no', $purchase_no)->get();
        return view('backend.pages.food_supplier_order.edit', compact('foods','purchase_no','datas','data','suppliers'));
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $purchase_no
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $purchase_no)
    {
        if (is_null($this->user) || !$this->user->can('food_supplier_order.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any order !');
        }

        $order_no = FoodSupplierOrder::where('purchase_no',$purchase_no)->value('order_no');

        $order = FoodSupplierOrder::where('order_no',$order_no)->first();

        $rules = array(
                'food_id.*'  => 'required',
                'date'  => 'required',
                'vat_supplier_payer'  => 'required',
                //'vat_rate'  => 'required',
                //'invoice_currency'  => 'required',
                'supplier_id'  => 'required',
                'quantity.*'  => 'required',
                'purchase_price.*'  => 'required',
                'purchase_no'  => 'required',
                'description'  => 'required|max:490'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            try {DB::beginTransaction();

            $reception_no = FoodReception::where('order_no',$order_no)->value('reception_no');

            if (!empty($reception_no)) {
                $status = -3;
            }else{
                $status = 3;
            }

            $food_id = $request->food_id;
            $date = Carbon::now();
            $purchase_no = $request->purchase_no;
            $description =$request->description; 
            $vat_supplier_payer = $request->vat_supplier_payer;
            $vat_rate = $request->vat_rate;
            $invoice_currency = $request->invoice_currency;
            $supplier_id = $request->supplier_id;
            $quantity = $request->quantity;
            $purchase_price = $request->purchase_price;

            if (!empty($vat_rate)) {
                $vat_rate = $vat_rate;
            }else{
                $vat_rate = 0;
            }
            

            $created_by = $this->user->name;

            for( $count = 0; $count < count($food_id); $count++ ){

                if($vat_supplier_payer == 1){
                    $price_nvat = ($purchase_price[$count]*$quantity[$count]);
                    $vat = ($price_nvat* $vat_rate)/100;
                    $price_wvat = $price_nvat + $vat;

                }else{
                    $price_nvat = ($purchase_price[$count]*$quantity[$count]);
                    $vat = 0;
                    $price_wvat = $price_nvat + $vat;
                }

                $order_signature = FoodSupplierOrder::where('order_no',$order_no)->value('order_signature');

                $total_value = $price_wvat;
                $data = array(
                    'food_id' => $food_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'vat_supplier_payer' => $vat_supplier_payer,
                    'vat_rate' => $vat_rate,
                    'invoice_currency' => $invoice_currency,
                    'supplier_id' => $supplier_id,
                    'purchase_price' => $purchase_price[$count],
                    'price_nvat' => $price_nvat,
                    'vat' => $vat,
                    'price_wvat' => $price_wvat,
                    'total_value' => $total_value,
                    'purchase_no' => $purchase_no,
                    'order_no' => $order_no,
                    'order_signature' => $order_signature,
                    'created_by' => $created_by,
                    'description' => $description,
                    'status' => $status,
                    'created_at' => \Carbon\Carbon::now()

                );
                $insert_data[] = $data;

                
            }

            FoodSupplierOrderDetail::where('order_no',$order_no)->delete();

            FoodSupplierOrderDetail::insert($insert_data);


            //edit order
            $order->date = $date;
            $order->purchase_no = $purchase_no;
            $order->vat_supplier_payer = $vat_supplier_payer;
            $order->vat_rate = $vat_rate;
            $order->invoice_currency = $invoice_currency;
            $order->supplier_id = $supplier_id;
            $order->created_by = $created_by;
            $order->status = $status;
            $order->description = $description;
            $order->created_at = \Carbon\Carbon::now();
            $order->save();

            DB::commit();
            session()->flash('success', 'order has been created !!');
            return redirect()->route('admin.food-supplier-orders.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

    public function validateOrder($order_no)
    {
       if (is_null($this->user) || !$this->user->can('food_supplier_order.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any order !');
        }

        try {DB::beginTransaction();

            FoodSupplierOrder::where('order_no', '=', $order_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            FoodSupplierOrderDetail::where('order_no', '=', $order_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

                DB::commit();
            session()->flash('success', 'order has been validated !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function reject($order_no)
    {
       if (is_null($this->user) || !$this->user->can('food_supplier_order.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any order !');
        }

        try {DB::beginTransaction();

        FoodSupplierOrder::where('order_no', '=', $order_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
            FoodSupplierOrderDetail::where('order_no', '=', $order_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

                DB::commit();
            session()->flash('success', 'Order has been rejected !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function reset($order_no)
    {
       if (is_null($this->user) || !$this->user->can('food_supplier_order.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any order !');
        }

        try {DB::beginTransaction();

        FoodSupplierOrder::where('order_no', '=', $order_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
            FoodSupplierOrderDetail::where('order_no', '=', $order_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

         DB::commit();
            session()->flash('success', 'Order has been reseted !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function confirm($order_no)
    {
       if (is_null($this->user) || !$this->user->can('food_supplier_order.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any order !');
        }

        try {DB::beginTransaction();

        FoodSupplierOrder::where('order_no', '=', $order_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            FoodSupplierOrderDetail::where('order_no', '=', $order_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'Order has been confirmed !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function approuve($order_no)
    {
       if (is_null($this->user) || !$this->user->can('food_supplier_order.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any order !');
        }

        try {DB::beginTransaction();

        FoodSupplierOrder::where('order_no', '=', $order_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);
            FoodSupplierOrderDetail::where('order_no', '=', $order_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);

        $purchase_no = FoodSupplierOrder::where('order_no',$order_no)->value('purchase_no');

        FoodPurchase::where('purchase_no', '=', $purchase_no)
                        ->update(['status' => 5]);
        FoodPurchaseDetail::where('purchase_no', '=', $purchase_no)
                        ->update(['status' => 5]);

        DB::commit();
            session()->flash('success', 'Order has been confirmed !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function foodSupplierOrder($order_no)
    {
        if (is_null($this->user) || !$this->user->can('food_supplier_order.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $stat = FoodSupplierOrder::where('order_no', $order_no)->value('status');
        $description = FoodSupplierOrder::where('order_no', $order_no)->value('description');
        $date = FoodSupplierOrder::where('order_no', $order_no)->value('date');
        $data = FoodSupplierOrder::where('order_no', $order_no)->first();
        if($stat == 2 || $stat == 3 || $stat == 4 || $stat == 5 || $stat == -3){
           $order_no = FoodSupplierOrder::where('order_no', $order_no)->value('order_no');
           $order_signature = FoodSupplierOrder::where('order_no', $order_no)->value('order_signature');
           $totalValue = DB::table('food_supplier_order_details')
            ->where('order_no', '=', $order_no)
            ->sum('total_value');
            $price_nvat = DB::table('food_supplier_order_details')
            ->where('order_no', '=', $order_no)
            ->sum('price_nvat');
            $vat = DB::table('food_supplier_order_details')
            ->where('order_no', '=', $order_no)
            ->sum('vat');

           $datas = FoodSupplierOrderDetail::where('order_no', $order_no)->get();
           $pdf = PDF::loadView('backend.pages.document.food_supplier_order',compact('datas','order_no','setting','description','date','order_signature','totalValue','data','price_nvat','vat'));

           Storage::put('public/pdf/food_supplier_order/'.'BON DE COMMANDE FOURNISSEUR '.$order_no.'.pdf', $pdf->output());

           // download pdf file
           return $pdf->download('BON DE COMMANDE FOURNISSEUR '.$order_no.'.pdf'); 
           
        }else if ($stat == -1) {
            session()->flash('error', 'Order has been rejected !!');
            return back();
        }else{
            session()->flash('error', 'wait until Order will be validated !!');
            return back();
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $order_no
     * @return \Illuminate\Http\Response
     */
    public function destroy($order_no)
    {
        if (is_null($this->user) || !$this->user->can('food_supplier_order.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any order !');
        }
        /*
        try {DB::beginTransaction();

        $order = FoodSupplierOrder::where('order_no',$order_no)->first();
        if (!is_null($order)) {
            $order->delete();
            FoodSupplierOrderDetail::where('order_no',$order_no)->delete();
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
        */
    }
}

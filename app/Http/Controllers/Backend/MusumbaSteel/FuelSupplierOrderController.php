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
use App\Models\MsFuelPurchase;
use App\Models\MsFuelPurchaseDetail;
use App\Models\MsFuelSupplierOrder;
use App\Models\MsFuelSupplierOrderDetail;
use App\Models\MsFuelSupplier;
use Validator;
use PDF;
use Mail;
use Carbon\Carbon;

class FuelSupplierOrderController extends Controller
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_supplier_order.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any order !');
        }

        $orders = MsFuelSupplierOrder::all();
        return view('backend.pages.musumba_steel.fuel.supplier_order.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($purchase_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_supplier_order.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any order !');
        }

        $fuels  = MsFuel::orderBy('name','asc')->get();
        $suppliers  = MsFuelSupplier::orderBy('name','asc')->get();
        $datas = MsFuelPurchaseDetail::where('purchase_no', $purchase_no)->get();
        return view('backend.pages.musumba_steel.fuel.supplier_order.create', compact('fuels','purchase_no','datas','suppliers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_supplier_order.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any order !');
        }

        $rules = array(
                'fuel_id.*'  => 'required',
                'date'  => 'required',
                'quantity.*'  => 'required',
                'purchase_price.*'  => 'required',
                'purchase_no'  => 'required',
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
            $purchase_no = $request->purchase_no;
            $description =$request->description; 
            $supplier_id =$request->supplier_id; 
            $quantity = $request->quantity;
            $purchase_price = $request->purchase_price;
            

            $latest = MsFuelSupplierOrder::latest()->first();
            if ($latest) {
               $order_no = 'BCF' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $order_no = 'BCF' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $order_signature = "4001711615".Carbon::parse(Carbon::now())->format('YmdHis')."/".$order_no;


            for( $count = 0; $count < count($fuel_id); $count++ ){
                $total_value = $quantity[$count] * $purchase_price[$count];
                $data = array(
                    'fuel_id' => $fuel_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'quantity' => $quantity[$count],
                    'purchase_price' => $purchase_price[$count],
                    'total_value' => $total_value,
                    'purchase_no' => $purchase_no,
                    'order_no' => $order_no,
                    'supplier_id' => $supplier_id,
                    'order_signature' => $order_signature,
                    'created_by' => $created_by,
                    'description' => $description,
                    'status' => 1,
                    'created_at' => \Carbon\Carbon::now()

                );
                $insert_data[] = $data;

                
            }
            MsFuelSupplierOrderDetail::insert($insert_data);


            //create order
            $order = new MsFuelSupplierOrder();
            $order->date = $date;
            $order->order_no = $order_no;
            $order->order_signature = $order_signature;
            $order->purchase_no = $purchase_no;
            $order->supplier_id = $supplier_id;
            $order->created_by = $created_by;
            $order->status = 1;
            $order->description = $description;
            $order->save();
            
        session()->flash('success', 'order has been created !!');
        return redirect()->route('admin.ms-fuel-supplier-orders.index');
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
        $code = MsFuelSupplierOrderDetail::where('order_no', $order_no)->value('order_no');
        $orders = MsFuelSupplierOrderDetail::where('order_no', $order_no)->get();
        return view('backend.pages.musumba_steel.fuel.supplier_order.show', compact('orders','code'));
         
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($order_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_supplier_order.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any order !');
        }

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
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_supplier_order.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any order !');
        }

        
    }

    public function validateOrder($order_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_supplier_order.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any order !');
        }
            MsFuelSupplierOrder::where('order_no', '=', $order_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            MsFuelSupplierOrderDetail::where('order_no', '=', $order_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        session()->flash('success', 'order has been validated !!');
        return back();
    }

    public function reject($order_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_supplier_order.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any order !');
        }

        MsFuelSupplierOrder::where('order_no', '=', $order_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
            MsFuelSupplierOrderDetail::where('order_no', '=', $order_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        session()->flash('success', 'Order has been rejected !!');
        return back();
    }

    public function reset($order_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_supplier_order.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any order !');
        }

        MsFuelSupplierOrder::where('order_no', '=', $order_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
            MsFuelSupplierOrderDetail::where('order_no', '=', $order_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        session()->flash('success', 'Order has been reseted !!');
        return back();
    }

    public function confirm($order_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_supplier_order.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any order !');
        }

        MsFuelSupplierOrder::where('order_no', '=', $order_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            MsFuelSupplierOrderDetail::where('order_no', '=', $order_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        session()->flash('success', 'Order has been confirmed !!');
        return back();
    }

    public function approuve($order_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_supplier_order.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any order !');
        }

        MsFuelSupplierOrder::where('order_no', '=', $order_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);
            MsFuelSupplierOrderDetail::where('order_no', '=', $order_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);

        $purchase_no = MsFuelSupplierOrder::where('order_no',$order_no)->value('purchase_no');

        MsFuelPurchase::where('purchase_no', '=', $purchase_no)
                        ->update(['status' => 5]);
        MsFuelPurchaseDetail::where('purchase_no', '=', $purchase_no)
                        ->update(['status' => 5]);

        session()->flash('success', 'Order has been confirmed !!');
        return back();
    }

    public function fuelSupplierOrder($order_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_supplier_order.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $stat = MsFuelSupplierOrder::where('order_no', $order_no)->value('status');
        $description = MsFuelSupplierOrder::where('order_no', $order_no)->value('description');
        $date = MsFuelSupplierOrder::where('order_no', $order_no)->value('date');
        $data = MsFuelSupplierOrder::where('order_no', $order_no)->first();
        if($stat == 2 && $stat == 3 || $stat == 4 || $stat == 5){
           $order_no = MsFuelSupplierOrder::where('order_no', $order_no)->value('order_no');
           $order_signature = MsFuelSupplierOrder::where('order_no', $order_no)->value('order_signature');
           $totalValue = DB::table('ms_fuel_supplier_order_details')
            ->where('order_no', '=', $order_no)
            ->sum('total_value');

           $datas = MsFuelSupplierOrderDetail::where('order_no', $order_no)->get();
           $pdf = PDF::loadView('backend.pages.musumba_steel.document.fuel_supplier_order',compact('datas','order_no','setting','description','date','order_signature','totalValue','data'));

           Storage::put('public/musumba_steel/fuel/supplier_order/'.'FICHE_COMMANDE_'.$order_no.'.pdf', $pdf->output());

           // download pdf file
           return $pdf->download('FICHE_COMMANDE_'.$order_no.'.pdf'); 
           
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_supplier_order.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any order !');
        }

        $order = MsFuelSupplierOrder::where('order_no',$order_no)->first();
        if (!is_null($order)) {
            $order->delete();           
            MsFuelSupplierOrderDetail::where('order_no',$order_no)->delete();
        }

        session()->flash('success', 'Order has been deleted !!');
        return back();
    }
}

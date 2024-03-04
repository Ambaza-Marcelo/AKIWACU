<?php

namespace App\Http\Controllers\Backend\Sotb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Storage;
use App\Models\SotbFuel;
use App\Models\SotbFuelPurchase;
use App\Models\SotbFuelPurchaseDetail;
use Validator;
use PDF;
use Mail;
use Carbon\Carbon;

class FuelPurchaseController extends Controller
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
        if (is_null($this->user) || !$this->user->can('sotb_fuel_purchase.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any purchase !');
        }

        $purchases = SotbFuelPurchase::all();
        return view('backend.pages.sotb.fuel.purchase.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('sotb_fuel_purchase.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any purchase !');
        }

        $fuels  = SotbFuel::orderBy('name','asc')->get();
        return view('backend.pages.sotb.fuel.purchase.create', compact('fuels'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('sotb_fuel_purchase.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any purchase !');
        }

        $rules = array(
                'fuel_id.*'  => 'required',
                'date'  => 'required',
                'quantity.*'  => 'required',
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
            $quantity = $request->quantity;
            $description =$request->description; 
            $latest = SotbFuelPurchase::latest()->first();
            if ($latest) {
               $purchase_no = 'BDA' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $purchase_no = 'BDA' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $purchase_signature = "4001711615".Carbon::parse(Carbon::now())->format('YmdHis')."/".$purchase_no;
            $created_by = $this->user->name;

            //create purchase
            $purchase = new SotbFuelPurchase();
            $purchase->date = $date;
            $purchase->purchase_signature = $purchase_signature;
            $purchase->purchase_no = $purchase_no;
            $purchase->created_by = $created_by;
            $purchase->description = $description;
            $purchase->save();
            //insert details of purchase No.
            for( $count = 0; $count < count($fuel_id); $count++ ){

                $price = SotbFuel::where('id', $fuel_id[$count])->value('purchase_price');
                $total_value = $quantity[$count] * $price;
                $data = array(
                    'fuel_id' => $fuel_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'price' => $price,
                    'description' => $description,
                    'total_value' => $total_value,
                    'created_by' => $created_by,
                    'purchase_no' => $purchase_no,
                    'purchase_signature' => $purchase_signature,
                );
                $insert_data[] = $data;
            }

            SotbFuelPurchaseDetail::insert($insert_data);

        session()->flash('success', 'Purchase Request has been created !!');
        return redirect()->route('admin.sotb-fuel-purchases.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($purchase_no)
    {
        //
         $code = SotbFuelPurchase::where('purchase_no', $purchase_no)->value('purchase_no');
         $purchases = SotbFuelPurchaseDetail::where('purchase_no', $purchase_no)->get();
         return view('backend.pages.sotb.fuel.purchase.show', compact('purchases','code'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $purchase_no
     * @return \Illuminate\Http\Response
     */
    public function edit($purchase_no)
    {
        if (is_null($this->user) || !$this->user->can('sotb_fuel_purchase.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any purchase !');
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
        if (is_null($this->user) || !$this->user->can('sotb_fuel_purchase.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any purchase !');
        }

        
    }

    public function validatePurchase($purchase_no)
    {
       if (is_null($this->user) || !$this->user->can('sotb_fuel_purchase.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any purchase !');
        }
            SotbFuelPurchase::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            SotbFuelPurchaseDetail::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        session()->flash('success', 'purchase has been validated !!');
        return back();
    }

    public function reject($purchase_no)
    {
       if (is_null($this->user) || !$this->user->can('sotb_fuel_purchase.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any purchase !');
        }

        SotbFuelPurchase::where('purchase_no', '=', $purchase_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
            SotbFuelPurchaseDetail::where('purchase_no', '=', $purchase_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        session()->flash('success', 'Purchase Request has been rejected !!');
        return back();
    }

    public function reset($purchase_no)
    {
       if (is_null($this->user) || !$this->user->can('sotb_fuel_purchase.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any purchase !');
        }

        SotbFuelPurchase::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
            SotbFuelPurchaseDetail::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        session()->flash('success', 'MaterialPurchase has been reseted !!');
        return back();
    }

    public function confirm($purchase_no)
    {
       if (is_null($this->user) || !$this->user->can('sotb_fuel_purchase.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any purchase !');
        }

        SotbFuelPurchase::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            SotbFuelPurchaseDetail::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        session()->flash('success', 'Purchase Request has been confirmed !!');
        return back();
    }

    public function approuve($purchase_no)
    {
       if (is_null($this->user) || !$this->user->can('sotb_fuel_purchase.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any purchase !');
        }

        SotbFuelPurchase::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);
            SotbFuelPurchaseDetail::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);

        session()->flash('success', 'Purchase Request has been confirmed !!');
        return back();
    }

    public function fuelPurchase($purchase_no)
    {
        if (is_null($this->user) || !$this->user->can('sotb_fuel_purchase.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $stat = SotbFuelPurchase::where('purchase_no', $purchase_no)->value('status');
        $description = SotbFuelPurchase::where('purchase_no', $purchase_no)->value('description');
        $date = SotbFuelPurchase::where('purchase_no', $purchase_no)->value('date');
        if($stat == 2 && $stat == 3 || $stat == 4 || $stat == 5){
           $purchase_no = SotbFuelPurchase::where('purchase_no', $purchase_no)->value('purchase_no');
           $purchase_signature = SotbFuelPurchase::where('purchase_no', $purchase_no)->value('purchase_signature');
           $totalValue = DB::table('sotb_fuel_purchase_details')
            ->where('purchase_no', '=', $purchase_no)
            ->sum('total_value');

           $datas = SotbFuelPurchaseDetail::where('purchase_no', $purchase_no)->get();
           $pdf = PDF::loadView('backend.pages.sotb.document.fuel.purchase',compact('datas','purchase_no','setting','description','date','purchase_signature','totalValue'));

           Storage::put('public/sotb/fuel/purchase/'.'FICHE_DEMANDE_ACHAT_'.$purchase_no.'.pdf', $pdf->output());

           // download pdf file
           return $pdf->download('FICHE_DEMANDE_ACHAT_'.$purchase_no.'.pdf'); 
           
        }else if ($stat == -1) {
            session()->flash('error', 'Purchase has been rejected !!');
            return back();
        }else{
            session()->flash('error', 'wait until purchase will be validated !!');
            return back();
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($purchase_no)
    {
        if (is_null($this->user) || !$this->user->can('sotb_fuel_purchase.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any purchase !');
        }

        $purchase = SotbFuelPurchase::where('purchase_no',$purchase_no)->first();
        if (!is_null($purchase)) {
            $purchase->delete();
            SotbFuelPurchaseDetail::where('purchase_no',$purchase_no)->delete();
        }

        session()->flash('success', 'Purchase Request has been deleted !!');
        return back();
    }
}

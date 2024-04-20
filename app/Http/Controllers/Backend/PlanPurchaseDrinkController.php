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
use App\Models\PlanPurchaseDrink;
use App\Models\PlanPurchaseDrinkDetail;
use Validator;
use PDF;
use Mail;
use Carbon\Carbon;

class PlanPurchaseDrinkController extends Controller
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
        if (is_null($this->user) || !$this->user->can('drink_purchase.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any purchase !');
        }

        $plans = PlanPurchaseDrink::all();
        return view('backend.pages.plan_purchase_drink.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('drink_purchase.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any purchase !');
        }

        $drinks  = Drink::orderBy('name','asc')->get();
        return view('backend.pages.plan_purchase_drink.create', compact('drinks'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('drink_purchase.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any purchase !');
        }

        $rules = array(
                'drink_id.*'  => 'required',
                'start_date'  => 'required',
                'end_date'  => 'required',
                'quantity.*'  => 'required',
                'unit.*'  => 'required',
                'description'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $drink_id = $request->drink_id;
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $quantity = $request->quantity;
            $unit = $request->unit;
            $description =$request->description; 
            $latest = PlanPurchaseDrink::latest()->first();
            if ($latest) {
               $plan_no = 'PA' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $plan_no = 'PA' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $plan_signature = "4001711615".Carbon::parse(Carbon::now())->format('YmdHis')."/".$plan_no;
            $created_by = $this->user->name;

            //create purchase
            $purchase = new PlanPurchaseDrink();
            $purchase->start_date = $start_date;
            $purchase->plan_signature = $plan_signature;
            $purchase->plan_no = $plan_no;
            $purchase->created_by = $created_by;
            $purchase->description = $description;
            $purchase->save();
            //insert details of purchase No.
            for( $count = 0; $count < count($drink_id); $count++ ){

                $purchase_price = Drink::where('id', $drink_id[$count])->value('purchase_price');
                $total_purchase_amount = $quantity[$count] * $purchase_price;
                $data = array(
                    'drink_id' => $drink_id[$count],
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'quantity' => $quantity[$count],
                    'unit' => $unit[$count],
                    'purchase_price' => $purchase_price,
                    'description' => $description,
                    'total_purchase_amount' => $total_purchase_amount,
                    'created_by' => $created_by,
                    'plan_no' => $plan_no,
                    'plan_signature' => $plan_signature,
                );
                $insert_data[] = $data;
            }

            PlanPurchaseDrinkDetail::insert($insert_data);

        session()->flash('success', 'Purchase has been created !!');
        return redirect()->route('admin.plan-purchase-drinks.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($plan_no)
    {
        //
         $code = PlanPurchaseDrink::where('plan_no', $plan_no)->value('plan_no');
         $plans = PlanPurchaseDrinkDetail::where('plan_no', $plan_no)->get();
         return view('backend.pages.plan_purchase_drink.show', compact('plans','code'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $plan_no
     * @return \Illuminate\Http\Response
     */
    public function edit($plan_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_purchase.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any purchase !');
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $plan_no
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $plan_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_purchase.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any purchase !');
        }

        
    }

    public function validatePlan($plan_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_purchase.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any purchase !');
        }
            PlanPurchaseDrink::where('plan_no', '=', $plan_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            PlanPurchaseDrinkDetail::where('plan_no', '=', $plan_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        session()->flash('success', 'Plan has been validated !!');
        return back();
    }

    public function reject($plan_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_purchase.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any purchase !');
        }

        PlanPurchaseDrink::where('plan_no', '=', $plan_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
            PlanPurchaseDrinkDetail::where('plan_no', '=', $plan_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        session()->flash('success', 'Plan has been rejected !!');
        return back();
    }

    public function reset($plan_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_purchase.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any purchase !');
        }

        PlanPurchaseDrink::where('plan_no', '=', $plan_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
            PlanPurchaseDrinkDetail::where('plan_no', '=', $plan_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        session()->flash('success', 'Plan has been reseted !!');
        return back();
    }

    public function confirm($plan_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_purchase.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any purchase !');
        }

        PlanPurchaseDrink::where('plan_no', '=', $plan_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            PlanPurchaseDrinkDetail::where('plan_no', '=', $plan_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        session()->flash('success', 'Plan has been confirmed !!');
        return back();
    }

    public function approuve($plan_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_purchase.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any purchase !');
        }

        PlanPurchaseDrink::where('plan_no', '=', $plan_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);
            PlanPurchaseDrinkDetail::where('plan_no', '=', $plan_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);

        session()->flash('success', 'Plan has been confirmed !!');
        return back();
    }

    public function fichePlan($plan_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_purchase.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $stat = PlanPurchaseDrink::where('plan_no', $plan_no)->value('status');
        $description = PlanPurchaseDrink::where('plan_no', $plan_no)->value('description');
        $start_date = PlanPurchaseDrink::where('plan_no', $plan_no)->value('start_date');

           $plan_no = PlanPurchaseDrink::where('plan_no', $plan_no)->value('plan_no');
           $plan_signature = PlanPurchaseDrink::where('plan_no', $plan_no)->value('plan_signature');
           $totalValue = DB::table('plan_purchase_drink_details')
            ->where('plan_no', '=', $plan_no)
            ->sum('total_purchase_amount');

           $datas = PlanPurchaseDrinkDetail::where('plan_no', $plan_no)->get();
           $pdf = PDF::loadView('backend.pages.document.plan_purchase_drink',compact('datas','plan_no','setting','description','start_date','plan_signature','totalValue'));

           Storage::put('public/pdf/plan_purchase_drink/'.'PLAN_APPROVISIONNEMENT'.$plan_no.'.pdf', $pdf->output());

           // download pdf file
           return $pdf->download('PLAN_APPROVISIONNEMENT'.$plan_no.'.pdf'); 
           
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($plan_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_purchase.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any purchase !');
        }

        $purchase = PlanPurchaseDrink::where('plan_no',$plan_no)->first();
        if (!is_null($purchase)) {
            $purchase->delete();
            PlanPurchaseDrinkDetail::where('plan_no',$plan_no)->delete();
        }

        session()->flash('success', 'Plan has been deleted !!');
        return back();
    }
}

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
use App\Models\PlanPurchaseMaterial;
use App\Models\PlanPurchaseMaterialDetail;
use Validator;
use PDF;
use Mail;
use Carbon\Carbon;

class PlanPurchaseMaterialController extends Controller
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
        if (is_null($this->user) || !$this->user->can('material_purchase.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any plan !');
        }

        $plans = PlanPurchaseMaterial::all();
        return view('backend.pages.plan_purchase_material.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('material_purchase.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any plan !');
        }

        $materials  = Material::orderBy('name','asc')->get();
        return view('backend.pages.plan_purchase_material.create', compact('materials'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('material_purchase.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any plan !');
        }

        $rules = array(
                'material_id.*'  => 'required',
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

            $material_id = $request->material_id;
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $quantity = $request->quantity;
            $unit = $request->unit;
            $description =$request->description; 
            $latest = PlanPurchaseMaterial::latest()->first();
            if ($latest) {
               $plan_no = 'PA' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $plan_no = 'PA' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $plan_signature = "4001711615".Carbon::parse(Carbon::now())->format('YmdHis')."/".$plan_no;
            $created_by = $this->user->name;

            //create plan
            $plan = new PlanPurchaseMaterial();
            $plan->start_date = $start_date;
            $plan->plan_signature = $plan_signature;
            $plan->plan_no = $plan_no;
            $plan->created_by = $created_by;
            $plan->description = $description;
            $plan->save();
            //insert details of plan No.
            for( $count = 0; $count < count($material_id); $count++ ){

                $purchase_price = Material::where('id', $material_id[$count])->value('purchase_price');
                $total_purchase_amount = $quantity[$count] * $purchase_price;
                $data = array(
                    'material_id' => $material_id[$count],
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

            PlanPurchaseMaterialDetail::insert($insert_data);

        session()->flash('success', 'plan has been created !!');
        return redirect()->route('admin.plan-purchase-materials.index');
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
         $code = PlanPurchaseMaterial::where('plan_no', $plan_no)->value('plan_no');
         $plans = PlanPurchaseMaterialDetail::where('plan_no', $plan_no)->get();
         return view('backend.pages.plan_purchase_material.show', compact('plans','code'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $plan_no
     * @return \Illuminate\Http\Response
     */
    public function edit($plan_no)
    {
        if (is_null($this->user) || !$this->user->can('material_purchase.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any plan !');
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
        if (is_null($this->user) || !$this->user->can('material_purchase.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any plan !');
        }

        
    }

    public function validatePlan($plan_no)
    {
       if (is_null($this->user) || !$this->user->can('material_purchase.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any plan !');
        }
            PlanPurchaseMaterial::where('plan_no', '=', $plan_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            PlanPurchaseMaterialDetail::where('plan_no', '=', $plan_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        session()->flash('success', 'Plan has been validated !!');
        return back();
    }

    public function reject($plan_no)
    {
       if (is_null($this->user) || !$this->user->can('material_purchase.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any plan !');
        }

        PlanPurchaseMaterial::where('plan_no', '=', $plan_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
            PlanPurchaseMaterialDetail::where('plan_no', '=', $plan_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        session()->flash('success', 'Plan has been rejected !!');
        return back();
    }

    public function reset($plan_no)
    {
       if (is_null($this->user) || !$this->user->can('material_purchase.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any plan !');
        }

        PlanPurchaseMaterial::where('plan_no', '=', $plan_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
            PlanPurchaseMaterialDetail::where('plan_no', '=', $plan_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        session()->flash('success', 'Plan has been reseted !!');
        return back();
    }

    public function confirm($plan_no)
    {
       if (is_null($this->user) || !$this->user->can('material_purchase.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any plan !');
        }

        PlanPurchaseMaterial::where('plan_no', '=', $plan_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            PlanPurchaseMaterialDetail::where('plan_no', '=', $plan_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        session()->flash('success', 'Plan has been confirmed !!');
        return back();
    }

    public function approuve($plan_no)
    {
       if (is_null($this->user) || !$this->user->can('material_purchase.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any plan !');
        }

        PlanPurchaseMaterial::where('plan_no', '=', $plan_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);
            PlanPurchaseMaterialDetail::where('plan_no', '=', $plan_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);

        session()->flash('success', 'Plan has been confirmed !!');
        return back();
    }

    public function fichePlan($plan_no)
    {
        if (is_null($this->user) || !$this->user->can('material_purchase.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $stat = PlanPurchaseMaterial::where('plan_no', $plan_no)->value('status');
        $description = PlanPurchaseMaterial::where('plan_no', $plan_no)->value('description');
        $start_date = PlanPurchaseMaterial::where('plan_no', $plan_no)->value('start_date');

           $plan_no = PlanPurchaseMaterial::where('plan_no', $plan_no)->value('plan_no');
           $plan_signature = PlanPurchaseMaterial::where('plan_no', $plan_no)->value('plan_signature');
           $totalValue = DB::table('plan_purchase_material_details')
            ->where('plan_no', '=', $plan_no)
            ->sum('total_purchase_amount');

           $datas = PlanPurchaseMaterialDetail::where('plan_no', $plan_no)->get();
           $pdf = PDF::loadView('backend.pages.document.plan_purchase_material',compact('datas','plan_no','setting','description','start_date','plan_signature','totalValue'));

           Storage::put('public/pdf/plan_purchase_material/'.'PLAN_APPROVISIONNEMENT'.$plan_no.'.pdf', $pdf->output());

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
        if (is_null($this->user) || !$this->user->can('material_purchase.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any plan !');
        }

        $plan = PlanPurchaseMaterial::where('plan_no',$plan_no)->first();
        if (!is_null($plan)) {
            $plan->delete();
            PlanPurchaseMaterialDetail::where('plan_no',$plan_no)->delete();
        }

        session()->flash('success', 'Plan has been deleted !!');
        return back();
    }
}

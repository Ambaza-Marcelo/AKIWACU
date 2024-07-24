<?php

namespace App\Http\Controllers\Backend\HomeConsumption;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Storage;
use App\Models\StaffMember;
use App\Models\BarristItem;
use App\Models\FoodItem;
use App\Models\HomeConsumption;
use App\Models\HomeConsumptionDetail;
use Carbon\Carbon;
use Validator;
use PDF;
use Excel;
use App\Exports\HomeConsumptionExport;

class HomeConsumptionController extends Controller
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

    public function indexBarrist($staff_member_id)
    {
        if (is_null($this->user) || !$this->user->can('drink_order_client.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any order !');
        }

        $consumptions = HomeConsumption::where('staff_member_id',$staff_member_id)->take(200)->orderBy('id','desc')->get();
        $staff_member = StaffMember::where('id',$staff_member_id)->first();

        $staff_member_id = $staff_member->id;

        $in_pending = count(HomeConsumptionDetail::where('staff_member_id',$staff_member_id)->where('status','!=',1)->get());
        return view('backend.pages.home_consumption.home_consumption.index_barrist', compact('consumptions','staff_member_id','staff_member','in_pending'));
    }

    public function indexFood($staff_member_id)
    {
        if (is_null($this->user) || !$this->user->can('drink_order_client.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any order !');
        }

        $consumptions = HomeConsumption::where('staff_member_id',$staff_member_id)->take(200)->orderBy('id','desc')->get();
        $staff_member = StaffMember::where('id',$staff_member_id)->first();

        $staff_member_id = $staff_member->id;

        $in_pending = count(HomeConsumptionDetail::where('staff_member_id',$staff_member_id)->where('status','!=',1)->get());
        return view('backend.pages.home_consumption.home_consumption.index_food', compact('consumptions','staff_member_id','staff_member','in_pending'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createBarrist($staff_member_id)
    {
        if (is_null($this->user) || !$this->user->can('drink_order_client.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any order !');
        }

        $articles  = BarristItem::where('name','like','%EDEN GARDEN STAFF%')->get();
        $staff_member = StaffMember::where('id',$staff_member_id)->first();
        $staff_member_id = $staff_member->id;
        return view('backend.pages.home_consumption.home_consumption.create_barrist', compact('articles','staff_member_id','staff_member'));
    }

    public function createFood($staff_member_id)
    {
        if (is_null($this->user) || !$this->user->can('drink_order_client.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any order !');
        }

        $articles  = FoodItem::where('name','like','%EDEN GARDEN STAFF%')->get();
        $staff_member = StaffMember::where('id',$staff_member_id)->first();
        $staff_member_id = $staff_member->id;
        return view('backend.pages.home_consumption.home_consumption.create_food', compact('articles','staff_member_id','staff_member'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('drink_order_client.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any order !');
        }

        $rules = array(
                'staff_member_id'  => 'required',
                'quantity.*'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            if (!empty($request->food_item_id)) {
            	$food_item_id = $request->food_item_id;
            	$flag = 0;

            }elseif (!empty($request->barrist_item_id)) {
            	$barrist_item_id = $request->barrist_item_id;
            	$flag = 1;
            }else{
            	session()->flash('error', 'Veuillez bien choisir la designation de la commande');
                return back();
            }

            $date = $request->date;
            $quantity = $request->quantity;
            $staff_member_id = $request->staff_member_id;
            $description = $request->description; 
            $status = 0; 
            $created_by = $this->user->name;

            $latest = HomeConsumption::orderBy('id','desc')->first();
            if ($latest) {
               $consumption_no = 'BC' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $consumption_no = 'BC' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $consumption_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$consumption_no;

            //create order
            $order = new HomeConsumption();
            $order->date = $date;
            $order->consumption_no = $consumption_no;
            $order->consumption_signature = $consumption_signature;
            $order->staff_member_id = $staff_member_id;
            $order->created_by = $created_by;
            $order->description = $description;
            $order->status = $status;
            $order->save();
            //insert details of order No.
            if ($flag == 0) {
            	for( $count = 0; $count < count($food_item_id); $count++ ){

                $amount = FoodItem::where('id', $food_item_id[$count])->value('selling_price');
                $total_amount = $quantity[$count] * $amount;
                $total_amount_consumed = StaffMember::where('id',$staff_member_id)->value('total_amount_consumed');
                $total_amount_consumptioning = $total_amount_consumed + $total_amount;
                $total_amount_authorized = StaffMember::where('id',$staff_member_id)->value('total_amount_authorized');
            	if ($total_amount_consumptioning > $total_amount_authorized) {
            		session()->flash('error', 'you have finished amount authorized');
                	return back();
            	}else{
            		
            	

                $data = array(
                    'food_item_id' => $food_item_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'amount_consumed' => $amount,
                    'staff_member_id' => $staff_member_id,
                    'description' => $description,
                    'total_amount_consumed' => $total_amount,
                    'created_by' => $created_by,
                    'consumption_no' => $consumption_no,
                    'status' => $status,
                    'consumption_signature' => $consumption_signature
                );
                $insert_data[] = $data;
            	}
            }
            
            HomeConsumptionDetail::insert($insert_data);

            }elseif ($flag == 1) {
            	for( $count = 0; $count < count($barrist_item_id); $count++ ){

                $amount = BarristItem::where('id', $barrist_item_id[$count])->value('selling_price');
                $total_amount = $quantity[$count] * $amount;
                $total_amount_consumed = StaffMember::where('id',$staff_member_id)->value('total_amount_consumed');
                $total_amount_consumptioning = $total_amount_consumed + $total_amount;
                $total_amount_authorized = StaffMember::where('id',$staff_member_id)->value('total_amount_authorized');
            	if ($total_amount_consumptioning > $total_amount_authorized) {
            		session()->flash('error', 'you have finished amount authorized');
                	return back();
            	}else{
            		
            	

                $data = array(
                    'barrist_item_id' => $barrist_item_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'amount_consumed' => $amount,
                    'staff_member_id' => $staff_member_id,
                    'description' => $description,
                    'total_amount_consumed' => $total_amount,
                    'created_by' => $created_by,
                    'consumption_no' => $consumption_no,
                    'status' => $status,
                    'consumption_signature' => $consumption_signature
                );
                $insert_data[] = $data;
            	}
            }
            
            HomeConsumptionDetail::insert($insert_data);

            }else{
            	session()->flash('error', 'oops!something wrong');
                return back();
            }

            $staff_member = StaffMember::where('id',$staff_member_id)->value('name');
            $total_amount = DB::table('home_consumption_details')
            ->where('consumption_no',$consumption_no)->sum('total_amount_consumed');
            $total_amount_authorized = StaffMember::where('id',$staff_member_id)->value('total_amount_authorized');
            $total_amount_consumed = StaffMember::where('id',$staff_member_id)->value('total_amount_consumed');
            $staff_member = StaffMember::where('id',$staff_member_id)->first();
            $staff_member->etat = 0;
            $staff_member->total_amount_consumed = $total_amount_consumed + $total_amount;
            $staff_member->total_amount_remaining = $total_amount_authorized - $staff_member->total_amount_consumed;
            $staff_member->save();

        session()->flash('success', 'Order has been sent successfuly!!');
        if ($flag == 0) {
        	return redirect()->route('admin.home-consumption-food.index',$staff_member_id);
        }else{
        	return redirect()->route('admin.home-consumption-barrist.index',$staff_member_id);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($consumption_no)
    {
        //
         $code = HomeConsumption::where('consumption_no', $consumption_no)->value('consumption_no');
         $consumption_details = HomeConsumptionDetail::where('consumption_no', $consumption_no)->get();
         return view('backend.pages.home_consumption.home_consumption.show', compact('consumption_details','consumption_no'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('drink_order_client.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any order !');
        }

        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('drink_order_client.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any order !');
        }

        
    }


    public function reject($order_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_order_client.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any order !');
        }

        HomeConsumption::where('order_no', '=', $order_no)
                ->update(['status' => -1]);
        HomeConsumptionDetail::where('order_no', '=', $order_no)
                ->update(['status' => -1]);

        session()->flash('success', 'Order has been rejected !!');
        return back();
    }

    public function htmlPdf($consumption_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_order_client.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $stat = HomeConsumption::where('consumption_no', $consumption_no)->value('status');
        $description = HomeConsumption::where('consumption_no', $consumption_no)->value('description');
        $consumption_signature = HomeConsumption::where('consumption_no', $consumption_no)->value('consumption_signature');
        $date = Carbon::now();
        $consumption = HomeConsumption::where('consumption_no', $consumption_no)->first();
        $totalValue = DB::table('home_consumption_details')
            ->where('consumption_no', '=', $consumption_no)
            ->sum('total_amount_consumed');


           $consumption_no = HomeConsumption::where('consumption_no', $consumption_no)->value('consumption_no');

           $datas = HomeConsumptionDetail::where('consumption_no', $consumption_no)->get();
           $pdf = PDF::loadView('backend.pages.document.consommation_maison',compact('datas','consumption_no','setting','description','consumption_signature','date','totalValue','consumption'))->setPaper('a6', 'portrait');

           Storage::put('public/CONSOMMATION_MAISON/'.$consumption_no.'.pdf', $pdf->output());

           HomeConsumption::where('consumption_no', '=', $consumption_no)
                ->update(['flag' => 1]);
            HomeConsumptionDetail::where('consumption_no', '=', $consumption_no)
                ->update(['flag' => 1]); 

           // download pdf file
           return $pdf->download('BON_CONSOMMATION_'.$consumption_no.'.pdf');
        
    }

    public function exportToExcel(Request $request)
    {
        return Excel::download(new HomeConsumptionExport, 'RAPPORT_CONSOMMATION_MAISON.xlsx');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($order_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_order_client.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any order !');
        }

        $order = HomeConsumption::where('order_no',$order_no)->first();
        if (!is_null($order)) {
            $order->delete();
            HomeConsumptionDetail::where('order_no',$order_no)->delete();
        }

        session()->flash('success', 'Order has been deleted !!');
        return back();
    }
}

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
use App\Models\Employe;
use App\Models\OrderKitchen;
use App\Models\OrderKitchenDetail;
use App\Models\FoodItem;
use App\Models\Accompagnement;
use App\Models\AccompagnementDetail;
use Carbon\Carbon;
use Validator;
use PDF;
use Excel;
use App\Exports\FoodOrderClientExport;


class OrderKitchenController extends Controller
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

    public function index()
    {
        if (is_null($this->user) || !$this->user->can('food_order_client.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any order !');
        }

        $orders = OrderKitchen::take(200)->orderBy('order_no','desc')->get();
        return view('backend.pages.order_kitchen.index', compact('orders'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('food_order_client.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any order !');
        }

        $articles  = FoodItem::orderBy('name','asc')->get();
        $employes  = Employe::orderBy('name','asc')->get();
        $accompagnements  = Accompagnement::orderBy('name','asc')->get();
        return view('backend.pages.order_kitchen.create', compact('articles','employes','accompagnements'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('food_order_client.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any order !');
        }

        $rules = array(
                'food_item_id.*'  => 'required',
                'employe_id'  => 'required',
                'quantity.*'  => 'required',
                'table_no'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $food_item_id = $request->food_item_id;
            $date = $request->date;
            $quantity = $request->quantity;
            $table_no = $request->table_no;
            $employe_id = $request->employe_id;
            $description =$request->description; 
            $status = 0; 
            $created_by = $this->user->name;
            $accompagnement_id = $request->accompagnement_id;

            $latest = OrderKitchen::orderBy('id','desc')->first();
            if ($latest) {
               $order_no = 'BC' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $order_no = 'BC' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $order_signature = "4001711615".Carbon::parse(Carbon::now())->format('YmdHis')."/".$order_no;

            //create order
            $order = new OrderKitchen();
            $order->date = $date;
            $order->order_no = $order_no;
            $order->order_signature = $order_signature;
            $order->employe_id = $employe_id;
            $order->created_by = $created_by;
            $order->description = $description;
            $order->table_no = $table_no;
            $order->status = $status;
            $order->save();
            //insert details of order No.
            for( $count = 0; $count < count($food_item_id); $count++ ){

                $selling_price = FoodItem::where('id', $food_item_id[$count])->value('selling_price');
                $total_amount_selling = $quantity[$count] * $selling_price;
                $data = array(
                    'food_item_id' => $food_item_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'selling_price' => $selling_price,
                    'table_no' => $table_no,
                    'status' => $status,
                    'description' => $description,
                    'total_amount_selling' => $total_amount_selling,
                    'created_by' => $created_by,
                    'order_no' => $order_no,
                    'order_signature' => $order_signature,
                    'employe_id' => $employe_id,
                    'accompagnement_id' => $accompagnement_id[$count],

                );
                $insert_data[] = $data;
        }

        for( $n = 0; $n < count($accompagnement_id); $n++ ){

                $accompagnement_data = array(
                    //'food_item_id' => $food_item_id[$n],
                    'order_no' => $order_no,
                    'employe_id' => $employe_id,
                    'accompagnement_id' => $accompagnement_id[$n],

                );
                    $insert_accompagnement_data[] = $accompagnement_data;
            }

            OrderKitchenDetail::insert($insert_data);
            AccompagnementDetail::insert($insert_accompagnement_data);

        session()->flash('success', 'Order has been sent successfuly!!');
        return redirect()->route('admin.order_kitchens.index');
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
         $code = OrderKitchen::where('order_no', $order_no)->value('order_no');
         $accompagnements = AccompagnementDetail::where('order_no', $order_no)->get();
         $orderDetails = OrderKitchenDetail::where('order_no', $order_no)->get();
         return view('backend.pages.order_kitchen.show', compact('orderDetails','order_no','accompagnements'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($order_no)
    {
        if (is_null($this->user) || !$this->user->can('food_order_client.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any order !');
        }

        $articles  = FoodItem::orderBy('name','asc')->get();
        $employes  = Employe::orderBy('name','asc')->get();
        $accompagnements  = Accompagnement::orderBy('name','asc')->get();
        $data = OrderKitchen::where('order_no',$order_no)->first();
        $datas = OrderKitchenDetail::where('order_no',$order_no)->get();
        return view('backend.pages.order_kitchen.edit', compact('articles','employes','accompagnements','data','datas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $order_no)
    {
        if (is_null($this->user) || !$this->user->can('food_order_client.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any order !');
        }

        $rules = array(
                'food_item_id.*'  => 'required',
                'employe_id'  => 'required',
                'quantity.*'  => 'required',
                'table_no'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $food_item_id = $request->food_item_id;
            $date = $request->date;
            $quantity = $request->quantity;
            $table_no = $request->table_no;
            $employe_id = $request->employe_id;
            $description =$request->description; 
            $status = -3; 
            $accompagnement_id = $request->accompagnement_id;
            //create order
            $order = OrderKitchen::where('order_no',$order_no)->first();
            $order->date = $date;
            $order->employe_id = $employe_id;
            $order->table_no = $table_no;
            $order->status = $status;
            $order->save();
            //insert details of order No.
            for( $count = 0; $count < count($food_item_id); $count++ ){

                $selling_price = FoodItem::where('id', $food_item_id[$count])->value('selling_price');
                $total_amount_selling = $quantity[$count] * $selling_price;
                $data = array(
                    'food_item_id' => $food_item_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'selling_price' => $selling_price,
                    'table_no' => $table_no,
                    'status' => $status,
                    'total_amount_selling' => $total_amount_selling,
                    'order_no' => $order_no,
                    'employe_id' => $employe_id,
                    'accompagnement_id' => $accompagnement_id[$count],

                );
                $insert_data[] = $data;

            }

            OrderKitchenDetail::where('order_no',$order_no)->delete();

            for( $n = 0; $n < count($accompagnement_id); $n++ ){

                $accompagnement_data = array(
                    //'food_item_id' => $food_item_id[$n],
                    'order_no' => $order_no,
                    'employe_id' => $employe_id,
                    'accompagnement_id' => $accompagnement_id[$n],

                );
                    $insert_accompagnement_data[] = $accompagnement_data;

                    AccompagnementDetail::where('order_no',$order_no)->delete();
                }

            OrderKitchenDetail::insert($insert_data);
            AccompagnementDetail::insert($insert_accompagnement_data);

        session()->flash('success', 'Order has been updated successfuly!!');
        return redirect()->route('admin.order_kitchens.index');
    }

    public function validateCommand($order_no)
    {
       if (is_null($this->user) || !$this->user->can('food_order_client.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any order !');
        }
            OrderKitchen::where('order_no', '=', $order_no)
                ->update(['status' => 1]);

        session()->flash('success', 'order has been validated !!');
        return back();
    }

    public function reject($order_no)
    {
       if (is_null($this->user) || !$this->user->can('food_order_client.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any order !');
        }

        OrderKitchen::where('order_no', '=', $order_no)
                ->update(['status' => -1]);

        session()->flash('success', 'Order has been rejected !!');
        return back();
    }

    public function reset($order_no)
    {
       if (is_null($this->user) || !$this->user->can('food_order_client.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any order !');
        }

        OrderKitchen::where('order_no', '=', $order_no)
                ->update(['status' => -2]);

        session()->flash('success', 'Order has been reseted !!');
        return back();
    }


    public function htmlPdf($order_no)
    {
        if (is_null($this->user) || !$this->user->can('food_order_client.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $stat = OrderKitchen::where('order_no', $order_no)->value('status');
        $description = OrderKitchen::where('order_no', $order_no)->value('description');
        $order_signature = OrderKitchen::where('order_no', $order_no)->value('order_signature');
        $date = OrderKitchen::where('order_no', $order_no)->value('created_at');
        $order = OrderKitchen::where('order_no', $order_no)->first();
        $totalValue = DB::table('order_kitchen_details')
            ->where('order_no', '=', $order_no)
            ->sum('total_amount_selling');
        if($stat == 1 || $stat == 2 || $stat == 3 || $stat == -3){
           $order_no = OrderKitchen::where('order_no', $order_no)->value('order_no');

           $datas = OrderKitchenDetail::where('order_no', $order_no)->get();
           $accompagnements = AccompagnementDetail::where('order_no', $order_no)->get();
           $pdf = PDF::loadView('backend.pages.document.food_order_client',compact('accompagnements','datas','order_no','setting','description','order_signature','date','order','totalValue'))->setPaper('a6', 'portrait');

           Storage::put('public/commande_cuisine/'.$order_no.'.pdf', $pdf->output());

           // download pdf file
           return $pdf->download($order_no.'.pdf'); 
           
        }else if ($stat == -1) {
            session()->flash('error', 'Order has been rejected !!');
            return back();
        }else{
            session()->flash('error', 'wait until order will be validated !!');
            return back();
        }
        
    }

    public function exportToExcel(Request $request)
    {
        return Excel::download(new FoodOrderClientExport, 'RAPPORT_COMMANDE_CUISINE.xlsx');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($order_no)
    {
        if (is_null($this->user) || !$this->user->can('food_order_client.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any order !');
        }

        $order = OrderKitchen::where('order_no',$order_no)->first();
        if (!is_null($order)) {
            $order->delete();
            OrderKitchenDetail::where('order_no',$order_no)->delete();
        }

        session()->flash('success', 'Order has been deleted !!');
        return back();
    }
}

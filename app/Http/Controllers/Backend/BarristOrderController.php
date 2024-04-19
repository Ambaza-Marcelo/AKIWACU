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
use App\Models\BarristOrder;
use App\Models\BarristOrderDetail;
use App\Models\BarristItem;
use App\Models\Ingredient;
use App\Models\IngredientDetail;
use Carbon\Carbon;
use Validator;
use PDF;
use Excel;
use App\Exports\BarristOrderClientExport;

class BarristOrderController extends Controller
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
        if (is_null($this->user) || !$this->user->can('drink_order_client.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any order !');
        }

        $orders = BarristOrder::take(200)->orderBy('id','desc')->get();
        return view('backend.pages.order_barrist.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('drink_order_client.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any order !');
        }

        $articles  = BarristItem::orderBy('name','asc')->get();
        $employes  = Employe::orderBy('name','asc')->get();
        $ingredients  = Ingredient::orderBy('name','asc')->get();
        return view('backend.pages.order_barrist.create', compact('articles','employes','ingredients'));
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
                'barrist_item_id.*'  => 'required',
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

            $barrist_item_id = $request->barrist_item_id;
            $date = $request->date;
            $quantity = $request->quantity;
            $table_no = $request->table_no;
            $employe_id = $request->employe_id;
            $description = $request->description; 
            $ingredient_id = $request->ingredient_id;
            $status = 0; 
            $created_by = $this->user->name;

            $latest = BarristOrder::orderBy('id','desc')->first();
            if ($latest) {
               $order_no = 'BC' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $order_no = 'BC' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $order_signature = "4001711615".Carbon::parse(Carbon::now())->format('YmdHis')."/".$order_no;

            //create order
            $order = new BarristOrder();
            $order->date = $date;
            $order->order_no = $order_no;
            $order->order_signature = $order_signature;
            $order->employe_id = $employe_id;
            $order->created_by = $created_by;
            $order->description = $description;
            $order->status = $status;
            $order->table_no = $table_no;
            $order->save();
            //insert details of order No.
            for( $count = 0; $count < count($barrist_item_id); $count++ ){

                $selling_price = BarristItem::where('id', $barrist_item_id[$count])->value('selling_price');
                $total_amount_selling = $quantity[$count] * $selling_price;
                $data = array(
                    'barrist_item_id' => $barrist_item_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'selling_price' => $selling_price,
                    'table_no' => $table_no,
                    'description' => $description,
                    'total_amount_selling' => $total_amount_selling,
                    'created_by' => $created_by,
                    'order_no' => $order_no,
                    'status' => $status,
                    'order_signature' => $order_signature,
                    'employe_id' => $employe_id,
                    'ingredient_id' => $ingredient_id[$count]
                );
                $insert_data[] = $data;
            }

            for( $n = 0; $n < count($ingredient_id); $n++ ){

                $ingredient_data = array(
                    //'barrist_item_id' => $barrist_item_id[$n],
                    'order_no' => $order_no,
                    'order_signature' => $order_signature,
                    'employe_id' => $employe_id,
                    'ingredient_id' => $ingredient_id[$n],

                );
                $insert_ingredient_data[] = $ingredient_data;
            }
            /*
            $mail = Employe::where('id', $employe_id)->value('mail');
            $name = Employe::where('id', $employe_id)->value('name');

            $mailData = [
                    'title' => 'COMMANDE',
                    'order_no' => $order_no,
                    'name' => $name,
                    //'body' => 'This is for testing email using smtp.'
                    ];
         
        Mail::to($mail)->send(new OrderMail($mailData));
        */

            BarristOrderDetail::insert($insert_data);
            IngredientDetail::insert($insert_ingredient_data);

        session()->flash('success', 'Order has been sent successfuly!!');
        return redirect()->route('admin.barrist-orders.index');
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
         $code = BarristOrder::where('order_no', $order_no)->value('order_no');
         $orderDetails = BarristOrderDetail::where('order_no', $order_no)->get();
         $ingredients = IngredientDetail::where('order_no', $order_no)->get();
         return view('backend.pages.order_barrist.show', compact('orderDetails','order_no','ingredients'));
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

        $order = BarristOrder::find($id);
        $employes  = Employe::all();
        $articles  = BarristItem::where('status','BOISSON')->orderBy('name','asc')->get();
        return view('backend.pages.order_barrist.edit', compact('order','employes','articles'));
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

    public function validateCommand($order_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_order_client.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any order !');
        }
            BarristOrder::where('order_no', '=', $order_no)
                ->update(['status' => 1]);
            BarristOrderDetail::where('order_no', '=', $order_no)
                ->update(['status' => 1]);

        session()->flash('success', 'order has been validated !!');
        return back();
    }

    public function reject($order_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_order_client.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any order !');
        }

        BarristOrder::where('order_no', '=', $order_no)
                ->update(['status' => -1]);
        BarristOrderDetail::where('order_no', '=', $order_no)
                ->update(['status' => -1]);

        session()->flash('success', 'Order has been rejected !!');
        return back();
    }

    public function reset($order_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_order_client.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any order !');
        }

        BarristOrder::where('order_no', '=', $order_no)
                ->update(['status' => -2]);
        BarristOrderDetail::where('order_no', '=', $order_no)
                ->update(['status' => -2]);

        session()->flash('success', 'Order has been reseted !!');
        return back();
    }

    public function htmlPdf($order_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_order_client.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $stat = BarristOrder::where('order_no', $order_no)->value('status');
        $description = BarristOrder::where('order_no', $order_no)->value('description');
        $order_signature = BarristOrder::where('order_no', $order_no)->value('order_signature');
        $date = BarristOrder::where('order_no', $order_no)->value('created_at');
        $order = BarristOrder::where('order_no', $order_no)->first();
        $totalValue = DB::table('order_drink_details')
            ->where('order_no', '=', $order_no)
            ->sum('total_amount_selling');
        $ingredients = IngredientDetail::where('order_no', $order_no)->get();

        if($stat == 1 || $stat == 2 || $stat == 3){
           $order_no = BarristOrder::where('order_no', $order_no)->value('order_no');

           $datas = BarristOrderDetail::where('order_no', $order_no)->get();
           $pdf = PDF::loadView('backend.pages.document.barrist_order',compact('datas','ingredients','order_no','setting','description','order_signature','date','totalValue','order'))->setPaper('a6', 'portrait');

           Storage::put('public/barrist_order/'.$order_no.'.pdf', $pdf->output());

           // download pdf file
           return $pdf->download('COMMANDE_'.$order_no.'.pdf'); 
           
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
        return Excel::download(new BarristOrderClientExport, 'RAPPORT_COMMANDE_BARRISTA.xlsx');
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

        $order = BarristOrder::where('order_no',$order_no)->first();
        if (!is_null($order)) {
            $order->delete();
            BarristOrderDetail::where('order_no',$order_no)->delete();
        }

        session()->flash('success', 'Order has been deleted !!');
        return back();
    }
}

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
use App\Models\OrderDrink;
use App\Models\OrderDrinkDetail;
use App\Models\Drink;
use App\Models\Facture;
use App\Models\DrinkSmallStore;
use App\Models\DrinkSmallStoreDetail;
use Carbon\Carbon;
use Validator;
use PDF;
use Excel;
use App\Exports\DrinkOrderClientExport;

class OrderDrinkController extends Controller
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

        $orders = OrderDrink::take(200)->orderBy('id','desc')->get();
        
        return view('backend.pages.order_drink.index', compact('orders'));
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

        $articles  = Drink::where('selling_price','>',0)->orderBy('name','asc')->get();
        $employes  = Employe::orderBy('name','asc')->get();
        return view('backend.pages.order_drink.create', compact('articles','employes'));
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
                'drink_id.*'  => 'required',
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

            $drink_id = $request->drink_id;
            $date = $request->date;
            $quantity = $request->quantity;
            $table_no = $request->table_no;
            $employe_id = $request->employe_id;
            $description =$request->description; 
            $status = 0; 
            $created_by = $this->user->name;

            $latest = OrderDrink::orderBy('id','desc')->first();
            if ($latest) {
               $order_no = 'BC' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $order_no = 'BC' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $order_signature = "4001711615".Carbon::parse(Carbon::now())->format('YmdHis')."/".$order_no;

            //create order
            $order = new OrderDrink();
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
            for( $count = 0; $count < count($drink_id); $count++ ){

                $selling_price = DrinkSmallStoreDetail::where('drink_id', $drink_id[$count])->value('selling_price');
                $total_amount_selling = $quantity[$count] * $selling_price;
                $data = array(
                    'drink_id' => $drink_id[$count],
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
                );
                $insert_data[] = $data;
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

            OrderDrinkDetail::insert($insert_data);

        session()->flash('success', 'Order has been sent successfuly!!');
        return redirect()->route('admin.order_drinks.index');
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
         $code = OrderDrink::where('order_no', $order_no)->value('order_no');
         $orderDetails = OrderDrinkDetail::where('order_no', $order_no)->get();
         return view('backend.pages.order_drink.show', compact('orderDetails','order_no'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($order_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_order_client.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any order !');
        }

        $articles  = Drink::orderBy('name','asc')->get();
        $employes  = Employe::orderBy('name','asc')->get();
        $data = OrderDrink::where('order_no',$order_no)->first();
        $datas = OrderDrinkDetail::where('order_no',$order_no)->get();
        return view('backend.pages.order_drink.edit', compact('articles','employes','data','datas'));
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
        if (is_null($this->user) || !$this->user->can('drink_order_client.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any order !');
        }

         $rules = array(
                'drink_id.*'  => 'required',
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

            $drink_id = $request->drink_id;
            $date = $request->date;
            $quantity = $request->quantity;
            $table_no = $request->table_no;
            $employe_id = $request->employe_id;
            $description =$request->description; 
            $status = -3; 

            //create order
            $order = OrderDrink::where('order_no',$order_no)->first();
            $order->date = $date;
            $order->employe_id = $employe_id;
            $order->description = $description;
            $order->table_no = $table_no;
            $order->status = $status;
            $order->save();
            //insert details of order No.
            for( $count = 0; $count < count($drink_id); $count++ ){

                $selling_price = DrinkSmallStoreDetail::where('drink_id', $drink_id[$count])->value('selling_price');
                $total_amount_selling = $quantity[$count] * $selling_price;
                $data = array(
                    'drink_id' => $drink_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'selling_price' => $selling_price,
                    'table_no' => $table_no,
                    'description' => $description,
                    'total_amount_selling' => $total_amount_selling,
                    'order_no' => $order_no,
                    'status' => $status,
                    'employe_id' => $employe_id,
                );
                $insert_data[] = $data;

            }

        OrderDrinkDetail::where('order_no',$order_no)->delete();

        OrderDrinkDetail::insert($insert_data);

        session()->flash('success', 'Order has been updated successfuly!!');
        return redirect()->route('admin.order_drinks.index');
    }

    public function validateCommand($order_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_order_client.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any order !');
        }
            OrderDrink::where('order_no', '=', $order_no)
                ->update(['status' => 1]);
            OrderDrinkDetail::where('order_no', '=', $order_no)
                ->update(['status' => 1]);

        session()->flash('success', 'order has been validated !!');
        return back();
    }

    public function reject($order_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_order_client.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any order !');
        }

        OrderDrink::where('order_no', '=', $order_no)
                ->update(['status' => -1]);
        OrderDrinkDetail::where('order_no', '=', $order_no)
                ->update(['status' => -1]);

        session()->flash('success', 'Order has been rejected !!');
        return back();
    }

    public function reset($order_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_order_client.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any order !');
        }

        OrderDrink::where('order_no', '=', $order_no)
                ->update(['status' => -2]);
        OrderDrinkDetail::where('order_no', '=', $order_no)
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
        $stat = OrderDrink::where('order_no', $order_no)->value('status');
        $description = OrderDrink::where('order_no', $order_no)->value('description');
        $order_signature = OrderDrink::where('order_no', $order_no)->value('order_signature');
        $date = OrderDrink::where('order_no', $order_no)->value('created_at');
        $order = OrderDrink::where('order_no', $order_no)->first();
        $totalValue = DB::table('order_drink_details')
            ->where('order_no', '=', $order_no)
            ->sum('total_amount_selling');

        if($stat == 1 || $stat == 2 || $stat == 3){
           $order_no = OrderDrink::where('order_no', $order_no)->value('order_no');

           $datas = OrderDrinkDetail::where('order_no', $order_no)->get();
           $pdf = PDF::loadView('backend.pages.document.drink_order_client',compact('datas','order_no','setting','description','order_signature','date','totalValue','order'))->setPaper('a6', 'portrait');

           Storage::put('public/commande_boisson/'.$order_no.'.pdf', $pdf->output());

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
        return Excel::download(new DrinkOrderClientExport, 'RAPPORT_COMMANDE_BOISSON.xlsx');
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

        $order = OrderDrink::where('order_no',$order_no)->first();
        if (!is_null($order)) {
            $order->delete();
            OrderDrinkDetail::where('order_no',$order_no)->delete();
        }

        session()->flash('success', 'Order has been deleted !!');
        return back();
    }
}

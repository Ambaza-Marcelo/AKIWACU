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
use App\Models\BartenderOrder;
use App\Models\BartenderOrderDetail;
use App\Models\BartenderItem;
use Carbon\Carbon;
use Validator;
use PDF;
use Excel;
use App\Exports\BartenderOrderClientExport;

class BartenderOrderController extends Controller
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

        $orders = BartenderOrder::take(200)->orderBy('order_no','desc')->get();
        return view('backend.pages.order_bartender.index', compact('orders'));
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

        $articles  = BartenderItem::orderBy('name','asc')->get();
        $employes  = Employe::orderBy('name','asc')->get();
        return view('backend.pages.order_bartender.create', compact('articles','employes'));
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
                'bartender_item_id.*'  => 'required',
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

            $bartender_item_id = $request->bartender_item_id;
            $date = $request->date;
            $quantity = $request->quantity;
            $table_no = $request->table_no;
            $employe_id = $request->employe_id;
            $description = $request->description; 
            $ingredient_id = $request->ingredient_id;
            $status = 0; 
            $created_by = $this->user->name;

            $latest = BartenderOrder::orderBy('id','desc')->first();
            if ($latest) {
               $order_no = 'BC' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $order_no = 'BC' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $order_signature = "4001711615".Carbon::parse(Carbon::now())->format('YmdHis')."/".$order_no;

            //create order
            $order = new BartenderOrder();
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
            for( $count = 0; $count < count($bartender_item_id); $count++ ){

                $selling_price = BartenderItem::where('id', $bartender_item_id[$count])->value('selling_price');
                $total_amount_selling = $quantity[$count] * $selling_price;
                $data = array(
                    'bartender_item_id' => $bartender_item_id[$count],
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
                    'employe_id' => $employe_id
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

            BartenderOrderDetail::insert($insert_data);

        session()->flash('success', 'Order has been sent successfuly!!');
        return redirect()->route('admin.bartender-orders.index');
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
         $code = BartenderOrder::where('order_no', $order_no)->value('order_no');
         $orderDetails = BartenderOrderDetail::where('order_no', $order_no)->get();
         return view('backend.pages.order_bartender.show', compact('orderDetails','order_no'));
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

        $order = BartenderOrder::find($id);
        $employes  = Employe::all();
        $articles  = BartenderItem::where('status','BOISSON')->orderBy('name','asc')->get();
        return view('backend.pages.order_bartender.edit', compact('order','employes','articles'));
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
            BartenderOrder::where('order_no', '=', $order_no)
                ->update(['status' => 1]);
            BartenderOrderDetail::where('order_no', '=', $order_no)
                ->update(['status' => 1]);

        session()->flash('success', 'order has been validated !!');
        return back();
    }

    public function reject($order_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_order_client.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any order !');
        }

        BartenderOrder::where('order_no', '=', $order_no)
                ->update(['status' => -1]);
        BartenderOrderDetail::where('order_no', '=', $order_no)
                ->update(['status' => -1]);

        session()->flash('success', 'Order has been rejected !!');
        return back();
    }

    public function reset($order_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_order_client.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any order !');
        }

        BartenderOrder::where('order_no', '=', $order_no)
                ->update(['status' => -2]);
        BartenderOrderDetail::where('order_no', '=', $order_no)
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
        $stat = BartenderOrder::where('order_no', $order_no)->value('status');
        $description = BartenderOrder::where('order_no', $order_no)->value('description');
        $order_signature = BartenderOrder::where('order_no', $order_no)->value('order_signature');
        $date = BartenderOrder::where('order_no', $order_no)->value('created_at');
        $order = BartenderOrder::where('order_no', $order_no)->first();
        $totalValue = DB::table('order_drink_details')
            ->where('order_no', '=', $order_no)
            ->sum('total_amount_selling');

        if($stat == 1 || $stat == 2 || $stat == 3){
           $order_no = BartenderOrder::where('order_no', $order_no)->value('order_no');

           $datas = BartenderOrderDetail::where('order_no', $order_no)->get();
           $pdf = PDF::loadView('backend.pages.document.bartender_order',compact('datas','order_no','setting','description','order_signature','date','totalValue','order'))->setPaper('a6', 'portrait');

           Storage::put('public/bartender_order/'.$order_no.'.pdf', $pdf->output());

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
        return Excel::download(new BartenderOrderClientExport, 'RAPPORT_COMMANDE_BARTENDER.xlsx');
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

        $order = BartenderOrder::where('order_no',$order_no)->first();
        if (!is_null($order)) {
            $order->delete();
            BartenderOrderDetail::where('order_no',$order_no)->delete();
        }

        session()->flash('success', 'Order has been deleted !!');
        return back();
    }
}

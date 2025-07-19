<?php

namespace App\Http\Controllers\Backend\F;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Storage;
use App\Models\Employe;
use App\Models\F\FDrinkOrder;
use App\Models\F\FDrinkOrderDetail;
use App\Models\Drink;
use App\Models\Facture;
use App\Models\DrinkSmallStore;
use App\Models\DrinkSmallStoreDetail;
use App\Models\F\FTable;
use Carbon\Carbon;
use Validator;
use PDF;
use Excel;
use App\Exports\DrinkOrderClientExport;

class FDrinkOrderController extends Controller
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


    public function listAll()
    {
        if (is_null($this->user) || !$this->user->can('f_drink_order.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any order !');
        }

        $orders = FDrinkOrder::orderBy('id','desc')->take(3000)->get();
        
        return view('backend.pages.f.drink_order.list_all', compact('orders'));
    }

    public function index($table_id)
    {
        if (is_null($this->user) || !$this->user->can('f_drink_order.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any order !');
        }

        $orders = FDrinkOrder::where('table_id',$table_id)->where('status','!=',-1)->take(50)->orderBy('id','desc')->get();
        $table = FTable::where('id',$table_id)->first();
        $table_id = $table->id;

        $in_pending = count(FDrinkOrderDetail::where('table_id',$table_id)->where('status','!=',3)->where('status','!=',2)->where('status','!=',-1)->where('status','!=',0)->get());
        
        return view('backend.pages.f.drink_order.index', compact('orders','table_id','table','in_pending'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($table_id)
    {
        if (is_null($this->user) || !$this->user->can('f_drink_order.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any order !');
        }

        $articles  = Drink::where('selling_price','>',0)->orderBy('name','asc')->get();
        $employes  = Employe::orderBy('name','asc')->get();
        $table = FTable::where('id',$table_id)->first();
        $table_id = $table->id;
        return view('backend.pages.f.drink_order.create', compact('articles','employes','table_id','table'));
    }

    public function choose()
    {
        
        return view('backend.pages.f.drink_order.choose');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('f_drink_order.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any order !');
        }

        $rules = array(
                'drink_id.*'  => 'required',
                'employe_id'  => 'required',
                'quantity.*'  => 'required',
                'table_id'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            try {DB::beginTransaction();

            $drink_id = $request->drink_id;
            $date = Carbon::now();
            $quantity = $request->quantity;
            $table_id = $request->table_id;
            $employe_id = $request->employe_id;
            $description =$request->description; 
            $status = 0; 
            $created_by = $this->user->name;

            $waiter_on_table = FTable::where('id',$table_id)->value('waiter_name');

            $waiter_name = Employe::where('id',$employe_id)->value('name');

            if (!empty($waiter_on_table) && $waiter_on_table == $waiter_name) {
                $employe_id = $request->employe_id;
            }elseif (empty($waiter_on_table) && $waiter_name == $created_by) {
                $employe_id = $request->employe_id;
            }else{
                session()->flash('error', 'Tu n\'es pas '.$waiter_name.', veuillez utiliser votre compte ou bien choisir une autre table s\'il vous plait!!');
                return back();
            }

            $order_no = date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6); 

            $order_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$order_no;

            //create order
            $order = new FDrinkOrder();
            $order->date = $date;
            $order->order_no = $order_no;
            $order->order_signature = $order_signature;
            $order->employe_id = $employe_id;
            $order->created_by = $created_by;
            $order->description = $description;
            $order->table_id = $table_id;
            $order->status = $status;
            $order->created_at = Carbon::now();
            $order->save();

            //insert details of order No.
            for( $count = 0; $count < count($drink_id); $count++ ){

                $selling_price = Drink::where('id', $drink_id[$count])->value('selling_price');
                $total_amount_selling = $quantity[$count] * $selling_price;
                $data = array(
                    'drink_id' => $drink_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'selling_price' => $selling_price,
                    'table_id' => $table_id,
                    'description' => $description,
                    'total_amount_selling' => $total_amount_selling,
                    'created_by' => $created_by,
                    'order_no' => $order_no,
                    'status' => $status,
                    'order_signature' => $order_signature,
                    'employe_id' => $employe_id,
                    'created_at'=> Carbon::now(),
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

            FDrinkOrderDetail::insert($insert_data);

            $waiter_name = Employe::where('id',$employe_id)->value('name');
            $total_amount = DB::table('f_drink_order_details')
            ->where('order_no',$order_no)->sum('total_amount_selling');
            $total_amount_paying = FTable::where('id',$table_id)->value('total_amount_paying');
            $table = FTable::where('id',$table_id)->first();
            $table->date = Carbon::parse(Carbon::now());
            $table->opening_date = Carbon::parse(Carbon::now());
            $table->etat = 1;
            $table->total_amount_paying = $total_amount_paying + $total_amount;
            $table->waiter_name = $waiter_name;
            $table->save();

            DB::commit();
            session()->flash('success', 'Order has been sent successfuly!!');
            return redirect()->route('admin.f-drink-orders.index',$table_id);
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
         $code = FDrinkOrder::where('order_no', $order_no)->value('order_no');
         $orderDetails = FDrinkOrderDetail::where('order_no', $order_no)->get();
         return view('backend.pages.f.drink_order.show', compact('orderDetails','order_no'));
    }

    public function voirCommandeRejeter($order_no)
    {
        if (is_null($this->user) || !$this->user->can('f_drink_order.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any order !');
        }
        //
         $data = FDrinkOrder::where('order_no', $order_no)->first();
         $datas = FDrinkOrderDetail::where('order_no', $order_no)->get();
         $articles  = Drink::where('selling_price','>',0)->orderBy('name','asc')->get();
         $employes  = Employe::orderBy('name','asc')->get();
         return view('backend.pages.f.drink_order.reject', compact('datas','data','articles','employes'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($order_no)
    {
        if (is_null($this->user) || !$this->user->can('f_drink_order.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any order !');
        }

        $articles  = Drink::orderBy('name','asc')->get();
        $employes  = Employe::orderBy('name','asc')->get();
        $data = FDrinkOrder::where('order_no',$order_no)->first();
        $datas = FDrinkOrderDetail::where('order_no',$order_no)->get();
        return view('backend.pages.f.drink_order.edit', compact('articles','employes','data','datas'));
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
        if (is_null($this->user) || !$this->user->can('f_drink_order.edit')) {
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

            try {DB::beginTransaction();

            $drink_id = $request->drink_id;
            $date = $request->date;
            $quantity = $request->quantity;
            $table_no = $request->table_no;
            $employe_id = $request->employe_id;
            $description =$request->description; 
            $status = -3; 

            //create order
            $order = FDrinkOrder::where('order_no',$order_no)->first();
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

        FDrinkOrderDetail::where('order_no',$order_no)->delete();

        FDrinkOrderDetail::insert($insert_data);

        DB::commit();
            session()->flash('success', 'Order has been updated successfuly!!');
            return redirect()->route('admin.f-drink-orders.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

    public function validateCommand($order_no)
    {
       if (is_null($this->user) || !$this->user->can('f_drink_order.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any order !');
        }

        try {DB::beginTransaction();

            FDrinkOrder::where('order_no', '=', $order_no)
                ->update(['status' => 1]);
            FDrinkOrderDetail::where('order_no', '=', $order_no)
                ->update(['status' => 1]);

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

    public function reject(Request $request,$order_no)
    {
       if (is_null($this->user) || !$this->user->can('f_drink_order.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any order !');
        }

        $request->validate([
            'rej_motif' => 'required|min:10|max:490',
            'table_id' => 'required'
        ]);

        try {DB::beginTransaction();

        $table_id = $request->table_id;

        $rej_motif = $request->rej_motif;

        $total_amount_selling = DB::table('f_drink_order_details')
            ->where('order_no', '=', $order_no)
            ->sum('total_amount_selling');

        $total_amount_paying = DB::table('f_tables')
            ->where('id', '=', $table_id)
            ->sum('total_amount_paying');

        FDrinkOrder::where('order_no', '=', $order_no)
                ->update(['status' => -1,'rej_motif' => $rej_motif,'rejected_by' => $this->user->name]);
        FDrinkOrderDetail::where('order_no', '=', $order_no)
                ->update(['status' => -1,'rej_motif' => $rej_motif,'rejected_by' => $this->user->name]);

        $in_pending = count(FDrinkOrderDetail::where('table_id',$table_id)->where('status','!=',3)->where('status','!=',-1)->get());

        if ($in_pending < 1 && $total_amount_selling >= $total_amount_paying) {
            FTable::where('id',$table_id)->update(['etat' => 0,'waiter_name' => '','opened_by' => '','total_amount_paying' => 0]);
        }else {
            $total_amount_remaining = $total_amount_paying - $total_amount_selling;
            FTable::where('id',$table_id)->update(['total_amount_paying' => $total_amount_remaining]);
        }
        
        DB::commit();
            session()->flash('success', 'Order has been rejected !!');
            return redirect()->route('admin.f-drink-orders.index',$table_id);
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

    public function reset($order_no)
    {
       if (is_null($this->user) || !$this->user->can('f_drink_order.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any order !');
        }

        try {DB::beginTransaction();

        FDrinkOrder::where('order_no', '=', $order_no)
                ->update(['status' => -2]);
        FDrinkOrderDetail::where('order_no', '=', $order_no)
                ->update(['status' => -2]);

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

    public function htmlPdf($order_no)
    {
        if (is_null($this->user) || !$this->user->can('f_drink_order.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        try {DB::beginTransaction();

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $stat = FDrinkOrder::where('order_no', $order_no)->value('status');
        $description = FDrinkOrder::where('order_no', $order_no)->value('description');
        $order_signature = FDrinkOrder::where('order_no', $order_no)->value('order_signature');
        $date = FDrinkOrder::where('order_no', $order_no)->value('created_at');
        $data = FDrinkOrder::where('order_no', $order_no)->first();
        $totalValue = DB::table('f_drink_order_details')
            ->where('order_no', '=', $order_no)
            ->sum('total_amount_selling');

        if($stat == 1 || $stat == 2 || $stat == 3 || $stat == -3){
           $order_no = FDrinkOrder::where('order_no', $order_no)->value('order_no');

           $datas = FDrinkOrderDetail::where('order_no', $order_no)->get();
           
           $pdf = PDF::loadView('backend.pages.f.document.drink_order_client',compact('datas','order_no','setting','description','order_signature','date','totalValue','data'))->setPaper('a6', 'portrait');

           Storage::put('public/f/commande_boisson/'.$order_no.'.pdf', $pdf->output());
            
           FDrinkOrder::where('order_no', '=', $order_no)
                ->update(['flag' => 1]);
            FDrinkOrderDetail::where('order_no', '=', $order_no)
                ->update(['flag' => 1]);
            
           // download pdf file
            DB::commit();
           //return $pdf->download('COMMANDE_'.$order_no.'.pdf'); 

            return view('backend.pages.f.document.drink_order_client',compact('datas','order_no','setting','description','order_signature','date','totalValue','data'));
           
        }else if ($stat == -1) {
            session()->flash('error', 'Order has been rejected !!');
            return back();
        }else{
            session()->flash('error', 'wait until order will be validated !!');
            return back();
        }

        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
        
    }

    public function exportToExcel(Request $request)
    {
        return Excel::download(new DrinkOrderClientExport, 'RAPPORT DES COMMANDES CLIENTS AUX BOISSONS.xlsx');
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($order_no)
    {
        if (is_null($this->user) || !$this->user->can('f_drink_order.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any order !');
        }
        /*

        try {DB::beginTransaction();

        $order = FDrinkOrder::where('order_no',$order_no)->first();
        if (!is_null($order)) {
            $order->delete();
            FDrinkOrderDetail::where('order_no',$order_no)->delete();
        }

        DB::commit();
            session()->flash('success', 'Order has been deleted !!');
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

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
use App\Models\DrinkPurchase;
use App\Models\DrinkPurchaseDetail;
use App\Models\DrinkSupplierOrder;
use App\Models\Supplier;
use Validator;
use PDF;
use Mail;
use Carbon\Carbon;

class DrinkPurchaseController extends Controller
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

        $purchases = DrinkPurchase::orderBy('id','desc')->take(200)->get();
        return view('backend.pages.drink_purchase.index', compact('purchases'));
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
        $suppliers = Supplier::orderBy('supplier_name','asc')->get();
        return view('backend.pages.drink_purchase.create', compact('drinks','suppliers'));
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
                'date'  => 'required',
                'quantity.*'  => 'required',
                'price.*'  => 'required',
                'vat_supplier_payer'  => 'required',
                //'vat_rate'  => 'required',
                //'invoice_currency'  => 'required',
                'supplier_id'  => 'required',
                'description'  => 'required|max:490'
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
            $price = $request->price;
            $vat_supplier_payer = $request->vat_supplier_payer;
            $vat_rate = $request->vat_rate;
            $invoice_currency = $request->invoice_currency;
            $supplier_id = $request->supplier_id;
            $description =$request->description;

            if (!empty($vat_rate)) {
                $vat_rate = $vat_rate;
            }else{
                $vat_rate = 0;
            }

            $latest = DrinkPurchase::orderBy('id','desc')->first();
            if ($latest) {
               $purchase_no = 'BDA' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $purchase_no = 'BDA' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $purchase_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$purchase_no;
            $created_by = $this->user->name;

            //create purchase
            $purchase = new DrinkPurchase();
            $purchase->date = $date;
            $purchase->purchase_signature = $purchase_signature;
            $purchase->purchase_no = $purchase_no;
            $purchase->vat_supplier_payer = $vat_supplier_payer;
            $purchase->vat_rate = $vat_rate;
            $purchase->invoice_currency = $invoice_currency;
            $purchase->supplier_id = $supplier_id;
            $purchase->created_by = $created_by;
            $purchase->description = $description;
            $purchase->created_at = \Carbon\Carbon::now();
            $purchase->save();
            //insert details of purchase No.
            for( $count = 0; $count < count($drink_id); $count++ ){

                if($vat_supplier_payer == 1){
                    $price_nvat = ($price[$count]*$quantity[$count]);
                    $vat = ($price_nvat* $vat_rate)/100;
                    $price_wvat = $price_nvat + $vat;

                }else{
                    $price_nvat = ($price[$count]*$quantity[$count]);
                    $vat = 0;
                    $price_wvat = $price_nvat + $vat;
                }

                $purchase_price = Drink::where('id', $drink_id[$count])->value('purchase_price');
                $total_value = $price_wvat;
                $data = array(
                    'drink_id' => $drink_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'vat_supplier_payer' => $vat_supplier_payer,
                    'vat_rate' => $vat_rate,
                    'invoice_currency' => $invoice_currency,
                    'supplier_id' => $supplier_id,
                    'purchase_price' => $purchase_price,
                    'price' => $price[$count],
                    'description' => $description,
                    'price_nvat' => $price_nvat,
                    'vat' => $vat,
                    'price_wvat' => $price_wvat,
                    'total_value' => $total_value,
                    'created_by' => $created_by,
                    'purchase_no' => $purchase_no,
                    'purchase_signature' => $purchase_signature,
                    'created_at' => \Carbon\Carbon::now()
                );
                $insert_data[] = $data;
            }
            /*
            $mail = Supplier::where('id', $supplier_id)->value('mail');
            $name = Supplier::where('id', $supplier_id)->value('name');

            $mailData = [
                    'title' => 'COMMANDE',
                    'purchase_no' => $purchase_no,
                    'name' => $name,
                    //'body' => 'This is for testing email using smtp.'
                    ];
         
        Mail::to($mail)->send(new OrderMail($mailData));
        */

            DrinkPurchaseDetail::insert($insert_data);

        DB::commit();
            session()->flash('success', 'Purchase has been created !!');
            return redirect()->route('admin.drink-purchases.index');
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
    public function show($purchase_no)
    {
        //
         $code = DrinkPurchase::where('purchase_no', $purchase_no)->value('purchase_no');
         $purchases = DrinkPurchaseDetail::where('purchase_no', $purchase_no)->get();
         return view('backend.pages.drink_purchase.show', compact('purchases','code'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $purchase_no
     * @return \Illuminate\Http\Response
     */
    public function edit($purchase_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_purchase.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any purchase !');
        }

        $drinks  = Drink::orderBy('name','asc')->get();
        $suppliers = Supplier::orderBy('supplier_name','asc')->get();
        $data = DrinkPurchase::where('purchase_no', $purchase_no)->first();
        $datas = DrinkPurchaseDetail::where('purchase_no', $purchase_no)->get();

        return view('backend.pages.drink_purchase.edit', compact('datas','data','drinks','suppliers'));

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
        if (is_null($this->user) || !$this->user->can('drink_purchase.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any purchase !');
        }

       $rules = array(
                'drink_id.*'  => 'required',
                'date'  => 'required',
                'quantity.*'  => 'required',
                'price.*'  => 'required',
                'vat_supplier_payer'  => 'required',
                //'vat_rate'  => 'required',
                //'invoice_currency'  => 'required',
                'supplier_id'  => 'required',
                'description'  => 'required|max:490'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            try {DB::beginTransaction();

            $order_no = DrinkSupplierOrder::where('purchase_no',$purchase_no)->value('order_no');

            if (!empty($order_no)) {
                $status = -3;
            }else{
                $status = 3;
            }

            $drink_id = $request->drink_id;
            $date = $request->date;
            $quantity = $request->quantity;
            $price = $request->price;
            $vat_supplier_payer = $request->vat_supplier_payer;
            $vat_rate = $request->vat_rate;
            $invoice_currency = $request->invoice_currency;
            $supplier_id = $request->supplier_id;
            $description =$request->description;

            if (!empty($vat_rate)) {
                $vat_rate = $vat_rate;
            }else{
                $vat_rate = 0;
            }

            $created_by = $this->user->name;

            $purchase = DrinkPurchase::where('purchase_no',$purchase_no)->first();
            $purchase->date = $date;
            $purchase->vat_supplier_payer = $vat_supplier_payer;
            $purchase->vat_rate = $vat_rate;
            $purchase->invoice_currency = $invoice_currency;
            $purchase->supplier_id = $supplier_id;
            $purchase->created_by = $created_by;
            $purchase->description = $description;
            $purchase->status = $status;
            $purchase->save();
            //insert details of purchase No.
            for( $count = 0; $count < count($drink_id); $count++ ){

                if($vat_supplier_payer == 1){
                    $price_nvat = ($price[$count]*$quantity[$count]);
                    $vat = ($price_nvat* $vat_rate)/100;
                    $price_wvat = $price_nvat + $vat;

                }else{
                    $price_nvat = ($price[$count]*$quantity[$count]);
                    $vat = 0;
                    $price_wvat = $price_nvat + $vat;
                }

                $purchase_signature = DrinkPurchase::where('purchase_no',$purchase_no)->value('purchase_signature');

                $purchase_price = Drink::where('id', $drink_id[$count])->value('purchase_price');
                $total_value = $price_wvat;
                $data = array(
                    'drink_id' => $drink_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'vat_supplier_payer' => $vat_supplier_payer,
                    'vat_rate' => $vat_rate,
                    'invoice_currency' => $invoice_currency,
                    'supplier_id' => $supplier_id,
                    'purchase_price' => $purchase_price,
                    'price' => $price[$count],
                    'description' => $description,
                    'price_nvat' => $price_nvat,
                    'vat' => $vat,
                    'price_wvat' => $price_wvat,
                    'total_value' => $total_value,
                    'created_by' => $created_by,
                    'purchase_no' => $purchase_no,
                    'purchase_signature' => $purchase_signature,
                    'status' => $status,
                    'created_at' => \Carbon\Carbon::now()
                );
                $insert_data[] = $data;
            }

            DrinkPurchaseDetail::where('purchase_no',$purchase_no)->delete();

            DrinkPurchaseDetail::insert($insert_data);

            DB::commit();
            session()->flash('success', 'Plan has been updated successfuly !!');
            return redirect()->route('admin.drink-purchases.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function validatePurchase($purchase_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_purchase.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any purchase !');
        }

            try {DB::beginTransaction();
            DrinkPurchase::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            DrinkPurchaseDetail::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

                DB::commit();
            session()->flash('success', 'purchase has been validated !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function reject($purchase_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_purchase.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any purchase !');
        }

        try {DB::beginTransaction();

        DrinkPurchase::where('purchase_no', '=', $purchase_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
            DrinkPurchaseDetail::where('purchase_no', '=', $purchase_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

                DB::commit();
            session()->flash('success', 'Plan has been rejected successfuly !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function reset($purchase_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_purchase.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any purchase !');
        }

        try {DB::beginTransaction();

        DrinkPurchase::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
            DrinkPurchaseDetail::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

                DB::commit();
            session()->flash('success', 'DrinkPurchase has been reseted !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function confirm($purchase_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_purchase.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any purchase !');
        }

        try {DB::beginTransaction();

            DrinkPurchase::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            DrinkPurchaseDetail::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

            DB::commit();
            session()->flash('success', 'Purchase has been confirmed !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function approuve($purchase_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_purchase.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any purchase !');
        }


        try {DB::beginTransaction();

            DrinkPurchase::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);
            DrinkPurchaseDetail::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);

            DB::commit();
            session()->flash('success', 'Purchase has been confirmed !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function drinkPurchase($purchase_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_purchase.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $stat = DrinkPurchase::where('purchase_no', $purchase_no)->value('status');
        $description = DrinkPurchase::where('purchase_no', $purchase_no)->value('description');
        $date = DrinkPurchase::where('purchase_no', $purchase_no)->value('date');
        $data = DrinkPurchase::where('purchase_no', $purchase_no)->first();
        if($stat == 2 || $stat == 3 || $stat == 4 || $stat == 5 || $stat == 6 || $stat == -3){
           $purchase_no = DrinkPurchase::where('purchase_no', $purchase_no)->value('purchase_no');
           $purchase_signature = DrinkPurchase::where('purchase_no', $purchase_no)->value('purchase_signature');
           $totalValue = DB::table('drink_purchase_details')
            ->where('purchase_no', '=', $purchase_no)
            ->sum('total_value');
            $price_nvat = DB::table('drink_purchase_details')
            ->where('purchase_no', '=', $purchase_no)
            ->sum('price_nvat');
            $vat = DB::table('drink_purchase_details')
            ->where('purchase_no', '=', $purchase_no)
            ->sum('vat');

           $datas = DrinkPurchaseDetail::where('purchase_no', $purchase_no)->get();
           $pdf = PDF::loadView('backend.pages.document.drink_purchase',compact('datas','purchase_no','setting','description','date','purchase_signature','totalValue','price_nvat','vat','data'));

           Storage::put('public/pdf/drink_purchase/'.'FICHE_DEMANDE_ACHAT_'.$purchase_no.'.pdf', $pdf->output());

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
        if (is_null($this->user) || !$this->user->can('drink_purchase.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any purchase !');
        }
        /*
        try {DB::beginTransaction();

        $purchase = DrinkPurchase::where('purchase_no',$purchase_no)->first();
        if (!is_null($purchase)) {
            $purchase->delete();
            DrinkPurchaseDetail::where('purchase_no',$purchase_no)->delete();
        }

        DB::commit();
            session()->flash('success', 'Purchase has been deleted !!');
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

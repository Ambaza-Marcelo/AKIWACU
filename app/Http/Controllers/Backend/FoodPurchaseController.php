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
use App\Models\Food;
use App\Models\FoodPurchase;
use App\Models\FoodPurchaseDetail;
use Validator;
use PDF;
use Mail;
use Carbon\Carbon;

class FoodPurchaseController extends Controller
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
        if (is_null($this->user) || !$this->user->can('food_purchase.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any purchase !');
        }

        $purchases = FoodPurchase::orderBy('id','desc')->take(200)->get();
        return view('backend.pages.food_purchase.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('food_purchase.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any purchase !');
        }

        $foods  = Food::orderBy('name','asc')->get();
        return view('backend.pages.food_purchase.create', compact('foods'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('food_purchase.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any purchase !');
        }

        $rules = array(
                'food_id.*'  => 'required',
                'date'  => 'required',
                'quantity.*'  => 'required',
                'price.*'  => 'required',
                'unit.*'  => 'required',
                'description'  => 'required|max:490'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            try {DB::beginTransaction();

            $food_id = $request->food_id;
            $date = $request->date;
            $quantity = $request->quantity;
            $price = $request->price;
            $unit = $request->unit;
            $description =$request->description; 
            $latest = FoodPurchase::latest()->first();
            if ($latest) {
               $purchase_no = 'BDA' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $purchase_no = 'BDA' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $purchase_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$purchase_no;
            $created_by = $this->user->name;

            //create purchase
            $purchase = new FoodPurchase();
            $purchase->date = $date;
            $purchase->purchase_signature = $purchase_signature;
            $purchase->purchase_no = $purchase_no;
            $purchase->created_by = $created_by;
            $purchase->description = $description;
            $purchase->created_at = \Carbon\Carbon::now();
            $purchase->save();
            //insert details of purchase No.
            for( $count = 0; $count < count($food_id); $count++ ){

                //$price = Food::where('id', $food_id[$count])->value('purchase_price');
                $total_value = $quantity[$count] * $price[$count];
                $data = array(
                    'food_id' => $food_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'unit' => $unit[$count],
                    'price' => $price[$count],
                    'description' => $description,
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

            FoodPurchaseDetail::insert($insert_data);

            DB::commit();
            session()->flash('success', 'Purchase has been created !!');
            return redirect()->route('admin.food-purchases.index');
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
         $code = FoodPurchase::where('purchase_no', $purchase_no)->value('purchase_no');
         $purchases = FoodPurchaseDetail::where('purchase_no', $purchase_no)->get();
         return view('backend.pages.food_purchase.show', compact('purchases','code'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $purchase_no
     * @return \Illuminate\Http\Response
     */
    public function edit($purchase_no)
    {
        if (is_null($this->user) || !$this->user->can('food_purchase.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any purchase !');
        }

        $foods  = Food::orderBy('name','asc')->get();

        $data = FoodPurchase::where('purchase_no', $purchase_no)->first();
        $datas = FoodPurchaseDetail::where('purchase_no', $purchase_no)->get();

        return view('backend.pages.food_purchase.edit', compact('datas','data','foods'));

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
        if (is_null($this->user) || !$this->user->can('food_purchase.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any purchase !');
        }

       $rules = array(
                'food_id.*'  => 'required',
                'date'  => 'required',
                'quantity.*'  => 'required',
                'price.*'  => 'required',
                'unit.*'  => 'required',
                'description'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            try {DB::beginTransaction();

            $food_id = $request->food_id;
            $date = $request->date;
            $quantity = $request->quantity;
            $price = $request->price;
            $unit = $request->unit;
            $description =$request->description; 

            $purchase = FoodPurchase::where('purchase_no',$purchase_no)->first();
            $purchase->date = $date;
            $purchase->description = $description;
            $purchase->save();
            //insert details of purchase No.
            for( $count = 0; $count < count($food_id); $count++ ){

                $created_by = $this->user->name;
                $purchase_signature = FoodPurchase::where('purchase_no',$purchase_no)->value('purchase_signature');
                //$price = Food::where('id', $food_id[$count])->value('price');
                $total_value = $quantity[$count] * $price[$count];
                $data = array(
                    'food_id' => $food_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'unit' => $unit[$count],
                    'price' => $price[$count],
                    'description' => $description,
                    'total_value' => $total_value,
                    'created_by' => $created_by,
                    'purchase_no' => $purchase_no,
                    'purchase_signature' => $purchase_signature,
                );
                $insert_data[] = $data;
            }

            FoodPurchaseDetail::where('purchase_no',$purchase_no)->delete();

            FoodPurchaseDetail::insert($insert_data);

            DB::commit();
            session()->flash('success', 'Purchase has been updated successfuly !!');
            return redirect()->route('admin.food-purchases.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function validatePurchase($purchase_no)
    {
       if (is_null($this->user) || !$this->user->can('food_purchase.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any purchase !');
        }

        try {DB::beginTransaction();

            FoodPurchase::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            FoodPurchaseDetail::where('purchase_no', '=', $purchase_no)
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
       if (is_null($this->user) || !$this->user->can('food_purchase.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any purchase !');
        }

        try {DB::beginTransaction();

        FoodPurchase::where('purchase_no', '=', $purchase_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
            FoodPurchaseDetail::where('purchase_no', '=', $purchase_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'Purchase has been rejected !!');
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
       if (is_null($this->user) || !$this->user->can('food_purchase.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any purchase !');
        }


        try {DB::beginTransaction();

        FoodPurchase::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
            FoodPurchaseDetail::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'FoodPurchase has been reseted !!');
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
       if (is_null($this->user) || !$this->user->can('food_purchase.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any purchase !');
        }

        try {DB::beginTransaction();

        FoodPurchase::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            FoodPurchaseDetail::where('purchase_no', '=', $purchase_no)
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
       if (is_null($this->user) || !$this->user->can('food_purchase.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any purchase !');
        }

        try {DB::beginTransaction();

        FoodPurchase::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);
            FoodPurchaseDetail::where('purchase_no', '=', $purchase_no)
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

    public function foodPurchase($purchase_no)
    {
        if (is_null($this->user) || !$this->user->can('food_purchase.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $stat = FoodPurchase::where('purchase_no', $purchase_no)->value('status');
        $description = FoodPurchase::where('purchase_no', $purchase_no)->value('description');
        $date = FoodPurchase::where('purchase_no', $purchase_no)->value('date');
        if($stat == 2 && $stat == 3 || $stat == 4 || $stat == 5){
           $purchase_no = FoodPurchase::where('purchase_no', $purchase_no)->value('purchase_no');
           $purchase_signature = FoodPurchase::where('purchase_no', $purchase_no)->value('purchase_signature');
           $totalValue = DB::table('food_purchase_details')
            ->where('purchase_no', '=', $purchase_no)
            ->sum('total_value');

           $datas = FoodPurchaseDetail::where('purchase_no', $purchase_no)->get();
           $pdf = PDF::loadView('backend.pages.document.food_purchase',compact('datas','purchase_no','setting','description','date','purchase_signature','totalValue'));

           Storage::put('public/pdf/food_purchase/'.'FICHE_DEMANDE_ACHAT_'.$purchase_no.'.pdf', $pdf->output());

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
        if (is_null($this->user) || !$this->user->can('food_purchase.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any purchase !');
        }

        try {DB::beginTransaction();

        $purchase = FoodPurchase::where('purchase_no',$purchase_no)->first();
        if (!is_null($purchase)) {
            $purchase->delete();
            FoodPurchaseDetail::where('purchase_no',$purchase_no)->delete();
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

    }
}

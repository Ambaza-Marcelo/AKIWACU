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
use App\Models\MaterialPurchase;
use App\Models\MaterialPurchaseDetail;
use App\Exports\MaterialPurchaseExport;
use Validator;
use PDF;
use Mail;
use Excel;
use Carbon\Carbon;

class MaterialPurchaseController extends Controller
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
            abort(403, 'Sorry !! You are Unauthorized to view any purchase !');
        }

        $purchases = MaterialPurchase::orderBy('id','desc')->take(200)->get();
        return view('backend.pages.material_purchase.index', compact('purchases'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('material_purchase.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any purchase !');
        }

        $materials  = Material::orderBy('name','asc')->get();
        return view('backend.pages.material_purchase.create', compact('materials'));
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
            abort(403, 'Sorry !! You are Unauthorized to create any purchase !');
        }

        $rules = array(
                'material_id.*'  => 'required',
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

            $material_id = $request->material_id;
            $date = $request->date;
            $quantity = $request->quantity;
            $price = $request->price;
            $unit = $request->unit;
            $description =$request->description; 
            $latest = MaterialPurchase::latest()->first();
            if ($latest) {
               $purchase_no = 'BDA' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $purchase_no = 'BDA' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $purchase_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$purchase_no;
            $created_by = $this->user->name;

            //create purchase
            $purchase = new MaterialPurchase();
            $purchase->date = $date;
            $purchase->purchase_signature = $purchase_signature;
            $purchase->purchase_no = $purchase_no;
            $purchase->created_by = $created_by;
            $purchase->description = $description;
            $purchase->created_at = \Carbon\Carbon::now();
            $purchase->save();
            //insert details of purchase No.
            for( $count = 0; $count < count($material_id); $count++ ){

                //$price = Material::where('id', $material_id[$count])->value('purchase_price');
                $total_value = $quantity[$count] * $price[$count];
                $data = array(
                    'material_id' => $material_id[$count],
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

            MaterialPurchaseDetail::insert($insert_data);

            DB::commit();
            session()->flash('success', 'Material has been created !!');
            return redirect()->route('admin.material-purchases.index');
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
         $code = MaterialPurchase::where('purchase_no', $purchase_no)->value('purchase_no');
         $purchases = MaterialPurchaseDetail::where('purchase_no', $purchase_no)->get();
         return view('backend.pages.material_purchase.show', compact('purchases','code'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $purchase_no
     * @return \Illuminate\Http\Response
     */
    public function edit($purchase_no)
    {
        if (is_null($this->user) || !$this->user->can('material_purchase.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any purchase !');
        }

        $materials  = Material::orderBy('name','asc')->get();

        $data = MaterialPurchase::where('purchase_no', $purchase_no)->first();
        $datas = MaterialPurchaseDetail::where('purchase_no', $purchase_no)->get();

        return view('backend.pages.material_purchase.edit', compact('datas','data','materials'));

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
        if (is_null($this->user) || !$this->user->can('material_purchase.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any purchase !');
        }

       $rules = array(
                'material_id.*'  => 'required',
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

            $material_id = $request->material_id;
            $date = $request->date;
            $quantity = $request->quantity;
            $price = $request->price;
            $unit = $request->unit;
            $description =$request->description; 

            $purchase = MaterialPurchase::where('purchase_no',$purchase_no)->first();
            $purchase->date = $date;
            $purchase->description = $description;
            $purchase->save();
            //insert details of purchase No.
            for( $count = 0; $count < count($material_id); $count++ ){

                $created_by = $this->user->name;
                $purchase_signature = MaterialPurchase::where('purchase_no',$purchase_no)->value('purchase_signature');
                //$price = Material::where('id', $material_id[$count])->value('price');
                $total_value = $quantity[$count] * $price[$count];
                $data = array(
                    'material_id' => $material_id[$count],
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

            MaterialPurchaseDetail::where('purchase_no',$purchase_no)->delete();

            MaterialPurchaseDetail::insert($insert_data);


        DB::commit();
            session()->flash('success', 'Purchase has been updated successfuly !!');
            return redirect()->route('admin.material-purchases.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function validatePurchase($purchase_no)
    {
       if (is_null($this->user) || !$this->user->can('material_purchase.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any purchase !');
        }

        try {DB::beginTransaction();

            MaterialPurchase::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            MaterialPurchaseDetail::where('purchase_no', '=', $purchase_no)
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
       if (is_null($this->user) || !$this->user->can('material_purchase.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any purchase !');
        }

        try {DB::beginTransaction();

        MaterialPurchase::where('purchase_no', '=', $purchase_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
            MaterialPurchaseDetail::where('purchase_no', '=', $purchase_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'Material has been rejected !!');
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
       if (is_null($this->user) || !$this->user->can('material_purchase.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any purchase !');
        }

        try {DB::beginTransaction();

        MaterialPurchase::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
            MaterialPurchaseDetail::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'MaterialPurchase has been reseted !!');
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
       if (is_null($this->user) || !$this->user->can('material_purchase.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any purchase !');
        }

        try {DB::beginTransaction();

        MaterialPurchase::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            MaterialPurchaseDetail::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'Material has been confirmed !!');
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
       if (is_null($this->user) || !$this->user->can('material_purchase.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any purchase !');
        }

        try {DB::beginTransaction();

        MaterialPurchase::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);
            MaterialPurchaseDetail::where('purchase_no', '=', $purchase_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'Material has been confirmed !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function materialPurchase($purchase_no)
    {
        if (is_null($this->user) || !$this->user->can('material_purchase.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $stat = MaterialPurchase::where('purchase_no', $purchase_no)->value('status');
        $description = MaterialPurchase::where('purchase_no', $purchase_no)->value('description');
        $date = MaterialPurchase::where('purchase_no', $purchase_no)->value('date');
        if($stat == 2 && $stat == 3 || $stat == 4 || $stat == 5){
           $purchase_no = MaterialPurchase::where('purchase_no', $purchase_no)->value('purchase_no');
           $purchase_signature = MaterialPurchase::where('purchase_no', $purchase_no)->value('purchase_signature');
           $totalValue = DB::table('material_purchase_details')
            ->where('purchase_no', '=', $purchase_no)
            ->sum('total_value');

           $datas = MaterialPurchaseDetail::where('purchase_no', $purchase_no)->get();
           $pdf = PDF::loadView('backend.pages.document.material_purchase',compact('datas','purchase_no','setting','description','date','purchase_signature','totalValue'));

           Storage::put('public/pdf/material_purchase/'.'FICHE_DEMANDE_ACHAT_'.$purchase_no.'.pdf', $pdf->output());

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

    public function exportToExcel(Request $request)
    {
        return Excel::download(new MaterialPurchaseExport, 'RAPPORT_DEMANDE_ACHAT.xlsx');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($purchase_no)
    {
        if (is_null($this->user) || !$this->user->can('material_purchase.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any purchase !');
        }

        try {DB::beginTransaction();

        $purchase = MaterialPurchase::where('purchase_no',$purchase_no)->first();
        if (!is_null($purchase)) {
            $purchase->delete();
            MaterialPurchaseDetail::where('purchase_no',$purchase_no)->delete();
        }

        DB::commit();
            session()->flash('success', 'Material has been deleted !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }
}

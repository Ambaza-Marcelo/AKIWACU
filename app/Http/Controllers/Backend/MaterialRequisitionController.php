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
use App\Models\MaterialRequisition;
use App\Models\MaterialRequisitionDetail;
use Validator;
use PDF;
use Mail;
use Carbon\Carbon;
use App\Mail\OrderMail;

class MaterialRequisitionController extends Controller
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
        if (is_null($this->user) || !$this->user->can('material_requisition.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any requisition !');
        }

        $requisitions = MaterialRequisition::all();
        return view('backend.pages.material_requisition.index', compact('requisitions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('material_requisition.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any requisition !');
        }

        $materials  = Material::orderBy('name','asc')->get();
        return view('backend.pages.material_requisition.create', compact('materials'));
    }

    public function createFromBig()
    {
        if (is_null($this->user) || !$this->user->can('material_requisition.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any requisition !');
        }

        $materials  = Material::orderBy('name','asc')->get();
        return view('backend.pages.material_requisition.create_from_big', compact('materials'));
    }

    public function choose()
    {
        if (is_null($this->user) || !$this->user->can('material_requisition.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any requisition !');
        }

        return view('backend.pages.material_requisition.choose');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('material_requisition.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any requisition !');
        }

        $rules = array(
                'material_id.*'  => 'required',
                'date'  => 'required',
                'quantity_requisitioned.*'  => 'required',
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
            $quantity_requisitioned = $request->quantity_requisitioned;
            $type_store = $request->type_store;
            $unit = $request->unit;
            $description =$request->description; 
            $latest = MaterialRequisition::latest()->first();
            if ($latest) {
               $requisition_no = 'BR' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $requisition_no = 'BR' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $requisition_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$requisition_no;
            $created_by = $this->user->name;

            //create requisition
            $requisition = new MaterialRequisition();
            $requisition->date = $date;
            $requisition->requisition_signature = $requisition_signature;
            $requisition->requisition_no = $requisition_no;
            $requisition->type_store = $type_store;
            $requisition->created_by = $created_by;
            $requisition->description = $description;
            $requisition->created_at = \Carbon\Carbon::now();
            $requisition->save();
            //insert details of requisition No.
            for( $count = 0; $count < count($material_id); $count++ ){

                $price = Material::where('id', $material_id[$count])->value('purchase_price');
                $total_value_requisitioned = $quantity_requisitioned[$count] * $price;
                $data = array(
                    'material_id' => $material_id[$count],
                    'date' => $date,
                    'quantity_requisitioned' => $quantity_requisitioned[$count],
                    'unit' => $unit[$count],
                    'price' => $price,
                    'description' => $description,
                    'total_value_requisitioned' => $total_value_requisitioned,
                    'created_by' => $created_by,
                    'requisition_no' => $requisition_no,
                    'type_store' => $type_store,
                    'requisition_signature' => $requisition_signature,
                    'created_at' => \Carbon\Carbon::now()
                );
                $insert_data[] = $data;
            }
       
        MaterialRequisitionDetail::insert($insert_data);

        DB::commit();
            session()->flash('success', 'Requisition has been created !!');
            return redirect()->route('admin.material-requisitions.index');
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
    public function show($requisition_no)
    {
        //
         $code = MaterialRequisition::where('requisition_no', $requisition_no)->value('requisition_no');
         $requisitions = MaterialRequisitionDetail::where('requisition_no', $requisition_no)->get();
         return view('backend.pages.material_requisition.show', compact('requisitions','code'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $requisition_no
     * @return \Illuminate\Http\Response
     */
    public function edit($requisition_no)
    {
        if (is_null($this->user) || !$this->user->can('material_requisition.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any requisition !');
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $requisition_no
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $requisition_no)
    {
        if (is_null($this->user) || !$this->user->can('material_requisition.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any requisition !');
        }

        
    }

    public function validateRequisition($requisition_no)
    {
       if (is_null($this->user) || !$this->user->can('material_requisition.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any requisition !');
        }

        try {DB::beginTransaction();

            MaterialRequisition::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            MaterialRequisitionDetail::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'requisition has been validated !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function reject($requisition_no)
    {
       if (is_null($this->user) || !$this->user->can('material_requisition.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any requisition !');
        } 

        try {DB::beginTransaction();

            MaterialRequisition::where('requisition_no', '=', $requisition_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
            MaterialRequisitionDetail::where('requisition_no', '=', $requisition_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'Requisition has been rejected !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function reset($requisition_no)
    {
       if (is_null($this->user) || !$this->user->can('material_requisition.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any requisition !');
        }

        try {DB::beginTransaction();

        MaterialRequisition::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
            MaterialRequisitionDetail::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'MaterialRequisition has been reseted !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function confirm($requisition_no)
    {
       if (is_null($this->user) || !$this->user->can('material_requisition.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any requisition !');
        }

        try {DB::beginTransaction();

        MaterialRequisition::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            MaterialRequisitionDetail::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'Requisition has been confirmed !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function approuve($requisition_no)
    {
       if (is_null($this->user) || !$this->user->can('material_requisition.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any requisition !');
        }

        try {DB::beginTransaction();

        MaterialRequisition::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);
            MaterialRequisitionDetail::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'Requisition has been confirmed !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function demande_requisition($requisition_no)
    {
        if (is_null($this->user) || !$this->user->can('material_requisition.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $stat = MaterialRequisition::where('requisition_no', $requisition_no)->value('status');
        $description = MaterialRequisition::where('requisition_no', $requisition_no)->value('description');
        $date = MaterialRequisition::where('requisition_no', $requisition_no)->value('date');
        if($stat == 2 && $stat == 3 || $stat == 4 || $stat == 5){
           $requisition_no = MaterialRequisition::where('requisition_no', $requisition_no)->value('requisition_no');
           $requisition_signature = MaterialRequisition::where('requisition_no', $requisition_no)->value('requisition_signature');
           $totalValue = DB::table('material_requisition_details')
            ->where('requisition_no', '=', $requisition_no)
            ->sum('total_value_requisitioned');

           $datas = MaterialRequisitionDetail::where('requisition_no', $requisition_no)->get();
           $pdf = PDF::loadView('backend.pages.document.material_requisition',compact('datas','requisition_no','setting','description','date','requisition_signature','totalValue'));

           Storage::put('public/pdf/material_requisition/'.'BON_REQUISITION_'.$requisition_no.'.pdf', $pdf->output());

           // download pdf file
           return $pdf->download('BON_REQUISITION_'.$requisition_no.'.pdf'); 
           
        }else if ($stat == -1) {
            session()->flash('error', 'Requisition has been rejected !!');
            return back();
        }else{
            session()->flash('error', 'wait until requisition will be validated !!');
            return back();
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($requisition_no)
    {
        if (is_null($this->user) || !$this->user->can('material_requisition.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any requisition !');
        }

        try {DB:;beginTransaction();

        $requisition = MaterialRequisition::where('requisition_no',$requisition_no)->first();
        if (!is_null($requisition)) {
            $requisition->delete();
            MaterialRequisitionDetail::where('requisition_no',$requisition_no)->delete();
        }

        DB::commit();
            session()->flash('success', 'Requisition has been deleted !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }
}

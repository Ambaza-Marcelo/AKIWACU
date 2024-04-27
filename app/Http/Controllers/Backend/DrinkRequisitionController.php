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
use App\Models\DrinkBigStoreDetail;
use App\Models\DrinkSmallStoreDetail;
use App\Models\Drink;
use App\Models\DrinkRequisition;
use App\Models\DrinkRequisitionDetail;
use Validator;
use PDF;
use Mail;
use Carbon\Carbon;
use App\Mail\OrderMail;

class DrinkRequisitionController extends Controller
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
        if (is_null($this->user) || !$this->user->can('drink_requisition.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any requisition !');
        }

        $requisitions = DrinkRequisition::orderBy('id','desc')->take(200)->get();
        return view('backend.pages.drink_requisition.index', compact('requisitions'));
    }

    public function choose()
    {
        if (is_null($this->user) || !$this->user->can('drink_requisition.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any requisition !');
        }

        return view('backend.pages.drink_requisition.choose');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('drink_requisition.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any requisition !');
        }

        $drinks  = Drink::orderBy('name','asc')->get();
        return view('backend.pages.drink_requisition.create', compact('drinks'));
    }

    public function createFromBig()
    {
        if (is_null($this->user) || !$this->user->can('drink_requisition.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any requisition !');
        }

        $drinks  = Drink::orderBy('name','asc')->get();
        return view('backend.pages.drink_requisition.create_from_big', compact('drinks'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('drink_requisition.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any requisition !');
        }

        $rules = array(
                'drink_id.*'  => 'required',
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

            $drink_id = $request->drink_id;
            $date = $request->date;
            $type_store = $request->type_store;
            $quantity_requisitioned = $request->quantity_requisitioned;
            $unit = $request->unit;
            $description =$request->description; 
            $latest = DrinkRequisition::latest()->first();
            if ($latest) {
               $requisition_no = 'BR' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $requisition_no = 'BR' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $requisition_signature = "4001711615".Carbon::parse(Carbon::now())->format('YmdHis')."/".$requisition_no;
            $created_by = $this->user->name;

            //create requisition
            $requisition = new DrinkRequisition();
            $requisition->date = $date;
            $requisition->type_store = $type_store;
            $requisition->requisition_signature = $requisition_signature;
            $requisition->requisition_no = $requisition_no;
            $requisition->created_by = $created_by;
            $requisition->description = $description;
            $requisition->save();
            //insert details of requisition No.
            for( $count = 0; $count < count($drink_id); $count++ ){

                $price = DrinkBigStoreDetail::where('drink_id', $drink_id[$count])->value('cump');
                $total_value_requisitioned = $quantity_requisitioned[$count] * $price;
                $data = array(
                    'drink_id' => $drink_id[$count],
                    'date' => $date,
                    'type_store' => $type_store,
                    'quantity_requisitioned' => $quantity_requisitioned[$count],
                    'unit' => $unit[$count],
                    'price' => $price,
                    'description' => $description,
                    'total_value_requisitioned' => $total_value_requisitioned,
                    'created_by' => $created_by,
                    'requisition_no' => $requisition_no,
                    'requisition_signature' => $requisition_signature,
                );
                $insert_data[] = $data;
            }

            DrinkRequisitionDetail::insert($insert_data);

        session()->flash('success', 'Requisition has been created !!');
        return redirect()->route('admin.drink-requisitions.index');
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
         $code = DrinkRequisition::where('requisition_no', $requisition_no)->value('requisition_no');
         $requisitions = DrinkRequisitionDetail::where('requisition_no', $requisition_no)->get();
         return view('backend.pages.drink_requisition.show', compact('requisitions','code'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $requisition_no
     * @return \Illuminate\Http\Response
     */
    public function edit($requisition_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_requisition.edit')) {
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
        if (is_null($this->user) || !$this->user->can('drink_requisition.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any requisition !');
        }

        
    }

    public function validateRequisition($requisition_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_requisition.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any requisition !');
        }
            DrinkRequisition::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            DrinkRequisitionDetail::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        session()->flash('success', 'requisition has been validated !!');
        return back();
    }

    public function reject($requisition_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_requisition.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any requisition !');
        }

        DrinkRequisition::where('requisition_no', '=', $requisition_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
            DrinkRequisitionDetail::where('requisition_no', '=', $requisition_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        session()->flash('success', 'Requisition has been rejected !!');
        return back();
    }

    public function reset($requisition_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_requisition.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any requisition !');
        }

        DrinkRequisition::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
            DrinkRequisitionDetail::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        session()->flash('success', 'DrinkRequisition has been reseted !!');
        return back();
    }

    public function confirm($requisition_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_requisition.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any requisition !');
        }

        DrinkRequisition::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            DrinkRequisitionDetail::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        session()->flash('success', 'Requisition has been confirmed !!');
        return back();
    }

    public function approuve($requisition_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_requisition.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any requisition !');
        }

        DrinkRequisition::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);
            DrinkRequisitionDetail::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);

        session()->flash('success', 'Requisition has been confirmed !!');
        return back();
    }

    public function demande_requisition($requisition_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_requisition.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $stat = DrinkRequisition::where('requisition_no', $requisition_no)->value('status');
        $description = DrinkRequisition::where('requisition_no', $requisition_no)->value('description');
        $date = DrinkRequisition::where('requisition_no', $requisition_no)->value('date');
        if($stat == 2 || $stat == 3 || $stat == 4 || $stat == 5){
           $requisition_no = DrinkRequisition::where('requisition_no', $requisition_no)->value('requisition_no');
           $requisition_signature = DrinkRequisition::where('requisition_no', $requisition_no)->value('requisition_signature');
           $totalValue = DB::table('drink_requisition_details')
            ->where('requisition_no', '=', $requisition_no)
            ->sum('total_value_requisitioned');

           $datas = DrinkRequisitionDetail::where('requisition_no', $requisition_no)->get();
           $pdf = PDF::loadView('backend.pages.document.drink_requisition',compact('datas','requisition_no','setting','description','date','requisition_signature','totalValue'));

           Storage::put('public/pdf/drink_requisition/'.'BON_REQUISITION_'.$requisition_no.'.pdf', $pdf->output());

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
        if (is_null($this->user) || !$this->user->can('drink_requisition.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any requisition !');
        }

        $requisition = DrinkRequisition::where('requisition_no',$requisition_no)->first();
        if (!is_null($requisition)) {
            $requisition->delete();
            DrinkRequisitionDetail::where('requisition_no',$requisition_no)->delete();
        }

        session()->flash('success', 'Requisition has been deleted !!');
        return back();
    }
}

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
use App\Models\Food;
use App\Models\BarristRequisition;
use App\Models\BarristRequisitionDetail;
use Validator;
use PDF;
use Mail;
use Carbon\Carbon;

class BarristRequisitionController extends Controller
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
        if (is_null($this->user) || !$this->user->can('barrist_requisition.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any requisition !');
        }

        $requisitions = BarristRequisition::all();
        return view('backend.pages.barrist_requisition.index', compact('requisitions'));
    }

    public function choose()
    {
        if (is_null($this->user) || !$this->user->can('barrist_requisition.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any requisition !');
        }
        return view('backend.pages.barrist_requisition.choose');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function createDrink()
    {
        if (is_null($this->user) || !$this->user->can('barrist_requisition.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any requisition !');
        }

        $drinks  = Drink::where('store_type','!=',2)->orderBy('name','asc')->get();
        return view('backend.pages.barrist_requisition.create_drink', compact('drinks'));
    }

    public function createFood()
    {
        if (is_null($this->user) || !$this->user->can('barrist_requisition.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any requisition !');
        }
        $foods  = Food::where('store_type','!=',2)->orderBy('name','asc')->get();
        return view('backend.pages.barrist_requisition.create_food', compact('foods'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeFood(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('barrist_requisition.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any requisition !');
        }

        $rules = array(
                'date'  => 'required',
                'quantity_requisitioned.*'  => 'required',
                'unit.*'  => 'required',
                'food_id.*'  => 'required',
                'description'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $food_id = $request->food_id;
            $date = $request->date;
            $quantity_requisitioned = $request->quantity_requisitioned;
            $unit = $request->unit;
            $description =$request->description; 
            $latest = BarristRequisition::latest()->first();
            if ($latest) {
               $requisition_no = 'BR' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $requisition_no = 'BR' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $requisition_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$requisition_no;
            $created_by = $this->user->name;

            //create requisition
            $requisition = new BarristRequisition();
            $requisition->date = $date;
            $requisition->requisition_signature = $requisition_signature;
            $requisition->requisition_no = $requisition_no;
            $requisition->created_by = $created_by;
            $requisition->description = $description;
            $requisition->save();
            //insert details of requisition No.
            for( $count = 0; $count < count($food_id); $count++ ){

                $price = Food::where('id', $food_id[$count])->value('selling_price');
                $total_value_requisitioned = $quantity_requisitioned[$count] * $price;
                $data = array(
                    'food_id' => $food_id[$count],
                    'date' => $date,
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
       
        BarristRequisitionDetail::insert($insert_data);

        session()->flash('success', 'Requisition has been created !!');
        return redirect()->route('admin.barrist-requisitions.index');
    }


    public function storeDrink(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('barrist_requisition.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any requisition !');
        }

        $rules = array(
                'date'  => 'required',
                'quantity_requisitioned.*'  => 'required',
                'unit.*'  => 'required',
                'drink_id.*'  => 'required',
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
            $quantity_requisitioned = $request->quantity_requisitioned;
            $unit = $request->unit;
            $description =$request->description; 
            $latest = BarristRequisition::latest()->first();
            if ($latest) {
               $requisition_no = 'BR' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $requisition_no = 'BR' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $requisition_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$requisition_no;
            $created_by = $this->user->name;

            //create requisition
            $requisition = new BarristRequisition();
            $requisition->date = $date;
            $requisition->requisition_signature = $requisition_signature;
            $requisition->requisition_no = $requisition_no;
            $requisition->created_by = $created_by;
            $requisition->description = $description;
            $requisition->save();
            //insert details of requisition No.
            for( $count = 0; $count < count($drink_id); $count++ ){

                $price = Drink::where('id', $drink_id[$count])->value('selling_price');
                $total_value_requisitioned = $quantity_requisitioned[$count] * $price;
                $data = array(
                    'drink_id' => $drink_id[$count],
                    'date' => $date,
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
       
        BarristRequisitionDetail::insert($insert_data);

        session()->flash('success', 'Requisition has been created !!');
        return redirect()->route('admin.barrist-requisitions.index');
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
         $code = BarristRequisition::where('requisition_no', $requisition_no)->value('requisition_no');
         $requisitions = BarristRequisitionDetail::where('requisition_no', $requisition_no)->get();
         return view('backend.pages.barrist_requisition.show', compact('requisitions','code'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $requisition_no
     * @return \Illuminate\Http\Response
     */
    public function edit($requisition_no)
    {
        if (is_null($this->user) || !$this->user->can('barrist_requisition.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any requisition !');
        }

        return view('backend.pages.barrist_requisition.edit');
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
        if (is_null($this->user) || !$this->user->can('barrist_requisition.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any requisition !');
        }

        
    }

    public function validateRequisition($requisition_no)
    {
       if (is_null($this->user) || !$this->user->can('barrist_requisition.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any requisition !');
        }
            BarristRequisition::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            BarristRequisitionDetail::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        session()->flash('success', 'requisition has been validated !!');
        return back();
    }

    public function reject($requisition_no)
    {
       if (is_null($this->user) || !$this->user->can('barrist_requisition.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any requisition !');
        }     
            BarristRequisition::where('requisition_no', '=', $requisition_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
            BarristRequisitionDetail::where('requisition_no', '=', $requisition_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        session()->flash('success', 'Requisition has been rejected !!');
        return back();
    }

    public function reset($requisition_no)
    {
       if (is_null($this->user) || !$this->user->can('barrist_requisition.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any requisition !');
        }

        BarristRequisition::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
            BarristRequisitionDetail::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        session()->flash('success', 'BarristRequisition has been reseted !!');
        return back();
    }

    public function confirm($requisition_no)
    {
       if (is_null($this->user) || !$this->user->can('barrist_requisition.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any requisition !');
        }

        BarristRequisition::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            BarristRequisitionDetail::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        session()->flash('success', 'Requisition has been confirmed !!');
        return back();
    }

    public function approuve($requisition_no)
    {
       if (is_null($this->user) || !$this->user->can('barrist_requisition.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any requisition !');
        }

        BarristRequisition::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);
            BarristRequisitionDetail::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);

        session()->flash('success', 'Requisition has been confirmed !!');
        return back();
    }

    public function demande_requisition($requisition_no)
    {
        if (is_null($this->user) || !$this->user->can('barrist_requisition.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $stat = BarristRequisition::where('requisition_no', $requisition_no)->value('status');
        $description = BarristRequisition::where('requisition_no', $requisition_no)->value('description');
        $date = BarristRequisition::where('requisition_no', $requisition_no)->value('date');
        $drink_id = BarristRequisitionDetail::where('requisition_no', $requisition_no)->value('drink_id');
        if($stat == 2 && $stat == 3 || $stat == 4 || $stat == 5){
           $requisition_no = BarristRequisition::where('requisition_no', $requisition_no)->value('requisition_no');
           $requisition_signature = BarristRequisition::where('requisition_no', $requisition_no)->value('requisition_signature');

           if (!empty($drink_id)) {
               $datas = BarristRequisitionDetail::where('requisition_no', $requisition_no)->where('drink_id','!=','')->get();
               $totalValue = DB::table('barrist_requisition_details')
            ->where('requisition_no', '=', $requisition_no)->where('drink_id','!=','')
            ->sum('total_value_requisitioned');
           }else{
                $datas = BarristRequisitionDetail::where('requisition_no', $requisition_no)->where('food_id','!=','')->get();
                $totalValue = DB::table('barrist_requisition_details')
            ->where('requisition_no', '=', $requisition_no)->where('food_id','!=','')
            ->sum('total_value_requisitioned');
           }
           $pdf = PDF::loadView('backend.pages.document.barrist_requisition',compact('datas','requisition_no','setting','description','date','requisition_signature','totalValue'));

           Storage::put('public/pdf/barrist_requisition/'.'BON_REQUISITION_'.$requisition_no.'.pdf', $pdf->output());

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
        if (is_null($this->user) || !$this->user->can('barrist_requisition.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any requisition !');
        }

        $requisition = BarristRequisition::where('requisition_no',$requisition_no)->first();
        if (!is_null($requisition)) {
            $requisition->delete();
            BarristRequisitionDetail::where('requisition_no',$requisition_no)->delete();
        }

        session()->flash('success', 'Requisition has been deleted !!');
        return back();
    }
}

<?php

namespace App\Http\Controllers\Backend\MusumbaSteel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Storage;
use App\Models\MsFuelRequisition;
use App\Models\MsFuelRequisitionDetail;
use App\Models\MsCar;
use App\Models\MsDriver;
use App\Models\MsFuel;
use Carbon\Carbon;
use PDF;
use Validator;
use Excel;

class FuelRequisitionController extends Controller
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_requisition.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any requisition !');
        }

        $requisitions = MsFuelRequisition::orderBy('requisition_no','desc')->get();

        return view('backend.pages.musumba_steel.fuel.requisition.index', compact('requisitions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_requisition.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any requisition !');
        }

        $cars = MsCar::all();
        $drivers = MsDriver::all();
        $fuels = MsFuel::all();
        return view('backend.pages.musumba_steel.fuel.requisition.create',compact('fuels','cars','drivers'));
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_requisition.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any requisition !');
        }

        $rules = array(
                'car_id.*'  => 'required',
                'date'  => 'required',
                'fuel_id'  => 'required',
                'quantity_requisitioned.*'  => 'required',
                'driver_id.*'  => 'required',
                'description'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $car_id = $request->car_id;
            $fuel_id = $request->fuel_id;
            $date = $request->date;
            $quantity_requisitioned = $request->quantity_requisitioned;
            $driver_id = $request->driver_id;
            $description =$request->description; 
            $latest = MsFuelRequisition::latest()->first();
            if ($latest) {
               $requisition_no = 'BR' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $requisition_no = 'BR' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $requisition_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$requisition_no;
            $created_by = $this->user->name;

            //create requisition
            $requisition = new MsFuelRequisition();
            $requisition->date = $date;
            $requisition->requisition_signature = $requisition_signature;
            $requisition->requisition_no = $requisition_no;
            $requisition->created_by = $created_by;
            $requisition->fuel_id = $fuel_id;
            $requisition->description = $description;
            $requisition->save();
            //insert details of requisition No.
            for( $count = 0; $count < count($car_id); $count++ ){

                $price = MsFuel::where('id', $fuel_id)->value('purchase_price');
                $total_value_requisitioned = $quantity_requisitioned[$count] * $price;
                $data = array(
                    'car_id' => $car_id[$count],
                    'date' => $date,
                    'fuel_id' => $fuel_id,
                    'quantity_requisitioned' => $quantity_requisitioned[$count],
                    'driver_id' => $driver_id[$count],
                    'price' => $price,
                    'description' => $description,
                    'total_value_requisitioned' => $total_value_requisitioned,
                    'created_by' => $created_by,
                    'requisition_no' => $requisition_no,
                    'requisition_signature' => $requisition_signature,
                );
                $insert_data[] = $data;
            }
       
        MsFuelRequisitionDetail::insert($insert_data);

        session()->flash('success', 'Requisition has been created !!');
        return redirect()->route('admin.ms-fuel-requisitions.index');
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
         $code = MsFuelRequisition::where('requisition_no', $requisition_no)->value('requisition_no');
         $requisitions = MsFuelRequisitionDetail::where('requisition_no', $requisition_no)->get();
         return view('backend.pages.musumba_steel.fuel.requisition.show', compact('requisitions','code'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $requisition_no
     * @return \Illuminate\Http\Response
     */
    public function edit($requisition_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_requisition.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any requisition !');
        }

        return view('backend.pages.musumba_steel.requisition.edit');
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_requisition.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any requisition !');
        }

        
    }

    public function validateRequisition($requisition_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_requisition.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any requisition !');
        }
            MsFuelRequisition::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            MsFuelRequisitionDetail::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        session()->flash('success', 'requisition has been validated !!');
        return back();
    }

    public function reject($requisition_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_requisition.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any requisition !');
        }     
            MsFuelRequisition::where('requisition_no', '=', $requisition_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
            MsFuelRequisitionDetail::where('requisition_no', '=', $requisition_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        session()->flash('success', 'Requisition has been rejected !!');
        return back();
    }

    public function reset($requisition_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_requisition.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any requisition !');
        }

        MsFuelRequisition::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
            MsFuelRequisitionDetail::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        session()->flash('success', 'MaterialRequisition has been reseted !!');
        return back();
    }

    public function confirm($requisition_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_requisition.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any requisition !');
        }

        MsFuelRequisition::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            MsFuelRequisitionDetail::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        session()->flash('success', 'Requisition has been confirmed !!');
        return back();
    }

    public function approuve($requisition_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_requisition.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any requisition !');
        }

        MsFuelRequisition::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);
            MsFuelRequisitionDetail::where('requisition_no', '=', $requisition_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);

        session()->flash('success', 'Requisition has been confirmed !!');
        return back();
    }

    public function bonRequisition($requisition_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_requisition.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $stat = MsFuelRequisition::where('requisition_no', $requisition_no)->value('status');
        $description = MsFuelRequisition::where('requisition_no', $requisition_no)->value('description');
        $date = MsFuelRequisition::where('requisition_no', $requisition_no)->value('date');
        if($stat == 2 && $stat == 3 || $stat == 4 || $stat == 5){
           $requisition_no = MsFuelRequisition::where('requisition_no', $requisition_no)->value('requisition_no');
           $requisition_signature = MsFuelRequisition::where('requisition_no', $requisition_no)->value('requisition_signature');
           $totalValue = DB::table('ms_fuel_requisition_details')
            ->where('requisition_no', '=', $requisition_no)
            ->sum('total_value_requisitioned');

           $datas = MsFuelRequisitionDetail::where('requisition_no', $requisition_no)->get();
           $pdf = PDF::loadView('backend.pages.musumba_steel.fuel.document.requisition',compact('datas','requisition_no','setting','description','date','requisition_signature','totalValue'));

           Storage::put('public/musumba_steel/fuel/requisition/'.'BON_REQUISITION_'.$requisition_no.'.pdf', $pdf->output());

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
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_requisition.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any requisition !');
        }

        $requisition = MsFuelRequisition::where('requisition_no',$requisition_no)->first();
        if (!is_null($requisition)) {
            $requisition->delete();
            MsFuelRequisitionDetail::where('requisition_no',$requisition_no)->delete();
        }

        session()->flash('success', 'Requisition has been deleted !!');
        return back();
    }
}

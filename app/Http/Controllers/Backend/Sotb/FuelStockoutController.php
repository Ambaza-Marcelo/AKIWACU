<?php

namespace App\Http\Controllers\Backend\Sotb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Storage;
use App\Models\SotbFuelStockout;
use App\Models\SotbFuelStockoutDetail;
use App\Models\SotbFuelPump;
use App\Models\SotbFuelRequisition;
use App\Models\SotbFuelRequisitionDetail;
use App\Models\SotbFuelReport;
use App\Models\SotbFuel;
use App\Models\SotbFuelIndexPump;
use Carbon\Carbon;
use PDF;
use Validator;
use Excel;

use Mail;
use App\Mail\DeleteFuelStockoutMail;

class FuelStockoutController extends Controller
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
        if (is_null($this->user) || !$this->user->can('sotb_fuel_stockout.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stockout !');
        }

        $fuel_stockouts = SotbFuelStockout::orderBy('date','desc')->get();

        $cars = SotbFuelStockoutDetail::select(
                        DB::raw('car_id,sum(quantity) as qtite'))->groupBy('car_id')->orderBy('qtite','desc')->get();

        return view('backend.pages.sotb.fuel.stockout.index', compact('fuel_stockouts','cars'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($requisition_no)
    {
        if (is_null($this->user) || !$this->user->can('sotb_fuel_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $pumps  = SotbFuelPump::all();
        $datas = SotbFuelRequisitionDetail::where('requisition_no', $requisition_no)->get();
        $requisition = SotbFuelRequisition::where('requisition_no', $requisition_no)->first();
        return view('backend.pages.sotb.fuel.stockout.create', compact('pumps','requisition','datas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('sotb_fuel_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $rules = array(
                'car_id.*'  => 'required',
                'date'  => 'required',
                'quantity.*'  => 'required',
                'driver_id.*'  => 'required',
                'pump_id'  => 'required',
                'fuel_id'  => 'required',
                'requisition_no'  => 'required',
                'description'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $pump_id = $request->pump_id;
            $date = $request->date;
            $description =$request->description; 
            $quantity = $request->quantity;
            $requisition_no = $request->requisition_no;
            $driver_id =$request->driver_id; 
            $car_id =$request->car_id; 
            $fuel_id =$request->fuel_id; 
            
            $latest = SotbFuelStockout::latest()->first();
            if ($latest) {
               $stockout_no = 'BS' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $stockout_no = 'BS' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $stockout_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$stockout_no;
            $created_by = $this->user->name;


            for( $count = 0; $count < count($car_id); $count++ ){

                $purchase_price = SotbFuel::where('id', $fuel_id)->value('purchase_price');

                $total_purchase_value = $quantity[$count] * $purchase_price;

                $data = array(
                    'car_id' => $car_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'purchase_price' => $purchase_price,
                    'total_purchase_value' => $total_purchase_value,
                    'pump_id' => $pump_id,
                    'fuel_id' => $fuel_id,
                    'stockout_no' => $stockout_no,
                    'stockout_signature' => $stockout_signature,
                    'requisition_no' => $requisition_no,
                    'created_by' => $created_by,
                    'description' => $description,
                    'driver_id' => $driver_id[$count],
                    'created_at' => \Carbon\Carbon::now()
                );
                $insert_data[] = $data;               

                
            }

            $stockout = new SotbFuelStockout();
            $stockout->stockout_no = $stockout_no;
            $stockout->stockout_signature = $stockout_signature;
            $stockout->date = $date;
            $stockout->pump_id = $pump_id;
            $stockout->fuel_id = $fuel_id;
            $stockout->description = $description;
            $stockout->requisition_no = $requisition_no;
            $stockout->created_by = $this->user->name;
            $stockout->save();

            SotbFuelStockoutDetail::insert($insert_data);

            session()->flash('success', 'Stockout has been created !!');
            return redirect()->route('admin.sotb-fuel-stockouts.index');

        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($stockout_no)
    {
        //
         $code = SotbFuelStockoutDetail::where('stockout_no', $stockout_no)->value('stockout_no');
         $fuel_stockouts = SotbFuelStockoutDetail::where('stockout_no', $stockout_no)->get();
         return view('backend.pages.sotb.fuel.stockout.show', compact('fuel_stockouts','code'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $bon_no
     * @return \Illuminate\Http\Response
     */
    public function edit($bon_no)
    {
        if (is_null($this->user) || !$this->user->can('sotb_fuel_stockout.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any stockout !');
        }

       
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $bon_no
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $bon_no)
    {
        if (is_null($this->user) || !$this->user->can('sotb_fuel_stockout.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any stockout !');
        }


        
    }


    public function bon_sortie($stockout_no)
    {
        if (is_null($this->user) || !$this->user->can('sotb_fuel_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        //$stockout = Stockout::find($stockout_no);
        $datas = SotbFuelStockoutDetail::where('stockout_no', $stockout_no)->get();
        $totalValue = DB::table('sotb_fuel_stockout_details')
            ->where('stockout_no', '=', $stockout_no)
            ->sum('total_purchase_value');
        $date = SotbFuelStockout::where('stockout_no', $stockout_no)->value('date');
        $description = SotbFuelStockout::where('stockout_no', $stockout_no)->value('description');
        $requisition_no = SotbFuelStockout::where('stockout_no', $stockout_no)->value('requisition_no');
        $pdf = PDF::loadView('backend.pages.sotb.fuel.document.stockout',compact('datas','stockout_no','totalValue','setting','description','requisition_no','date'));

        Storage::put('public/sotb/fuel/bon_sortie/'.$stockout_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('bon_sortie_'.$stockout_no.'.pdf');
    }

    public function validateStockout($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('sotb_fuel_stockout.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any stockout !');
        }
            SotbFuelStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            SotbFuelStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        session()->flash('success', 'stockout has been validated !!');
        return back();
    }

    public function reject($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('sotb_fuel_stockout.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any stockout !');
        }

        SotbFuelStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
        SotbFuelStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        session()->flash('success', 'Stockout has been rejected !!');
        return back();
    }

    public function reset($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('sotb_fuel_stockout.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any stockout !');
        }

        SotbFuelStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
        SotbFuelStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        session()->flash('success', 'Stockout has been reseted !!');
        return back();
    }

    public function confirm($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('sotb_fuel_stockout.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any stockout !');
        }

        SotbFuelStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            SotbFuelStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        session()->flash('success', 'Stockout has been confirmed !!');
        return back();
    }

    public function approuve($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('sotb_fuel_stockout.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any stockout !');
        }


        $datas = SotbFuelStockoutDetail::where('stockout_no', $stockout_no)->get();

        foreach($datas as $data){

                $valeurStockInitial = SotbFuelPump::where('id',$data->pump_id)->value('total_purchase_value');
                $quantityStockInitial = SotbFuelPump::where('id',$data->pump_id)->value('quantity');

                $quantityRestant = $quantityStockInitial - $data->quantity;


                $reportData = array(
                    'pump_id' => $data->pump_id,
                    'fuel_id' => $data->fuel_id,
                    'car_id' => $data->car_id,
                    'driver_id' => $data->driver_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'quantity_stockout' => $data->quantity,
                    'value_stockout' => $data->quantity * $data->purchase_price,
                    'created_by' => $this->user->name,
                    'stockout_no' => $data->stockout_no,
                    'quantity_stock_final' => $quantityRestant,
                    'value_stock_final' => $quantityRestant * $data->purchase_price,
                    'date' => $data->date,
                    'transaction' => "SORTIE",
                    'created_at' => \Carbon\Carbon::now()
                );
                $report[] = $reportData;
                
                    $donnees = array(
                        'id' => $data->pump_id,
                        'quantity' => $quantityRestant,
                        'total_purchase_value' => $quantityRestant * $data->purchase_price,
                        'verified' => false
                    );
                    
                    if ($data->quantity <= $quantityStockInitial) {
                        
                        SotbFuelPump::where('id',$data->pump_id)
                        ->update($donnees);

                        SotbFuelReport::insert($reportData);

                        $indexPump = DB::table('sotb_fuel_index_pumps')->orderBy('created_at','desc')->first();

                        if (!empty($indexPump)) {
                            $start_index = $indexPump->end_index;
                            $end_index = $start_index + $data->quantity;

                            $index_pump = new SotbFuelIndexPump();
                            $index_pump->start_index = $start_index;
                            $index_pump->end_index = $end_index;
                            $index_pump->final_index = $index_pump->end_index - $index_pump->start_index;
                            $index_pump->auteur = $this->user->name;
                            $index_pump->updated_at = \Carbon\Carbon::now();
                            $index_pump->date = \Carbon\Carbon::now();
                            $index_pump->save();

                            $fuelReport = new SotbFuelReport();
                            $fuelReport->start_index = $start_index;
                            $fuelReport->pump_id = $data->pump_id;
                            $fuelReport->end_index = $end_index;
                            $fuelReport->final_index = $index_pump->end_index - $index_pump->start_index;
                            $fuelReport->updated_at = \Carbon\Carbon::now();
                            $fuelReport->save();

                            SotbFuelStockout::where('stockout_no', '=', $stockout_no)
                            ->update(['status' => 4,'approuved_by' => $this->user->name]);
                            SotbFuelStockoutDetail::where('stockout_no', '=', $stockout_no)
                            ->update(['status' => 4,'approuved_by' => $this->user->name]);

                            session()->flash('success', 'Stockout has been done successfuly !');
                            return redirect()->back();

                        }else{
                           session()->flash('error', $this->user->name.' , please begin to write start index pump!');
                            return redirect()->back(); 
                        }
                        

                        
                    }else{
                        session()->flash('error', $this->user->name.' ,Why do you want to stockout a quantity you do not have in your store? please rewrite a valid quantity!');
                        return redirect()->back();
                    }
                
  
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $stockout_no
     * @return \Illuminate\Http\Response
     */
    public function destroy($stockout_no)
    {
        if (is_null($this->user) || !$this->user->can('material_stockout.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any stockout !');
        }

        $stockout = MaterialStockout::where('stockout_no',$stockout_no)->first();
        if (!is_null($stockout)) {
            $stockout->delete();
            MaterialStockoutDetail::where('stockout_no',$stockout_no)->delete();
        }

        session()->flash('success', 'Stockout has been deleted !!');
        return back();
    }
}

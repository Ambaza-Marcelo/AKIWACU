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
use App\Models\MsFuelStockout;
use App\Models\MsFuelStockoutDetail;
use App\Models\MsFuelPump;
use App\Models\MsFuelRequisition;
use App\Models\MsFuelRequisitionDetail;
use App\Models\MsFuelReport;
use App\Models\MsFuel;
use App\Models\MsFuelIndexPump;
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_stockout.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stockout !');
        }

        $fuel_stockouts = MsFuelStockout::orderBy('stockout_no','desc')->get();

        $cars = MsFuelStockoutDetail::select(
                        DB::raw('car_id,sum(quantity) as qtite'))->groupBy('car_id')->orderBy('qtite','desc')->get();

        return view('backend.pages.musumba_steel.fuel.stockout.index', compact('fuel_stockouts','cars'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($requisition_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $pumps  = MsFuelPump::all();
        $datas = MsFuelRequisitionDetail::where('requisition_no', $requisition_no)->get();
        $requisition = MsFuelRequisition::where('requisition_no', $requisition_no)->first();
        return view('backend.pages.musumba_steel.fuel.stockout.create', compact('pumps','requisition','datas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_stockout.create')) {
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
            
            $latest = MsFuelStockout::latest()->first();
            if ($latest) {
               $stockout_no = 'BS' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $stockout_no = 'BS' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $stockout_signature = "4001711615".Carbon::parse(Carbon::now())->format('YmdHis')."/".$stockout_no;
            $created_by = $this->user->name;


            for( $count = 0; $count < count($car_id); $count++ ){

                $purchase_price = MsFuel::where('id', $fuel_id)->value('purchase_price');

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

            $stockout = new MsFuelStockout();
            $stockout->stockout_no = $stockout_no;
            $stockout->stockout_signature = $stockout_signature;
            $stockout->date = $date;
            $stockout->pump_id = $pump_id;
            $stockout->fuel_id = $fuel_id;
            $stockout->description = $description;
            $stockout->requisition_no = $requisition_no;
            $stockout->created_by = $this->user->name;
            $stockout->save();

            MsFuelStockoutDetail::insert($insert_data);

            session()->flash('success', 'Stockout has been created !!');
            return redirect()->route('admin.ms-fuel-stockouts.index');

        
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
         $code = MsFuelStockoutDetail::where('stockout_no', $stockout_no)->value('stockout_no');
         $fuel_stockouts = MsFuelStockoutDetail::where('stockout_no', $stockout_no)->get();
         return view('backend.pages.musumba_steel.fuel.stockout.show', compact('fuel_stockouts','code'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $bon_no
     * @return \Illuminate\Http\Response
     */
    public function edit($bon_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_stockout.edit')) {
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_stockout.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any stockout !');
        }


        
    }


    public function bon_sortie($stockout_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        //$stockout = Stockout::find($stockout_no);
        $datas = MsFuelStockoutDetail::where('stockout_no', $stockout_no)->get();
        $totalValue = DB::table('ms_fuel_stockout_details')
            ->where('stockout_no', '=', $stockout_no)
            ->sum('total_purchase_value');
        $date = MsFuelStockout::where('stockout_no', $stockout_no)->value('date');
        $description = MsFuelStockout::where('stockout_no', $stockout_no)->value('description');
        $requisition_no = MsFuelStockout::where('stockout_no', $stockout_no)->value('requisition_no');
        $pdf = PDF::loadView('backend.pages.musumba_steel.fuel.document.stockout',compact('datas','stockout_no','totalValue','setting','description','requisition_no','date'));

        Storage::put('public/musumba_steel/fuel/bon_sortie/'.$stockout_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('bon_sortie_'.$stockout_no.'.pdf');
    }

    public function validateStockout($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_stockout.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any stockout !');
        }
            MsFuelStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            MsFuelStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        session()->flash('success', 'stockout has been validated !!');
        return back();
    }

    public function reject($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_stockout.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any stockout !');
        }

        MsFuelStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
        MsFuelStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        session()->flash('success', 'Stockout has been rejected !!');
        return back();
    }

    public function reset($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_stockout.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any stockout !');
        }

        MsFuelStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
        MsFuelStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        session()->flash('success', 'Stockout has been reseted !!');
        return back();
    }

    public function confirm($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_stockout.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any stockout !');
        }

        MsFuelStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            MsFuelStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        session()->flash('success', 'Stockout has been confirmed !!');
        return back();
    }

    public function approuve($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_stockout.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any stockout !');
        }


        $datas = MsFuelStockoutDetail::where('stockout_no', $stockout_no)->get();

        foreach($datas as $data){

                $valeurStockInitial = MsFuelPump::where('id',$data->pump_id)->value('total_cost_value');
                $quantityStockInitial = MsFuelPump::where('id',$data->pump_id)->value('quantity');

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
                    'description' => $data->description,
                    'created_at' => \Carbon\Carbon::now()
                );
                $report[] = $reportData;
                
                    $donnees = array(
                        'id' => $data->pump_id,
                        'quantity' => $quantityRestant,
                        'total_cost_value' => $quantityRestant * $data->cost_price,
                        'verified' => false
                    );
                    
                    if ($data->quantity <= $quantityStockInitial) {

                        $indexPump = DB::table('ms_fuel_index_pumps')->orderBy('created_at','desc')->first();

                        if (!empty($indexPump)) {

                            $start_index = $indexPump->end_index;
                            $end_index = $start_index + $data->quantity;

                            $index_pump = new MsFuelIndexPump();
                            $index_pump->start_index = $start_index;
                            $index_pump->end_index = $end_index;
                            $index_pump->final_index = $index_pump->end_index - $index_pump->start_index;
                            $index_pump->updated_at = \Carbon\Carbon::now();
                            $index_pump->auteur = $this->user->name;
                            $index_pump->save();

                            $fuelReport = new MsFuelReport();
                            $fuelReport->start_index = $start_index;
                            $fuelReport->pump_id = $data->pump_id;
                            $fuelReport->end_index = $end_index;
                            $fuelReport->final_index = $index_pump->end_index - $index_pump->start_index;
                            $fuelReport->created_by = $this->user->name;
                            $fuelReport->description = "AUTO AJUSTEMENT DES INDEX";
                            $fuelReport->updated_at = \Carbon\Carbon::now();
                            $fuelReport->date = \Carbon\Carbon::now();
                            $fuelReport->save();

                            MsFuelPump::where('id',$data->pump_id)
                            ->update($donnees);
                            MsFuelReport::insert($reportData);

                        }else{
                           session()->flash('error', $this->user->name.' , please begin to write start index pump!');
                            return redirect()->back(); 
                        }
                        

                        
                    }else{
                        session()->flash('error', $this->user->name.' ,Why do you want to stockout a quantity you do not have in your store? please rewrite a valid quantity!');
                        return redirect()->back();
                    }
                
  
        }

        MsFuelStockout::where('stockout_no', '=', $stockout_no)
                            ->update(['status' => 4,'approuved_by' => $this->user->name]);
        MsFuelStockoutDetail::where('stockout_no', '=', $stockout_no)
                            ->update(['status' => 4,'approuved_by' => $this->user->name]);

        session()->flash('success', 'Stockout has been done successfuly !');
                            return redirect()->back();

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $stockout_no
     * @return \Illuminate\Http\Response
     */
    public function destroy($stockout_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_stockout.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any stockout !');
        }

        $stockout = MsFuelStockout::where('stockout_no',$stockout_no)->first();
        if (!is_null($stockout)) {
            $stockout->delete();
            MsFuelStockoutDetail::where('stockout_no',$stockout_no)->delete();
        }

        session()->flash('success', 'Stockout has been deleted !!');
        return back();
    }
}

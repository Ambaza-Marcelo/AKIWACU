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
use App\Models\MsFuelInventory;
use App\Models\MsFuelInventoryDetail;
use App\Models\MsFuelPump;
use App\Models\MsFuel;
use App\Models\MsFuelReport;
use Carbon\Carbon;
use PDF;
use Validator;
use Excel;

class FuelInventoryController extends Controller
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_inventory.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any inventory !');
        }

        $fuel_inventories = MsFuelInventory::orderBy('date','desc')->get();

        return view('backend.pages.musumba_steel.fuel.inventory.index', compact('fuel_inventories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_inventory.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any inventory !');
        }

        $pumps  = MsFuelPump::all();
        $fuels  = MsFuel::all();
        $datas = MsFuelPump::all();
        return view('backend.pages.musumba_steel.fuel.inventory.create', compact('datas','pumps','fuels'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_inventory.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any inventory !');
        }
        $rules = array(
            'pump_id.*' => 'required',
            'date' => 'required|date',
            'quantity.*' => 'required',
            //'jauge_id.*' => 'required',
            'cost_price.*' => 'required',
            'new_quantity.*' => 'required',
            'new_cost_price.*' => 'required',
            'description' => 'required',
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $pump_id = $request->pump_id;
            $date = $request->date;
            $jauge_id = $request->jauge_id;
            $quantity = $request->quantity;
            $cost_price = $request->cost_price;
            $new_quantity = $request->new_quantity;
            $new_cost_price = $request->new_cost_price;
            $title = $request->title;  

            $latest = MsFuelInventory::latest()->first();
            if ($latest) {
               $inventory_no = 'BI' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $inventory_no = 'BI' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $inventory_signature = "4001711615".Carbon::parse(Carbon::now())->format('YmdHis')."/".$inventory_no;
            $created_by = $this->user->name;


            $created_by = $this->user->name;
            $description =$request->description; 

            for( $count = 0; $count < count($pump_id); $count++ ){
                $total_cost_value = $quantity[$count] * $cost_price[$count];
                $new_total_cost_value = $new_quantity[$count] * $new_cost_price[$count];
                $relicat = $quantity[$count] - $new_quantity[$count];

                if (!empty($jauge_id[$count])) {
                    $data = array(
                    'pump_id' => $pump_id[$count],
                    'date' => $date,
                    'title' => $title,
                    'quantity' => $quantity[$count],
                    'jauge_id' => $jauge_id[$count],
                    'cost_price' => $cost_price[$count],
                    'total_cost_value' => $total_cost_value,
                    'new_quantity' => $new_quantity[$count],
                    'new_cost_price' => $new_cost_price[$count],
                    'new_total_cost_value' => $new_total_cost_value,
                    'relicat' => $relicat,
                    'inventory_no' => $inventory_no,
                    'inventory_signature' => $inventory_signature,
                    'created_by' => $created_by,
                    'description' => $description,
                    'created_at' => \Carbon\Carbon::now()
                );
                }else{
                    $data = array(
                    'pump_id' => $pump_id[$count],
                    'date' => $date,
                    'title' => $title,
                    'quantity' => $quantity[$count],
                    //'jauge_id' => $jauge_id[$count],
                    'cost_price' => $cost_price[$count],
                    'total_cost_value' => $total_cost_value,
                    'new_quantity' => $new_quantity[$count],
                    'new_cost_price' => $new_cost_price[$count],
                    'new_total_cost_value' => $new_total_cost_value,
                    'relicat' => $relicat,
                    'inventory_no' => $inventory_no,
                    'inventory_signature' => $inventory_signature,
                    'created_by' => $created_by,
                    'description' => $description,
                    'created_at' => \Carbon\Carbon::now()
                );
                }
                
                $insert_data[] = $data;
                
            }
            MsFuelInventoryDetail::insert($insert_data);
            $fuel_inventory = new MsFuelInventory();
            $fuel_inventory->date = $date;
            $fuel_inventory->title = $title;
            $fuel_inventory->inventory_no = $inventory_no;
            $fuel_inventory->description = $description;
            $fuel_inventory->created_by = $created_by;
            $fuel_inventory->save();
         
        session()->flash('success', 'Inventory has been created !!');
        return redirect()->route('admin.ms-fuel-inventories.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $bon_no
     * @return \Illuminate\Http\Response
     */
    public function show($bon_no)
    {
        //
        $code = MsFuelInventoryDetail::where('inventory_no', $bon_no)->value('inventory_no');
        $fuel_inventories = MsFuelInventoryDetail::where('inventory_no', $bon_no)->get();
        return view('backend.pages.musumba_steel.fuel.inventory.show', compact('fuel_inventories','code'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($bon_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_inventory.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any inventory !');
        }

        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $inventory_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_inventory.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any inventory !');
        }

        
    }

    public function bon_inventaire($inventory_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_inventory.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $description = MsFuelInventory::where('inventory_no', $inventory_no)->value('description');
        $title = MsFuelInventory::where('inventory_no', $inventory_no)->value('title');
        $inventory_signature = MsFuelInventory::where('inventory_no', $inventory_no)->value('inventory_signature');
        $date = MsFuelInventory::where('inventory_no', $inventory_no)->value('date');
        $datas = MsFuelInventoryDetail::where('inventory_no', $inventory_no)->get();


        $totalValueActuelle = DB::table('ms_fuel_inventory_details')
            ->where('inventory_no', '=', $inventory_no)
            ->sum('total_cost_value');
         $totalValueNew = DB::table('ms_fuel_inventory_details')
            ->where('inventory_no', '=', $inventory_no)
            ->sum('new_total_cost_value');
        $pdf = PDF::loadView('backend.pages.musumba_steel.fuel.document.inventory',compact('datas','inventory_no','totalValueActuelle','totalValueNew','inventory_signature','setting','title','description','date'));//->setPaper('a4', 'landscape');

        Storage::put('public/musumba_steel/carburant/bon_inventaire/'.$inventory_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download($inventory_no.'.pdf');
    }

    public function get_inventory_data()
    {
        return Excel::download(new InventoryExport, 'inventories.xlsx');
    }


    public function validateInventory($inventory_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_inventory.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any inventory !');
        }

        $datas = MsFuelInventoryDetail::where('inventory_no', $inventory_no)->get();

        foreach($datas as $data){
            $valeurStockInitial = MsFuelPump::where('id', $data->pump_id)->value('total_cost_value');

                $valeurAcquisition = $data->new_quantity * $data->new_cost_price;

                $valeurTotalUnite = $data->new_quantity + $data->quantity;
                $cump = ($valeurStockInitial + $valeurAcquisition) / $valeurTotalUnite;

                $calcul_cump = array(
                        'cost_price' => $cump,
                    );
                $fuel_calc = array(
                        'cost_price' => $data->new_cost_price
                    );

                $fuel_id = MsFuelPump::where('id', $data->pump_id)->value('fuel_id');
                MsFuel::where('id',$fuel_id)
                        ->update($fuel_calc);

                    $carburant = array(
                        'id' => $data->pump_id,
                        'quantity' => $data->new_quantity,
                        'total_cost_value' => $data->new_cost_price * $data->new_quantity,
                        'cost_price' => $data->new_cost_price,
                        'auteur' => $this->user->name,
                        'verified' => true,
                    );

                    $reportData = array(
                        'date' => $data->date,
                        'pump_id' => $data->pump_id,
                        'quantity_stock_initial' => $data->quantity,
                        'value_stock_initial' => $data->total_cost_value,
                        'quantity_inventory' => $data->new_quantity,
                        'value_inventory' => $data->new_cost_price * $data->new_quantity,
                        'inventory_no' => $data->inventory_no,
                        'date' => $data->date,
                        'cump' => $data->new_cost_price,
                        'created_by' => $this->user->name,
                        'description' => $data->description,
                        'created_at' => \Carbon\Carbon::now()
                    );

                    MsFuelPump::where('id',$data->pump_id)
                        ->update($carburant);

        }

        MsFuelReport::insert($reportData);
        MsFuelInventory::where('inventory_no', '=', $inventory_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        session()->flash('success', 'inventory has been validated !!');
        return back();
    }

    public function rejectInventory($inventory_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_inventory.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any inventory !');
        }
            MsFuelInventory::where('inventory_no', '=', $inventory_no)
                ->update(['status' => -1]);

        session()->flash('success', 'inventory has been rejected !!');
        return back();
    }

    public function resetInventory($inventory_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_inventory.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any inventory !');
        }
            MsFuelInventory::where('inventory_no', '=', $inventory_no)
                ->update(['status' => 0]);

        session()->flash('success', 'inventory has been reseted !!');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($inventory_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_inventory.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any inventory !');
        }

        $inventory = MsFuelInventory::where('inventory_no', $inventory_no)->first();
        if (!is_null($inventory)) {
            $inventory->delete();
        }

        session()->flash('success', 'Inventory has been deleted !!');
        return back();
    }
}

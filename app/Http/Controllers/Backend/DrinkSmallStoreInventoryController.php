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
use Illuminate\Support\Facades\Http;
use App\Models\Drink;
use App\Models\DrinkSmallStore;
use App\Models\DrinkSmallStoreDetail;
use App\Models\DrinkSmallStoreInventory;
use App\Models\DrinkSmallStoreInventoryDetail;
use App\Models\DrinkSmallReport;
use App\Exports\DrinkSmStoreInventoryExport;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Excel;
use App\Models\Setting;
use App\Mail\DeleteInventoryMail;
use PDF;
use Mail;
use Validator;

class DrinkSmallStoreInventoryController extends Controller
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
        if (is_null($this->user) || !$this->user->can('drink_small_inventory.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any inventory !');
        }

        $inventories = DrinkSmallStoreInventory::all();
        return view('backend.pages.drink_small_store_inventory.index', compact('inventories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($code)
    {
        if (is_null($this->user) || !$this->user->can('drink_small_inventory.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any inventory !');
        }

        $drinks  = Drink::where('code_store',$code)->orderBy('name','asc')->get();
        $datas = DrinkSmallStoreDetail::where('code',$code)->where('drink_id','!=','')->where('verified',false)->get();
        return view('backend.pages.drink_small_store_inventory.create', compact('datas','drinks','code'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('drink_small_inventory.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any inventory !');
        }
        $rules = array(
            'drink_id.*' => 'required',
            'date' => 'required|date',
            'title' => 'required',
            'quantity.*' => 'required',
            'unit.*' => 'required',
            'purchase_price.*' => 'required',
            'selling_price.*' => 'required',
            'new_quantity.*' => 'required',
            'new_purchase_price.*' => 'required',
            'new_selling_price.*' => 'required',
            'new_unit.*' => 'required',
            'description' => 'required|max:490',
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $drink_id = $request->drink_id;
            $date = $request->date;
            $unit = $request->unit;
            $quantity = $request->quantity;
            $quantity_ml = $request->quantity_ml;
            $purchase_price = $request->purchase_price;
            $selling_price = $request->selling_price;
            $selling_price_ml = $request->selling_price_ml;
            $new_quantity = $request->new_quantity;
            $new_quantity_ml = $request->new_quantity_ml;
            $new_price = $request->new_price;
            $new_selling_price_ml = $request->new_selling_price_ml;
            $title = $request->title;
            $code_store = $request->code_store;
            $new_purchase_price = $request->new_purchase_price;
            $new_selling_price = $request->new_selling_price; 
            $new_unit = $request->new_unit; 

            $latest = DrinkSmallStoreInventory::latest()->first();
            if ($latest) {
               $inventory_no = 'BI' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $inventory_no = 'BI' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $inventory_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$inventory_no;

            $created_by = $this->user->name;
            $description =$request->description; 

            for( $count = 0; $count < count($drink_id); $count++ ){
                $total_purchase_value = $quantity[$count] * $purchase_price[$count];
                $total_selling_value = $quantity[$count] * $selling_price[$count];
                $new_total_purchase_value = $new_quantity[$count] * $new_purchase_price[$count];
                $new_total_selling_value = $new_quantity[$count] * $new_selling_price[$count];
                $new_total_selling_value_ml = $new_quantity_ml[$count] * $new_selling_price_ml[$count];
                $relicat = $quantity[$count] - $new_quantity[$count];
                $relicat_ml = $quantity_ml[$count] - $new_quantity_ml[$count];
                $data = array(
                    'drink_id' => $drink_id[$count],
                    'date' => $date,
                    'title' => $title,
                    'code_store' => $code_store,
                    'quantity' => $quantity[$count],
                    'quantity_ml' => $quantity_ml[$count],
                    'unit' => $unit[$count],
                    'purchase_price' => $purchase_price[$count],
                    'selling_price' => $selling_price[$count],
                    'selling_price_ml' => $selling_price_ml[$count],
                    'total_purchase_value' => $total_purchase_value,
                    'total_selling_value' => $total_selling_value,
                    'new_quantity' => $new_quantity[$count],
                    'new_quantity_ml' => $new_quantity_ml[$count],
                    'new_purchase_price' => $new_purchase_price[$count],
                    'new_selling_price' => $new_selling_price[$count],
                    'new_selling_price_ml' => $new_selling_price_ml[$count],
                    'new_total_purchase_value' => $new_total_purchase_value,
                    'new_total_selling_value' => $new_total_selling_value,
                    'new_total_selling_value_ml' => $new_total_selling_value_ml,
                    'new_unit' => $new_unit[$count],
                    'relicat' => $relicat,
                    'relicat_ml' => $relicat_ml,
                    'inventory_no' => $inventory_no,
                    'inventory_signature' => $inventory_signature,
                    'created_by' => $created_by,
                    'description' => $description,
                    'created_at' => \Carbon\Carbon::now()
                );
                $insert_data[] = $data;
                
            }
            DrinkSmallStoreInventoryDetail::insert($insert_data);
            //create inventory
            $inventory = new DrinkSmallStoreInventory();
            $inventory->date = $date;
            $inventory->title = $title;
            $inventory->inventory_no = $inventory_no;
            $inventory->code_store = $code_store;
            $inventory->inventory_signature = $inventory_signature;
            $inventory->description = $description;
            $inventory->created_by = $created_by;
            $inventory->save();
         
        session()->flash('success', 'Inventory has been created !!');
        return redirect()->route('admin.drink-small-store-inventory.index');
    }

    public function referenceInventaire()
    {
        if (is_null($this->user) || !$this->user->can('drink_small_inventory.view')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $datas = DrinkSmallStoreDetail::where('verified',false)->get();
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $pdf = PDF::loadView('backend.pages.document.reference_inventaire',compact('datas','setting'));
        return $pdf->download('reference-inventaire'.'.pdf');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $inventory_no
     * @return \Illuminate\Http\Response
     */
    public function show($inventory_no)
    {
        //
        $code = DrinkSmallStoreInventoryDetail::where('inventory_no', $inventory_no)->value('inventory_no');
        $inventories = DrinkSmallStoreInventoryDetail::where('inventory_no', $inventory_no)->get();
        return view('backend.pages.drink_small_store_inventory.show', compact('inventories','code'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function inventory($id)
    {
        if (is_null($this->user) || !$this->user->can('drink_small_inventory.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to make any inventory !');
        }

    }

    public function edit($inventory_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_small_inventory.edit')) {
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
        if (is_null($this->user) || !$this->user->can('drink_small_inventory.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any inventory !');
        }

        $rules = array(
            'drink_id.*' => 'required',
            'date' => 'required|date',
            'quantity.*' => 'required',
            'unit.*' => 'required',
            'unit_price.*' => 'required',
            'new_quantity.*' => 'required',
            'new_price.*' => 'required',
            'description' => 'required',
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $drink_id = $request->drink_id;
            $date = $request->date;
            $unit = $request->unit;
            $quantity = $request->quantity;
            $unit_price = $request->unit_price;
            $new_quantity = $request->new_quantity;
            $new_price = $request->new_price;
            $title = $request->title;  

            $created_by = $this->user->name;
            $description =$request->description; 

            for( $count = 0; $count < count($drink_id); $count++ ){
                $total_value = $quantity[$count] * $unit_price[$count];
                $new_total_value = $new_quantity[$count] * $new_price[$count];
                $relicat = $quantity[$count] - $new_quantity[$count];
                $data = array(
                    'drink_id' => $drink_id[$count],
                    'date' => $date,
                    'title' => $title,
                    'quantity' => $quantity[$count],
                    'unit' => $unit[$count],
                    'unit_price' => $unit_price[$count],
                    'total_value' => $total_value,
                    'new_quantity' => $new_quantity[$count],
                    'new_price' => $new_price[$count],
                    'new_total_value' => $new_total_value,
                    'relicat' => $relicat,
                    'created_by' => $created_by,
                    'description' => $description,
                    'created_at' => \Carbon\Carbon::now()
                );
                $insert_data[] = $data;
            }
            //create inventory
            $inventory = DrinkSmallStoreInventory::where('inventory_no', $inventory_no)->first();
            $inventory->date = $date;
            $inventory->title = $title;
            $inventory->description = $description;
            $inventory->created_by = $created_by;
            $inventory->save();

        session()->flash('success', 'Inventory has been updated !!');
        return back();
    }

    public function bon_inventaire($inventory_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_small_inventory.view')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $description = DrinkSmallStoreInventory::where('inventory_no', $inventory_no)->value('description');
        $title = DrinkSmallStoreInventory::where('inventory_no', $inventory_no)->value('title');
        $inventory_no = DrinkSmallStoreInventory::where('inventory_no', $inventory_no)->value('inventory_no');
        $date = DrinkSmallStoreInventory::where('inventory_no', $inventory_no)->value('date');
        $inventory_signature = DrinkSmallStoreInventory::where('inventory_no', $inventory_no)->value('inventory_signature');
        $datas = DrinkSmallStoreInventoryDetail::where('inventory_no', $inventory_no)->get();

        $totalValueActuelle = DB::table('drink_small_store_inventory_details')
            ->where('inventory_no', '=', $inventory_no)
            ->sum('total_purchase_value');
         $totalValueNew = DB::table('drink_small_store_inventory_details')
            ->where('inventory_no', '=', $inventory_no)
            ->sum('new_total_purchase_value');
        $totalValueNewGodet = DB::table('drink_small_store_inventory_details')
            ->where('inventory_no', '=', $inventory_no)
            ->sum('new_total_selling_value_ml');
        $total_quantity = DB::table('drink_small_store_inventory_details')
            ->where('inventory_no', '=', $inventory_no)
            ->sum('quantity');
        $new_total_quantity = DB::table('drink_small_store_inventory_details')
            ->where('inventory_no', '=', $inventory_no)
            ->sum('new_quantity');
        $total_relicat = DB::table('drink_small_store_inventory_details')
            ->where('inventory_no', '=', $inventory_no)
            ->sum('relicat');

        $gestionnaire = DrinkSmallStoreInventory::where('inventory_no', $inventory_no)->value('created_by');
        $pdf = PDF::loadView('backend.pages.document.drink_small_store_inventory',compact('datas','inventory_no','totalValueActuelle','totalValueNew','totalValueNewGodet','gestionnaire','setting','title','description','date','inventory_signature','total_quantity','new_total_quantity','total_relicat'));//->setPaper('a4', 'landscape');

        Storage::put('public/pdf/drink_small_store_inventory/'.'BON_INVENTAIRE_'.$inventory_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('BON_INVENTAIRE_'.$inventory_no.'.pdf');
    }

    public function get_inventory_data()
    {
        return Excel::download(new InventoryExport, 'inventories.xlsx');
    }


    public function validateInventory($inventory_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_small_inventory.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any inventory !');
        }

        $datas = DrinkSmallStoreInventoryDetail::where('inventory_no', $inventory_no)->get();

        foreach($datas as $data){
            $quantiteStockInitial = DrinkSmallStoreDetail::where('drink_id', $data->drink_id)->value('quantity_bottle');
            $quantiteStockInitialGodet = DrinkSmallStoreDetail::where('drink_id', $data->drink_id)->value('quantity_ml');
            $valeurStockInitial = DrinkSmallStoreDetail::where('drink_id', $data->drink_id)->value('total_selling_value');
            $valeurStockInitialGodet = DrinkSmallStoreDetail::where('drink_id', $data->drink_id)->value('total_selling_value_ml');

                $drink_calc = array(
                        'purchase_price' => $data->new_purchase_price,
                        'cump' => $data->new_purchase_price,
                        'selling_price' => $data->new_selling_price,
                        'unit' => $data->new_unit,
                        'quantity_bottle' => $data->new_quantity
                    );


                Drink::where('id',$data->drink_id)
                        ->update($drink_calc);

                    $sto = array(
                        'drink_id' => $data->drink_id,
                        'quantity_bottle' => $data->new_quantity,
                        'quantity_ml' => $data->new_quantity_ml,
                        'unit' => $data->new_unit,
                        'cump' => $data->new_selling_price,
                        'purchase_price' => $data->purchase_price,
                        'selling_price' => $data->new_selling_price,
                        'selling_price_ml' => $data->new_selling_price_ml,
                        'total_purchase_value' => $data->purchase_price * $data->new_quantity,
                        'total_selling_value' => $data->new_selling_price * $data->new_quantity,
                        'total_selling_value_ml' => $data->new_selling_price_ml * $data->new_quantity_ml,
                        'updated_by' => $this->user->name,
                    );

                    $reportData = array(
                        'drink_id' => $data->drink_id,
                        'quantity_stock_initial' => $quantiteStockInitial,
                        'quantity_stock_initial_ml' => $quantiteStockInitialGodet,
                        'value_stock_initial' => $valeurStockInitial,
                        'value_stock_initial_ml' => $valeurStockInitialGodet,
                        'quantity_inventory' => $data->new_quantity,
                        'value_inventory' => $data->new_selling_price * $data->new_quantity,
                        'quantity_inventory_ml' => $data->new_quantity_ml,
                        'value_inventory_ml' => $data->new_selling_price_ml * $data->new_quantity_ml,
                        'inventory_no' => $data->inventory_no,
                        'code_store' => $data->code_store,
                        'created_by' => $this->user->name,
                    );

                    DrinkSmallStoreDetail::where('code',$data->code_store)->where('drink_id',$data->drink_id)
                        ->update($sto);
                    DrinkSmallStoreDetail::where('code',$data->code_store)->where('drink_id', '=', $data->drink_id)
                ->update(['verified' => true]);

                DrinkSmallReport::insert($reportData);
        /*
        
        $theUrl = config('app.guzzle_test_url').'/ebms_api/login/';
        $response = Http::post($theUrl, [
            'username'=> "wsconfig('app.tin_number_company')00565",
            'password'=> "5VS(GO:p"

        ]);
        $data1 =  json_decode($response);
        $data2 = ($data1->result);       
    
        $token = $data2->token;

        $theUrl = config('app.guzzle_test_url').'/ebms_api/AddStockMovement';  
        $response = Http::withHeaders([
        'Authorization' => 'Bearer '.$token,
        'Accept' => 'application/json'])->post($theUrl, [
            'system_or_device_id'=> "wsconfig('app.tin_number_company')00565",
            'item_code'=> $data->drink->code,
            'item_designation'=>$data->drink->name,
            'item_quantity'=>$data->new_quantity,
            'item_measurement_unit'=>$data->new_unit,
            'item_purchase_or_sale_price'=>$data->new_purchase_price,
            'item_purchase_or_sale_currency'=> "BIF",
            'item_movement_type'=>"EI",
            'item_movement_invoice_ref'=>"",
            'item_movement_description'=>$data->description,
            'item_movement_date'=> Carbon::parse($data->updated_at)->format('Y-m-d H:i:s'),

        ]); 
        */
     
    }
        DrinkSmallStoreInventory::where('inventory_no', '=', $inventory_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            DrinkSmallStoreInventoryDetail::where('inventory_no', '=', $inventory_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

            session()->flash('success', 'inventory has been validated !!');
            return back();
        
    }

    public function rejectInventory($inventory_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_small_inventory.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any inventory !');
        }
            DrinkSmallStoreInventory::where('inventory_no', '=', $inventory_no)
                ->update(['status' => 1,'rejected_by' => $this->user->name]);
             DrinkSmallStoreInventoryDetail::where('inventory_no', '=', $inventory_no)
                ->update(['status' => 1,'rejected_by' => $this->user->name]);

        session()->flash('success', 'inventory has been rejected !!');
        return back();
    }

    public function resetInventory($inventory_no)
    {
       if (is_null($this->user) || !$this->user->can('drink_small_inventory.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any inventory !');
        }
            DrinkSmallStoreInventory::where('inventory_no', '=', $inventory_no)
                ->update(['status' => 0,'reseted_by' => $this->user->name]);
                DrinkSmallStoreInventoryDetail::where('inventory_no', '=', $inventory_no)
                ->update(['status' => 0,'reseted_by' => $this->user->name]);

        session()->flash('success', 'inventory has been reseted !!');
        return back();
    }

    public function exportToExcel(Request $request,$code)
    {
        return Excel::download(new DrinkSmStoreInventoryExport($code), 'inventaire_petit_stock.xlsx');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($inventory_no)
    {
        if (is_null($this->user) || !$this->user->can('drink_small_inventory.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any inventory !');
        }

        $inventory = DrinkSmallStoreInventory::where('inventory_no', $inventory_no)->first();
        if (!is_null($inventory)) {
            $inventory->delete();
            DrinkSmallStoreInventoryDetail::where('inventory_no',$inventory_no)->delete();

            $email = 'ambazamarcellin2001@gmail.com';
            $auteur = $this->user->name;
            $mailData = [
                    'title' => 'Suppression Inventaire',
                    'email' => $email,
                    'inventory_no' => $inventory_no,
                    'auteur' => $auteur,
                    ];
         
            Mail::to($email)->send(new DeleteInventoryMail($mailData));
        }

        session()->flash('success', 'Inventory has been deleted !!');
        return back();
    }
}

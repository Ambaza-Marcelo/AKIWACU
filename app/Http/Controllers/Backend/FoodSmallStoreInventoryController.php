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
use App\Models\FoodSmallStore;
use App\Models\FoodSmallStoreDetail;
use App\Models\FoodSmallStoreInventory;
use App\Models\FoodSmallStoreInventoryDetail;
use App\Models\FoodSmallReport;
use Carbon\Carbon;
use Excel;
use App\Models\Setting;
use App\Mail\DeleteInventoryMail;
use PDF;
use Mail;
use Validator;

class FoodSmallStoreInventoryController extends Controller
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
        if (is_null($this->user) || !$this->user->can('food_small_inventory.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any inventory !');
        }

        $inventories = FoodSmallStoreInventory::all();
        return view('backend.pages.food_small_store_inventory.index', compact('inventories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($code)
    {
        if (is_null($this->user) || !$this->user->can('food_small_inventory.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any inventory !');
        }

        $foods  = Food::where('code_store',$code)->orderBy('name','asc')->get();
        $datas = FoodSmallStoreDetail::where('code',$code)->where('food_id','!=','')->where('verified',false)->get();
        return view('backend.pages.food_small_store_inventory.create', compact('datas','foods','code'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('food_small_inventory.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any inventory !');
        }
        $rules = array(
            'food_id.*' => 'required',
            'date' => 'required|date',
            'title' => 'required',
            'quantity.*' => 'required',
            'unit.*' => 'required',
            'purchase_price.*' => 'required',
            'new_quantity.*' => 'required',
            'new_purchase_price.*' => 'required',
            'new_unit.*' => 'required',
            'description' => 'required',
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $food_id = $request->food_id;
            $date = $request->date;
            $unit = $request->unit;
            $quantity = $request->quantity;
            $quantity_portion = $request->quantity_portion;
            $purchase_price = $request->purchase_price;
            $new_quantity = $request->new_quantity;
            $new_quantity_portion = $request->new_quantity_portion;
            $new_price = $request->new_price;
            $title = $request->title;
            $code_store = $request->code_store;
            $new_purchase_price = $request->new_purchase_price;
            $new_unit = $request->new_unit; 

            $latest = FoodSmallStoreInventory::latest()->first();
            if ($latest) {
               $inventory_no = 'BI' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $inventory_no = 'BI' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $inventory_signature = "4001711615".Carbon::parse(Carbon::now())->format('YmdHis')."/".$inventory_no;

            $created_by = $this->user->name;
            $description =$request->description; 

            for( $count = 0; $count < count($food_id); $count++ ){
                $total_purchase_value = $quantity[$count] * $purchase_price[$count];
                $total_purchase_value_portion = $quantity_portion[$count] * $purchase_price[$count];
                $new_total_purchase_value = $new_quantity[$count] * $new_purchase_price[$count];
                $new_total_purchase_value_portion = $new_quantity_portion[$count] * $new_purchase_price[$count];
                $relicat = $quantity[$count] - $new_quantity[$count];
                $relicat_portion = $quantity_portion[$count] - $new_quantity_portion[$count];
                $data = array(
                    'food_id' => $food_id[$count],
                    'date' => $date,
                    'title' => $title,
                    'code_store' => $code_store,
                    'quantity' => $quantity[$count],
                    'quantity_portion' => $quantity_portion[$count],
                    'unit' => $unit[$count],
                    'purchase_price' => $purchase_price[$count],
                    'total_purchase_value' => $total_purchase_value,
                    'total_purchase_value_portion' => $total_purchase_value_portion,
                    'new_quantity' => $new_quantity[$count],
                    'new_quantity_portion' => $new_quantity_portion[$count],
                    'new_purchase_price' => $new_purchase_price[$count],
                    'new_total_purchase_value' => $new_total_purchase_value,
                    'new_total_purchase_value_portion' => $new_total_purchase_value_portion,
                    'new_unit' => $new_unit[$count],
                    'relicat' => $relicat,
                    'relicat_portion' => $relicat_portion,
                    'inventory_no' => $inventory_no,
                    'inventory_signature' => $inventory_signature,
                    'created_by' => $created_by,
                    'description' => $description,
                    'created_at' => \Carbon\Carbon::now()
                );
                $insert_data[] = $data;
                
            }
            FoodSmallStoreInventoryDetail::insert($insert_data);
            //create inventory
            $inventory = new FoodSmallStoreInventory();
            $inventory->date = $date;
            $inventory->title = $title;
            $inventory->inventory_no = $inventory_no;
            $inventory->code_store = $code_store;
            $inventory->inventory_signature = $inventory_signature;
            $inventory->description = $description;
            $inventory->created_by = $created_by;
            $inventory->save();
         
        session()->flash('success', 'Inventory has been created !!');
        return redirect()->route('admin.food-small-store-inventory.index');
    }

    public function referenceInventaire()
    {
        if (is_null($this->user) || !$this->user->can('food_small_inventory.view')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $datas = FoodBigStoreDetail::where('verified',false)->get();
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
        $code = FoodSmallStoreInventoryDetail::where('inventory_no', $inventory_no)->value('inventory_no');
        $inventories = FoodSmallStoreInventoryDetail::where('inventory_no', $inventory_no)->get();
        return view('backend.pages.food_small_store_inventory.show', compact('inventories','code'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function inventory($id)
    {
        if (is_null($this->user) || !$this->user->can('food_small_inventory.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to make any inventory !');
        }

        $stock = FoodBigStoreDetail::find($id);
        $articles  = Article::all();
        return view('backend.pages.inventory.create', compact('stock', 'articles'));
    }

    public function edit($inventory_no)
    {
        if (is_null($this->user) || !$this->user->can('food_small_inventory.edit')) {
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
        if (is_null($this->user) || !$this->user->can('food_small_inventory.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any inventory !');
        }

    }

    public function bon_inventaire($inventory_no)
    {
        if (is_null($this->user) || !$this->user->can('food_small_inventory.view')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $description = FoodSmallStoreInventory::where('inventory_no', $inventory_no)->value('description');
        $title = FoodSmallStoreInventory::where('inventory_no', $inventory_no)->value('title');
        $inventory_no = FoodSmallStoreInventory::where('inventory_no', $inventory_no)->value('inventory_no');
        $date = FoodSmallStoreInventory::where('inventory_no', $inventory_no)->value('date');
        $inventory_signature = FoodSmallStoreInventory::where('inventory_no', $inventory_no)->value('inventory_signature');
        $datas = FoodSmallStoreInventoryDetail::where('inventory_no', $inventory_no)->get();

        $totalValueActuelle = DB::table('food_small_store_inventory_details')
            ->where('inventory_no', '=', $inventory_no)
            ->sum('total_purchase_value');
         $totalValueNew = DB::table('food_small_store_inventory_details')
            ->where('inventory_no', '=', $inventory_no)
            ->sum('new_total_purchase_value');
        $totalValueNewPortion = DB::table('food_small_store_inventory_details')
            ->where('inventory_no', '=', $inventory_no)
            ->sum('new_total_purchase_value_portion');
        $gestionnaire = FoodSmallStoreInventory::where('inventory_no', $inventory_no)->value('created_by');
        $pdf = PDF::loadView('backend.pages.document.food_small_store_inventory',compact('datas','inventory_no','totalValueActuelle','totalValueNew','totalValueNewPortion','gestionnaire','setting','title','description','date','inventory_signature'));//->setPaper('a4', 'landscape');

        Storage::put('public/pdf/food_small_store_inventory/'.'BON_INVENTAIRE_'.$inventory_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('BON_INVENTAIRE_'.$inventory_no.'.pdf');
    }

    public function get_inventory_data()
    {
        return Excel::download(new InventoryExport, 'inventories.xlsx');
    }


    public function validateInventory($inventory_no)
    {
       if (is_null($this->user) || !$this->user->can('food_small_inventory.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any inventory !');
        }

        $datas = FoodSmallStoreInventoryDetail::where('inventory_no', $inventory_no)->get();

        foreach($datas as $data){
            $quantiteStockInitial = FoodSmallStoreDetail::where('food_id', $data->food_id)->value('quantity');
            $quantiteStockInitialPortion = FoodSmallStoreDetail::where('food_id', $data->food_id)->value('quantity_portion');
            $valeurStockInitial = FoodSmallStoreDetail::where('food_id', $data->food_id)->value('total_purchase_value');
            $valeurStockInitialPortion = FoodSmallStoreDetail::where('food_id', $data->food_id)->value('total_purchase_value');

                $food_calc = array(
                        'purchase_price' => $data->new_purchase_price,
                        'cump' => $data->new_purchase_price,
                        'unit' => $data->new_unit,
                        'quantity' => $data->new_quantity
                    );


                Food::where('id',$data->food_id)
                        ->update($food_calc);

                    $sto = array(
                        'food_id' => $data->food_id,
                        'quantity' => $data->new_quantity,
                        'quantity_portion' => $data->new_quantity_portion,
                        'unit' => $data->new_unit,
                        'cump' => $data->new_purchase_price,
                        'purchase_price' => $data->new_purchase_price,
                        'total_purchase_value' => $data->new_purchase_price * $data->new_quantity,
                        'total_purchase_value_portion' => $data->new_purchase_price * $data->new_quantity_portion,
                        'updated_by' => $this->user->name,
                    );

                    $reportData = array(
                        'food_id' => $data->food_id,
                        'quantity_stock_initial' => $quantiteStockInitial,
                        'quantity_stock_initial_portion' => $quantiteStockInitialPortion,
                        'value_stock_initial' => $valeurStockInitial,
                        'value_stock_initial_portion' => $valeurStockInitialPortion,
                        'quantity_inventory' => $data->new_quantity,
                        'quantity_inventory_portion' => $data->new_quantity_portion,
                        'value_inventory' => $data->new_purchase_price * $data->new_quantity,
                        'value_inventory_portion' => $data->new_purchase_price * $data->new_quantity,
                        'inventory_no' => $data->inventory_no,
                        'code_store' => $data->code_store,
                        'created_by' => $this->user->name,
                    );

                    FoodSmallStoreDetail::where('code',$data->code_store)->where('food_id',$data->food_id)
                        ->update($sto);
                    FoodSmallStoreDetail::where('code',$data->code_store)->where('food_id', '=', $data->food_id)
                ->update(['verified' => true]);

                FoodSmallReport::insert($reportData);
        }
            FoodSmallStoreInventory::where('inventory_no', '=', $inventory_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            FoodSmallStoreInventoryDetail::where('inventory_no', '=', $inventory_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        session()->flash('success', 'inventory has been validated !!');
        return back();
    }

    public function rejectInventory($inventory_no)
    {
       if (is_null($this->user) || !$this->user->can('food_small_inventory.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any inventory !');
        }
            FoodSmallStoreInventory::where('inventory_no', '=', $inventory_no)
                ->update(['status' => 1,'rejected_by' => $this->user->name]);
             FoodSmallStoreInventoryDetail::where('inventory_no', '=', $inventory_no)
                ->update(['status' => 1,'rejected_by' => $this->user->name]);

        session()->flash('success', 'inventory has been rejected !!');
        return back();
    }

    public function resetInventory($inventory_no)
    {
       if (is_null($this->user) || !$this->user->can('food_small_inventory.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any inventory !');
        }
            FoodSmallStoreInventory::where('inventory_no', '=', $inventory_no)
                ->update(['status' => 0,'reseted_by' => $this->user->name]);
                FoodSmallStoreInventoryDetail::where('inventory_no', '=', $inventory_no)
                ->update(['status' => 0,'reseted_by' => $this->user->name]);

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
        if (is_null($this->user) || !$this->user->can('food_small_inventory.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any inventory !');
        }

        $inventory = FoodSmallStoreInventory::where('inventory_no', $inventory_no)->first();
        if (!is_null($inventory)) {
            $inventory->delete();
            FoodSmallStoreInventoryDetail::where('inventory_no',$inventory_no)->delete();

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

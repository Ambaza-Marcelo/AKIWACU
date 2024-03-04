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
use App\Models\MaterialBigStore;
use App\Models\MaterialSmallStoreDetail;
use App\Models\MaterialSmallStoreInventory;
use App\Models\MaterialSmallStoreInventoryDetail;
use App\Models\MaterialSmallReport;
use Carbon\Carbon;
use Excel;
use App\Models\Setting;
use App\Mail\DeleteInventoryMail;
use PDF;
use Mail;
use Validator;

class MaterialSmallStoreInventoryController extends Controller
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
        if (is_null($this->user) || !$this->user->can('material_small_inventory.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any inventory !');
        }

        $inventories = MaterialSmallStoreInventory::all();
        return view('backend.pages.material_small_store_inventory.index', compact('inventories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($code)
    {
        if (is_null($this->user) || !$this->user->can('material_small_inventory.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any inventory !');
        }

        $materials  = Material::where('code_store',$code)->orderBy('name','asc')->get();
        $datas = MaterialSmallStoreDetail::where('code',$code)->where('material_id','!=','')->where('verified',false)->get();
        return view('backend.pages.material_small_store_inventory.create', compact('datas','materials','code'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('material_small_inventory.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any inventory !');
        }
        $rules = array(
            'material_id.*' => 'required',
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

            $material_id = $request->material_id;
            $date = $request->date;
            $unit = $request->unit;
            $quantity = $request->quantity;
            $purchase_price = $request->purchase_price;
            $new_quantity = $request->new_quantity;
            $new_price = $request->new_price;
            $title = $request->title;
            $code_store = $request->code_store;
            $new_purchase_price = $request->new_purchase_price;
            $new_unit = $request->new_unit; 

            $latest = MaterialSmallStoreInventory::latest()->first();
            if ($latest) {
               $inventory_no = 'BI' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $inventory_no = 'BI' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $inventory_signature = "4001711615".Carbon::parse(Carbon::now())->format('YmdHis')."/".$inventory_no;

            $created_by = $this->user->name;
            $description =$request->description; 

            for( $count = 0; $count < count($material_id); $count++ ){
                $total_purchase_value = $quantity[$count] * $purchase_price[$count];
                $new_total_purchase_value = $new_quantity[$count] * $new_purchase_price[$count];
                $relicat = $quantity[$count] - $new_quantity[$count];
                $data = array(
                    'material_id' => $material_id[$count],
                    'date' => $date,
                    'title' => $title,
                    'code_store' => $code_store,
                    'quantity' => $quantity[$count],
                    'unit' => $unit[$count],
                    'purchase_price' => $purchase_price[$count],
                    'total_purchase_value' => $total_purchase_value,
                    'new_quantity' => $new_quantity[$count],
                    'new_purchase_price' => $new_purchase_price[$count],
                    'new_total_purchase_value' => $new_total_purchase_value,
                    'new_unit' => $new_unit[$count],
                    'relicat' => $relicat,
                    'inventory_no' => $inventory_no,
                    'inventory_signature' => $inventory_signature,
                    'created_by' => $created_by,
                    'description' => $description,
                    'created_at' => \Carbon\Carbon::now()
                );
                $insert_data[] = $data;
                
            }
            MaterialSmallStoreInventoryDetail::insert($insert_data);
            //create inventory
            $inventory = new MaterialSmallStoreInventory();
            $inventory->date = $date;
            $inventory->title = $title;
            $inventory->inventory_no = $inventory_no;
            $inventory->code_store = $code_store;
            $inventory->inventory_signature = $inventory_signature;
            $inventory->description = $description;
            $inventory->created_by = $created_by;
            $inventory->save();
         
        session()->flash('success', 'Inventory has been created !!');
        return redirect()->route('admin.material-small-store-inventory.index');
    }

    public function referenceInventaire()
    {
        if (is_null($this->user) || !$this->user->can('material_small_inventory.view')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $datas = MaterialSmallStoreDetail::where('verified',false)->get();
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
        $code = MaterialSmallStoreInventoryDetail::where('inventory_no', $inventory_no)->value('inventory_no');
        $inventories = MaterialSmallStoreInventoryDetail::where('inventory_no', $inventory_no)->get();
        return view('backend.pages.material_small_store_inventory.show', compact('inventories','code'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function inventory($id)
    {
        if (is_null($this->user) || !$this->user->can('material_small_inventory.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to make any inventory !');
        }

        $stock = MaterialSmallStoreDetail::find($id);
        $articles  = Article::all();
        return view('backend.pages.inventory.create', compact('stock', 'articles'));
    }

    public function edit($inventory_no)
    {
        if (is_null($this->user) || !$this->user->can('material_small_inventory.edit')) {
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
        if (is_null($this->user) || !$this->user->can('material_small_inventory.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any inventory !');
        }

       
    }

    public function bon_inventaire($inventory_no)
    {
        if (is_null($this->user) || !$this->user->can('material_small_inventory.view')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $description = MaterialSmallStoreInventory::where('inventory_no', $inventory_no)->value('description');
        $title = MaterialSmallStoreInventory::where('inventory_no', $inventory_no)->value('title');
        $inventory_no = MaterialSmallStoreInventory::where('inventory_no', $inventory_no)->value('inventory_no');
        $date = MaterialSmallStoreInventory::where('inventory_no', $inventory_no)->value('date');
        $inventory_signature = MaterialSmallStoreInventory::where('inventory_no', $inventory_no)->value('inventory_signature');
        $datas = MaterialSmallStoreInventoryDetail::where('inventory_no', $inventory_no)->get();

        $totalValueActuelle = DB::table('material_small_store_inventory_details')
            ->where('inventory_no', '=', $inventory_no)
            ->sum('total_purchase_value');
         $totalValueNew = DB::table('material_small_store_inventory_details')
            ->where('inventory_no', '=', $inventory_no)
            ->sum('new_total_purchase_value');
        $gestionnaire = MaterialSmallStoreInventory::where('inventory_no', $inventory_no)->value('created_by');
        $pdf = PDF::loadView('backend.pages.document.material_small_store_inventory',compact('datas','inventory_no','totalValueActuelle','totalValueNew','gestionnaire','setting','title','description','date','inventory_signature'));//->setPaper('a4', 'landscape');

        Storage::put('public/pdf/material_small_store_inventory/'.'BON_INVENTAIRE_'.$inventory_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('BON_INVENTAIRE_'.$inventory_no.'.pdf');
    }

    public function get_inventory_data()
    {
        return Excel::download(new InventoryExport, 'inventories.xlsx');
    }


    public function validateInventory($inventory_no)
    {
       if (is_null($this->user) || !$this->user->can('material_small_inventory.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any inventory !');
        }

        $datas = MaterialSmallStoreInventoryDetail::where('inventory_no', $inventory_no)->get();

        foreach($datas as $data){
            $valeurStockInitial = MaterialSmallStoreDetail::where('material_id', $data->material_id)->value('total_purchase_value');

                $material_calc = array(
                        'purchase_price' => $data->new_purchase_price,
                        'cump' => $data->new_purchase_price,
                        'unit' => $data->new_unit,
                        'quantity' => $data->new_quantity
                    );


                Material::where('id',$data->material_id)
                        ->update($material_calc);

                    $sto = array(
                        'material_id' => $data->material_id,
                        'quantity' => $data->new_quantity,
                        'unit' => $data->new_unit,
                        'cump' => $data->new_purchase_price,
                        'purchase_price' => $data->new_purchase_price,
                        'total_purchase_value' => $data->new_purchase_price * $data->new_quantity,
                        'updated_by' => $this->user->name,
                    );

                    $reportData = array(
                        'material_id' => $data->material_id,
                        'quantity_stock_initial' => $data->quantity,
                        'value_stock_initial' => $data->total_purchase_value,
                        'quantity_inventory' => $data->new_quantity,
                        'value_inventory' => $data->new_purchase_price * $data->new_quantity,
                        'inventory_no' => $data->inventory_no,
                        'code_store' => $data->code_store,
                        'created_by' => $this->user->name,
                    );

                    MaterialSmallStoreDetail::where('code',$data->code_store)->where('material_id',$data->material_id)
                        ->update($sto);
                    MaterialSmallStoreDetail::where('code',$data->code_store)->where('material_id', '=', $data->material_id)
                ->update(['verified' => true]);

                MaterialSmallReport::insert($reportData);
        }
            MaterialSmallStoreInventory::where('inventory_no', '=', $inventory_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            MaterialSmallStoreInventoryDetail::where('inventory_no', '=', $inventory_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        session()->flash('success', 'inventory has been validated !!');
        return back();
    }

    public function rejectInventory($inventory_no)
    {
       if (is_null($this->user) || !$this->user->can('material_small_inventory.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any inventory !');
        }
            MaterialSmallStoreInventory::where('inventory_no', '=', $inventory_no)
                ->update(['status' => 1,'rejected_by' => $this->user->name]);
             MaterialSmallStoreInventoryDetail::where('inventory_no', '=', $inventory_no)
                ->update(['status' => 1,'rejected_by' => $this->user->name]);

        session()->flash('success', 'inventory has been rejected !!');
        return back();
    }

    public function resetInventory($inventory_no)
    {
       if (is_null($this->user) || !$this->user->can('material_small_inventory.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any inventory !');
        }
            MaterialSmallStoreInventory::where('inventory_no', '=', $inventory_no)
                ->update(['status' => 0,'reseted_by' => $this->user->name]);
                MaterialSmallStoreInventoryDetail::where('inventory_no', '=', $inventory_no)
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
        if (is_null($this->user) || !$this->user->can('material_small_inventory.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any inventory !');
        }

        $inventory = MaterialSmallStoreInventory::where('inventory_no', $inventory_no)->first();
        if (!is_null($inventory)) {
            $inventory->delete();
            MaterialSmallStoreInventoryDetail::where('inventory_no',$inventory_no)->delete();

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

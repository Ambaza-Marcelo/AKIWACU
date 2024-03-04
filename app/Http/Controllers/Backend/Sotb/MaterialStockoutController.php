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
use App\Models\SotbMaterial;
use App\Models\SotbMaterialStockout;
use App\Models\SotbMaterialStockoutDetail;
use App\Models\SotbMaterialMdStoreDetail;
use App\Models\SotbMaterialSmStoreDetail;
use App\Models\SotbMaterialMdStore;
use App\Models\SotbMaterialSmStore;
use App\Models\SotbMaterialMdStoreReport;
use App\Models\SotbMaterialSmStoreReport;
use App\Models\SotbMaterialBgStoreDetail;
use App\Models\SotbMaterialBgStore;
use App\Models\SotbMaterialBgStoreReport;
use Carbon\Carbon;
use PDF;
use Validator;
use Excel;
use Mail;

class MaterialStockoutController extends Controller
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
        if (is_null($this->user) || !$this->user->can('sotb_material_stockout.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stockout !');
        }

        $stockouts = SotbMaterialStockout::all();
        return view('backend.pages.sotb.material_stockout.index', compact('stockouts'));
    }

    public function choose()
    {
        if (is_null($this->user) || !$this->user->can('sotb_material_stockout.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stockout !');
        }

        return view('backend.pages.sotb.material_stockout.choose');
    }

    public function selectBgStore()
    {
        if (is_null($this->user) || !$this->user->can('sotb_material_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $stores = SotbMaterialBgStore::orderBy('name')->get();
        return view('backend.pages.sotb.material_stockout.select_bg_store', compact('stores'));
    }

    public function selectMdStore()
    {
        if (is_null($this->user) || !$this->user->can('sotb_material_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $stores = SotbMaterialMdStore::orderBy('name')->get();
        return view('backend.pages.sotb.material_stockout.select_md_store', compact('stores'));
    }

    public function selectSmStore()
    {
        if (is_null($this->user) || !$this->user->can('sotb_material_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $stores = SotbMaterialSmStore::orderBy('name')->get();
        return view('backend.pages.sotb.material_stockout.select_sm_store', compact('stores'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($code)
    {
        if (is_null($this->user) || !$this->user->can('sotb_material_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $materials = SotbMaterialMdStoreDetail::where('material_id','!=','')->where('code',$code)->get();
        $material_origin_stores = SotbMaterialMdStore::all();
        return view('backend.pages.sotb.material_stockout.create', compact('materials','material_origin_stores','code'));
    }

    public function createFromBig($code)
    {
        if (is_null($this->user) || !$this->user->can('sotb_material_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $materials = SotbMaterialBgStoreDetail::where('material_id','!=','')->where('code',$code)->get();
        $material_origin_stores = SotbMaterialBgStore::all();
        return view('backend.pages.sotb.material_stockout.create_from_big', compact('materials','material_origin_stores','code'));
    }

    public function createFromSmall($code)
    {
        if (is_null($this->user) || !$this->user->can('sotb_material_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $materials = SotbMaterialSmStoreDetail::where('material_id','!=','')->where('code',$code)->get();
        $material_origin_stores = SotbMaterialSmStore::all();
        return view('backend.pages.sotb.material_stockout.create_from_small', compact('materials','material_origin_stores','code'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('sotb_material_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $rules = array(
                'material_id.*'  => 'required',
                'date'  => 'required',
                'unit.*'  => 'required',
                'quantity.*'  => 'required',
                'asker'  => 'required',
                'origin_md_store_id'  => 'required',
                'destination'  => 'required',
                'description'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $material_id = $request->material_id;
            $date = $request->date;
            $asker = $request->asker;
            $destination = $request->destination;
            $code_store = $request->code_store;
            $item_movement_type = $request->item_movement_type;
            $description =$request->description; 
            $origin_md_store_id = $request->origin_md_store_id;
            $unit = $request->unit;
            $quantity = $request->quantity;
            $store_type = $request->store_type;
            

            $latest = SotbMaterialStockout::latest()->first();
            if ($latest) {
               $stockout_no = 'BS' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $stockout_no = 'BS' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $stockout_signature = "4001711615".Carbon::parse(Carbon::now())->format('YmdHis')."/".$stockout_no;


            for( $count = 0; $count < count($material_id); $count++ ){

                $selling_price = SotbMaterial::where('id', $material_id[$count])->value('selling_price');
                $purchase_price = SotbMaterial::where('id', $material_id[$count])->value('purchase_price');

                $total_value = $quantity[$count] * $purchase_price;
                $total_purchase_value = $quantity[$count] * $purchase_price;
                $total_selling_value = $quantity[$count] * $selling_price;

                $data = array(
                    'material_id' => $material_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'unit' => $unit[$count],
                    'purchase_price' => $purchase_price,
                    'selling_price' => $selling_price,
                    'total_purchase_value' => $total_purchase_value,
                    'total_selling_value' => $total_selling_value,
                    'asker' => $asker,
                    'code_store' => $code_store,
                    'item_movement_type' => $item_movement_type,
                    'destination' => $destination,
                    'origin_md_store_id' => $origin_md_store_id,
                    'stockout_no' => $stockout_no,
                    'stockout_signature' => $stockout_signature,
                    'store_type' => $store_type,
                    'created_by' => $created_by,
                    'description' => $description,
                    'status' => 1,
                    'created_at' => \Carbon\Carbon::now()

                );
                $insert_data[] = $data;

                
            }
            SotbMaterialStockoutDetail::insert($insert_data);


            //create stockout
            $stockout = new SotbMaterialStockout();
            $stockout->date = $date;
            $stockout->stockout_no = $stockout_no;
            $stockout->stockout_signature = $stockout_signature;
            $stockout->store_type = $store_type;
            $stockout->code_store = $code_store;
            $stockout->item_movement_type = $item_movement_type;
            $stockout->asker = $asker;
            $stockout->destination = $destination;
            $stockout->origin_md_store_id = $origin_md_store_id;
            $stockout->created_by = $created_by;
            $stockout->status = 1;
            $stockout->description = $description;
            $stockout->save();
            
        session()->flash('success', 'stockout has been created !!');
        return redirect()->route('admin.sotb-material-stockouts.index');
    }

    public function storeFromBig(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('sotb_material_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $rules = array(
                'material_id.*'  => 'required',
                'date'  => 'required',
                'unit.*'  => 'required',
                'quantity.*'  => 'required',
                'asker'  => 'required',
                'origin_bg_store_id'  => 'required',
                'destination'  => 'required',
                'description'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $material_id = $request->material_id;
            $date = $request->date;
            $asker = $request->asker;
            $destination = $request->destination;
            $code_store = $request->code_store;
            $item_movement_type = $request->item_movement_type;
            $description =$request->description; 
            $origin_bg_store_id = $request->origin_bg_store_id;
            $unit = $request->unit;
            $quantity = $request->quantity;
            $store_type = $request->store_type;
            

            $latest = SotbMaterialStockout::latest()->first();
            if ($latest) {
               $stockout_no = 'BS' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $stockout_no = 'BS' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $stockout_signature = "4001711615".Carbon::parse(Carbon::now())->format('YmdHis')."/".$stockout_no;


            for( $count = 0; $count < count($material_id); $count++ ){

                $selling_price = SotbMaterial::where('id', $material_id[$count])->value('selling_price');
                $purchase_price = SotbMaterial::where('id', $material_id[$count])->value('purchase_price');

                $total_value = $quantity[$count] * $purchase_price;
                $total_purchase_value = $quantity[$count] * $purchase_price;
                $total_selling_value = $quantity[$count] * $selling_price;

                $data = array(
                    'material_id' => $material_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'unit' => $unit[$count],
                    'purchase_price' => $purchase_price,
                    'selling_price' => $selling_price,
                    'total_purchase_value' => $total_purchase_value,
                    'total_selling_value' => $total_selling_value,
                    'asker' => $asker,
                    'destination' => $destination,
                    'code_store' => $code_store,
                    'item_movement_type' => $item_movement_type,
                    'origin_bg_store_id' => $origin_bg_store_id,
                    'stockout_no' => $stockout_no,
                    'stockout_signature' => $stockout_signature,
                    'store_type' => $store_type,
                    'created_by' => $created_by,
                    'description' => $description,
                    'status' => 1,
                    'created_at' => \Carbon\Carbon::now()

                );
                $insert_data[] = $data;

                
            }
            SotbMaterialStockoutDetail::insert($insert_data);


            //create stockout
            $stockout = new SotbMaterialStockout();
            $stockout->date = $date;
            $stockout->stockout_no = $stockout_no;
            $stockout->stockout_signature = $stockout_signature;
            $stockout->store_type = $store_type;
            $stockout->asker = $asker;
            $stockout->destination = $destination;
            $stockout->code_store = $code_store;
            $stockout->item_movement_type = $item_movement_type;
            $stockout->origin_bg_store_id = $origin_bg_store_id;
            $stockout->created_by = $created_by;
            $stockout->status = 1;
            $stockout->description = $description;
            $stockout->save();
            
        session()->flash('success', 'stockout has been created !!');
        return redirect()->route('admin.sotb-material-stockouts.index');
    }


    public function storeFromSmall(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('sotb_material_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $rules = array(
                'material_id.*'  => 'required',
                'date'  => 'required',
                'unit.*'  => 'required',
                'quantity.*'  => 'required',
                'asker'  => 'required',
                'origin_sm_store_id'  => 'required',
                'destination'  => 'required',
                'description'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $material_id = $request->material_id;
            $date = $request->date;
            $asker = $request->asker;
            $destination = $request->destination;
            $code_store = $request->code_store;
            $item_movement_type = $request->item_movement_type;
            $origin_sm_store_id = $request->origin_sm_store_id;
            $description =$request->description; 
            $unit = $request->unit;
            $quantity = $request->quantity;
            $store_type = $request->store_type;
            

            $latest = SotbMaterialStockout::latest()->first();
            if ($latest) {
               $stockout_no = 'BS' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $stockout_no = 'BS' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $stockout_signature = "4001711615".Carbon::parse(Carbon::now())->format('YmdHis')."/".$stockout_no;


            for( $count = 0; $count < count($material_id); $count++ ){

                $selling_price = SotbMaterial::where('id', $material_id[$count])->value('selling_price');
                $purchase_price = SotbMaterial::where('id', $material_id[$count])->value('purchase_price');

                $total_value = $quantity[$count] * $purchase_price;
                $total_purchase_value = $quantity[$count] * $purchase_price;
                $total_selling_value = $quantity[$count] * $purchase_price;

                $data = array(
                    'material_id' => $material_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'unit' => $unit[$count],
                    'purchase_price' => $purchase_price,
                    'selling_price' => $selling_price,
                    'total_purchase_value' => $total_purchase_value,
                    'total_selling_value' => $total_selling_value,
                    'asker' => $asker,
                    'destination' => $destination,
                    'code_store' => $code_store,
                    'item_movement_type' => $item_movement_type,
                    'origin_sm_store_id' => $origin_sm_store_id,
                    'stockout_no' => $stockout_no,
                    'stockout_signature' => $stockout_signature,
                    'store_type' => $store_type,
                    'created_by' => $created_by,
                    'description' => $description,
                    'status' => 1,
                    'created_at' => \Carbon\Carbon::now()

                );
                $insert_data[] = $data;

                
            }
            SotbMaterialStockoutDetail::insert($insert_data);


            //create stockout
            $stockout = new SotbMaterialStockout();
            $stockout->date = $date;
            $stockout->stockout_no = $stockout_no;
            $stockout->stockout_signature = $stockout_signature;
            $stockout->store_type = $store_type;
            $stockout->code_store = $code_store;
            $stockout->item_movement_type = $item_movement_type;
            $stockout->asker = $asker;
            $stockout->destination = $destination;
            $stockout->origin_sm_store_id = $origin_sm_store_id;
            $stockout->created_by = $created_by;
            $stockout->status = 1;
            $stockout->description = $description;
            $stockout->save();
            
        session()->flash('success', 'stockout has been created !!');
        return redirect()->route('admin.sotb-material-stockouts.index');
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
        $code = SotbMaterialStockoutDetail::where('stockout_no', $stockout_no)->value('stockout_no');
        $stockouts = SotbMaterialStockoutDetail::where('stockout_no', $stockout_no)->get();
        return view('backend.pages.sotb.material_stockout.show', compact('stockouts','code'));
         
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($stockout_no)
    {
        if (is_null($this->user) || !$this->user->can('sotb_material_stockout.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any stockout !');
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $stockout_no)
    {
        if (is_null($this->user) || !$this->user->can('sotb_material_stockout.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any stockout !');
        }

        
    }

    public function bonSortie($stockout_no)
    {
        if (is_null($this->user) || !$this->user->can('sotb_material_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to print handover document!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $code = SotbMaterialStockout::where('stockout_no', $stockout_no)->value('stockout_no');
        $datas = SotbMaterialStockoutDetail::where('stockout_no', $stockout_no)->get();
        $data = SotbMaterialStockoutDetail::where('stockout_no', $stockout_no)->first();
        $demandeur = SotbMaterialStockout::where('stockout_no', $stockout_no)->value('asker');
        $description = SotbMaterialStockout::where('stockout_no', $stockout_no)->value('description');
        $stockout_signature = SotbMaterialStockout::where('stockout_no', $stockout_no)->value('stockout_signature');
        $date = SotbMaterialStockout::where('stockout_no', $stockout_no)->value('date');
        $totalValue = DB::table('sotb_material_stockout_details')
            ->where('stockout_no', '=', $stockout_no)
            ->sum('total_purchase_value');
        $pdf = PDF::loadView('backend.pages.sotb.document.material_stockout',compact('datas','code','totalValue','demandeur','description','stockout_no','setting','date','stockout_signature','data'));

        Storage::put('public/sotb/material_stockout/'.$stockout_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('BON_SORTIE_'.$stockout_no.'.pdf');
        
    }

    public function validateStockout($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('sotb_material_stockout.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any stockout !');
        }
            SotbMaterialStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            SotbMaterialStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        session()->flash('success', 'stockout has been validated !!');
        return back();
    }

    public function reject($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('sotb_material_stockout.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any stockout !');
        }

        SotbMaterialStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
        SotbMaterialStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        session()->flash('success', 'Stockout has been rejected !!');
        return back();
    }

    public function reset($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('sotb_material_stockout.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any stockout !');
        }

        SotbMaterialStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
        SotbMaterialStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        session()->flash('success', 'Stockout has been reseted !!');
        return back();
    }

    public function confirm($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('sotb_material_stockout.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any stockout !');
        }

        SotbMaterialStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            SotbMaterialStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        session()->flash('success', 'Stockout has been confirmed !!');
        return back();
    }

    public function approuve($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('sotb_material_stockout.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any stockout !');
        }


        $datas = SotbMaterialStockoutDetail::where('stockout_no', $stockout_no)->get();

        foreach($datas as $data){

            if ($data->store_type == 'md') {
                $code_store_origin = SotbMaterialMdStore::where('id',$data->origin_md_store_id)->value('code');

                $valeurStockInitial = SotbMaterialMdStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('total_cump_value');
                $quantityStockInitial = SotbMaterialMdStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('quantity');
                $quantityRestantBigStore = $quantityStockInitial - $data->quantity;

                $reportMdStore = array(
                    'material_id' => $data->material_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'code_store_origin' => $code_store_origin,
                    'stockout_no' => $data->stockout_no,
                    'quantity_stockout' => $data->quantity,
                    'value_stockout' => $data->total_purchase_value,
                    'quantity_stock_final' => $quantityStockInitial - $data->quantity,
                    'value_stock_final' => $valeurStockInitial - $data->total_purchase_value,
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportMdStoreData[] = $reportMdStore;

                    $mdStore = array(
                        'material_id' => $data->material_id,
                        'quantity' => $quantityRestantBigStore,
                        'total_purchase_value' => $quantityRestantBigStore * $data->purchase_price,
                        'total_cump_value' => $quantityRestantBigStore * $data->purchase_price,
                        'created_by' => $this->user->name,
                        'verified' => false,
                        'created_at' => \Carbon\Carbon::now()
                    );


                    if ($data->quantity <= $quantityStockInitial) {

                        SotbMaterialMdStoreReport::insert($reportMdStoreData);
                        
                        SotbMaterialMdStoreDetail::where('code',$code_store_origin)->where('material_id',$data->material_id)
                        ->update($mdStore);

                        
                    }else{
                        session()->flash('error', 'Why do you want to stockout quantity you do not have in your store?please rewrite a valid quantity!');
                        return redirect()->back();
                    }

                SotbMaterialStockout::where('stockout_no', '=', $stockout_no)
                    ->update(['status' => 4,'approuved_by' => $this->user->name]);
                SotbMaterialStockoutDetail::where('stockout_no', '=', $stockout_no)
                    ->update(['status' => 4,'approuved_by' => $this->user->name]);

                session()->flash('success', 'Stockout has been done successfuly !, from '.$code_store_origin);
                return back();

            }elseif ($data->store_type == 'sm') {
                $code_store_origin = SotbMaterialSmStore::where('id',$data->origin_sm_store_id)->value('code');

                $valeurStockInitial = SotbMaterialSmStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('total_cump_value');
                $quantityStockInitial = SotbMaterialSmStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('quantity');
                $quantityRestantSmallStore = $quantityStockInitial - $data->quantity;

                $reportSmallStore = array(
                    'material_id' => $data->material_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'code_store_origin' => $code_store_origin,
                    'stockout_no' => $data->stockout_no,
                    'quantity_stockout' => $data->quantity,
                    'value_stockout' => $data->value_stockout,
                    'quantity_stock_final' => $quantityStockInitial - $data->quantity,
                    'value_stock_final' => $valeurStockInitial - $data->total_purchase_value,
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportSmallStoreData[] = $reportSmallStore;

                    $smallStore = array(
                        'material_id' => $data->material_id,
                        'quantity' => $quantityRestantSmallStore,
                        'total_purchase_value' => $quantityRestantSmallStore * $data->purchase_price,
                        'total_cump_value' => $quantityRestantSmallStore * $data->purchase_price,
                        'created_by' => $this->user->name,
                        'verified' => false,
                        'created_at' => \Carbon\Carbon::now()
                    );


                    if ($data->quantity <= $quantityStockInitial) {

                        SotbMaterialSmStoreReport::insert($reportSmallStoreData);
                        
                        SotbMaterialSmStoreDetail::where('code',$code_store_origin)->where('material_id',$data->material_id)
                        ->update($smallStore);

                        
                    }else{
                        session()->flash('error', $this->user->name.' ,Why do you want to stockout a quantity you do not have in your store? please rewrite a valid quantity!');
                        return redirect()->back();
                    }
                SotbMaterialStockout::where('stockout_no', '=', $stockout_no)
                    ->update(['status' => 4,'approuved_by' => $this->user->name]);
                SotbMaterialStockoutDetail::where('stockout_no', '=', $stockout_no)
                    ->update(['status' => 4,'approuved_by' => $this->user->name]);

                session()->flash('success', 'Stockout has been done successfuly !, from '.$code_store_origin);
                return back();
            }else{
                $code_store_origin = SotbMaterialBgStore::where('id',$data->origin_bg_store_id)->value('code');

                $valeurStockInitial = SotbMaterialBgStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('total_cump_value');
                $quantityStockInitial = SotbMaterialBgStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('quantity');
                $quantityRestantSmallStore = $quantityStockInitial - $data->quantity;

                $reportBgStore = array(
                    'material_id' => $data->material_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'code_store_origin' => $code_store_origin,
                    'stockout_no' => $data->stockout_no,
                    'quantity_stockout' => $data->quantity,
                    'value_stockout' => $data->value_stockout,
                    'quantity_stock_final' => $quantityStockInitial - $data->quantity,
                    'value_stock_final' => $valeurStockInitial - $data->total_purchase_value,
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportBgStoreData[] = $reportBgStore;

                    $bgStore = array(
                        'material_id' => $data->material_id,
                        'quantity' => $quantityRestantSmallStore,
                        'total_purchase_value' => $quantityRestantSmallStore * $data->purchase_price,
                        'total_cump_value' => $quantityRestantSmallStore * $data->purchase_price,
                        'created_by' => $this->user->name,
                        'verified' => false,
                        'created_at' => \Carbon\Carbon::now()
                    );


                    if ($data->quantity <= $quantityStockInitial) {

                        SotbMaterialBgStoreReport::insert($reportBgStoreData);
                        
                        SotbMaterialBgStoreDetail::where('code',$code_store_origin)->where('material_id',$data->material_id)
                        ->update($bgStore);

                        
                    }else{
                        session()->flash('error', $this->user->name.' ,Why do you want to stockout a quantity you do not have in your store? please rewrite a valid quantity!');
                        return redirect()->back();
                    }

                SotbMaterialStockout::where('stockout_no', '=', $stockout_no)
                    ->update(['status' => 4,'approuved_by' => $this->user->name]);
                SotbMaterialStockoutDetail::where('stockout_no', '=', $stockout_no)
                    ->update(['status' => 4,'approuved_by' => $this->user->name]);

                session()->flash('success', 'Stockout has been done successfuly !, from '.$code_store_origin);
                return back();
            }
                
  
        }
    }

    public function get_reception_data()
    {
        return Excel::download(new ReceptionExport, 'stockouts.xlsx');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $stockout_no
     * @return \Illuminate\Http\Response
     */
    public function destroy($stockout_no)
    {
        if (is_null($this->user) || !$this->user->can('sotb_material_stockout.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any stockout !');
        }

        $stockout = SotbMaterialStockout::where('stockout_no',$stockout_no)->first();
        if (!is_null($stockout)) {
            $stockout->delete();
            SotbMaterialStockoutDetail::where('stockout_no',$stockout_no)->delete();
        }

        session()->flash('success', 'Stockout has been deleted !!');
        return back();
    }
}

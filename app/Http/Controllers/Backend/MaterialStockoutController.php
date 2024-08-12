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
use App\Models\MaterialStockout;
use App\Models\MaterialStockoutDetail;
use App\Models\MaterialBigStoreDetail;
use App\Models\MaterialSmallStoreDetail;
use App\Models\MaterialBigStore;
use App\Models\MaterialSmallStore;
use App\Models\MaterialBigReport;
use App\Models\MaterialSmallReport;
use App\Models\MaterialExtraBigStoreDetail;
use App\Models\MaterialExtraBigStore;
use App\Models\MaterialExtraBigReport;
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
        if (is_null($this->user) || !$this->user->can('material_stockout.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stockout !');
        }

        $stockouts = MaterialStockout::orderBy('id','desc')->take(200)->get();
        return view('backend.pages.material_stockout.index', compact('stockouts'));
    }

    public function choose()
    {
        if (is_null($this->user) || !$this->user->can('material_stockout.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stockout !');
        }

        return view('backend.pages.material_stockout.choose');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('material_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $materials  = Material::orderBy('name','asc')->get();
        $material_origin_stores = MaterialBigStore::all();
        return view('backend.pages.material_stockout.create', compact('materials','material_origin_stores'));
    }

    public function createFromBig()
    {
        if (is_null($this->user) || !$this->user->can('material_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $materials  = Material::orderBy('name','asc')->get();
        $material_origin_stores = MaterialExtraBigStore::all();
        return view('backend.pages.material_stockout.create_from_big', compact('materials','material_origin_stores'));
    }

    public function createFromSmall()
    {
        if (is_null($this->user) || !$this->user->can('material_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $materials  = Material::orderBy('name','asc')->get();
        $material_origin_stores = MaterialSmallStore::all();
        return view('backend.pages.material_stockout.create_from_small', compact('materials','material_origin_stores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('material_stockout.create')) {
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
                'description'  => 'required|max:490'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            try {DB::beginTransaction();

            $material_id = $request->material_id;
            $date = $request->date;
            $asker = $request->asker;
            $destination = $request->destination;
            $description =$request->description; 
            $origin_bg_store_id = $request->origin_bg_store_id;
            $unit = $request->unit;
            $quantity = $request->quantity;
            $store_type = $request->store_type;
            

            $latest = MaterialStockout::latest()->first();
            if ($latest) {
               $stockout_no = 'BS' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $stockout_no = 'BS' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $stockout_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$stockout_no;


            for( $count = 0; $count < count($material_id); $count++ ){

                $selling_price = Material::where('id', $material_id[$count])->value('selling_price');
                $purchase_price = Material::where('id', $material_id[$count])->value('purchase_price');

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
            MaterialStockoutDetail::insert($insert_data);


            //create stockout
            $stockout = new MaterialStockout();
            $stockout->date = $date;
            $stockout->stockout_no = $stockout_no;
            $stockout->stockout_signature = $stockout_signature;
            $stockout->store_type = $store_type;
            $stockout->asker = $asker;
            $stockout->destination = $destination;
            $stockout->origin_bg_store_id = $origin_bg_store_id;
            $stockout->created_by = $created_by;
            $stockout->status = 1;
            $stockout->description = $description;
            $stockout->save();

            DB::commit();
            session()->flash('success', 'stockout has been created !!');
            return redirect()->route('admin.material-stockouts.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
            
    }

    public function storeFromBig(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('material_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $rules = array(
                'material_id.*'  => 'required',
                'date'  => 'required',
                'unit.*'  => 'required',
                'quantity.*'  => 'required',
                'asker'  => 'required',
                'origin_extra_store_id'  => 'required',
                'destination'  => 'required',
                'description'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            try {DB::beginTransaction();

            $material_id = $request->material_id;
            $date = $request->date;
            $asker = $request->asker;
            $destination = $request->destination;
            $description =$request->description; 
            $origin_extra_store_id = $request->origin_extra_store_id;
            $unit = $request->unit;
            $quantity = $request->quantity;
            $store_type = $request->store_type;
            

            $latest = MaterialStockout::latest()->first();
            if ($latest) {
               $stockout_no = 'BS' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $stockout_no = 'BS' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $stockout_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$stockout_no;


            for( $count = 0; $count < count($material_id); $count++ ){

                $selling_price = Material::where('id', $material_id[$count])->value('selling_price');
                $purchase_price = Material::where('id', $material_id[$count])->value('purchase_price');

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
                    'origin_extra_store_id' => $origin_extra_store_id,
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
            MaterialStockoutDetail::insert($insert_data);


            //create stockout
            $stockout = new MaterialStockout();
            $stockout->date = $date;
            $stockout->stockout_no = $stockout_no;
            $stockout->stockout_signature = $stockout_signature;
            $stockout->store_type = $store_type;
            $stockout->asker = $asker;
            $stockout->destination = $destination;
            $stockout->origin_extra_store_id = $origin_extra_store_id;
            $stockout->created_by = $created_by;
            $stockout->status = 1;
            $stockout->description = $description;
            $stockout->save();

            DB::commit();
            session()->flash('success', 'stockout has been created !!');
            return redirect()->route('admin.material-stockouts.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
            
    }


    public function storeFromSmall(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('material_stockout.create')) {
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

            try {DB::beginTransaction();

            $material_id = $request->material_id;
            $date = $request->date;
            $asker = $request->asker;
            $destination = $request->destination;
            $origin_sm_store_id = $request->origin_sm_store_id;
            $description =$request->description; 
            $unit = $request->unit;
            $quantity = $request->quantity;
            $store_type = $request->store_type;
            

            $latest = MaterialStockout::latest()->first();
            if ($latest) {
               $stockout_no = 'BS' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $stockout_no = 'BS' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $stockout_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$stockout_no;


            for( $count = 0; $count < count($material_id); $count++ ){

                $selling_price = Material::where('id', $material_id[$count])->value('selling_price');
                $purchase_price = Material::where('id', $material_id[$count])->value('purchase_price');

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
            MaterialStockoutDetail::insert($insert_data);


            //create stockout
            $stockout = new MaterialStockout();
            $stockout->date = $date;
            $stockout->stockout_no = $stockout_no;
            $stockout->stockout_signature = $stockout_signature;
            $stockout->store_type = $store_type;
            $stockout->asker = $asker;
            $stockout->destination = $destination;
            $stockout->origin_sm_store_id = $origin_sm_store_id;
            $stockout->created_by = $created_by;
            $stockout->status = 1;
            $stockout->description = $description;
            $stockout->save();

            DB::commit();
            session()->flash('success', 'stockout has been created !!');
            return redirect()->route('admin.material-stockouts.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
            
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
        $code = MaterialStockoutDetail::where('stockout_no', $stockout_no)->value('stockout_no');
        $stockouts = MaterialStockoutDetail::where('stockout_no', $stockout_no)->get();
        return view('backend.pages.material_stockout.show', compact('stockouts','code'));
         
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($stockout_no)
    {
        if (is_null($this->user) || !$this->user->can('material_stockout.edit')) {
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
        if (is_null($this->user) || !$this->user->can('material_stockout.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any stockout !');
        }

        
    }

    public function bonSortie($stockout_no)
    {
        if (is_null($this->user) || !$this->user->can('material_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to print handover document!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $code = MaterialStockout::where('stockout_no', $stockout_no)->value('stockout_no');
        $datas = MaterialStockoutDetail::where('stockout_no', $stockout_no)->get();
        $demandeur = MaterialStockout::where('stockout_no', $stockout_no)->value('asker');
        $description = MaterialStockout::where('stockout_no', $stockout_no)->value('description');
        $stockout_signature = MaterialStockout::where('stockout_no', $stockout_no)->value('stockout_signature');
        $date = MaterialStockout::where('stockout_no', $stockout_no)->value('date');
        $totalValue = DB::table('material_stockout_details')
            ->where('stockout_no', '=', $stockout_no)
            ->sum('total_purchase_value');
        $pdf = PDF::loadView('backend.pages.document.material_stockout',compact('datas','code','totalValue','demandeur','description','stockout_no','setting','date','stockout_signature'));

        Storage::put('public/pdf/material_stockout/'.$stockout_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('BON_SORTIE_'.$stockout_no.'.pdf');
        
    }

    public function validateStockout($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('material_stockout.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any stockout !');
        }

        try {DB::beginTransaction();

            MaterialStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            MaterialStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'stockout has been validated !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

    public function reject($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('material_stockout.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any stockout !');
        }

        try {DB::beginTransaction();

        MaterialStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
        MaterialStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'Stockout has been rejected !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function reset($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('material_stockout.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any stockout !');
        }

        try {DB::beginTransaction();

        MaterialStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
        MaterialStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'Stockout has been reseted !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

    public function confirm($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('material_stockout.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any stockout !');
        }

        try {DB::beginTransaction();

        MaterialStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            MaterialStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'Stockout has been confirmed !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function approuve($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('material_stockout.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any stockout !');
        }

        try {DB::beginTransaction();

        $datas = MaterialStockoutDetail::where('stockout_no', $stockout_no)->get();
        $data = MaterialStockoutDetail::where('stockout_no', $stockout_no)->first();

        foreach($datas as $data){

            if ($data->store_type == 'md') {
                $code_store_origin = MaterialBigStore::where('id',$data->origin_bg_store_id)->value('code');

                $valeurStockInitial = MaterialBigStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('total_cump_value');
                $quantityStockInitial = MaterialBigStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('quantity');
                $quantityRestantBigStore = $quantityStockInitial - $data->quantity;

                $reportBigStore = array(
                    'material_id' => $data->material_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'code_store' => $code_store_origin,
                    'code_store_origin' => $code_store_origin,
                    'stockout_no' => $data->stockout_no,
                    'date' => $data->date,
                    'quantity_stockout' => $data->quantity,
                    'value_stockout' => $data->total_purchase_value,
                    'quantity_stock_final' => $quantityStockInitial - $data->quantity,
                    'value_stock_final' => $valeurStockInitial - $data->total_purchase_value,
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportBigStoreData[] = $reportBigStore;

                    $bigStore = array(
                        'material_id' => $data->material_id,
                        'quantity' => $quantityRestantBigStore,
                        'total_purchase_value' => $quantityRestantBigStore * $data->purchase_price,
                        'total_cump_value' => $quantityRestantBigStore * $data->purchase_price,
                        'created_by' => $this->user->name,
                        'verified' => true,
                        'created_at' => \Carbon\Carbon::now()
                    );


                    if ($data->quantity <= $quantityStockInitial) {

                        
                        
                        MaterialBigStoreDetail::where('code',$code_store_origin)->where('material_id',$data->material_id)
                        ->update($bigStore);

                        $flag = 0;

                        
                    }else{
                        foreach ($datas as $data) {
                            $code_store_origin = MaterialBigStore::where('id',$data->origin_bg_store_id)->value('code');

                            $valeurStockInitial = MaterialBigStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('total_cump_value');
                            $quantityStockInitial = MaterialBigStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('quantity');
                            $quantityTotalBigStore = $quantityStockInitial + $data->quantity;

                            $returnDataBigStore = array(
                                'material_id' => $data->material_id,
                                'quantity' => $quantityTotalBigStore,
                                'total_purchase_value' => $quantityTotalBigStore * $data->purchase_price,
                                'total_cump_value' => $quantityTotalBigStore * $data->purchase_price,
                                'created_by' => $this->user->name,
                                'verified' => false,
                                'created_at' => \Carbon\Carbon::now()
                            );

                            $statusBigStore = MaterialBigStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('verified');
                    
                            if ($statusBigStore == true) {
                        
                                MaterialBigStoreDetail::where('code',$code_store_origin)->where('material_id',$data->material_id)
                                ->update($returnDataBigStore);

                                $flag = 1;
                            }
                        }

                            MaterialBigStoreDetail::where('material_id','!=','')->update(['verified' => false]);
                            MaterialSmallStoreDetail::where('material_id','!=','')->update(['verified' => false]);
                            MaterialExtraBigStoreDetail::where('material_id','!=','')->update(['verified' => false]);
                            session()->flash('error', $this->user->name.' ,Why do you want to stockout a quantity you do not have in your store?please rewrite a valid quantity!');
                            return redirect()->back();

                    }


            }elseif ($data->store_type == 'sm') {
                $code_store_origin = MaterialSmallStore::where('id',$data->origin_sm_store_id)->value('code');

                $valeurStockInitial = MaterialSmallStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('total_cump_value');
                $quantityStockInitial = MaterialSmallStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('quantity');
                $quantityRestantSmallStore = $quantityStockInitial - $data->quantity;

                $reportSmallStore = array(
                    'material_id' => $data->material_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'code_store' => $code_store_origin,
                    'code_store_origin' => $code_store_origin,
                    'stockout_no' => $data->stockout_no,
                    'date' => $data->date,
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

                        
                        
                        MaterialSmallStoreDetail::where('code',$code_store_origin)->where('material_id',$data->material_id)
                        ->update($smallStore);

                        $flag = 0;

                        
                    }else{
                        foreach ($datas as $data) {
                            $code_store_origin = MaterialSmallStore::where('id',$data->origin_sm_store_id)->value('code');

                            $valeurStockInitial = MaterialSmallStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('total_cump_value');
                            $quantityStockInitial = MaterialSmallStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('quantity');
                            $quantityTotalSmallStore = $quantityStockInitial + $data->quantity;

                            $returnDataSmallStore = array(
                                'material_id' => $data->material_id,
                                'quantity' => $quantityTotalSmallStore,
                                'total_purchase_value' => $quantityTotalSmallStore * $data->purchase_price,
                                'total_cump_value' => $quantityTotalSmallStore * $data->purchase_price,
                                'created_by' => $this->user->name,
                                'verified' => false,
                                'created_at' => \Carbon\Carbon::now()
                            );

                            $statusSmallStore = MaterialSmallStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('verified');
                    
                            if ($statusSmallStore == true) {
                        
                                MaterialSmallStoreDetail::where('code',$code_store_origin)->where('material_id',$data->material_id)
                                ->update($returnDataSmallStore);

                                $flag = 1;
                            }
                        }

                        MaterialBigStoreDetail::where('material_id','!=','')->update(['verified' => false]);
                        MaterialSmallStoreDetail::where('material_id','!=','')->update(['verified' => false]);
                        MaterialExtraBigStoreDetail::where('material_id','!=','')->update(['verified' => false]);

                        session()->flash('error', $this->user->name.' ,Why do you want to stockout a quantity you do not have in your store?please rewrite a valid quantity!');
                        return redirect()->back();
                    }

            }else{
                $code_store_origin = MaterialExtraBigStore::where('id',$data->origin_extra_store_id)->value('code');

                $valeurStockInitial = MaterialExtraBigStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('total_cump_value');
                $quantityStockInitial = MaterialExtraBigStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('quantity');
                $quantityRestantSmallStore = $quantityStockInitial - $data->quantity;

                $reportExtraBigStore = array(
                    'material_id' => $data->material_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'code_store' => $code_store_origin,
                    'code_store_origin' => $code_store_origin,
                    'stockout_no' => $data->stockout_no,
                    'date' => $data->date,
                    'quantity_stockout' => $data->quantity,
                    'value_stockout' => $data->value_stockout,
                    'quantity_stock_final' => $quantityStockInitial - $data->quantity,
                    'value_stock_final' => $valeurStockInitial - $data->total_purchase_value,
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportExtraBigStoreData[] = $reportExtraBigStore;

                    $extraStore = array(
                        'material_id' => $data->material_id,
                        'quantity' => $quantityRestantSmallStore,
                        'total_purchase_value' => $quantityRestantSmallStore * $data->purchase_price,
                        'total_cump_value' => $quantityRestantSmallStore * $data->purchase_price,
                        'created_by' => $this->user->name,
                        'verified' => false,
                        'created_at' => \Carbon\Carbon::now()
                    );


                    if ($data->quantity <= $quantityStockInitial) {

                        
                        
                        MaterialExtraBigStoreDetail::where('code',$code_store_origin)->where('material_id',$data->material_id)
                        ->update($extraStore);
                        $flag = 0;

                        
                    }else{
                        foreach ($datas as $data) {
                            $code_store_origin = MaterialExtraBigStore::where('id',$data->origin_extra_store_id)->value('code');

                            $valeurStockInitial = MaterialExtraBigStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('total_cump_value');
                            $quantityStockInitial = MaterialExtraBigStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('quantity');
                            $quantityTotalSmallStore = $quantityStockInitial + $data->quantity;

                            $returnDataExtraStore = array(
                                'material_id' => $data->material_id,
                                'quantity' => $quantityTotalSmallStore,
                                'total_purchase_value' => $quantityTotalSmallStore * $data->purchase_price,
                                'total_cump_value' => $quantityTotalSmallStore * $data->purchase_price,
                                'created_by' => $this->user->name,
                                'verified' => false,
                                'created_at' => \Carbon\Carbon::now()
                            );

                            $statusExtraStore = MaterialExtraBigStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('verified');
                    
                            if ($statusExtraStore == true) {
                        
                                MaterialExtraBigStoreDetail::where('code',$code_store_origin)->where('material_id',$data->material_id)
                                ->update($returnDataExtraStore);

                                $flag = 1;
                            }
                        }

                        MaterialBigStoreDetail::where('material_id','!=','')->update(['verified' => false]);
                        MaterialSmallStoreDetail::where('material_id','!=','')->update(['verified' => false]);
                        MaterialExtraBigStoreDetail::where('material_id','!=','')->update(['verified' => false]);

                        session()->flash('error', $this->user->name.' ,Why do you want to stockout a quantity you do not have in your store?please rewrite a valid quantity!');
                        return redirect()->back();
                    }

            }
                
  
        }
        if($data->store_type == 'md' && $flag != 1){
            MaterialBigReport::insert($reportBigStoreData);
        }elseif ($data->store_type == 'sm' && $flag != 1) {
            MaterialSmallReport::insert($reportSmallStoreData);
        }else{
            MaterialExtraBigReport::insert($reportExtraBigStoreData);
        }

        MaterialBigStoreDetail::where('material_id','!=','')->update(['verified' => false]);
        MaterialSmallStoreDetail::where('material_id','!=','')->update(['verified' => false]);
        MaterialExtraBigStoreDetail::where('material_id','!=','')->update(['verified' => false]);

        MaterialStockout::where('stockout_no', '=', $stockout_no)
                    ->update(['status' => 4,'approuved_by' => $this->user->name]);
        MaterialStockoutDetail::where('stockout_no', '=', $stockout_no)
                    ->update(['status' => 4,'approuved_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'Stockout has been done successfuly !, from '.$code_store_origin);
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
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

        try {DB::beginTransaction();

        $stockout = MaterialStockout::where('stockout_no',$stockout_no)->first();
        if (!is_null($stockout)) {
            $stockout->delete();
            MaterialStockoutDetail::where('stockout_no',$stockout_no)->delete();
        }

        DB::commit();
            session()->flash('success', 'Stockout has been deleted !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }
}

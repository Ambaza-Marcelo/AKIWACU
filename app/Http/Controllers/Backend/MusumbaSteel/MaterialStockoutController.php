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
use App\Models\MsMaterial;
use App\Models\MsMaterialStockout;
use App\Models\MsMaterialStockoutDetail;
use App\Models\MsMaterialStoreDetail;
use App\Models\MsMaterialStore;
use App\Models\MsMaterialReport;
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_stockout.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stockout !');
        }

        $stockouts = MsMaterialStockout::orderBy('id','desc')->get();
        return view('backend.pages.musumba_steel.material_stockout.index', compact('stockouts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $materials = MsMaterial::orderBy('name')->get();
        $material_origin_stores = MsMaterialStore::all();
        return view('backend.pages.musumba_steel.material_stockout.create', compact('materials','material_origin_stores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('musumba_steel_material_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $rules = array(
                'material_id.*'  => 'required',
                'date'  => 'required',
                'unit.*'  => 'required',
                'quantity.*'  => 'required',
                'asker'  => 'required',
                'origin_store_id'  => 'required',
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
            $origin_store_id = $request->origin_store_id;
            $unit = $request->unit;
            $quantity = $request->quantity;
            $store_type = $request->store_type;
            

            $latest = MsMaterialStockout::latest()->first();
            if ($latest) {
               $stockout_no = 'BS' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $stockout_no = 'BS' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $stockout_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$stockout_no;


            for( $count = 0; $count < count($material_id); $count++ ){

                $purchase_price = MsMaterial::where('id', $material_id[$count])->value('purchase_price');

                $total_value = $quantity[$count] * $purchase_price;
                $total_purchase_value = $quantity[$count] * $purchase_price;

                $data = array(
                    'material_id' => $material_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'unit' => $unit[$count],
                    'purchase_price' => $purchase_price,
                    'total_purchase_value' => $total_purchase_value,
                    'asker' => $asker,
                    'code_store' => $code_store,
                    'item_movement_type' => $item_movement_type,
                    'destination' => $destination,
                    'origin_store_id' => $origin_store_id,
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
            MsMaterialStockoutDetail::insert($insert_data);


            //create stockout
            $stockout = new MsMaterialStockout();
            $stockout->date = $date;
            $stockout->stockout_no = $stockout_no;
            $stockout->stockout_signature = $stockout_signature;
            $stockout->store_type = $store_type;
            $stockout->code_store = $code_store;
            $stockout->item_movement_type = $item_movement_type;
            $stockout->asker = $asker;
            $stockout->destination = $destination;
            $stockout->origin_store_id = $origin_store_id;
            $stockout->created_by = $created_by;
            $stockout->status = 1;
            $stockout->description = $description;
            $stockout->save();
            
        session()->flash('success', 'stockout has been created !!');
        return redirect()->route('admin.ms-material-stockouts.index');
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
        $code = MsMaterialStockoutDetail::where('stockout_no', $stockout_no)->value('stockout_no');
        $stockouts = MsMaterialStockoutDetail::where('stockout_no', $stockout_no)->get();
        return view('backend.pages.musumba_steel.material_stockout.show', compact('stockouts','code'));
         
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($stockout_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_stockout.edit')) {
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_stockout.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any stockout !');
        }

        
    }

    public function bonSortie($stockout_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to print handover document!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $code = MsMaterialStockout::where('stockout_no', $stockout_no)->value('stockout_no');
        $datas = MsMaterialStockoutDetail::where('stockout_no', $stockout_no)->get();
        $data = MsMaterialStockoutDetail::where('stockout_no', $stockout_no)->first();
        $demandeur = MsMaterialStockout::where('stockout_no', $stockout_no)->value('asker');
        $description = MsMaterialStockout::where('stockout_no', $stockout_no)->value('description');
        $stockout_signature = MsMaterialStockout::where('stockout_no', $stockout_no)->value('stockout_signature');
        $date = MsMaterialStockout::where('stockout_no', $stockout_no)->value('date');
        $totalValue = DB::table('ms_material_stockout_details')
            ->where('stockout_no', '=', $stockout_no)
            ->sum('total_purchase_value');
        $pdf = PDF::loadView('backend.pages.musumba_steel.document.material_stockout',compact('datas','code','totalValue','demandeur','description','stockout_no','setting','date','stockout_signature','data'));

        Storage::put('public/musumba_steel/material_stockout/'.$stockout_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('BON_SORTIE_'.$stockout_no.'.pdf');
        
    }

    public function validateStockout($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_material_stockout.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any stockout !');
        }
            MsMaterialStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            MsMaterialStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        session()->flash('success', 'stockout has been validated !!');
        return back();
    }

    public function reject($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_material_stockout.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any stockout !');
        }

        MsMaterialStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
        MsMaterialStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        session()->flash('success', 'Stockout has been rejected !!');
        return back();
    }

    public function reset($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_material_stockout.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any stockout !');
        }

        MsMaterialStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
        MsMaterialStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        session()->flash('success', 'Stockout has been reseted !!');
        return back();
    }

    public function confirm($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_material_stockout.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any stockout !');
        }

        MsMaterialStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            MsMaterialStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        session()->flash('success', 'Stockout has been confirmed !!');
        return back();
    }

    public function approuve($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_material_stockout.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any stockout !');
        }


        $datas = MsMaterialStockoutDetail::where('stockout_no', $stockout_no)->get();

        foreach($datas as $data){

                $code_store_origin = MsMaterialStore::where('id',$data->origin_store_id)->value('code');

                $valeurStockInitial = MsMaterialStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('total_cump_value');
                $quantityStockInitial = MsMaterialStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('quantity');
                $quantityRestantStore = $quantityStockInitial - $data->quantity;
                
                $reportStore = array(
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
                    'description' => $data->description,
                    'date' => $data->date,
                    'type_transaction' => $data->item_movement_type,
                    'document_no' => $stockout_no,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportStoreData[] = $reportStore;

                    $mdStore = array(
                        'material_id' => $data->material_id,
                        'quantity' => $quantityRestantStore,
                        'total_purchase_value' => $quantityRestantStore * $data->purchase_price,
                        'total_cump_value' => $quantityRestantStore * $data->purchase_price,
                        'created_by' => $this->user->name,
                        'verified' => true,
                        'created_at' => \Carbon\Carbon::now()
                    );


                    if ($data->quantity <= $quantityStockInitial) {
                        
                        MsMaterialStoreDetail::where('code',$code_store_origin)->where('material_id',$data->material_id)
                        ->update($mdStore);

                        $flag = 0;

                        
                    }else{

                            foreach ($datas as $data) {
                            $cump = MsMaterialStoreDetail::where('material_id', $data->material_id)->value('cump');

                            $code_store_origin = MsMaterialStore::where('id',$data->origin_store_id)->value('code');

                            $valeurStockInitial = MsMaterialStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('total_cump_value');
                            $quantityStockInitial = MsMaterialStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->where('verified',true)->value('quantity');

                            $quantityTotal = $quantityStockInitial + $data->quantity;
                      
                
                            $returnData = array(
                                'material_id' => $data->material_id,
                                'quantity' => $quantityTotal,
                                'total_purchase_value' => $quantityTotal * $cump,
                                'total_cump_value' => $quantityTotal * $cump,
                                'verified' => false,
                            );

                            $status = MsMaterialStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('verified');
                    

                        
                                MsMaterialStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->where('verified',true)
                                ->update($returnData);
                                $flag = 1;
                            
                        }

                        MsMaterialStoreDetail::where('material_id','!=','')->update(['verified' => false]);

                        session()->flash('error', 'Why do you want to stockout quantity you do not have in your store?please rewrite a valid quantity!');
                        return redirect()->back();
                    }
                
            }

            if ($flag != 1) {
                MsMaterialReport::insert($reportStoreData);
            }

            MsMaterialStoreDetail::where('material_id','!=','')->update(['verified' => false]);

                MsMaterialStockout::where('stockout_no', '=', $stockout_no)
                    ->update(['status' => 4,'approuved_by' => $this->user->name]);
                MsMaterialStockoutDetail::where('stockout_no', '=', $stockout_no)
                    ->update(['status' => 4,'approuved_by' => $this->user->name]);

                session()->flash('success', 'Stockout has been done successfuly !, from '.$code_store_origin);
                return back();

    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $stockout_no
     * @return \Illuminate\Http\Response
     */
    public function destroy($stockout_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_stockout.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any stockout !');
        }

        $stockout = MsMaterialStockout::where('stockout_no',$stockout_no)->first();
        if (!is_null($stockout)) {
            $stockout->delete();
            MsMaterialStockoutDetail::where('stockout_no',$stockout_no)->delete();
        }

        session()->flash('success', 'Stockout has been deleted !!');
        return back();
    }
}

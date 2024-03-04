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
use App\Models\MsMaterialStockin;
use App\Models\MsMaterialStockinDetail;
use App\Models\MsMaterialStore;
use App\Models\MsMaterialStoreDetail;
use App\Models\MsMaterialReport;
use Carbon\Carbon;
use PDF;
use Validator;
use Excel;
use Mail;

class MaterialStockinController extends Controller
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_stockin.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stockin !');
        }

        $stockins = MsMaterialStockin::orderBy('id','desc')->get();
        return view('backend.pages.musumba_steel.material_stockin.index', compact('stockins'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_stockin.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockin !');
        }

        $materials = MsMaterialStoreDetail::where('material_id','!=','')->get();
        $destination_stores = MsMaterialStore::all();
        return view('backend.pages.musumba_steel.material_stockin.create', compact('materials','destination_stores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('musumba_steel_material_stockin.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockin !');
        }

        $rules = array(
                'material_id.*'  => 'required',
                'date'  => 'required',
                'unit.*'  => 'required',
                'quantity.*'  => 'required',
                'purchase_price.*'  => 'required',
                'handingover'  => 'required',
                'origin'  => 'required',
                'receptionist'  => 'required',
                'destination_store_id'  => 'required',
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
            $invoice_currency = $request->invoice_currency;
            $handingover = $request->handingover;
            $receptionist = $request->receptionist;
            $origin = $request->origin;
            $store_type = $request->store_type;
            $code_store = $request->code_store;
            $item_movement_type = $request->item_movement_type;
            $description =$request->description; 
            $destination_store_id = $request->destination_store_id;
            $unit = $request->unit;
            $quantity = $request->quantity;
            $purchase_price = $request->purchase_price;
            

            $latest = MsMaterialStockin::latest()->first();
            if ($latest) {
               $stockin_no = 'BE' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $stockin_no = 'BE' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $stockin_signature = "4001711615".Carbon::parse(Carbon::now())->format('YmdHis')."/".$stockin_no;


            for( $count = 0; $count < count($material_id); $count++ ){
                $total_amount_purchase = $quantity[$count] * $purchase_price[$count];

                $data = array(
                    'material_id' => $material_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'unit' => $unit[$count],
                    'purchase_price' => $purchase_price[$count],
                    'total_amount_purchase' => $total_amount_purchase,
                    'receptionist' => $receptionist,
                    'handingover' => $handingover,
                    'origin' => $origin,
                    'store_type' => $store_type,
                    'code_store' => $code_store,
                    'item_movement_type' => $item_movement_type,
                    'destination_store_id' => $destination_store_id,
                    'stockin_no' => $stockin_no,
                    'stockin_signature' => $stockin_signature,
                    'created_by' => $created_by,
                    'description' => $description,
                    'status' => 1,
                    'created_at' => \Carbon\Carbon::now()

                );
                $insert_data[] = $data;

                
            }
            MsMaterialStockinDetail::insert($insert_data);


            //create stockin
            $stockin = new MsMaterialStockin();
            $stockin->date = $date;
            $stockin->stockin_no = $stockin_no;
            $stockin->stockin_signature = $stockin_signature;
            $stockin->receptionist = $receptionist;
            $stockin->handingover = $handingover;
            $stockin->origin = $origin;
            $stockin->store_type = $store_type;
            $stockin->code_store = $code_store;
            $stockin->item_movement_type = $item_movement_type;
            $stockin->destination_store_id = $destination_store_id;
            $stockin->created_by = $created_by;
            $stockin->status = 1;
            $stockin->description = $description;
            $stockin->save();
            
        session()->flash('success', 'stockin has been created !!');
        return redirect()->route('admin.ms-material-stockins.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($stockin_no)
    {
        //
        $code = MsMaterialStockinDetail::where('stockin_no', $stockin_no)->value('stockin_no');
        $stockins = MsMaterialStockinDetail::where('stockin_no', $stockin_no)->get();
        return view('backend.pages.musumba_steel.material_stockin.show', compact('stockins','code'));
         
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($stockin_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_stockin.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any stockin !');
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $stockin_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_stockin.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any stockin !');
        }

        
        
    }

    public function bonEntree($stockin_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_stockin.create')) {
            abort(403, 'Sorry !! You are Unauthorized to print handover document!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $code = MsMaterialStockin::where('stockin_no', $stockin_no)->value('stockin_no');
        $datas = MsMaterialStockinDetail::where('stockin_no', $stockin_no)->get();
        $data = MsMaterialStockin::where('stockin_no',$stockin_no)->first();
        $receptionniste = MsMaterialStockin::where('stockin_no', $stockin_no)->value('receptionist');
        $description = MsMaterialStockin::where('stockin_no', $stockin_no)->value('description');
        $stockin_signature = MsMaterialStockin::where('stockin_no', $stockin_no)->value('stockin_signature');
        $date = MsMaterialStockin::where('stockin_no', $stockin_no)->value('date');
        $totalValue = DB::table('ms_material_stockin_details')
            ->where('stockin_no', '=', $stockin_no)
            ->sum('total_amount_purchase');
        $pdf = PDF::loadView('backend.pages.musumba_steel.document.material_stockin',compact('datas','code','totalValue','receptionniste','description','setting','stockin_no','date','stockin_signature','data'));

        Storage::put('public/musumba_steel/material_stockin/'.$stockin_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('BON_ENTREE_'.$stockin_no.'.pdf');
        
    }

    public function validateStockin($stockin_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_material_stockin.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any stockin !');
        }
            MsMaterialStockin::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            MsMaterialStockinDetail::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        session()->flash('success', 'stockin has been validated !!');
        return back();
    }

    public function reject($stockin_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_material_stockin.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any stockin !');
        }

        MsMaterialStockin::where('stockin_no', '=', $stockin_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
        MsMaterialStockinDetail::where('stockin_no', '=', $stockin_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        session()->flash('success', 'Stockin has been rejected !!');
        return back();
    }

    public function reset($stockin_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_material_stockin.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any stockin !');
        }

        MsMaterialStockin::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
        MsMaterialStockinDetail::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        session()->flash('success', 'Stockin has been reseted !!');
        return back();
    }

    public function confirm($stockin_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_material_stockin.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any stockin !');
        }

        MsMaterialStockin::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            MsMaterialStockinDetail::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        session()->flash('success', 'Stockin has been confirmed !!');
        return back();
    }

    public function approuve($stockin_no)
    {
       if (is_null($this->user) || !$this->user->can('musumba_steel_material_stockin.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any stockin !');
        }


        $datas = MsMaterialStockinDetail::where('stockin_no', $stockin_no)->get();

        foreach($datas as $data){

                $code_store_destination = MsMaterialStore::where('id',$data->destination_store_id)->value('code');

                $valeurStockInitialDestination = MsMaterialStoreDetail::where('code',$code_store_destination)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('total_cump_value');
                $quantityStockInitialDestination = MsMaterialStoreDetail::where('code',$code_store_destination)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('quantity');
                $quantityTotalBigStore = $quantityStockInitialDestination + $data->quantity;


                $valeurAcquisition = $data->quantity * $data->purchase_price;

                $valeurTotalUnite = $data->quantity + $quantityStockInitialDestination;
                $cump = ($valeurStockInitialDestination + $valeurAcquisition) / $valeurTotalUnite;

                $reportStore = array(
                    'material_id' => $data->material_id,
                    'quantity_stock_initial' => $quantityStockInitialDestination,
                    'value_stock_initial' => $valeurStockInitialDestination,
                    'code_store_destination' => $code_store_destination,
                    'stockin_no' => $data->stockin_no,
                    'quantity_stockin' => $data->quantity,
                    'value_stockin' => $data->total_amount_purchase,
                    'quantity_stock_final' => $quantityStockInitialDestination + $data->quantity,
                    'value_stock_final' => $valeurStockInitialDestination + $data->total_amount_purchase,
                    'created_by' => $this->user->name,
                    'description' => $data->description,
                    'date' => $data->date,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportStoreData[] = $reportStore;

                    $mediumStore = array(
                        'material_id' => $data->material_id,
                        'quantity' => $quantityTotalBigStore,
                        'total_purchase_value' => $quantityTotalBigStore * $data->purchase_price,
                        'cump' => $cump,
                        'total_cump_value' => $quantityTotalBigStore * $cump,
                        'created_by' => $this->user->name,
                        'verified' => false
                    );

                    $mediumStoreData[] = $mediumStore;

                    $materialData = array(
                        'id' => $data->material_id,
                        'quantity' => $quantityTotalBigStore,
                        'cump' => $cump
                    );

                    MsMaterial::where('id',$data->material_id)
                        ->update($materialData);

                        $material = MsMaterialStoreDetail::where('code',$code_store_destination)->where("material_id",$data->material_id)->value('material_id');

                        if (!empty($material)) {
                            MsMaterialReport::insert($reportStoreData);
                            MsMaterialStoreDetail::where('code',$code_store_destination)->where('material_id',$data->material_id)
                        ->update($mediumStore);
                        }else{
                            MsMaterialReport::insert($reportStoreData);
                            MsMaterialStoreDetail::insert($mediumStoreData);
                        }
  
        }

        MsMaterialStockin::where('stockin_no', '=', $stockin_no)
                        ->update(['status' => 4,'approuved_by' => $this->user->name]);
        MsMaterialStockinDetail::where('stockin_no', '=', $stockin_no)
                        ->update(['status' => 4,'approuved_by' => $this->user->name]);

        session()->flash('success', 'Stockin has been done successfuly !, to '.$code_store_destination);
                    return back();

    }

    public function get_reception_data()
    {
        return Excel::download(new ReceptionExport, 'stockins.xlsx');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $stockin_no
     * @return \Illuminate\Http\Response
     */
    public function destroy($stockin_no)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_stockin.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any stockin !');
        }

        $stockin = MsMaterialStockin::where('stockin_no',$stockin_no)->first();
        if (!is_null($stockin)) {
            $stockin->delete();
            MsMaterialStockinDetail::where('stockin_no',$stockin_no)->delete();
        }

        session()->flash('success', 'Stockin has been deleted !!');
        return back();
    }
}

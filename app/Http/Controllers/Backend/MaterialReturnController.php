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
use App\Models\MaterialTransfer;
use App\Models\MaterialTransferDetail;
use App\Models\MaterialReturn;
use App\Models\MaterialReturnDetail;
use App\Models\MaterialBigStoreDetail;
use App\Models\MaterialSmallStoreDetail;
use App\Models\MaterialBigStore;
use App\Models\MaterialSmallStore;
use App\Models\MaterialBigReport;
use App\Models\MaterialSmallReport;
use Carbon\Carbon;
use PDF;
use Validator;
use Excel;
use Mail;

class MaterialReturnController extends Controller
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
        if (is_null($this->user) || !$this->user->can('material_return.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any return !');
        }

        $returns = MaterialReturn::all();
        return view('backend.pages.material_return.index', compact('returns'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($transfer_no)
    {
        if (is_null($this->user) || !$this->user->can('material_return.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any return !');
        }

        $materials  = Material::orderBy('name','asc')->get();
        $origin_stores = MaterialSmallStore::all();
        $destination_stores = MaterialBigStore::all();
        $datas = MaterialTransferDetail::where('transfer_no', $transfer_no)->get();
        return view('backend.pages.material_return.create', compact('materials','transfer_no','datas','origin_stores','destination_stores'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('material_return.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any return !');
        }

        $rules = array(
                'material_id.*'  => 'required',
                'date'  => 'required',
                'unit.*'  => 'required',
                'quantity_transfered.*'  => 'required',
                'price.*'  => 'required',
                'quantity_returned.*'  => 'required',
                'origin_store_id'  => 'required',
                'transfer_no'  => 'required',
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
            $origin_store_id = $request->origin_store_id;
            $transfer_no = $request->transfer_no;
            $description =$request->description; 
            $destination_store_id = $request->destination_store_id;
            $unit = $request->unit;
            $quantity_transfered = $request->quantity_transfered;
            $price = $request->price;
            $quantity_returned = $request->quantity_returned;
            

            $latest = MaterialReturn::latest()->first();
            if ($latest) {
               $return_no = 'RET' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $return_no = 'RET' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $return_signature = "4001711615".Carbon::parse(Carbon::now())->format('YmdHis')."/".$return_no;


            for( $count = 0; $count < count($material_id); $count++ ){
                $total_value_transfered = $quantity_transfered[$count] * $price[$count];
                $total_value_returned = $quantity_returned[$count] * $price[$count];
                $data = array(
                    'material_id' => $material_id[$count],
                    'date' => $date,
                    'quantity_transfered' => $quantity_transfered[$count],
                    'quantity_returned' => $quantity_returned[$count],
                    'unit' => $unit[$count],
                    'price' => $price[$count],
                    'total_value_transfered' => $total_value_transfered,
                    'total_value_returned' => $total_value_returned,
                    'transfer_no' => $transfer_no,
                    'origin_store_id' => $origin_store_id,
                    'destination_store_id' => $destination_store_id,
                    'return_no' => $return_no,
                    'return_signature' => $return_signature,
                    'created_by' => $created_by,
                    'description' => $description,
                    'status' => 1,
                    'created_at' => \Carbon\Carbon::now()

                );
                $insert_data[] = $data;

                
            }
            MaterialReturnDetail::insert($insert_data);


            //create return
            $return = new MaterialReturn();
            $return->date = $date;
            $return->transfer_no = $transfer_no;
            $return->return_signature = $return_signature;
            $return->return_no = $return_no;
            $return->origin_store_id = $origin_store_id;
            $return->destination_store_id = $destination_store_id;
            $return->created_by = $created_by;
            $return->status = 1;
            $return->description = $description;
            $return->save();
            
        session()->flash('success', 'return has been created !!');
        return redirect()->route('admin.material-return.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($return_no)
    {
        //
        $code = MaterialReturnDetail::where('return_no', $return_no)->value('return_no');
        $returns = MaterialReturnDetail::where('return_no', $return_no)->get();
        return view('backend.pages.material_return.show', compact('returns','code'));
         
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($return_no)
    {
        if (is_null($this->user) || !$this->user->can('material_return.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any return !');
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $return_no)
    {
        if (is_null($this->user) || !$this->user->can('material_return.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any return !');
        }
        
    }

    public function bonRetour($return_no)
    {
        if (is_null($this->user) || !$this->user->can('material_return.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $return_no = MaterialReturn::where('return_no', $return_no)->value('return_no');
        $datas = MaterialReturnDetail::where('return_no', $return_no)->get();
        $data = MaterialReturn::where('return_no', $return_no)->first();
        $description = MaterialReturn::where('return_no', $return_no)->value('description');
        $transfer_no = MaterialReturn::where('return_no', $return_no)->value('transfer_no');
        $return_signature = MaterialReturn::where('return_no', $return_no)->value('return_signature');
        $date = MaterialReturn::where('return_no', $return_no)->value('date');
        $totalValueReturned = DB::table('material_return_details')
            ->where('return_no', '=', $return_no)
            ->sum('total_value_returned');
        $totalValuetransfered = DB::table('material_return_details')
            ->where('return_no', '=', $return_no)
            ->sum('total_value_transfered');
        $pdf = PDF::loadView('backend.pages.document.material_return',compact('datas','return_no','totalValueReturned','totalValuetransfered','data','description','setting','date','return_signature'));

        Storage::put('public/pdf/material_retour/'.$return_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('bon_retour'.$return_no.'.pdf');
        
    }

    public function validateReturn($return_no)
    {
       if (is_null($this->user) || !$this->user->can('material_return.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any return !');
        }
            MaterialReturn::where('return_no', '=', $return_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            MaterialReturnDetail::where('return_no', '=', $return_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        session()->flash('success', 'return has been validated !!');
        return back();
    }

    public function reject($return_no)
    {
       if (is_null($this->user) || !$this->user->can('material_return.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any return !');
        }

        MaterialReturn::where('return_no', '=', $return_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
        MaterialReturnDetail::where('return_no', '=', $return_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        session()->flash('success', 'Return has been rejected !!');
        return back();
    }

    public function reset($return_no)
    {
       if (is_null($this->user) || !$this->user->can('material_return.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any return !');
        }

        MaterialReturn::where('return_no', '=', $return_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
        MaterialReturnDetail::where('return_no', '=', $return_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        session()->flash('success', 'Return has been reseted !!');
        return back();
    }

    public function confirm($return_no)
    {
       if (is_null($this->user) || !$this->user->can('material_return.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any return !');
        }

        MaterialReturn::where('return_no', '=', $return_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            MaterialReturnDetail::where('return_no', '=', $return_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        session()->flash('success', 'Return has been confirmed !!');
        return back();
    }

    public function approuve($return_no)
    {
       if (is_null($this->user) || !$this->user->can('material_return.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any return !');
        }


        $datas = MaterialReturnDetail::where('return_no', $return_no)->get();

        foreach($datas as $data){

                $code_store_origin = MaterialSmallStore::where('id',$data->origin_store_id)->value('code');
                $code_store_destination = MaterialBigStore::where('id',$data->destination_store_id)->value('code');

                $valeurStockInitialOrigine = MaterialSmallStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('total_purchase_value');
                $quantityStockInitialOrigine = MaterialSmallStoreDetail::where('code',$code_store_origin)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('quantity');
                $quantityRestantBigStore = $quantityStockInitialOrigine - $data->quantity_returned;

                $valeurStockInitialDestination = MaterialBigStoreDetail::where('code',$code_store_destination)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('total_purchase_value');
                $quantityStockInitialDestination = MaterialBigStoreDetail::where('code',$code_store_destination)->where('material_id','!=', '')->where('material_id', $data->material_id)->value('quantity');
                $quantityRestantSmallStore = $quantityStockInitialDestination - $data->quantity_returned;


                $valeurAcquisition = $data->quantity_returned * $data->price;

                $valeurTotalUnite = $data->quantity_returned + $quantityStockInitialDestination;
                $cump = ($valeurStockInitialDestination + $valeurAcquisition) / $valeurTotalUnite;

                $reportSmallStore = array(
                    'material_id' => $data->material_id,
                    'quantity_stock_initial' => $quantityStockInitialOrigine,
                    'value_stock_initial' => $valeurStockInitialOrigine,
                    'code_store' => $code_store_origin,
                    'code_store_origin' => $code_store_origin,
                    'code_store_destination' => $code_store_destination,
                    'return_no' => $data->return_no,
                    'quantity_stockout' => $data->quantity_returned,
                    'value_stockout' => $data->total_value_returned,
                    'quantity_stock_final' => $quantityStockInitialOrigine - $data->quantity_returned,
                    'value_stock_final' => $valeurStockInitialOrigine - $data->total_value_returned,
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportSmallStoreData[] = $reportSmallStore;


                $reportBigStore = array(
                    'material_id' => $data->material_id,
                    'quantity_stock_initial' => $quantityStockInitialDestination,
                    'value_stock_initial' => $valeurStockInitialDestination,
                    'code_store' => $code_store_destination,
                    'code_store_origin' => $code_store_origin,
                    'code_store_destination' => $code_store_destination,
                    'return_no' => $data->return_no,
                    'quantity_stockin' => $data->quantity_returned,
                    'value_stockin' => $data->total_value_returned,
                    'quantity_stock_final' => $quantityStockInitialDestination + $data->quantity_returned,
                    'value_stock_final' => $valeurStockInitialDestination + $data->total_value_returned,
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportBigStoreData[] = $reportBigStore;

                    $smStore = array(
                        'material_id' => $data->material_id,
                        'quantity' => $quantityRestantBigStore,
                        'total_selling_value' => $quantityRestantBigStore * $data->price,
                        'total_purchase_value' => $quantityRestantBigStore * $data->price,
                        'total_cump_value' => $quantityRestantBigStore * $data->price,
                        'created_by' => $this->user->name,
                        'verified' => false,
                        'created_at' => \Carbon\Carbon::now()
                    );


                    $bigStoreData = array(
                            'material_id' => $data->material_id,
                            'quantity' => $data->quantity_returned,
                            'cump' => $cump,
                            'unit' => $data->unit,
                            'code' => $code_store_destination,
                            'purchase_price' => $data->price,
                            'selling_price' => $data->price,
                            'total_cump_value' => $cump * ($data->quantity_returned + $quantityStockInitialDestination),
                            'total_purchase_value' => $data->price * ($data->quantity_returned + $quantityStockInitialDestination),
                            'total_selling_value' => $data->price * ($data->quantity_returned + $quantityStockInitialDestination),
                            'verified' => false,
                            'created_by' => $this->user->name,
                            'created_at' => \Carbon\Carbon::now()
                        );
                        $bigStoreD[] = $bigStoreData;

                        $bigStore = array(
                            'material_id' => $data->material_id,
                            'quantity' => $quantityStockInitialDestination + $data->quantity_returned,
                            'cump' => $cump,
                            //'purchase_price' => $data->price,
                            //'selling_price' => $data->price,
                            'total_cump_value' => $cump * ($data->quantity_returned + $quantityStockInitialDestination),
                            'total_purchase_value' => $data->price * ($data->quantity_returned + $quantityStockInitialDestination),
                            'total_selling_value' => $data->price * ($data->quantity_returned + $quantityStockInitialDestination),
                            'verified' => false,
                            'created_by' => $this->user->name,
                            'created_at' => \Carbon\Carbon::now()
                        );

                        $material = MaterialBigStoreDetail::where('code',$code_store_destination)->where("material_id",$data->material_id)->value('material_id');


                    if ($data->quantity_returned <= $quantityStockInitialOrigine) {

                        MaterialSmallReport::insert($reportSmallStoreData);
                        
                        MaterialSmallStoreDetail::where('code',$code_store_origin)->where('material_id',$data->material_id)
                        ->update($smStore);

                        if (!empty($material)) {
                            MaterialBigReport::insert($reportBigStoreData);
                            MaterialBigStoreDetail::where('code',$code_store_destination)->where('material_id',$data->material_id)
                        ->update($bigStore);
                        }else{
                            MaterialBigReport::insert($reportBigStoreData);
                            MaterialBigStoreDetail::insert($bigStoreD);
                        }

                        MaterialTransfer::where('transfer_no', '=', $data->transfer_no)
                        ->update(['status' => 5]);
                        MaterialTransferDetail::where('transfer_no', '=', $data->transfer_no)
                        ->update(['status' => 5]);

                        
                    }else{
                        session()->flash('error', 'Why do you want returning quantity you do not have in your store?please rewrite a valid quantity!');
                        return redirect()->back();
                    }
  
        }


            MaterialReturn::where('return_no', '=', $return_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);
            MaterialReturnDetail::where('return_no', '=', $return_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);

        session()->flash('success', 'Return has been done successfuly !,from store '.$code_store_origin.' to '.$code_store_destination);
        return back();
    }

    public function get_reception_data()
    {
        return Excel::download(new ReceptionExport, 'receptions.xlsx');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $return_no
     * @return \Illuminate\Http\Response
     */
    public function destroy($return_no)
    {
        if (is_null($this->user) || !$this->user->can('material_return.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any return !');
        }

        $return = MaterialReturn::where('return_no',$return_no)->first();
        if (!is_null($return)) {
            $return->delete();
            MaterialReturnDetail::where('return_no',$return_no)->delete();
        }

        session()->flash('success', 'Return has been deleted !!');
        return back();
    }
}

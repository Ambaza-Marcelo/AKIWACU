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
use App\Models\FoodTransfer;
use App\Models\FoodTransferDetail;
use App\Models\FoodBigStoreDetail;
use App\Models\FoodSmallStoreDetail;
use App\Models\FoodBigStore;
use App\Models\FoodSmallStore;
use App\Models\FoodRequisitionDetail;
use App\Models\FoodRequisition;
use App\Models\FoodBigReport;
use App\Models\FoodSmallReport;
use Carbon\Carbon;
use PDF;
use Validator;
use Excel;
use Mail;

class FoodTransferController extends Controller
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
        if (is_null($this->user) || !$this->user->can('food_transfer.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any transfer !');
        }

        $transfers = FoodTransfer::all();
        return view('backend.pages.food_transfer.index', compact('transfers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($requisition_no)
    {
        if (is_null($this->user) || !$this->user->can('food_transfer.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any transfer !');
        }

        $foods  = Food::orderBy('name','asc')->get();
        $origin_stores = FoodBigStore::all();
        $destination_stores = FoodSmallStore::all();
        $datas = FoodRequisitionDetail::where('requisition_no', $requisition_no)->get();
        return view('backend.pages.food_transfer.create', compact('foods','requisition_no','datas','origin_stores','destination_stores'));
    }

    public function portion($transfer_no)
    {
        if (is_null($this->user) || !$this->user->can('food_transfer.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any transfer !');
        }

        $foods  = Food::where('store_type','!=',2)->orderBy('name','asc')->get();
        $origin_stores = FoodBigStore::all();
        $destination_stores = FoodSmallStore::all();
        $data = FoodTransferDetail::where('transfer_no', $transfer_no)->first();
        $datas = FoodTransferDetail::where('transfer_no', $transfer_no)->get();
        return view('backend.pages.food_transfer.portion', compact('foods','transfer_no','datas','origin_stores','destination_stores','data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('food_transfer.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any transfer !');
        }

        $rules = array(
                'food_id.*'  => 'required',
                'date'  => 'required',
                'unit.*'  => 'required',
                'quantity_requisitioned.*'  => 'required',
                'price.*'  => 'required',
                'quantity_transfered.*'  => 'required',
                'origin_store_id'  => 'required',
                'requisition_no'  => 'required',
                'destination_store_id'  => 'required',
                'description'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $food_id = $request->food_id;
            $date = $request->date;
            $origin_store_id = $request->origin_store_id;
            $requisition_no = $request->requisition_no;
            $description =$request->description; 
            $destination_store_id = $request->destination_store_id;
            $unit = $request->unit;
            $quantity_requisitioned = $request->quantity_requisitioned;
            $price = $request->price;
            $quantity_transfered = $request->quantity_transfered;
            

            $latest = FoodTransfer::latest()->first();
            if ($latest) {
               $transfer_no = 'BT' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $transfer_no = 'BT' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $transfer_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$transfer_no;


            for( $count = 0; $count < count($food_id); $count++ ){
                $total_value_requisitioned = $quantity_requisitioned[$count] * $price[$count];
                $total_value_transfered = $quantity_transfered[$count] * $price[$count];
                $data = array(
                    'food_id' => $food_id[$count],
                    'date' => $date,
                    'quantity_requisitioned' => $quantity_requisitioned[$count],
                    'quantity_transfered' => $quantity_transfered[$count],
                    'unit' => $unit[$count],
                    'price' => $price[$count],
                    'total_value_requisitioned' => $total_value_requisitioned,
                    'total_value_transfered' => $total_value_transfered,
                    'requisition_no' => $requisition_no,
                    'origin_store_id' => $origin_store_id,
                    'destination_store_id' => $destination_store_id,
                    'transfer_no' => $transfer_no,
                    'transfer_signature' => $transfer_signature,
                    'created_by' => $created_by,
                    'description' => $description,
                    'status' => 1,
                    'created_at' => \Carbon\Carbon::now()

                );
                $insert_data[] = $data;

                
            }
            FoodTransferDetail::insert($insert_data);


            //create transfer
            $transfer = new FoodTransfer();
            $transfer->date = $date;
            $transfer->transfer_no = $transfer_no;
            $transfer->transfer_signature = $transfer_signature;
            $transfer->requisition_no = $requisition_no;
            $transfer->origin_store_id = $origin_store_id;
            $transfer->destination_store_id = $destination_store_id;
            $transfer->created_by = $created_by;
            $transfer->status = 1;
            $transfer->description = $description;
            $transfer->save();
            
        session()->flash('success', 'transfer has been created !!');
        return redirect()->route('admin.food-transfers.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($transfer_no)
    {
        //
        $code = FoodTransferDetail::where('transfer_no', $transfer_no)->value('transfer_no');
        $transfers = FoodTransferDetail::where('transfer_no', $transfer_no)->get();
        return view('backend.pages.food_transfer.show', compact('transfers','code'));
         
    }

    public function storePortion(Request $request, $transfer_no)
    {
        if (is_null($this->user) || !$this->user->can('food_transfer.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to portion any food !');
        }

        $rules = array(
                'food_id.*'  => 'required',
                'date_portion'  => 'required',
                'unit_portion.*'  => 'required',
                'quantity_portion.*'  => 'required',
                'price.*'  => 'required',
                'quantity_transfered.*'  => 'required',
                'description_portion'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $food_id = $request->food_id;
            $date_portion = $request->date_portion;
            $origin_store_id = $request->origin_store_id;
            $description_portion =$request->description_portion; 
            $destination_store_id = $request->destination_store_id;
            $unit_portion = $request->unit_portion;
            $quantity_portion = $request->quantity_portion;
            $price = $request->price;
            $quantity_transfered = $request->quantity_transfered;
            $status_portion = 0;
            $portioned_by = $this->user->name;


            for( $count = 0; $count < count($food_id); $count++ ){
                $value_portion = $quantity_transfered[$count] * $price[$count];
                $data = array(
                    'food_id' => $food_id[$count],
                    'date_portion' => $date_portion,
                    'quantity_portion' => $quantity_portion[$count],
                    'unit_portion' => $unit_portion[$count],
                    'value_portion' => $value_portion,
                    'portioned_by' => $portioned_by,
                    'description_portion' => $description_portion,
                    'status_portion' => $status_portion,
                    'updated_at' => \Carbon\Carbon::now()

                );
                //$insert_data[] = $data;
                FoodTransferDetail::where('transfer_no',$transfer_no)->where('food_id',$food_id[$count])->update($data);
                
            }

            FoodTransfer::where('transfer_no', '=', $transfer_no)
                ->update(['status_portion' => 0]);
            

        session()->flash('success', 'Food has been portioned successfuly !!');
        return redirect()->route('admin.food-transfers.index');
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($transfer_no)
    {
        if (is_null($this->user) || !$this->user->can('food_transfer.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any transfer !');
        }

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $transfer_no)
    {
        if (is_null($this->user) || !$this->user->can('food_transfer.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any transfer !');
        }
        
    }

    public function bonTransfert($transfer_no)
    {
        if (is_null($this->user) || !$this->user->can('food_transfer.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $transfer_no = FoodTransfer::where('transfer_no', $transfer_no)->value('transfer_no');
        $datas = FoodTransferDetail::where('transfer_no', $transfer_no)->get();
        $data = FoodTransfer::where('transfer_no', $transfer_no)->first();
        $description = FoodTransfer::where('transfer_no', $transfer_no)->value('description');
        $requisition_no = FoodTransfer::where('transfer_no', $transfer_no)->value('requisition_no');
        $transfer_signature = FoodTransfer::where('transfer_no', $transfer_no)->value('transfer_signature');
        $date = FoodTransfer::where('transfer_no', $transfer_no)->value('date');
        $totalValueTransfered = DB::table('food_transfer_details')
            ->where('transfer_no', '=', $transfer_no)
            ->sum('total_value_transfered');
        $totalValueRequisitioned = DB::table('food_transfer_details')
            ->where('transfer_no', '=', $transfer_no)
            ->sum('total_value_requisitioned');
        $pdf = PDF::loadView('backend.pages.document.food_transfert',compact('datas','transfer_no','totalValueTransfered','totalValueRequisitioned','data','description','requisition_no','setting','date','transfer_signature'));

        Storage::put('public/pdf/food_transfert/'.$transfer_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('bon_transfert'.$transfer_no.'.pdf');
        
    }

    public function validateTransfer($transfer_no)
    {
       if (is_null($this->user) || !$this->user->can('food_transfer.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any transfer !');
        }
            FoodTransfer::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            FoodTransferDetail::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        session()->flash('success', 'transfer has been validated !!');
        return back();
    }

    public function reject($transfer_no)
    {
       if (is_null($this->user) || !$this->user->can('food_transfer.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any transfer !');
        }

        FoodTransfer::where('transfer_no', '=', $transfer_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
        FoodTransferDetail::where('transfer_no', '=', $transfer_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        session()->flash('success', 'Transfer has been rejected !!');
        return back();
    }

    public function reset($transfer_no)
    {
       if (is_null($this->user) || !$this->user->can('food_transfer.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any transfer !');
        }

        FoodTransfer::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
        FoodTransferDetail::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        session()->flash('success', 'Transfer has been reseted !!');
        return back();
    }

    public function confirm($transfer_no)
    {
       if (is_null($this->user) || !$this->user->can('food_transfer.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any transfer !');
        }

        FoodTransfer::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            FoodTransferDetail::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        session()->flash('success', 'Transfer has been confirmed !!');
        return back();
    }

    public function approuve($transfer_no)
    {
       if (is_null($this->user) || !$this->user->can('food_transfer.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any transfer !');
        }


        $datas = FoodTransferDetail::where('transfer_no', $transfer_no)->get();

        foreach($datas as $data){

                $code_store_origin = FoodBigStore::where('id',$data->origin_store_id)->value('code');
                $code_store_destination = FoodSmallStore::where('id',$data->destination_store_id)->value('code');

                $valeurStockInitialOrigine = FoodBigStoreDetail::where('code',$code_store_origin)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('total_cump_value');
                $quantityStockInitialOrigine = FoodBigStoreDetail::where('code',$code_store_origin)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('quantity');
                $quantityRestantBigStore = $quantityStockInitialOrigine - $data->quantity_transfered;

                $valeurStockInitialDestination = FoodSmallStoreDetail::where('code',$code_store_destination)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('total_cump_value');
                $quantityStockInitialDestination = FoodSmallStoreDetail::where('code',$code_store_destination)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('quantity');
                $quantityRestantSmallStore = $quantityStockInitialDestination - $data->quantity_transfered;


                $valeurAcquisition = $data->quantity_transfered * $data->price;

                $valeurTotalUnite = $data->quantity_transfered + $quantityStockInitialDestination;
                $cump = ($valeurStockInitialDestination + $valeurAcquisition) / $valeurTotalUnite;

                $reportBigStore = array(
                    'food_id' => $data->food_id,
                    'quantity_stock_initial' => $quantityStockInitialOrigine,
                    'value_stock_initial' => $valeurStockInitialOrigine,
                    'code_store' => $code_store_origin,
                    'code_store_origin' => $code_store_origin,
                    'code_store_destination' => $code_store_destination,
                    'transfer_no' => $data->transfer_no,
                    'date' => $data->date,
                    'quantity_transfer' => $data->quantity_transfered,
                    'value_transfer' => $data->total_value_transfered,
                    'quantity_stock_final' => $quantityStockInitialOrigine - $data->quantity_transfered,
                    'value_stock_final' => $valeurStockInitialOrigine - $data->total_value_transfered,
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportBigStoreData[] = $reportBigStore;


                $reportSmallStore = array(
                    'food_id' => $data->food_id,
                    'quantity_stock_initial' => $quantityStockInitialDestination,
                    'value_stock_initial' => $valeurStockInitialDestination,
                    'code_store' => $code_store_destination,
                    'code_store_origin' => $code_store_origin,
                    'code_store_destination' => $code_store_destination,
                    'transfer_no' => $data->transfer_no,
                    'date' => $data->date,
                    'quantity_transfer' => $data->quantity_transfered,
                    'value_transfer' => $data->total_value_transfered,
                    'quantity_stock_final' => $quantityStockInitialDestination + $data->quantity_transfered,
                    'value_stock_final' => $valeurStockInitialDestination + $data->total_value_transfered,
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportSmallStoreData[] = $reportSmallStore;

                    $bigStore = array(
                        'food_id' => $data->food_id,
                        'quantity' => $quantityRestantBigStore,
                        'total_selling_value' => $quantityRestantBigStore * $data->price,
                        'total_purchase_value' => $quantityRestantBigStore * $data->price,
                        'total_cump_value' => $quantityRestantBigStore * $data->price,
                        'created_by' => $this->user->name,
                        'verified' => false,
                        'created_at' => \Carbon\Carbon::now()
                    );


                    $smallStoreData = array(
                            'food_id' => $data->food_id,
                            'quantity' => $data->quantity_transfered,
                            'cump' => $cump,
                            'unit' => $data->unit,
                            'code' => $code_store_destination,
                            'purchase_price' => $data->price,
                            'selling_price' => $data->price,
                            'total_cump_value' => $cump * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'total_purchase_value' => $data->price * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'total_selling_value' => $data->price * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'verified' => false,
                            'created_by' => $this->user->name,
                            'created_at' => \Carbon\Carbon::now()
                        );
                        $smallStore[] = $smallStoreData;

                        $smStore = array(
                            'food_id' => $data->food_id,
                            'quantity' => $quantityStockInitialDestination + $data->quantity_transfered,
                            'cump' => $cump,
                            //'purchase_price' => $data->price,
                            //'selling_price' => $data->price,
                            'total_cump_value' => $cump * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'total_purchase_value' => $data->price * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'total_selling_value' => $data->price * ($data->quantity_transfered + $quantityStockInitialDestination),
                            'verified' => false,
                            'created_by' => $this->user->name,
                            'created_at' => \Carbon\Carbon::now()
                        );

                        $food = FoodSmallStoreDetail::where('code',$code_store_destination)->where("food_id",$data->food_id)->value('food_id');


                    if ($data->quantity_transfered <= $quantityStockInitialOrigine) {

                        FoodBigReport::insert($reportBigStoreData);
                        
                        FoodBigStoreDetail::where('code',$code_store_origin)->where('food_id',$data->food_id)
                        ->update($bigStore);

                        if (!empty($food)) {
                            FoodSmallReport::insert($reportSmallStoreData);
                            FoodSmallStoreDetail::where('code',$code_store_destination)->where('food_id',$data->food_id)
                        ->update($smStore);
                        }else{
                            FoodSmallReport::insert($reportSmallStoreData);
                            FoodSmallStoreDetail::insert($smallStore);
                        }

                        FoodRequisition::where('requisition_no', '=', $data->requisition_no)
                        ->update(['status' => 5]);
                        FoodRequisitionDetail::where('requisition_no', '=', $data->requisition_no)
                        ->update(['status' => 5]);

                        
                    }else{
                        session()->flash('error', 'Why do you want transfering quantity you do not have in your store?please rewrite a valid quantity!');
                        return redirect()->back();
                    }
  
        }


            FoodTransfer::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);
            FoodTransferDetail::where('transfer_no', '=', $transfer_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);

        session()->flash('success', 'Transfer has been done successfuly !,from store '.$code_store_origin.' to '.$code_store_destination);
        return back();
    }


    public function validatePortion($transfer_no)
    {
       if (is_null($this->user) || !$this->user->can('food_transfer.validatePortion')) {
            abort(403, 'Sorry !! You are Unauthorized to balidate any portion !');
        }


        $datas = FoodTransferDetail::where('transfer_no', $transfer_no)->get();

        foreach($datas as $data){
                $code_store = FoodSmallStore::where('id',$data->destination_store_id)->value('code');
                $valeurStockInitial = FoodSmallStoreDetail::where('code',$code_store)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('value_portion');
                $quantityStockInitial = FoodSmallStoreDetail::where('code',$code_store)->where('food_id','!=', '')->where('food_id', $data->food_id)->value('quantity_portion');
                $quantityTotal = $quantityStockInitial + $data->quantity_portion;

                $reportSmallStore = array(
                    'food_id' => $data->food_id,
                    'quantity_stock_initial_portion' => $quantityStockInitial,
                    'value_stock_initial_portion' => $valeurStockInitial,
                    'code_store' => $code_store,
                    'transfer_no' => $data->transfer_no,
                    'quantity_portion' => $data->quantity_portion,
                    'value_portion' => $data->value_portion,
                    'quantity_stock_final_portion' => $quantityStockInitial + $data->quantity_portion,
                    'value_stock_final_portion' => $valeurStockInitial + $data->value_portion,
                    'created_portion_by' => $this->user->name,
                    'description_portion' => $data->description_portion,
                    'updated_at' => \Carbon\Carbon::now()
                );
                //$reportSmallStoreData[] = $reportSmallStore;

                        $smStore = array(
                            'food_id' => $data->food_id,
                            'quantity_portion' => $quantityStockInitial + $data->quantity_portion,
                            'value_portion' => $data->value_portion + $valeurStockInitial,
                            'verified' => false,
                            //'created_by' => $this->user->name,
                            'created_at' => \Carbon\Carbon::now()
                        );
                        
                        FoodSmallStoreDetail::where('code',$code_store)->where('food_id',$data->food_id)
                        ->update($smStore);
                        FoodSmallReport::where('transfer_no',$transfer_no)->where('food_id',$data->food_id)
                        ->update($reportSmallStore);
  
        }


            FoodTransfer::where('transfer_no', '=', $transfer_no)
                ->update(['status_portion' => 1]);
            FoodTransferDetail::where('transfer_no', '=', $transfer_no)
                ->update(['status_portion' => 1]);

        session()->flash('success', 'Food Portioned has been validated successfuly !');
        return back();
    }

    public function get_reception_data()
    {
        return Excel::download(new ReceptionExport, 'receptions.xlsx');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $transfer_no
     * @return \Illuminate\Http\Response
     */
    public function destroy($transfer_no)
    {
        if (is_null($this->user) || !$this->user->can('food_transfer.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any transfer !');
        }

        $transfer = FoodTransfer::where('transfer_no',$transfer_no)->first();
        if (!is_null($transfer)) {
            $transfer->delete();
            FoodTransferDetail::where('transfer_no',$transfer_no)->delete();
        }

        session()->flash('success', 'Transfer has been deleted !!');
        return back();
    }
}

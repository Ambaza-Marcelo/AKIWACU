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
use App\Models\PrivateStoreItem;
use App\Models\PrivateDrinkInventory;
use App\Models\PrivateDrinkInventoryDetail;
use App\Exports\PrivateStoreInventoryExport;
use App\Models\PrivateStoreReport;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Excel;
use App\Models\Setting;
use App\Mail\DeleteInventoryMail;
use PDF;
use Mail;
use Validator;

class PrivateDrinkInventoryController extends Controller
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
        if (is_null($this->user) || !$this->user->can('private_drink_inventory.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any inventory !');
        }

        $inventories = PrivateDrinkInventory::all();
        return view('backend.pages.private_drink_inventory.index', compact('inventories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('private_drink_inventory.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any inventory !');
        }

        $datas  = PrivateStoreItem::orderBy('name','asc')->get();
        return view('backend.pages.private_drink_inventory.create', compact('datas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('private_drink_inventory.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any inventory !');
        }
        $rules = array(
            'private_store_item_id.*' => 'required',
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
            'description' => 'required',
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            try {DB::beginTransaction();

            $private_store_item_id = $request->private_store_item_id;
            $date = $request->date;
            $unit = $request->unit;
            $quantity = $request->quantity;
            $purchase_price = $request->purchase_price;
            $selling_price = $request->selling_price;
            $new_quantity = $request->new_quantity;
            $new_price = $request->new_price;
            $title = $request->title;
            $new_purchase_price = $request->new_purchase_price;
            $new_selling_price = $request->new_selling_price; 
            $new_unit = $request->new_unit; 

            $latest = PrivateDrinkInventory::latest()->first();
            if ($latest) {
               $inventory_no = 'BI' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $inventory_no = 'BI' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $inventory_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$inventory_no;

            $created_by = $this->user->name;
            $description =$request->description; 

            for( $count = 0; $count < count($private_store_item_id); $count++ ){
                $total_purchase_value = $quantity[$count] * $purchase_price[$count];
                $total_selling_value = $quantity[$count] * $selling_price[$count];
                $new_total_purchase_value = $new_quantity[$count] * $new_purchase_price[$count];
                $new_total_selling_value = $new_quantity[$count] * $new_selling_price[$count];
                $relicat = $quantity[$count] - $new_quantity[$count];
                $data = array(
                    'private_store_item_id' => $private_store_item_id[$count],
                    'date' => $date,
                    'title' => $title,
                    'quantity' => $quantity[$count],
                    'unit' => $unit[$count],
                    'purchase_price' => $purchase_price[$count],
                    'selling_price' => $selling_price[$count],
                    'total_purchase_value' => $total_purchase_value,
                    'total_selling_value' => $total_selling_value,
                    'new_quantity' => $new_quantity[$count],
                    'new_purchase_price' => $new_purchase_price[$count],
                    'new_selling_price' => $new_selling_price[$count],
                    'new_total_purchase_value' => $new_total_purchase_value,
                    'new_total_selling_value' => $new_total_selling_value,
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
            PrivateDrinkInventoryDetail::insert($insert_data);
            //create inventory
            $inventory = new PrivateDrinkInventory();
            $inventory->date = $date;
            $inventory->title = $title;
            $inventory->inventory_no = $inventory_no;
            $inventory->inventory_signature = $inventory_signature;
            $inventory->description = $description;
            $inventory->created_by = $created_by;
            $inventory->save();

            DB::commit();
            session()->flash('success', 'Inventory has been created !!');
            return redirect()->route('admin.private-drink-inventory.index');
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
     * @param  int  $inventory_no
     * @return \Illuminate\Http\Response
     */
    public function show($inventory_no)
    {
        //
        $code = PrivateDrinkInventoryDetail::where('inventory_no', $inventory_no)->value('inventory_no');
        $inventories = PrivateDrinkInventoryDetail::where('inventory_no', $inventory_no)->get();
        return view('backend.pages.private_drink_inventory.show', compact('inventories','code'));
    }

    public function bon_inventaire($inventory_no)
    {
        if (is_null($this->user) || !$this->user->can('private_drink_inventory.view')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $description = PrivateDrinkInventory::where('inventory_no', $inventory_no)->value('description');
        $title = PrivateDrinkInventory::where('inventory_no', $inventory_no)->value('title');
        $inventory_no = PrivateDrinkInventory::where('inventory_no', $inventory_no)->value('inventory_no');
        $date = PrivateDrinkInventory::where('inventory_no', $inventory_no)->value('date');
        $inventory_signature = PrivateDrinkInventory::where('inventory_no', $inventory_no)->value('inventory_signature');
        $datas = PrivateDrinkInventoryDetail::where('inventory_no', $inventory_no)->get();

        $totalValueActuelle = DB::table('private_drink_inventory_details')
            ->where('inventory_no', '=', $inventory_no)
            ->sum('total_purchase_value');
         $totalValueNew = DB::table('private_drink_inventory_details')
            ->where('inventory_no', '=', $inventory_no)
            ->sum('new_total_purchase_value');
        $totalQuantity = DB::table('private_drink_inventory_details')
            ->where('inventory_no', '=', $inventory_no)
            ->sum('quantity');
        $totalNewQuantity = DB::table('private_drink_inventory_details')
            ->where('inventory_no', '=', $inventory_no)
            ->sum('new_quantity');
        $gestionnaire = PrivateDrinkInventory::where('inventory_no', $inventory_no)->value('created_by');
        $pdf = PDF::loadView('backend.pages.document.private_drink_inventory',compact('datas','inventory_no','totalValueActuelle','totalValueNew','gestionnaire','setting','title','description','date','inventory_signature','totalQuantity','totalNewQuantity'));//->setPaper('a4', 'landscape');

        Storage::put('public/pdf/private_drink_inventory/'.'BON_INVENTAIRE_'.$inventory_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('BON_INVENTAIRE_'.$inventory_no.'.pdf');
    }

    public function validateInventory($inventory_no)
    {
       if (is_null($this->user) || !$this->user->can('private_drink_inventory.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any inventory !');
        }

        try {DB::beginTransaction();

        $datas = PrivateDrinkInventoryDetail::where('inventory_no', $inventory_no)->get();

        foreach($datas as $data){

                $item_calc = array(
                        'purchase_price' => $data->new_purchase_price,
                        'cump' => $data->new_purchase_price,
                        'selling_price' => $data->new_selling_price,
                        'unit' => $data->new_unit,
                        'quantity' => $data->new_quantity,
                        'total_purchase_value' => $data->new_purchase_price * $data->new_quantity,
                        'total_selling_value' => $data->new_selling_price * $data->new_quantity,
                        'total_cump_value' => $data->new_purchase_price * $data->new_quantity
                    );

                $reportData = array(
                        'private_store_item_id' => $data->private_store_item_id,
                        'quantity_stock_initial' => $data->quantity,
                        'value_stock_initial' => $data->purchase_price * $data->quantity,
                        'quantity_inventory' => $data->new_quantity,
                        'value_inventory' => $data->new_purchase_price * $data->new_quantity,
                        'document_no' => $data->inventory_no,
                        'created_by' => $this->user->name,
                    );


                PrivateStoreItem::where('id',$data->private_store_item_id)
                        ->update($item_calc);

                PrivateStoreReport::insert($reportData);
     
        }

            PrivateDrinkInventory::where('inventory_no', '=', $inventory_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            PrivateDrinkInventoryDetail::where('inventory_no', '=', $inventory_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'inventory has been validated !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
        
    }

    public function rejectInventory($inventory_no)
    {
       if (is_null($this->user) || !$this->user->can('private_drink_inventory.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any inventory !');
        }

        try {DB::beginTransaction();

            PrivateDrinkInventory::where('inventory_no', '=', $inventory_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
             PrivateDrinkInventoryDetail::where('inventory_no', '=', $inventory_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'inventory has been rejected !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

    public function resetInventory($inventory_no)
    {
       if (is_null($this->user) || !$this->user->can('private_drink_inventory.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any inventory !');
        }

        try {DB::beginTransaction();

            PrivateDrinkInventory::where('inventory_no', '=', $inventory_no)
                ->update(['status' => 0,'reseted_by' => $this->user->name]);
                PrivateDrinkInventoryDetail::where('inventory_no', '=', $inventory_no)
                ->update(['status' => 0,'reseted_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'inventory has been reseted !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

    public function exportToExcel($code)
    {
        return Excel::download(new PrivateStoreInventoryExport($code), 'INVENTAIRE_DU_STOCK_BOISSONS_PDG.xlsx');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($inventory_no)
    {
        if (is_null($this->user) || !$this->user->can('private_drink_inventory.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any inventory !');
        }

        try {DB::beginTransaction();

        $inventory = PrivateDrinkInventory::where('inventory_no', $inventory_no)->first();
        if (!is_null($inventory)) {
            $inventory->delete();
            PrivateDrinkInventoryDetail::where('inventory_no',$inventory_no)->delete();

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

        DB::commit();
            session()->flash('success', 'Inventory has been deleted !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }
}

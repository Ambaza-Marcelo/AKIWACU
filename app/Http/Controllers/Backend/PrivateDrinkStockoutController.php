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
use App\Models\PrivateDrinkStockout;
use App\Models\PrivateDrinkStockoutDetail;
use App\Exports\PrivateStore\PrivateDrinkStockoutExport;
use Carbon\Carbon;
use App\Mail\PrivateStockoutMail;
use PDF;
use Validator;
use Excel;
use Mail;

class PrivateDrinkStockoutController extends Controller
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
        if (is_null($this->user) || !$this->user->can('private_drink_stockout.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stockout !');
        }

        $stockouts = PrivateDrinkStockout::all();
        return view('backend.pages.private_drink_stockout.index', compact('stockouts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('private_drink_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $items  = PrivateStoreItem::orderBy('name','asc')->get();
        return view('backend.pages.private_drink_stockout.create', compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('private_drink_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $rules = array(
                'private_store_item_id.*'  => 'required',
                'date'  => 'required',
                'unit.*'  => 'required',
                'quantity.*'  => 'required',
                'asker'  => 'required',
                'destination'  => 'required',
                'item_movement_type'  => 'required',
                'description'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $private_store_item_id = $request->private_store_item_id;
            $date = $request->date;
            $invoice_currency = $request->invoice_currency;
            $asker = $request->asker;
            $destination = $request->destination;
            $description =$request->description; 
            $item_movement_type = $request->item_movement_type;
            $unit = $request->unit;
            $quantity = $request->quantity;
            

            $latest = PrivateDrinkStockout::latest()->first();
            if ($latest) {
               $stockout_no = 'BS' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $stockout_no = 'BS' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $stockout_signature = "4001711615".Carbon::parse(Carbon::now())->format('YmdHis')."/".$stockout_no;


            for( $count = 0; $count < count($private_store_item_id); $count++ ){

                $selling_price = PrivateStoreItem::where('id', $private_store_item_id[$count])->value('selling_price');
                $purchase_price = PrivateStoreItem::where('id', $private_store_item_id[$count])->value('purchase_price');
                $cump = PrivateStoreItem::where('id', $private_store_item_id[$count])->value('cump');

                $total_value = $quantity[$count] * $purchase_price;
                $total_purchase_value = $quantity[$count] * $cump;
                $total_selling_value = $quantity[$count] * $selling_price;

                $data = array(
                    'private_store_item_id' => $private_store_item_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'unit' => $unit[$count],
                    'purchase_price' => $purchase_price,
                    'price' => $cump,
                    'selling_price' => $selling_price,
                    'total_purchase_value' => $total_purchase_value,
                    'total_selling_value' => $total_selling_value,
                    'asker' => $asker,
                    'destination' => $destination,
                    'item_movement_type' => $item_movement_type,
                    'stockout_no' => $stockout_no,
                    'stockout_signature' => $stockout_signature,
                    'created_by' => $created_by,
                    'description' => $description,
                    'status' => 1,
                    'created_at' => \Carbon\Carbon::now()

                );
                $insert_data[] = $data;

                
            }
            PrivateDrinkStockoutDetail::insert($insert_data);


            //create stockout
            $stockout = new PrivateDrinkStockout();
            $stockout->date = $date;
            $stockout->stockout_no = $stockout_no;
            $stockout->stockout_signature = $stockout_signature;
            $stockout->asker = $asker;
            $stockout->destination = $destination;
            $stockout->item_movement_type = $item_movement_type;
            $stockout->created_by = $created_by;
            $stockout->status = 1;
            $stockout->description = $description;
            $stockout->save();
            
        session()->flash('success', 'stockout has been created !!');
        return redirect()->route('admin.private-drink-stockouts.index');
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
        $code = PrivateDrinkStockoutDetail::where('stockout_no', $stockout_no)->value('stockout_no');
        $stockouts = PrivateDrinkStockoutDetail::where('stockout_no', $stockout_no)->get();
        return view('backend.pages.private_drink_stockout.show', compact('stockouts','code'));
         
    }

    public function bonSortie($stockout_no)
    {
        if (is_null($this->user) || !$this->user->can('private_drink_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to print handover document!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $datas = PrivateDrinkStockoutDetail::where('stockout_no', $stockout_no)->get();
        $data = PrivateDrinkStockout::where('stockout_no', $stockout_no)->first();
        $description = PrivateDrinkStockout::where('stockout_no', $stockout_no)->value('description');
        $stockout_signature = PrivateDrinkStockout::where('stockout_no', $stockout_no)->value('stockout_signature');
        $date = PrivateDrinkStockout::where('stockout_no', $stockout_no)->value('date');
        $totalValue = DB::table('private_drink_stockout_details')
            ->where('stockout_no', '=', $stockout_no)
            ->sum('total_purchase_value');
        $pdf = PDF::loadView('backend.pages.document.private_drink_stockout',compact('datas','totalValue','data','description','stockout_no','setting','date','stockout_signature'));

        Storage::put('public/pdf/private_drink_stockout/'.$stockout_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('BON_SORTIE_'.$stockout_no.'.pdf');
        
    }

    public function validateStockout($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('private_drink_stockout.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any stockout !');
        }
            PrivateDrinkStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            PrivateDrinkStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        session()->flash('success', 'stockout has been validated !!');
        return back();
    }

    public function reject($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('private_drink_stockout.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any stockout !');
        }

        PrivateDrinkStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
        PrivateDrinkStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        session()->flash('success', 'Stockout has been rejected !!');
        return back();
    }

    public function reset($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('private_drink_stockout.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any stockout !');
        }

        PrivateDrinkStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
        PrivateDrinkStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        session()->flash('success', 'Stockout has been reseted !!');
        return back();
    }

    public function confirm($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('private_drink_stockout.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any stockout !');
        }

        PrivateDrinkStockout::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            PrivateDrinkStockoutDetail::where('stockout_no', '=', $stockout_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        session()->flash('success', 'Stockout has been confirmed !!');
        return back();
    }

    public function approuve($stockout_no)
    {
       if (is_null($this->user) || !$this->user->can('private_drink_stockout.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any stockout !');
        }


        $datas = PrivateDrinkStockoutDetail::where('stockout_no', $stockout_no)->get();

        $description = PrivateDrinkStockoutDetail::where('stockout_no', $stockout_no)->value('description');
        $totalQuantity = DB::table('private_drink_stockout_details')
            ->where('stockout_no', '=', $stockout_no)
            ->sum('quantity');
        $totalValue = DB::table('private_drink_stockout_details')
            ->where('stockout_no', '=', $stockout_no)
            ->sum('total_purchase_value');

        foreach($datas as $data){

                $valeurStockInitial = PrivateStoreItem::where('id', $data->private_store_item_id)->value('total_cump_value');
                $quantityStockInitial = PrivateStoreItem::where('id', $data->private_store_item_id)->value('quantity');
                $quantityRestante = $quantityStockInitial - $data->quantity;

                    $privateStore = array(
                        'id' => $data->private_store_item_id,
                        'quantity' => $quantityRestante,
                        'total_selling_value' => $quantityRestante * $data->price,
                        'total_purchase_value' => $quantityRestante * $data->price,
                        'total_cump_value' => $quantityRestante * $data->price,
                        'status' => true,
                        'created_at' => \Carbon\Carbon::now()
                    );


                    if ($data->quantity <= $quantityStockInitial) {
                        
                        PrivateStoreItem::where('id',$data->private_store_item_id)
                        ->update($privateStore);

                        $flag = 0;

                    }else{
                        foreach ($datas as $data) {
                            $valeurStockInitial = PrivateStoreItem::where('id', $data->private_store_item_id)->value('total_cump_value');
                            $quantityStockInitial = PrivateStoreItem::where('id', $data->private_store_item_id)->value('quantity');
                            $quantityRestante = $quantityStockInitial - $data->quantity;

                            $returnData = array(
                                'id' => $data->private_store_item_id,
                                'quantity' => $quantityRestante,
                                'total_selling_value' => $quantityRestante * $data->price,
                                'total_purchase_value' => $quantityRestante * $data->price,
                                'total_cump_value' => $quantityRestante * $data->price,
                                'status' => false,
                                'created_at' => \Carbon\Carbon::now()
                            );

                            $status = PrivateStoreItem::where('id', $data->private_store_item_id)->value('status');
                    
                            if ($status == true) {
                        
                                PrivateStoreItem::where('id', $data->private_store_item_id)
                                ->update($returnData);

                                $flag = 1;
                            }
                        }

                        PrivateStoreItem::where('id','!=','')->update(['status' => false]);
                        
                        session()->flash('error', $this->user->name.' ,Why do you want to stockout a quantity you do not have in your store?please rewrite a valid quantity!');
                        return redirect()->back();
                    }
                
  
        }

        PrivateStoreItem::where('id','!=','')->update(['status' => false]);

        PrivateDrinkStockout::where('stockout_no', '=', $stockout_no)
                            ->update(['status' => 4,'approuved_by' => $this->user->name]);
        PrivateDrinkStockoutDetail::where('stockout_no', '=', $stockout_no)
                            ->update(['status' => 4,'approuved_by' => $this->user->name]);

            $email1 = 'ambazamarcellin2001@gmail.com';
            $email2 = 'frangiye@gmail.com';
            //$email3 = 'khaembamartin@gmail.com';
            $email4 = 'munyembari_mp@yahoo.fr';
            $auteur = $this->user->name;
            $mailData = [
                    'title' => 'SORTIE DES ARTICLES AU STOCK PDG',
                    'stockout_no' => $stockout_no,
                    'auteur' => $auteur,
                    'description' => $description,
                    'totalQuantity' => $totalQuantity,
                    'totalValue' => $totalValue,
                    ];
         
            Mail::to($email1)->send(new PrivateStockoutMail($mailData));
            Mail::to($email2)->send(new PrivateStockoutMail($mailData));
            //Mail::to($email3)->send(new PrivateStockoutMail($mailData));
            Mail::to($email4)->send(new PrivateStockoutMail($mailData));

        session()->flash('success', 'Stockout has been done successfuly !');
        return back();


    }

    public function exportToExcel(Request $request)
    {
        return Excel::download(new PrivateDrinkStockoutExport, 'RAPPORT_SORTIES.xlsx');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $stockout_no
     * @return \Illuminate\Http\Response
     */
    public function destroy($stockout_no)
    {
        if (is_null($this->user) || !$this->user->can('private_drink_stockout.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any stockout !');
        }

        $stockout = PrivateDrinkStockout::where('stockout_no',$stockout_no)->first();
        if (!is_null($stockout)) {
            $stockout->delete();
            PrivateDrinkStockoutDetail::where('stockout_no',$stockout_no)->delete();
        }

        session()->flash('success', 'Stockout has been deleted !!');
        return back();
    }
}

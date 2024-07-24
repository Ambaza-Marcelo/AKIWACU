<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\BartenderItem;
use App\Models\BartenderProductionStore;
use App\Models\BartenderTransfer;
use App\Models\BartenderSmallReport;
use App\Models\Setting;
use Excel;
use Carbon\Carbon;
use Validator;
use PDF;

use Mail;
use App\Mail\DeleteStockMail;

class BartenderProductionStoreController extends Controller
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
        if (is_null($this->user) || !$this->user->can('bartender_production_store.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any barrist store !');
        }

        $stocks = BartenderProductionStore::all();
        return view('backend.pages.bartender_production_store.index', compact('stocks'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('bartender_production_store.view')) {
            abort(403, 'Sorry !! You are Unauthorized to create any item !');
        }
        $bartender_items = BartenderItem::orderBy('name','asc')->get();
        return view('backend.pages.bartender_production_store.create', compact(
            'bartender_items'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('bartender_production_store.view')) {
            abort(403, 'Sorry !! You are Unauthorized to create any item !');
        }

        $rules = array(
                'quantity.*'  => 'required',
                'unit.*'  => 'required',
                'bartender_item_id.*'  => 'required',
                'description'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $code = date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);

            $bartender_item_id = $request->bartender_item_id;
            $date = $request->date;
            $quantity = $request->quantity;
            $unit = $request->unit;
            $selling_price =$request->selling_price; 
            $description =$request->description;
            $food_transfer_no =$request->food_transfer_no;
            $drink_transfer_no =$request->drink_transfer_no;
            $created_by = $this->user->name;

            for( $count = 0; $count < count($bartender_item_id); $count++ ){

                $price = BartenderItem::where("id",$bartender_item_id[$count])->value('selling_price');
                $threshold_quantity = BartenderItem::where("id",$bartender_item_id[$count])->value('threshold_quantity');

                $item_code = BartenderItem::where("id",$bartender_item_id[$count])->value('code');
                $item_designation = BartenderItem::where("id",$bartender_item_id[$count])->value('name');

                $data = array(
                    'bartender_item_id' => $bartender_item_id[$count],
                    'quantity' => $quantity[$count],
                    'unit' => $unit[$count],
                    'selling_price' => $price,
                    'purchase_price' => $price,
                    'food_transfer_no' => $food_transfer_no,
                    'drink_transfer_no' => $drink_transfer_no,
                    'description' => $description,
                    'code' => $code,
                    'total_purchase_value' => $price * $quantity[$count],
                    'total_selling_value' => $price * $quantity[$count],
                    'created_by' => $created_by,
                );
                $insert_data[] = $data;


                $valeurStockInitial = BartenderProductionStore::where('bartender_item_id', $bartender_item_id[$count])->value('total_selling_value');
                $quantityStockInitial = BartenderProductionStore::where('bartender_item_id', $bartender_item_id[$count])->value('quantity');


                $valeurAcquisition = $quantity[$count] * $price;

                $valeurTotalUnite = $quantity[$count] + $quantityStockInitial;
                $cump = ($valeurStockInitial + $valeurAcquisition) / $valeurTotalUnite;

                $reportData = array(
                    'bartender_item_id' => $bartender_item_id[$count],
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'quantity_stockin' => $quantity[$count],
                    'value_stockin' => $price * $quantity[$count],
                    'stock_total' => $quantityStockInitial + $quantity[$count],
                    'quantity_stock_final' => $quantityStockInitial + $quantity[$count],
                    'value_stock_final' => $quantityStockInitial * $price,
                    'created_by' => $this->user->name,
                    'description' => $description,
                    'transfer_no' => $drink_transfer_no,
                    'stockin_no' => $food_transfer_no,
                    'created_at' => \Carbon\Carbon::now()
                );
                $report[] = $reportData;

                    $donnees = array(
                        'bartender_item_id' => $bartender_item_id[$count],
                        'quantity' => $valeurTotalUnite,
                        'selling_price' => $price,
                        'purchase_price' => $price,
                        'threshold_quantity' => $threshold_quantity,
                        'food_transfer_no' => $food_transfer_no,
                        'drink_transfer_no' => $drink_transfer_no,
                        'description' => $description,
                        'total_purchase_value' => $price * $quantity[$count],
                        'total_selling_value' => $price * $quantity[$count],
                        'unit' => $unit[$count],
                        'cump' => $cump,
                        'verified' => false,
                        'created_by' => $this->user->name,
                    );
                    $stock[] = $donnees;

                    $artic = BartenderProductionStore::where("bartender_item_id",$bartender_item_id[$count])->value('bartender_item_id');
                    if (!empty($artic)) {
                        BartenderSmallReport::insert($report);
                        BartenderProductionStore::where('bartender_item_id',$bartender_item_id[$count])
                        ->update($donnees);
                    }else{
                        BartenderSmallReport::insert($report);
                        BartenderProductionStore::insert($stock);
                    }
                    /*
                    $theUrl = config('app.guzzle_test_url').'/ebms_api/login/';
                        $response = Http::post($theUrl, [
                            'username'=> "wsconfig('app.tin_number_company')00565",
                            'password'=> "5VS(GO:p"

                        ]);
                        $data1 =  json_decode($response);
                        $data2 = ($data1->result);       
    
                        $token = $data2->token;

                        $theUrl = config('app.guzzle_test_url').'/ebms_api/AddStockMovement';  
                        $response = Http::withHeaders([
                        'Authorization' => 'Bearer '.$token,
                        'Accept' => 'application/json'])->post($theUrl, [
                            'system_or_device_id'=> "wsconfig('app.tin_number_company')00565",
                            'item_code'=> $item_code,
                            'item_designation'=>$item_designation,
                            'item_quantity'=>$quantity[$count],
                            'item_measurement_unit'=>$unit[$count],
                            'item_purchase_or_sale_price'=>$price,
                            'item_purchase_or_sale_currency'=> "BIF",
                            'item_movement_type'=>"EAU",
                            'item_movement_invoice_ref'=>"",
                            'item_movement_description'=>$description,
                            'item_movement_date'=> Carbon::parse(now())->format('Y-m-d H:i:s'),

                        ]); 
                        */
            }

            session()->flash('success', 'Data has been Saved Successfuly !!');
            return redirect()->route('admin.bartender-production-store.index');
    
    }

    public function toPdf()
    {
        if (is_null($this->user) || !$this->user->can('bartender_production_store.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any barrist store !');
        }

        $datas = BartenderProductionStore::all();
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();
        $totalValue = DB::table('stocks')->sum('total_value');

        $dateT =  $currentTime->toDateTimeString();

        $totalValue = DB::table('stocks')->sum('total_value');

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.stock_status',compact('datas','dateTime','setting','totalValue'))->setPaper('a4', 'landscape');

        Storage::put('public/pdf/Bartender_rapport/'.'rapport_bartender_'.$dateTime.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('rapport_bartender_'.$dateTime.'.pdf');
    }

    public function rapport(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('drink.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stock !');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        $datas = BartenderProductionStore::select(
                        DB::raw('bartender_item_id,quantity,selling_price,total_selling_value'))->whereBetween('updated_at',[$start_date,$end_date])->groupBy('bartender_item_id','quantity','selling_price','total_selling_value')->get();
        $total_amount = DB::table('bartender_production_stores')->whereBetween('updated_at',[$start_date,$end_date])->sum('total_selling_value');


        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.rapport_bartender',compact('datas','dateTime','setting','end_date','start_date','total_amount'))->setPaper('a4', 'landscape');

        // download pdf file
        return $pdf->download("rapport_bartender_".$dateTime.'.pdf');

        
    }

    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('bartender_production_store.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any drink_small_store !');
        }

        $stock = BartenderProductionStore::find($id);
        if (!is_null($stock)) {
            $stock->delete();
            /*
            $email = 'ambazamarcellin2001@gmail.com';
            $auteur = $this->user->name;
            $mailData = [
                    'title' => 'suppression du barrist store fournitures',
                    'email' => $email,
                    'auteur' => $auteur,
                    ];
         
            Mail::to($email)->send(new DeleteStockMail($mailData));
            */
        }

        session()->flash('success', 'barrist store has been deleted !!');
        return back();
    }
}

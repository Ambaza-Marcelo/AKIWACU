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
use Carbon\Carbon;
use PDF;
use Excel;
use Mail;
use Validator;
use GuzzleHttp\Client;
use App\Models\Facture;
use App\Models\FactureDetail;
use App\Models\FactureRestaurant;
use App\Models\FactureRestaurantDetail;
use App\Models\Setting;
use App\Models\OrderDrink;
use App\Models\OrderDrinkDetail;
use App\Models\OrderKitchenDetail;
use App\Models\OrderKitchen;
use App\Models\BarristOrder;
use App\Models\BarristOrderDetail;
use App\Models\BartenderOrder;
use App\Models\BartenderOrderDetail;
use App\Models\Drink;
use App\Models\BarristItem;
use App\Models\FoodItem;
use App\Models\FoodItemDetail;
use App\Models\BartenderItem;
use App\Models\Employe;
use App\Models\DrinkBigStoreDetail;
use App\Models\DrinkSmallStore;
use App\Models\DrinkSmallStoreDetail;
use App\Models\DrinkSmallReport;
use App\Models\BarristProductionStore;
use App\Models\BartenderProductionStore;
use App\Models\BarristSmallReport;
use App\Models\BartenderSmallReport;
use App\Models\FoodStore;
use App\Models\FoodBigStoreDetail;
use App\Models\FoodSmallStoreDetail;
use App\Models\FoodStoreReport;
use App\Models\FoodBigReport;
use App\Models\FoodSmallReport;
use App\Models\BookingBooking;
use App\Models\BookingBookingDetail;
use App\Models\BookingSalle;
use App\Models\BookingService;
use App\Models\BookingEGRClient;
use App\Models\EGRClient;
use App\Models\Table;
use App\Models\BookingTable;
use App\Models\KidnessSpace;
use App\Models\NoteCredit;
use App\Models\NoteCreditDetail;
use App\Models\BreakFast;
use App\Models\SwimingPool;
use App\Mail\DeleteFactureMail;
use App\Mail\InvoiceResetedMail;
use App\Mail\ReportDrinkMail;
use charlieuki\ReceiptPrinter\ReceiptPrinter as ReceiptPrinter;


class FactureController extends Controller
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

    public function index()
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factures = Facture::where('drink_order_no','!=','')->take(300)->orderBy('id','desc')->get();
        return view('backend.pages.invoice.index',compact('factures'));
    }

    public function listAll()
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factures = Facture::take(500)->orderBy('invoice_number','desc')->get();
        return view('backend.pages.invoice_all.index',compact('factures'));
    }


    public function create($order_no)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $drinks =  Drink::orderBy('name','asc')->get();
        $orders =  OrderDrinkDetail::where('order_no',$order_no)->orderBy('id','desc')->get();
        $drink_small_stores = DrinkSmallStore::all();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();

        $table_id = OrderDrink::where('order_no',$order_no)->value('table_id');

        $data =  OrderDrinkDetail::where('order_no',$order_no)->orderBy('id','desc')->first();
        $total_amount = DB::table('order_drink_details')
            ->where('order_no',$order_no)
            ->sum('total_amount_selling');

        return view('backend.pages.invoice.create',compact('drinks','data','setting','orders','order_no','drink_small_stores','clients','table_id','total_amount'));
    }

    public function createByTable($table_id)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $drinks =  Drink::orderBy('name','asc')->get();
        $orders =  OrderDrinkDetail::where('table_id',$table_id)->where('status',1)->orderBy('id','desc')->get();
        $drink_small_stores = DrinkSmallStore::all();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();

        $data =  OrderDrinkDetail::where('table_id',$table_id)->where('status',1)->orderBy('id','desc')->first();
        $total_amount = DB::table('order_drink_details')
            ->where('table_id',$table_id)->where('status',1)
            ->sum('total_amount_selling');

        return view('backend.pages.invoice.create',compact('drinks','data','setting','orders','table_id','drink_small_stores','clients','total_amount'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeDrink(Request  $request)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $rules = array(
                //'invoice_date' => 'required',
                //'invoice_number' => 'required',
                'tp_type' => 'required',
                'tp_name' => 'required|max:100|min:3',
                'tp_TIN' => 'required|max:30|min:4',
                'tp_trade_number' => 'required|max:20|min:4',
                'tp_phone_number' => 'required|max:20|min:6',
                'tp_address_commune' => 'required|max:50|min:5',
                'tp_address_quartier' => 'required|max:50|min:5',
                //'client_id' => 'required|max:100|min:3',
                //'customer_TIN' => 'required|max:30|min:4',
                //'customer_address' => 'required|max:100|min:5',
                //'invoice_signature' => 'required|max:90|min:10',
                //'invoice_signature_date' => 'required|max: |min:',
                'code_store' => 'required',
                'drink_id.*'  => 'required',
                'drink_order_no.*'  => 'required',
                'item_quantity.*'  => 'required',
                'item_price.*'  => 'required',
                'item_ct.*'  => 'required',
                'item_tl.*'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            try {DB::beginTransaction();

            $drink_id = $request->drink_id;
            $item_quantity = $request->item_quantity;
            $item_price = $request->item_price;
            $item_ct = $request->item_ct;
            $item_tl =$request->item_tl; 
            $employe_id = $request->employe_id;

            
            if (!empty($request->table_id)) {
                $table_id = $request->table_id;
                $drink_order_no = $request->drink_order_no;
            }else{
                $table_id = 1;
                $drink_order_no = $request->drink_order_no;
            }
            
            $latest = Facture::orderBy('id','desc')->first();
            if ($latest) {
               $invoice_number = 'FN' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $invoice_number = 'FN' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }
            

            $invoice_date = Carbon::now();
            

            $invoice_signature = $request->tp_TIN."/".config('app.obr_test_username')."/".Carbon::parse($invoice_date)->format('YmdHis')."/".$invoice_number;

        for( $count = 0; $count < count($drink_id); $count++ )
        {
            $taux_tva = Drink::where('id', $drink_id[$count])->value('vat');
            $brarudi_price = Drink::where('id', $drink_id[$count])->value('brarudi_price');
            $cump = DrinkBigStoreDetail::where('drink_id', $drink_id[$count])->value('cump');

            if($request->vat_taxpayer == 1){

                if (!empty($brarudi_price) || $brarudi_price != 0) {
                    $d_prix_tva = $item_price[$count] - $brarudi_price;
                    if ($d_prix_tva <= 0) {
                        $item_total_amount = ($item_price[$count]*$item_quantity[$count]);
                        $vat = 0;
                        $item_price_nvat = ($item_total_amount - $vat);
                        $item_price_wvat = ($item_price_nvat + $vat);
                    }else{
                        $item_total_amount = ($item_price[$count]*$item_quantity[$count]);
                        $item_total_amount_brarudi = ($d_prix_tva*$item_quantity[$count]);
                        $item_price_nvat2 = ($item_total_amount_brarudi * 100)/110;
                        $vat = ($item_price_nvat2 * $taux_tva)/100;
                        $item_price_nvat = ($item_total_amount - $vat);
                        $item_price_wvat = ($item_price_nvat + $vat);
                    } 
                }else{

                    $item_total_amount = ($item_price[$count]*$item_quantity[$count]);
                    
                    $item_price_nvat = ($item_total_amount* 100)/110;
                    $vat = ($item_price_nvat * $taux_tva)/100;
                    $item_price_wvat = ($item_price_nvat + $vat); 
                }

            }else{
                $item_price_nvat = ($item_price[$count]*$item_quantity[$count])+$item_ct[$count];
                $vat = 0;
                $item_price_wvat = ($item_price_nvat + $vat);
                $item_total_amount = $item_price_wvat + $item_tl[$count];
            }

          $data = array(
            'invoice_number'=>$invoice_number,
            'invoice_date'=> $invoice_date,
            'tp_type'=>$request->tp_type,
            'table_id'=>$table_id,
            'tp_name'=>$request->tp_name,
            'tp_TIN'=>$request->tp_TIN,
            'tp_trade_number'=>$request->tp_trade_number,
            'tp_phone_number'=>$request->tp_phone_number,
            'tp_address_province'=>$request->tp_address_province,
            'tp_address_commune'=>$request->tp_address_commune,
            'tp_address_quartier'=>$request->tp_address_quartier,
            'tp_address_avenue'=>$request->tp_address_avenue,
            'tp_address_rue'=>$request->tp_address_rue,
            'vat_taxpayer'=>$request->vat_taxpayer,
            'ct_taxpayer'=>$request->ct_taxpayer,
            'tl_taxpayer'=>$request->tl_taxpayer,
            'tp_fiscal_center'=>$request->tp_fiscal_center,
            'tp_activity_sector'=>$request->tp_activity_sector,
            'tp_legal_form'=>$request->tp_legal_form,
            'payment_type'=>$request->payment_type,
            'client_id'=>$request->client_id,
            'customer_TIN'=>$request->customer_TIN,
            'customer_address'=>$request->customer_address,
            'invoice_signature'=> $invoice_signature,
            'drink_order_no'=>$request->drink_order_no[$count],
            'cancelled_invoice_ref'=>$request->cancelled_invoice_ref,
            'cancelled_invoice'=>$request->cancelled_invoice,
            'invoice_currency'=>$request->invoice_currency,
            'invoice_ref'=>$request->invoice_ref,
            'code_store'=>$request->code_store,
            'auteur' => $this->user->name,
            'invoice_signature_date'=> Carbon::now(),
            'drink_id'=>$drink_id[$count],
            'item_quantity'=>$item_quantity[$count],
            'item_price'=>$item_price[$count],
            'item_ct'=>$item_ct[$count],
            'item_tl'=>$item_tl[$count],
            'item_price_nvat'=>$item_price_nvat,
            'vat'=>$vat,
            'item_price_wvat'=>$item_price_wvat,
            'item_total_amount'=>$item_total_amount,
            'cump'=>$cump,
            'employe_id'=> $employe_id,
            'created_at'=> Carbon::now(),
        );
          $data1[] = $data;
      }


        FactureDetail::insert($data1);

            $order_no = FactureDetail::where('invoice_number',$invoice_number)->value('drink_order_no');

            //create facture
            $facture = new Facture();
            $facture->invoice_date = $invoice_date;
            $facture->invoice_number = $invoice_number;
            $facture->table_id = $table_id;
            $facture->drink_order_no = $order_no;
            $facture->tp_type = $request->tp_type;
            $facture->tp_name = $request->tp_name;
            $facture->tp_TIN = $request->tp_TIN;
            $facture->tp_trade_number = $request->tp_trade_number;
            $facture->tp_phone_number = $request->tp_phone_number;
            $facture->tp_address_province = $request->tp_address_province;
            $facture->tp_address_commune = $request->tp_address_commune;
            $facture->tp_address_quartier = $request->tp_address_quartier;
            $facture->vat_taxpayer = $request->vat_taxpayer;
            $facture->ct_taxpayer = $request->ct_taxpayer;
            $facture->tl_taxpayer = $request->tl_taxpayer;
            $facture->tp_fiscal_center = $request->tp_fiscal_center;
            $facture->tp_activity_sector = $request->tp_activity_sector;
            $facture->tp_legal_form = $request->tp_legal_form;
            $facture->invoice_currency = $request->invoice_currency;
            $facture->payment_type = $request->payment_type;
            $facture->client_id = $request->client_id;
            $facture->customer_TIN = $request->customer_TIN;
            $facture->customer_address = $request->customer_address;
            $facture->invoice_signature = $invoice_signature;
            $facture->cancelled_invoice_ref = $request->cancelled_invoice_ref;
            $facture->cancelled_invoice = $request->cancelled_invoice;
            $facture->invoice_ref = $request->invoice_ref;
            $facture->code_store = $request->code_store;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->created_at = Carbon::now();
            $facture->save();


            for( $count = 0; $count < count($drink_id); $count++ )
            {
                 $orderData = array(
                    'confirmed_by' => $this->user->name,
                    'status' => 2,
                    'flag' => 1
                );

                OrderDrink::where('order_no', '=', $request->drink_order_no[$count])
                    ->update($orderData);
                OrderDrinkDetail::where('order_no', '=', $request->drink_order_no[$count])
                    ->update($orderData);
            }


            DB::commit();
            session()->flash('success', 'Le vente est fait avec succés!!');
            return redirect()->route('ebms_api.invoices.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function storeBarrist(Request  $request)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $rules = array(
                //'invoice_date' => 'required',
                //'invoice_number' => 'required',
                'tp_type' => 'required',
                'tp_name' => 'required|max:100|min:3',
                'tp_TIN' => 'required|max:30|min:4',
                'tp_trade_number' => 'required|max:20|min:4',
                'tp_phone_number' => 'required|max:20|min:6',
                'tp_address_commune' => 'required|max:50|min:5',
                'tp_address_quartier' => 'required|max:50|min:5',
                //'client_id' => 'required|max:100|min:3',
                //'customer_TIN' => 'required|max:30|min:4',
                //'customer_address' => 'required|max:100|min:5',
                //'invoice_signature' => 'required|max:90|min:10',
                //'invoice_signature_date' => 'required|max: |min:',
                'barrist_order_no.*'  => 'required',
                'barrist_item_id.*'  => 'required',
                'item_quantity.*'  => 'required',
                'item_price.*'  => 'required',
                'item_ct.*'  => 'required',
                'item_tl.*'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            try {DB::beginTransaction();

            $barrist_item_id = $request->barrist_item_id;
            $item_quantity = $request->item_quantity;
            $item_price = $request->item_price;
            $item_ct = $request->item_ct;
            $item_tl =$request->item_tl; 

            $employe_id = $request->employe_id;
            
            if (!empty($request->table_id)) {
                $table_id = $request->table_id;
                $barrist_order_no = $request->barrist_order_no;
            }else{
                $table_id = 1;
                $barrist_order_no = $request->barrist_order_no;
            }

            $latest = Facture::orderBy('id','desc')->first();
            if ($latest) {
               $invoice_number = 'FN' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $invoice_number = 'FN' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }
            


            $invoice_date = Carbon::now();

            $invoice_signature = $request->tp_TIN."/".config('app.obr_test_username')."/".Carbon::parse($invoice_date)->format('YmdHis')."/".$invoice_number;

        for( $count = 0; $count < count($barrist_item_id); $count++ )
        {
            $taux_tva = BarristItem::where('id', $barrist_item_id[$count])->value('vat');

            if($request->vat_taxpayer == 1){
                $item_total_amount = ($item_price[$count]*$item_quantity[$count]);
                
                $item_price_nvat = ($item_total_amount * 100)/110 ;
                $vat = ($item_price_nvat * $taux_tva)/100;
                $item_price_wvat = ($item_price_nvat + $vat);

            }else{
                $item_price_nvat = ($item_price[$count]*$item_quantity[$count])+$item_ct[$count];
                $vat = 0;
                $item_price_wvat = ($item_price_nvat + $vat);
                $item_total_amount = $item_price_wvat + $item_tl[$count];
            }

          $data = array(
            'invoice_number'=>$invoice_number,
            'invoice_date'=> $invoice_date,
            'table_id'=> $table_id,
            'tp_type'=>$request->tp_type,
            'tp_name'=>$request->tp_name,
            'tp_TIN'=>$request->tp_TIN,
            'tp_trade_number'=>$request->tp_trade_number,
            'tp_phone_number'=>$request->tp_phone_number,
            'tp_address_province'=>$request->tp_address_province,
            'tp_address_commune'=>$request->tp_address_commune,
            'tp_address_quartier'=>$request->tp_address_quartier,
            'tp_address_avenue'=>$request->tp_address_avenue,
            'tp_address_rue'=>$request->tp_address_rue,
            'vat_taxpayer'=>$request->vat_taxpayer,
            'ct_taxpayer'=>$request->ct_taxpayer,
            'tl_taxpayer'=>$request->tl_taxpayer,
            'tp_fiscal_center'=>$request->tp_fiscal_center,
            'tp_activity_sector'=>$request->tp_activity_sector,
            'tp_legal_form'=>$request->tp_legal_form,
            'payment_type'=>$request->payment_type,
            'client_id'=>$request->client_id,
            'customer_TIN'=>$request->customer_TIN,
            'customer_address'=>$request->customer_address,
            'invoice_signature'=> $invoice_signature,
            'barrist_order_no'=>$request->barrist_order_no[$count],
            'cancelled_invoice_ref'=>$request->cancelled_invoice_ref,
            'cancelled_invoice'=>$request->cancelled_invoice,
            'invoice_currency'=>$request->invoice_currency,
            'invoice_ref'=>$request->invoice_ref,
            'auteur' => $this->user->name,
            'invoice_signature_date'=> Carbon::now(),
            'barrist_item_id'=>$barrist_item_id[$count],
            'item_quantity'=>$item_quantity[$count],
            'item_price'=>$item_price[$count],
            'item_ct'=>$item_ct[$count],
            'item_tl'=>$item_tl[$count],
            'item_price_nvat'=>$item_price_nvat,
            'vat'=>$vat,
            'item_price_wvat'=>$item_price_wvat,
            'item_total_amount'=>$item_total_amount,
            'employe_id'=> $employe_id,
            'created_at'=> Carbon::now(),
        );
          $data1[] = $data;
      }


        FactureDetail::insert($data1);

        $order_no = FactureDetail::where('invoice_number',$invoice_number)->value('barrist_order_no');

            //create facture
            $facture = new Facture();
            $facture->invoice_date = $invoice_date;
            $facture->invoice_number = $invoice_number;
            $facture->tp_type = $request->tp_type;
            $facture->tp_name = $request->tp_name;
            $facture->tp_TIN = $request->tp_TIN;
            $facture->tp_trade_number = $request->tp_trade_number;
            $facture->tp_phone_number = $request->tp_phone_number;
            $facture->tp_address_province = $request->tp_address_province;
            $facture->tp_address_commune = $request->tp_address_commune;
            $facture->tp_address_quartier = $request->tp_address_quartier;
            $facture->barrist_order_no = $order_no;
            $facture->vat_taxpayer = $request->vat_taxpayer;
            $facture->ct_taxpayer = $request->ct_taxpayer;
            $facture->tl_taxpayer = $request->tl_taxpayer;
            $facture->tp_fiscal_center = $request->tp_fiscal_center;
            $facture->tp_activity_sector = $request->tp_activity_sector;
            $facture->tp_legal_form = $request->tp_legal_form;
            $facture->invoice_currency = $request->invoice_currency;
            $facture->payment_type = $request->payment_type;
            $facture->client_id = $request->client_id;
            $facture->customer_TIN = $request->customer_TIN;
            $facture->customer_address = $request->customer_address;
            $facture->invoice_signature = $invoice_signature;
            $facture->cancelled_invoice_ref = $request->cancelled_invoice_ref;
            $facture->cancelled_invoice = $request->cancelled_invoice;
            $facture->invoice_ref = $request->invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->created_at = Carbon::now();
            $facture->save();

            for( $count = 0; $count < count($barrist_item_id); $count++ )
            {
                 $orderData = array(
                    'confirmed_by' => $this->user->name,
                    'status' => 2,
                    'flag' => 1
                );

                BarristOrder::where('order_no', '=', $request->barrist_order_no[$count])
                    ->update($orderData);
                BarristOrderDetail::where('order_no', '=', $request->barrist_order_no[$count])
                    ->update($orderData);
            }

        DB::commit();
            session()->flash('success', 'Le vente est fait avec succés!!');
            return redirect()->route('admin.barrist-invoices.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function storeFood(Request  $request)
    {
        if (is_null($this->user) || !$this->user->can('invoice_kitchen.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $rules = array(
                //'invoice_date' => 'required',
                //'invoice_number' => 'required',
                'tp_type' => 'required',
                'tp_name' => 'required|max:100|min:3',
                'tp_TIN' => 'required|max:30|min:4',
                'tp_trade_number' => 'required|max:20|min:4',
                'tp_phone_number' => 'required|max:20|min:6',
                'tp_address_commune' => 'required|max:50|min:5',
                'tp_address_quartier' => 'required|max:50|min:5',
                //'client_id' => 'required|max:100|min:3',
                //'customer_TIN' => 'required|max:30|min:4',
                //'customer_address' => 'required|max:100|min:5',
                //'invoice_signature' => 'required|max:90|min:10',
                //'invoice_signature_date' => 'required|max: |min:',
                'food_order_no.*'  => 'required',
                'food_item_id.*'  => 'required',
                'item_quantity.*'  => 'required',
                'item_price.*'  => 'required',
                'item_ct.*'  => 'required',
                'item_tl.*'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            try {DB::beginTransaction();

            $food_item_id = $request->food_item_id;
            $item_quantity = $request->item_quantity;
            $item_price = $request->item_price;
            $item_ct = $request->item_ct;
            $item_tl =$request->item_tl; 

            $employe_id = $request->employe_id;

            if (!empty($request->table_id)) {
                $table_id = $request->table_id;
                $food_order_no = $request->food_order_no;
            }else{
                $table_id = 1;
                $food_order_no = $request->food_order_no;
            }
            
            $latest = Facture::orderBy('id','desc')->first();
            if ($latest) {
               $invoice_number = 'FN' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $invoice_number = 'FN' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }
            


            $invoice_date = Carbon::now();

            $invoice_signature = $request->tp_TIN."/".config('app.obr_test_username')."/".Carbon::parse($invoice_date)->format('YmdHis')."/".$invoice_number;

        for( $count = 0; $count < count($food_item_id); $count++ )
        {
            $taux_tva = FoodItem::where('id', $food_item_id[$count])->value('vat');

            if($request->vat_taxpayer == 1){
                $item_total_amount = ($item_price[$count]*$item_quantity[$count]);
                
                $item_price_nvat = ($item_total_amount * 100)/110;
                $vat = ($item_price_nvat * $taux_tva)/100;
                $item_price_wvat = ($item_price_nvat + $vat);

            }else{
                $item_price_nvat = ($item_price[$count]*$item_quantity[$count])+$item_ct[$count];
                $vat = 0;
                $item_price_wvat = ($item_price_nvat + $vat);
                $item_total_amount = $item_price_wvat + $item_tl[$count];
            }

          $data = array(
            'invoice_number'=>$invoice_number,
            'invoice_date'=> $invoice_date,
            'table_id'=>$table_id,
            'tp_type'=>$request->tp_type,
            'tp_name'=>$request->tp_name,
            'tp_TIN'=>$request->tp_TIN,
            'tp_trade_number'=>$request->tp_trade_number,
            'tp_phone_number'=>$request->tp_phone_number,
            'tp_address_province'=>$request->tp_address_province,
            'tp_address_commune'=>$request->tp_address_commune,
            'tp_address_quartier'=>$request->tp_address_quartier,
            'tp_address_avenue'=>$request->tp_address_avenue,
            'tp_address_rue'=>$request->tp_address_rue,
            'vat_taxpayer'=>$request->vat_taxpayer,
            'ct_taxpayer'=>$request->ct_taxpayer,
            'tl_taxpayer'=>$request->tl_taxpayer,
            'tp_fiscal_center'=>$request->tp_fiscal_center,
            'tp_activity_sector'=>$request->tp_activity_sector,
            'tp_legal_form'=>$request->tp_legal_form,
            'payment_type'=>$request->payment_type,
            'client_id'=>$request->client_id,
            'customer_TIN'=>$request->customer_TIN,
            'customer_address'=>$request->customer_address,
            'invoice_signature'=> $invoice_signature,
            'food_order_no'=>$request->food_order_no[$count],
            'cancelled_invoice_ref'=>$request->cancelled_invoice_ref,
            'cancelled_invoice'=>$request->cancelled_invoice,
            'invoice_currency'=>$request->invoice_currency,
            'auteur' => $this->user->name,
            'invoice_ref'=>$request->invoice_ref,
            'invoice_signature_date'=> Carbon::now(),
            'food_item_id'=>$food_item_id[$count],
            'item_quantity'=>$item_quantity[$count],
            'item_price'=>$item_price[$count],
            'item_ct'=>$item_ct[$count],
            'item_tl'=>$item_tl[$count],
            'item_price_nvat'=>$item_price_nvat,
            'vat'=>$vat,
            'item_price_wvat'=>$item_price_wvat,
            'item_total_amount'=>$item_total_amount,
            'employe_id'=> $employe_id,
            'created_at'=> Carbon::now(),
        );
          $data1[] = $data;
      }


        FactureDetail::insert($data1);

        $order_no = FactureDetail::where('invoice_number',$invoice_number)->value('food_order_no');
            //create facture
            $facture = new Facture();
            $facture->invoice_date = $invoice_date;
            $facture->invoice_number = $invoice_number;
            $facture->tp_type = $request->tp_type;
            $facture->tp_name = $request->tp_name;
            $facture->tp_TIN = $request->tp_TIN;
            $facture->tp_trade_number = $request->tp_trade_number;
            $facture->tp_phone_number = $request->tp_phone_number;
            $facture->tp_address_province = $request->tp_address_province;
            $facture->tp_address_commune = $request->tp_address_commune;
            $facture->tp_address_quartier = $request->tp_address_quartier;
            $facture->food_order_no = $order_no;
            $facture->vat_taxpayer = $request->vat_taxpayer;
            $facture->ct_taxpayer = $request->ct_taxpayer;
            $facture->tl_taxpayer = $request->tl_taxpayer;
            $facture->tp_fiscal_center = $request->tp_fiscal_center;
            $facture->tp_activity_sector = $request->tp_activity_sector;
            $facture->tp_legal_form = $request->tp_legal_form;
            $facture->invoice_currency = $request->invoice_currency;
            $facture->payment_type = $request->payment_type;
            $facture->client_id = $request->client_id;
            $facture->customer_TIN = $request->customer_TIN;
            $facture->customer_address = $request->customer_address;
            $facture->invoice_signature = $invoice_signature;
            $facture->cancelled_invoice_ref = $request->cancelled_invoice_ref;
            $facture->cancelled_invoice = $request->cancelled_invoice;
            $facture->invoice_ref = $request->invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->created_at = Carbon::now();
            $facture->save();

            for( $count = 0; $count < count($food_item_id); $count++ )
            {
                 $orderData = array(
                    'confirmed_by' => $this->user->name,
                    'status' => 2,
                    'flag' => 1
                );

                OrderKitchen::where('order_no', '=', $request->food_order_no[$count])
                    ->update($orderData);
                OrderKitchenDetail::where('order_no', '=', $request->food_order_no[$count])
                    ->update($orderData);
            }

            DB::commit();
            session()->flash('success', 'Le vente est fait avec succés!!');
            return redirect()->route('admin.invoice-kitchens.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function storeBartender(Request  $request)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $rules = array(
                //'invoice_date' => 'required',
                //'invoice_number' => 'required',
                'tp_type' => 'required',
                'tp_name' => 'required|max:100|min:3',
                'tp_TIN' => 'required|max:30|min:4',
                'tp_trade_number' => 'required|max:20|min:4',
                'tp_phone_number' => 'required|max:20|min:6',
                'tp_address_commune' => 'required|max:50|min:5',
                'tp_address_quartier' => 'required|max:50|min:5',
                //'client_id' => 'required|max:100|min:3',
                //'customer_TIN' => 'required|max:30|min:4',
                //'customer_address' => 'required|max:100|min:5',
                //'invoice_signature' => 'required|max:90|min:10',
                //'invoice_signature_date' => 'required|max: |min:',
                'bartender_order_no.*'  => 'required',
                'bartender_item_id.*'  => 'required',
                'item_quantity.*'  => 'required',
                'item_price.*'  => 'required',
                'item_ct.*'  => 'required',
                'item_tl.*'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            try {DB::beginTransaction();

            $bartender_item_id = $request->bartender_item_id;
            $item_quantity = $request->item_quantity;
            $item_price = $request->item_price;
            $item_ct = $request->item_ct;
            $item_tl =$request->item_tl; 

            $employe_id = $request->employe_id;
            
            if (!empty($request->table_id)) {
                $table_id = $request->table_id;
                $bartender_order_no = $request->bartender_order_no;
            }else{
                $table_id = '';
                $bartender_order_no = $request->bartender_order_no;
            }

            $latest = Facture::orderBy('id','desc')->first();
            if ($latest) {
               $invoice_number = 'FN' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $invoice_number = 'FN' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }
            

            $invoice_date = Carbon::now();
            

            $invoice_signature = $request->tp_TIN."/".config('app.obr_test_username')."/".Carbon::parse($invoice_date)->format('YmdHis')."/".$invoice_number;

        for( $count = 0; $count < count($bartender_item_id); $count++ )
        {
            $taux_tva = BartenderItem::where('id', $bartender_item_id[$count])->value('vat');

            if($request->vat_taxpayer == 1){
                $item_total_amount = ($item_price[$count]*$item_quantity[$count]);
                
                $item_price_nvat = ($item_total_amount * 100)/110;
                $vat = ($item_price_nvat * $taux_tva)/100;
                $item_price_wvat = ($item_price_nvat + $vat);

            }else{
                $item_price_nvat = ($item_price[$count]*$item_quantity[$count])+$item_ct[$count];
                $vat = 0;
                $item_price_wvat = ($item_price_nvat + $vat);
                $item_total_amount = $item_price_wvat + $item_tl[$count];
            }

          $data = array(
            'invoice_number'=>$invoice_number,
            'invoice_date'=> $invoice_date,
            'table_id'=>$table_id,
            'tp_type'=>$request->tp_type,
            'tp_name'=>$request->tp_name,
            'tp_TIN'=>$request->tp_TIN,
            'tp_trade_number'=>$request->tp_trade_number,
            'tp_phone_number'=>$request->tp_phone_number,
            'tp_address_province'=>$request->tp_address_province,
            'tp_address_commune'=>$request->tp_address_commune,
            'tp_address_quartier'=>$request->tp_address_quartier,
            'tp_address_avenue'=>$request->tp_address_avenue,
            'tp_address_rue'=>$request->tp_address_rue,
            'vat_taxpayer'=>$request->vat_taxpayer,
            'ct_taxpayer'=>$request->ct_taxpayer,
            'tl_taxpayer'=>$request->tl_taxpayer,
            'tp_fiscal_center'=>$request->tp_fiscal_center,
            'tp_activity_sector'=>$request->tp_activity_sector,
            'tp_legal_form'=>$request->tp_legal_form,
            'payment_type'=>$request->payment_type,
            'client_id'=>$request->client_id,
            'customer_TIN'=>$request->customer_TIN,
            'customer_address'=>$request->customer_address,
            'invoice_signature'=> $invoice_signature,
            'bartender_order_no'=>$request->bartender_order_no[$count],
            'cancelled_invoice_ref'=>$request->cancelled_invoice_ref,
            'cancelled_invoice'=>$request->cancelled_invoice,
            'invoice_currency'=>$request->invoice_currency,
            'invoice_ref'=>$request->invoice_ref,
            'auteur' => $this->user->name,
            'invoice_signature_date'=> Carbon::now(),
            'bartender_item_id'=>$bartender_item_id[$count],
            'item_quantity'=>$item_quantity[$count],
            'item_price'=>$item_price[$count],
            'item_ct'=>$item_ct[$count],
            'item_tl'=>$item_tl[$count],
            'item_price_nvat'=>$item_price_nvat,
            'vat'=>$vat,
            'item_price_wvat'=>$item_price_wvat,
            'item_total_amount'=>$item_total_amount,
            'employe_id'=> $employe_id,
            'created_at'=> Carbon::now(),
        );
          $data1[] = $data;
      }


        FactureDetail::insert($data1);

        $order_no = FactureDetail::where('invoice_number',$invoice_number)->value('bartender_order_no');
            //create facture
            $facture = new Facture();
            $facture->invoice_date = $invoice_date;
            $facture->invoice_number = $invoice_number;
            $facture->tp_type = $request->tp_type;
            $facture->tp_name = $request->tp_name;
            $facture->tp_TIN = $request->tp_TIN;
            $facture->tp_trade_number = $request->tp_trade_number;
            $facture->tp_phone_number = $request->tp_phone_number;
            $facture->tp_address_province = $request->tp_address_province;
            $facture->tp_address_commune = $request->tp_address_commune;
            $facture->tp_address_quartier = $request->tp_address_quartier;
            $facture->bartender_order_no = $order_no;
            $facture->vat_taxpayer = $request->vat_taxpayer;
            $facture->ct_taxpayer = $request->ct_taxpayer;
            $facture->tl_taxpayer = $request->tl_taxpayer;
            $facture->tp_fiscal_center = $request->tp_fiscal_center;
            $facture->tp_activity_sector = $request->tp_activity_sector;
            $facture->tp_legal_form = $request->tp_legal_form;
            $facture->invoice_currency = $request->invoice_currency;
            $facture->payment_type = $request->payment_type;
            $facture->client_id = $request->client_id;
            $facture->customer_TIN = $request->customer_TIN;
            $facture->customer_address = $request->customer_address;
            $facture->invoice_signature = $invoice_signature;
            $facture->cancelled_invoice_ref = $request->cancelled_invoice_ref;
            $facture->cancelled_invoice = $request->cancelled_invoice;
            $facture->invoice_ref = $request->invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->created_at = Carbon::now();
            $facture->save();

            for( $count = 0; $count < count($bartender_item_id); $count++ )
            {
                 $orderData = array(
                    'confirmed_by' => $this->user->name,
                    'status' => 2,
                    'flag' => 1
                );

                BartenderOrder::where('order_no', '=', $request->bartender_order_no[$count])
                    ->update($orderData);
                BartenderOrderDetail::where('order_no', '=', $request->bartender_order_no[$count])
                    ->update($orderData);
            }

            DB::commit();
            session()->flash('success', 'Le vente est fait avec succés!!');
            return redirect()->route('admin.bartender-invoices.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function storeBooking(Request  $request)
    {
        if (is_null($this->user) || !$this->user->can('invoice_booking.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $rules = array(
                //'invoice_date' => 'required',
                //'invoice_number' => 'required',
                'tp_type' => 'required',
                'tp_name' => 'required|max:100|min:3',
                'tp_TIN' => 'required|max:30|min:4',
                'tp_trade_number' => 'required|max:20|min:4',
                'tp_phone_number' => 'required|max:20|min:6',
                'tp_address_commune' => 'required|max:50|min:5',
                'tp_address_quartier' => 'required|max:50|min:5',
                //'client_id' => 'required|max:100|min:3',
                //'customer_TIN' => 'required|max:30|min:4',
                //'customer_address' => 'required|max:100|min:5',
                //'invoice_signature' => 'required|max:90|min:10',
                //'invoice_signature_date' => 'required|max: |min:',
                //'salle_id.*'  => 'required',
                'item_quantity.*'  => 'required',
                'item_price.*'  => 'required',
                'item_ct.*'  => 'required',
                'item_tl.*'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }


            try {DB::beginTransaction();

            $salle_id = $request->salle_id;
            $service_id = $request->service_id;
            $table_id = $request->table_id;
            $breakfast_id = $request->breakfast_id;
            $swiming_pool_id = $request->swiming_pool_id;
            $kidness_space_id = $request->kidness_space_id;
            $item_quantity = $request->item_quantity;
            $item_price = $request->item_price;
            $item_ct = $request->item_ct;
            $item_tl =$request->item_tl; 

            $employe_id = $request->employe_id;
            
            $latest = Facture::orderBy('id','desc')->first();
            if ($latest) {
               $invoice_number = 'FN' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $invoice_number = 'FN' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }
            

            $invoice_date = Carbon::now();
            

            $invoice_signature = $request->tp_TIN."/".config('app.obr_test_username')."/".Carbon::parse($invoice_date)->format('YmdHis')."/".$invoice_number;

        if (!empty($salle_id)) {
            for( $count = 0; $count < count($salle_id); $count++ )
        {
            $taux_tva = BookingSalle::where('id', $salle_id[$count])->value('vat');

            if($request->vat_taxpayer == 1){
                $item_total_amount = ($item_price[$count]*$item_quantity[$count]);
                
                $item_price_nvat = ($item_total_amount * 100)/110;
                $vat = ($item_price_nvat * $taux_tva)/100;
                $item_price_wvat = ($item_price_nvat + $vat);

            }else{
                $item_price_nvat = ($item_price[$count]*$item_quantity[$count])+$item_ct[$count];
                $vat = 0;
                $item_price_wvat = ($item_price_nvat + $vat);
                $item_total_amount = $item_price_wvat + $item_tl[$count];
            }

          $data = array(
            'invoice_number'=>$invoice_number,
            'invoice_date'=> $invoice_date,
            'tp_type'=>$request->tp_type,
            'tp_name'=>$request->tp_name,
            'tp_TIN'=>$request->tp_TIN,
            'tp_trade_number'=>$request->tp_trade_number,
            'tp_phone_number'=>$request->tp_phone_number,
            'tp_address_province'=>$request->tp_address_province,
            'tp_address_commune'=>$request->tp_address_commune,
            'tp_address_quartier'=>$request->tp_address_quartier,
            'tp_address_avenue'=>$request->tp_address_avenue,
            'tp_address_rue'=>$request->tp_address_rue,
            'vat_taxpayer'=>$request->vat_taxpayer,
            'ct_taxpayer'=>$request->ct_taxpayer,
            'tl_taxpayer'=>$request->tl_taxpayer,
            'tp_fiscal_center'=>$request->tp_fiscal_center,
            'tp_activity_sector'=>$request->tp_activity_sector,
            'tp_legal_form'=>$request->tp_legal_form,
            'payment_type'=>$request->payment_type,
            'client_id'=>$request->client_id,
            'customer_TIN'=>$request->customer_TIN,
            'customer_address'=>$request->customer_address,
            'invoice_signature'=> $invoice_signature,
            'booking_no'=>$request->booking_no,
            'cancelled_invoice_ref'=>$request->cancelled_invoice_ref,
            'cancelled_invoice'=>$request->cancelled_invoice,
            'invoice_currency'=>$request->invoice_currency,
            'invoice_ref'=>$request->invoice_ref,
            'auteur' => $this->user->name,
            'invoice_signature_date'=> Carbon::now(),
            'salle_id'=>$salle_id[$count],
            'item_quantity'=>$item_quantity[$count],
            'item_price'=>$item_price[$count],
            'item_ct'=>$item_ct[$count],
            'item_tl'=>$item_tl[$count],
            'item_price_nvat'=>$item_price_nvat,
            'vat'=>$vat,
            'item_price_wvat'=>$item_price_wvat,
            'item_total_amount'=>$item_total_amount,
            'employe_id'=> $employe_id,
            'created_at'=> Carbon::now(),
        );
          $data1[] = $data;
      }


        FactureDetail::insert($data1);


            //create facture
            $facture = new Facture();
            $facture->invoice_date = $invoice_date;
            $facture->invoice_number = $invoice_number;
            $facture->tp_type = $request->tp_type;
            $facture->tp_name = $request->tp_name;
            $facture->tp_TIN = $request->tp_TIN;
            $facture->tp_trade_number = $request->tp_trade_number;
            $facture->tp_phone_number = $request->tp_phone_number;
            $facture->tp_address_province = $request->tp_address_province;
            $facture->tp_address_commune = $request->tp_address_commune;
            $facture->tp_address_quartier = $request->tp_address_quartier;
            $facture->booking_no = $request->booking_no;
            $facture->vat_taxpayer = $request->vat_taxpayer;
            $facture->ct_taxpayer = $request->ct_taxpayer;
            $facture->tl_taxpayer = $request->tl_taxpayer;
            $facture->tp_fiscal_center = $request->tp_fiscal_center;
            $facture->tp_activity_sector = $request->tp_activity_sector;
            $facture->tp_legal_form = $request->tp_legal_form;
            $facture->invoice_currency = $request->invoice_currency;
            $facture->payment_type = $request->payment_type;
            $facture->client_id = $request->client_id;
            $facture->customer_TIN = $request->customer_TIN;
            $facture->customer_address = $request->customer_address;
            $facture->invoice_signature = $invoice_signature;
            $facture->cancelled_invoice_ref = $request->cancelled_invoice_ref;
            $facture->cancelled_invoice = $request->cancelled_invoice;
            $facture->invoice_ref = $request->invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->created_at = Carbon::now();
            $facture->save();

            BookingBooking::where('booking_no', '=', $request->booking_no)
                ->update(['status' => 2]);
            BookingBookingDetail::where('booking_no', '=', $request->booking_no)
                ->update(['status' => 2]);

        }elseif(!empty($service_id)){
            for( $count = 0; $count < count($service_id); $count++ )
        {
            $taux_tva = BookingService::where('id', $service_id[$count])->value('vat');

            if($request->vat_taxpayer == 1){
                $item_total_amount = ($item_price[$count]*$item_quantity[$count]);
                $item_price_nvat = ($item_total_amount * 100)/110;
                $vat = ($item_price_nvat * $taux_tva)/100;
                $item_price_wvat = ($item_price_nvat + $vat);

            }else{
                $item_price_nvat = ($item_price[$count]*$item_quantity[$count])+$item_ct[$count];
                $vat = 0;
                $item_price_wvat = ($item_price_nvat + $vat);
                $item_total_amount = $item_price_wvat + $item_tl[$count];
            }

          $data = array(
            'invoice_number'=>$invoice_number,
            'invoice_date'=> $invoice_date,
            'tp_type'=>$request->tp_type,
            'tp_name'=>$request->tp_name,
            'tp_TIN'=>$request->tp_TIN,
            'tp_trade_number'=>$request->tp_trade_number,
            'tp_phone_number'=>$request->tp_phone_number,
            'tp_address_province'=>$request->tp_address_province,
            'tp_address_commune'=>$request->tp_address_commune,
            'tp_address_quartier'=>$request->tp_address_quartier,
            'tp_address_avenue'=>$request->tp_address_avenue,
            'tp_address_rue'=>$request->tp_address_rue,
            'vat_taxpayer'=>$request->vat_taxpayer,
            'ct_taxpayer'=>$request->ct_taxpayer,
            'tl_taxpayer'=>$request->tl_taxpayer,
            'tp_fiscal_center'=>$request->tp_fiscal_center,
            'tp_activity_sector'=>$request->tp_activity_sector,
            'tp_legal_form'=>$request->tp_legal_form,
            'payment_type'=>$request->payment_type,
            'client_id'=>$request->client_id,
            'customer_TIN'=>$request->customer_TIN,
            'customer_address'=>$request->customer_address,
            'invoice_signature'=> $invoice_signature,
            'booking_no'=>$request->booking_no,
            'cancelled_invoice_ref'=>$request->cancelled_invoice_ref,
            'cancelled_invoice'=>$request->cancelled_invoice,
            'invoice_currency'=>$request->invoice_currency,
            'auteur' => $this->user->name,
            'invoice_ref'=>$request->invoice_ref,
            'invoice_signature_date'=> Carbon::now(),
            'service_id'=>$service_id[$count],
            'item_quantity'=>$item_quantity[$count],
            'item_price'=>$item_price[$count],
            'item_ct'=>$item_ct[$count],
            'item_tl'=>$item_tl[$count],
            'item_price_nvat'=>$item_price_nvat,
            'vat'=>$vat,
            'item_price_wvat'=>$item_price_wvat,
            'item_total_amount'=>$item_total_amount,
            'employe_id'=> $employe_id,
            'created_at'=> Carbon::now(),
        );
          $data1[] = $data;
      }


        FactureDetail::insert($data1);


            //create facture
            $facture = new Facture();
            $facture->invoice_date = $invoice_date;
            $facture->invoice_number = $invoice_number;
            $facture->tp_type = $request->tp_type;
            $facture->tp_name = $request->tp_name;
            $facture->tp_TIN = $request->tp_TIN;
            $facture->tp_trade_number = $request->tp_trade_number;
            $facture->tp_phone_number = $request->tp_phone_number;
            $facture->tp_address_province = $request->tp_address_province;
            $facture->tp_address_commune = $request->tp_address_commune;
            $facture->tp_address_quartier = $request->tp_address_quartier;
            $facture->booking_no = $request->booking_no;
            $facture->vat_taxpayer = $request->vat_taxpayer;
            $facture->ct_taxpayer = $request->ct_taxpayer;
            $facture->tl_taxpayer = $request->tl_taxpayer;
            $facture->tp_fiscal_center = $request->tp_fiscal_center;
            $facture->tp_activity_sector = $request->tp_activity_sector;
            $facture->tp_legal_form = $request->tp_legal_form;
            $facture->invoice_currency = $request->invoice_currency;
            $facture->payment_type = $request->payment_type;
            $facture->client_id = $request->client_id;
            $facture->customer_TIN = $request->customer_TIN;
            $facture->customer_address = $request->customer_address;
            $facture->invoice_signature = $invoice_signature;
            $facture->cancelled_invoice_ref = $request->cancelled_invoice_ref;
            $facture->cancelled_invoice = $request->cancelled_invoice;
            $facture->invoice_ref = $request->invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->created_at = Carbon::now();
            $facture->save();

            BookingBooking::where('booking_no', '=', $request->booking_no)
                ->update(['status' => 2]);
            BookingBookingDetail::where('booking_no', '=', $request->booking_no)
                ->update(['status' => 2]);

        }elseif(!empty($kidness_space_id)){
            for( $count = 0; $count < count($kidness_space_id); $count++ )
        {
            $taux_tva = KidnessSpace::where('id', $kidness_space_id[$count])->value('vat');

            if($request->vat_taxpayer == 1){
                $item_total_amount = ($item_price[$count]*$item_quantity[$count]);
                $item_price_nvat = ($item_total_amount * 100)/110;
                $vat = ($item_price_nvat * $taux_tva)/100;
                $item_price_wvat = ($item_price_nvat + $vat);

            }else{
                $item_price_nvat = ($item_price[$count]*$item_quantity[$count])+$item_ct[$count];
                $vat = 0;
                $item_price_wvat = ($item_price_nvat + $vat);
                $item_total_amount = $item_price_wvat + $item_tl[$count];
            }

          $data = array(
            'invoice_number'=>$invoice_number,
            'invoice_date'=> $invoice_date,
            'tp_type'=>$request->tp_type,
            'tp_name'=>$request->tp_name,
            'tp_TIN'=>$request->tp_TIN,
            'tp_trade_number'=>$request->tp_trade_number,
            'tp_phone_number'=>$request->tp_phone_number,
            'tp_address_province'=>$request->tp_address_province,
            'tp_address_commune'=>$request->tp_address_commune,
            'tp_address_quartier'=>$request->tp_address_quartier,
            'tp_address_avenue'=>$request->tp_address_avenue,
            'tp_address_rue'=>$request->tp_address_rue,
            'vat_taxpayer'=>$request->vat_taxpayer,
            'ct_taxpayer'=>$request->ct_taxpayer,
            'tl_taxpayer'=>$request->tl_taxpayer,
            'tp_fiscal_center'=>$request->tp_fiscal_center,
            'tp_activity_sector'=>$request->tp_activity_sector,
            'tp_legal_form'=>$request->tp_legal_form,
            'payment_type'=>$request->payment_type,
            'client_id'=>$request->client_id,
            'customer_TIN'=>$request->customer_TIN,
            'customer_address'=>$request->customer_address,
            'invoice_signature'=> $invoice_signature,
            'booking_no'=>$request->booking_no,
            'cancelled_invoice_ref'=>$request->cancelled_invoice_ref,
            'cancelled_invoice'=>$request->cancelled_invoice,
            'invoice_currency'=>$request->invoice_currency,
            'auteur' => $this->user->name,
            'invoice_ref'=>$request->invoice_ref,
            'invoice_signature_date'=> Carbon::now(),
            'kidness_space_id'=>$kidness_space_id[$count],
            'item_quantity'=>$item_quantity[$count],
            'item_price'=>$item_price[$count],
            'item_ct'=>$item_ct[$count],
            'item_tl'=>$item_tl[$count],
            'item_price_nvat'=>$item_price_nvat,
            'vat'=>$vat,
            'item_price_wvat'=>$item_price_wvat,
            'item_total_amount'=>$item_total_amount,
            'employe_id'=> $employe_id,
            'created_at'=> Carbon::now(),
        );
          $data1[] = $data;
      }


        FactureDetail::insert($data1);


            //create facture
            $facture = new Facture();
            $facture->invoice_date = $invoice_date;
            $facture->invoice_number = $invoice_number;
            $facture->tp_type = $request->tp_type;
            $facture->tp_name = $request->tp_name;
            $facture->tp_TIN = $request->tp_TIN;
            $facture->tp_trade_number = $request->tp_trade_number;
            $facture->tp_phone_number = $request->tp_phone_number;
            $facture->tp_address_province = $request->tp_address_province;
            $facture->tp_address_commune = $request->tp_address_commune;
            $facture->tp_address_quartier = $request->tp_address_quartier;
            $facture->booking_no = $request->booking_no;
            $facture->vat_taxpayer = $request->vat_taxpayer;
            $facture->ct_taxpayer = $request->ct_taxpayer;
            $facture->tl_taxpayer = $request->tl_taxpayer;
            $facture->tp_fiscal_center = $request->tp_fiscal_center;
            $facture->tp_activity_sector = $request->tp_activity_sector;
            $facture->tp_legal_form = $request->tp_legal_form;
            $facture->invoice_currency = $request->invoice_currency;
            $facture->payment_type = $request->payment_type;
            $facture->client_id = $request->client_id;
            $facture->customer_TIN = $request->customer_TIN;
            $facture->customer_address = $request->customer_address;
            $facture->invoice_signature = $invoice_signature;
            $facture->cancelled_invoice_ref = $request->cancelled_invoice_ref;
            $facture->cancelled_invoice = $request->cancelled_invoice;
            $facture->invoice_ref = $request->invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->created_at = Carbon::now();
            $facture->save();

            BookingBooking::where('booking_no', '=', $request->booking_no)
                ->update(['status' => 2]);
            BookingBookingDetail::where('booking_no', '=', $request->booking_no)
                ->update(['status' => 2]);

        }elseif(!empty($swiming_pool_id)){
            for( $count = 0; $count < count($swiming_pool_id); $count++ )
        {
            $taux_tva = SwimingPool::where('id', $swiming_pool_id[$count])->value('vat');

            if($request->vat_taxpayer == 1){
                $item_total_amount = ($item_price[$count]*$item_quantity[$count]);
                $item_price_nvat = ($item_total_amount * 100)/110;
                $vat = ($item_price_nvat * $taux_tva)/100;
                $item_price_wvat = ($item_price_nvat + $vat);

            }else{
                $item_price_nvat = ($item_price[$count]*$item_quantity[$count])+$item_ct[$count];
                $vat = 0;
                $item_price_wvat = ($item_price_nvat + $vat);
                $item_total_amount = $item_price_wvat + $item_tl[$count];
            }

          $data = array(
            'invoice_number'=>$invoice_number,
            'invoice_date'=> $invoice_date,
            'tp_type'=>$request->tp_type,
            'tp_name'=>$request->tp_name,
            'tp_TIN'=>$request->tp_TIN,
            'tp_trade_number'=>$request->tp_trade_number,
            'tp_phone_number'=>$request->tp_phone_number,
            'tp_address_province'=>$request->tp_address_province,
            'tp_address_commune'=>$request->tp_address_commune,
            'tp_address_quartier'=>$request->tp_address_quartier,
            'tp_address_avenue'=>$request->tp_address_avenue,
            'tp_address_rue'=>$request->tp_address_rue,
            'vat_taxpayer'=>$request->vat_taxpayer,
            'ct_taxpayer'=>$request->ct_taxpayer,
            'tl_taxpayer'=>$request->tl_taxpayer,
            'tp_fiscal_center'=>$request->tp_fiscal_center,
            'tp_activity_sector'=>$request->tp_activity_sector,
            'tp_legal_form'=>$request->tp_legal_form,
            'payment_type'=>$request->payment_type,
            'client_id'=>$request->client_id,
            'customer_TIN'=>$request->customer_TIN,
            'customer_address'=>$request->customer_address,
            'invoice_signature'=> $invoice_signature,
            'booking_no'=>$request->booking_no,
            'cancelled_invoice_ref'=>$request->cancelled_invoice_ref,
            'cancelled_invoice'=>$request->cancelled_invoice,
            'invoice_currency'=>$request->invoice_currency,
            'auteur' => $this->user->name,
            'invoice_ref'=>$request->invoice_ref,
            'invoice_signature_date'=> Carbon::now(),
            'swiming_pool_id'=>$swiming_pool_id[$count],
            'item_quantity'=>$item_quantity[$count],
            'item_price'=>$item_price[$count],
            'item_ct'=>$item_ct[$count],
            'item_tl'=>$item_tl[$count],
            'item_price_nvat'=>$item_price_nvat,
            'vat'=>$vat,
            'item_price_wvat'=>$item_price_wvat,
            'item_total_amount'=>$item_total_amount,
            'employe_id'=> $employe_id,
            'created_at'=> Carbon::now(),
        );
          $data1[] = $data;
      }


        FactureDetail::insert($data1);


            //create facture
            $facture = new Facture();
            $facture->invoice_date = $invoice_date;
            $facture->invoice_number = $invoice_number;
            $facture->tp_type = $request->tp_type;
            $facture->tp_name = $request->tp_name;
            $facture->tp_TIN = $request->tp_TIN;
            $facture->tp_trade_number = $request->tp_trade_number;
            $facture->tp_phone_number = $request->tp_phone_number;
            $facture->tp_address_province = $request->tp_address_province;
            $facture->tp_address_commune = $request->tp_address_commune;
            $facture->tp_address_quartier = $request->tp_address_quartier;
            $facture->booking_no = $request->booking_no;
            $facture->vat_taxpayer = $request->vat_taxpayer;
            $facture->ct_taxpayer = $request->ct_taxpayer;
            $facture->tl_taxpayer = $request->tl_taxpayer;
            $facture->tp_fiscal_center = $request->tp_fiscal_center;
            $facture->tp_activity_sector = $request->tp_activity_sector;
            $facture->tp_legal_form = $request->tp_legal_form;
            $facture->invoice_currency = $request->invoice_currency;
            $facture->payment_type = $request->payment_type;
            $facture->client_id = $request->client_id;
            $facture->customer_TIN = $request->customer_TIN;
            $facture->customer_address = $request->customer_address;
            $facture->invoice_signature = $invoice_signature;
            $facture->cancelled_invoice_ref = $request->cancelled_invoice_ref;
            $facture->cancelled_invoice = $request->cancelled_invoice;
            $facture->invoice_ref = $request->invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->created_at = Carbon::now();
            $facture->save();

            BookingBooking::where('booking_no', '=', $request->booking_no)
                ->update(['status' => 2]);
            BookingBookingDetail::where('booking_no', '=', $request->booking_no)
                ->update(['status' => 2]);

        }elseif(!empty($breakfast_id)){
            for( $count = 0; $count < count($breakfast_id); $count++ )
        {
            $taux_tva = BreakFast::where('id', $breakfast_id[$count])->value('vat');

            if($request->vat_taxpayer == 1){
                $item_total_amount = ($item_price[$count]*$item_quantity[$count]);
                $item_price_nvat = ($item_total_amount * 100)/110;
                $vat = ($item_price_nvat * $taux_tva)/100;
                $item_price_wvat = ($item_price_nvat + $vat);

            }else{
                $item_price_nvat = ($item_price[$count]*$item_quantity[$count])+$item_ct[$count];
                $vat = 0;
                $item_price_wvat = ($item_price_nvat + $vat);
                $item_total_amount = $item_price_wvat + $item_tl[$count];
            }

          $data = array(
            'invoice_number'=>$invoice_number,
            'invoice_date'=> $invoice_date,
            'tp_type'=>$request->tp_type,
            'tp_name'=>$request->tp_name,
            'tp_TIN'=>$request->tp_TIN,
            'tp_trade_number'=>$request->tp_trade_number,
            'tp_phone_number'=>$request->tp_phone_number,
            'tp_address_province'=>$request->tp_address_province,
            'tp_address_commune'=>$request->tp_address_commune,
            'tp_address_quartier'=>$request->tp_address_quartier,
            'tp_address_avenue'=>$request->tp_address_avenue,
            'tp_address_rue'=>$request->tp_address_rue,
            'vat_taxpayer'=>$request->vat_taxpayer,
            'ct_taxpayer'=>$request->ct_taxpayer,
            'tl_taxpayer'=>$request->tl_taxpayer,
            'tp_fiscal_center'=>$request->tp_fiscal_center,
            'tp_activity_sector'=>$request->tp_activity_sector,
            'tp_legal_form'=>$request->tp_legal_form,
            'payment_type'=>$request->payment_type,
            'client_id'=>$request->client_id,
            'customer_TIN'=>$request->customer_TIN,
            'customer_address'=>$request->customer_address,
            'invoice_signature'=> $invoice_signature,
            'booking_no'=>$request->booking_no,
            'cancelled_invoice_ref'=>$request->cancelled_invoice_ref,
            'cancelled_invoice'=>$request->cancelled_invoice,
            'invoice_currency'=>$request->invoice_currency,
            'auteur' => $this->user->name,
            'invoice_ref'=>$request->invoice_ref,
            'invoice_signature_date'=> Carbon::now(),
            'breakfast_id'=>$breakfast_id[$count],
            'item_quantity'=>$item_quantity[$count],
            'item_price'=>$item_price[$count],
            'item_ct'=>$item_ct[$count],
            'item_tl'=>$item_tl[$count],
            'item_price_nvat'=>$item_price_nvat,
            'vat'=>$vat,
            'item_price_wvat'=>$item_price_wvat,
            'item_total_amount'=>$item_total_amount,
            'employe_id'=> $employe_id,
            'created_at'=> Carbon::now(),
        );
          $data1[] = $data;
      }


        FactureDetail::insert($data1);


            //create facture
            $facture = new Facture();
            $facture->invoice_date = $invoice_date;
            $facture->invoice_number = $invoice_number;
            $facture->tp_type = $request->tp_type;
            $facture->tp_name = $request->tp_name;
            $facture->tp_TIN = $request->tp_TIN;
            $facture->tp_trade_number = $request->tp_trade_number;
            $facture->tp_phone_number = $request->tp_phone_number;
            $facture->tp_address_province = $request->tp_address_province;
            $facture->tp_address_commune = $request->tp_address_commune;
            $facture->tp_address_quartier = $request->tp_address_quartier;
            $facture->booking_no = $request->booking_no;
            $facture->vat_taxpayer = $request->vat_taxpayer;
            $facture->ct_taxpayer = $request->ct_taxpayer;
            $facture->tl_taxpayer = $request->tl_taxpayer;
            $facture->tp_fiscal_center = $request->tp_fiscal_center;
            $facture->tp_activity_sector = $request->tp_activity_sector;
            $facture->tp_legal_form = $request->tp_legal_form;
            $facture->invoice_currency = $request->invoice_currency;
            $facture->payment_type = $request->payment_type;
            $facture->client_id = $request->client_id;
            $facture->customer_TIN = $request->customer_TIN;
            $facture->customer_address = $request->customer_address;
            $facture->invoice_signature = $invoice_signature;
            $facture->cancelled_invoice_ref = $request->cancelled_invoice_ref;
            $facture->cancelled_invoice = $request->cancelled_invoice;
            $facture->invoice_ref = $request->invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->created_at = Carbon::now();
            $facture->save();

            BookingBooking::where('booking_no', '=', $request->booking_no)
                ->update(['status' => 2]);
            BookingBookingDetail::where('booking_no', '=', $request->booking_no)
                ->update(['status' => 2]);

        }elseif(!empty($table_id)){
            for( $count = 0; $count < count($table_id); $count++ )
        {
            $taux_tva = BookingTable::where('id', $table_id[$count])->value('vat');

            if($request->vat_taxpayer == 1){
                $item_total_amount = ($item_price[$count]*$item_quantity[$count]);
                
                $item_price_nvat = ($item_total_amount * 100)/110;
                $vat = ($item_price_nvat * $taux_tva)/100;
                $item_price_wvat = ($item_price_nvat + $vat);

            }else{
                $item_price_nvat = ($item_price[$count]*$item_quantity[$count])+$item_ct[$count];
                $vat = 0;
                $item_price_wvat = ($item_price_nvat + $vat);
                $item_total_amount = $item_price_wvat + $item_tl[$count];
            }

          $data = array(
            'invoice_number'=>$invoice_number,
            'invoice_date'=> $invoice_date,
            'tp_type'=>$request->tp_type,
            'tp_name'=>$request->tp_name,
            'tp_TIN'=>$request->tp_TIN,
            'tp_trade_number'=>$request->tp_trade_number,
            'tp_phone_number'=>$request->tp_phone_number,
            'tp_address_province'=>$request->tp_address_province,
            'tp_address_commune'=>$request->tp_address_commune,
            'tp_address_quartier'=>$request->tp_address_quartier,
            'tp_address_avenue'=>$request->tp_address_avenue,
            'tp_address_rue'=>$request->tp_address_rue,
            'vat_taxpayer'=>$request->vat_taxpayer,
            'ct_taxpayer'=>$request->ct_taxpayer,
            'tl_taxpayer'=>$request->tl_taxpayer,
            'tp_fiscal_center'=>$request->tp_fiscal_center,
            'tp_activity_sector'=>$request->tp_activity_sector,
            'tp_legal_form'=>$request->tp_legal_form,
            'payment_type'=>$request->payment_type,
            'client_id'=>$request->client_id,
            'customer_TIN'=>$request->customer_TIN,
            'customer_address'=>$request->customer_address,
            'invoice_signature'=> $invoice_signature,
            'booking_no'=>$request->booking_no,
            'cancelled_invoice_ref'=>$request->cancelled_invoice_ref,
            'cancelled_invoice'=>$request->cancelled_invoice,
            'invoice_currency'=>$request->invoice_currency,
            'invoice_ref'=>$request->invoice_ref,
            'auteur' => $this->user->name,
            'invoice_signature_date'=> Carbon::now(),
            'table_id'=>$table_id[$count],
            'item_quantity'=>$item_quantity[$count],
            'item_price'=>$item_price[$count],
            'item_ct'=>$item_ct[$count],
            'item_tl'=>$item_tl[$count],
            'item_price_nvat'=>$item_price_nvat,
            'vat'=>$vat,
            'item_price_wvat'=>$item_price_wvat,
            'item_total_amount'=>$item_total_amount,
            'employe_id'=> $employe_id,
            'created_at'=> Carbon::now(),
        );
          $data1[] = $data;
      }


        FactureDetail::insert($data1);


            //create facture
            $facture = new Facture();
            $facture->invoice_date = $invoice_date;
            $facture->invoice_number = $invoice_number;
            $facture->tp_type = $request->tp_type;
            $facture->tp_name = $request->tp_name;
            $facture->tp_TIN = $request->tp_TIN;
            $facture->tp_trade_number = $request->tp_trade_number;
            $facture->tp_phone_number = $request->tp_phone_number;
            $facture->tp_address_province = $request->tp_address_province;
            $facture->tp_address_commune = $request->tp_address_commune;
            $facture->tp_address_quartier = $request->tp_address_quartier;
            $facture->booking_no = $request->booking_no;
            $facture->vat_taxpayer = $request->vat_taxpayer;
            $facture->ct_taxpayer = $request->ct_taxpayer;
            $facture->tl_taxpayer = $request->tl_taxpayer;
            $facture->tp_fiscal_center = $request->tp_fiscal_center;
            $facture->tp_activity_sector = $request->tp_activity_sector;
            $facture->tp_legal_form = $request->tp_legal_form;
            $facture->invoice_currency = $request->invoice_currency;
            $facture->payment_type = $request->payment_type;
            $facture->client_id = $request->client_id;
            $facture->customer_TIN = $request->customer_TIN;
            $facture->customer_address = $request->customer_address;
            $facture->invoice_signature = $invoice_signature;
            $facture->cancelled_invoice_ref = $request->cancelled_invoice_ref;
            $facture->cancelled_invoice = $request->cancelled_invoice;
            $facture->invoice_ref = $request->invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->created_at = Carbon::now();
            $facture->save();

            BookingBooking::where('booking_no', '=', $request->booking_no)
                ->update(['status' => 2]);
            BookingBookingDetail::where('booking_no', '=', $request->booking_no)
                ->update(['status' => 2]);

        }

        DB::commit();
            session()->flash('success', 'Le vente est fait avec succés!!');
            return redirect()->route('admin.booking-invoices.choose');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function validerFactureBoisson($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        try {DB::beginTransaction();

        $datas = FactureDetail::where('invoice_number', $invoice_number)->get();

        $table_id = FactureDetail::where('invoice_number', $invoice_number)->value('table_id');

        foreach($datas as $data){
            $valeurStockInitial = DrinkSmallStoreDetail::where('code',$data->code_store)->where('drink_id', $data->drink_id)->value('total_cump_value');
            $quantityStockInitial = DrinkSmallStoreDetail::where('code',$data->code_store)->where('drink_id', $data->drink_id)->value('quantity_bottle');
            $cump = Drink::where('id', $data->drink_id)->value('cump');

            $quantityRestant = $quantityStockInitial - $data->item_quantity;
                      
                $reportData = array(
                    'drink_id' => $data->drink_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'code_store' => $data->code_store,
                    'quantity_sold' => $data->item_quantity,
                    'value_sold' => $data->item_quantity * $data->item_price,
                    'quantity_stock_final' => $quantityRestant,
                    'value_stock_final' => $quantityRestant * $cump,
                    'invoice_no' => $data->invoice_number,
                    'date' => $data->invoice_date,
                    'commande_boisson_no' => $data->drink_order_no,
                    'type_transaction' => 'VENTE',
                    'document_no' => $data->invoice_number,
                    'cump' => $cump,
                    'created_by' => $this->user->name,
                    'employe_id' => $data->employe_id,
                    'origine_facture' => 'BOISSON',
                    'description' => "SORTIE DES BOISSONS APRES VENTE",
                    'created_at' => \Carbon\Carbon::now()
                );
                $report[] = $reportData;
                
                    $donnees = array(
                        'drink_id' => $data->drink_id,
                        'quantity_bottle' => $quantityRestant,
                        'total_cump_value' => $quantityRestant * $cump,
                        'created_by' => $this->user->name,
                        'verified' => true
                    );
                    
                    if ($data->item_quantity <= $quantityStockInitial) {
                        
                        DrinkSmallStoreDetail::where('code',$data->code_store)->where('drink_id',$data->drink_id)
                        ->update($donnees);
                        $flag = 0;
                        /*
                        $theUrl = config('app.guzzle_test_url').'/ebms_api/login/';
                        $response = Http::post($theUrl, [
                            'username'=> config('app.obr_test_username'),
                            'password'=> config('app.obr_test_pwd')

                        ]);
                        $data1 =  json_decode($response);
                        $data2 = ($data1->result);       
    
                        $token = $data2->token;

                        $theUrl = config('app.guzzle_test_url').'/ebms_api/AddStockMovement';  
                        $response = Http::withHeaders([
                        'Authorization' => 'Bearer '.$token,
                        'Accept' => 'application/json'])->post($theUrl, [
                            'system_or_device_id'=> "wsconfig('app.tin_number_company')00565",
                            'item_code'=> $data->drink->code,
                            'item_designation'=>$data->drink->name,
                            'item_quantity'=>$data->item_quantity,
                            'item_measurement_unit'=>$data->drink->unit,
                            'item_purchase_or_sale_price'=>$data->drink->purchase_price,
                            'item_purchase_or_sale_currency'=> "BIF",
                            'item_movement_type'=> 'SN',
                            'item_movement_invoice_ref'=> "",
                            'item_movement_description'=> 'SORTIES NORMALES DE VENTE DES MARCHANDISE',
                            'item_movement_date'=> Carbon::parse($data->updated_at)->format('Y-m-d H:i:s'),

                        ]);
                        */
                        
                    }else{

                        foreach ($datas as $data) {
                            $valeurStockInitial = DrinkSmallStoreDetail::where('code',$data->code_store)->where('drink_id', $data->drink_id)->value('total_cump_value');
                            $quantityStockInitial = DrinkSmallStoreDetail::where('code',$data->code_store)->where('drink_id', $data->drink_id)->where('verified',true)->value('quantity_bottle');
                            $cump = Drink::where('id', $data->drink_id)->value('cump');

                            $quantityTotal = $quantityStockInitial + $data->item_quantity;
                      
                
                            $returnData = array(
                                'drink_id' => $data->drink_id,
                                'quantity_bottle' => $quantityTotal,
                                'total_cump_value' => $quantityTotal * $cump,
                                'created_by' => $this->user->name,
                                'verified' => false
                            );

                            $status = DrinkSmallStoreDetail::where('code',$data->code_store)->where('drink_id', $data->drink_id)->value('verified');
                    
                        
                                DrinkSmallStoreDetail::where('code',$data->code_store)->where('drink_id',$data->drink_id)->where('verified',true)
                                ->update($returnData);
                                $flag = 1;
                        }

                        DrinkSmallStoreDetail::where('drink_id','!=','')->update(['verified' => false]);

                        session()->flash('error', $this->user->name.' ,why do you want selling a quantity that you do not have!');
                        return redirect()->back();
                    }
        }
        if ($flag != 1) {
            DrinkSmallReport::insert($report);
        }

        foreach($datas as $data){
                $orderData = array(
                    'confirmed_by' => $this->user->name,
                    'status' => 3,
                );

                OrderDrink::where('order_no', '=', $data->drink_order_no)
                    ->update($orderData);
                OrderDrinkDetail::where('order_no', '=', $data->drink_order_no)
                    ->update($orderData);
        }

        $item_total_amount = DB::table('facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');

        $total_amount_paying = DB::table('tables')
            ->where('id', '=', $table_id)
            ->sum('total_amount_paying');

        $in_pending = count(OrderDrinkDetail::where('table_id',$table_id)->where('status','!=',3)->where('status','!=',2)->where('status','!=',-1)->get());

        if ($in_pending < 1 && $item_total_amount >= $total_amount_paying) {
            Table::where('id',$table_id)->update(['etat' => 0,'waiter_name' => '','opened_by' => '','total_amount_paying' => 0]);
        }else {
            $total_amount_remaining = $total_amount_paying - $item_total_amount;
            Table::where('id',$table_id)->update(['total_amount_paying' => $total_amount_remaining]);
        }
        
        DrinkSmallStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
        Facture::where('invoice_number', '=', $invoice_number)
            ->update(['etat' => 1,'statut_paied' => '0','validated_by' => $this->user->name]);
        FactureDetail::where('invoice_number', '=', $invoice_number)
            ->update(['etat' => 1,'statut_paied' => '0','validated_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'La Facture  est validée avec succés');
            return redirect()->route('ebms_api.invoices.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }


    }

    public function validerFactureBoissonCredit(Request  $request,$invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        $request->validate([
            'client_id' => 'required'
        ]);

        try {DB::beginTransaction();

        $client_id = $request->client_id;

        $datas = FactureDetail::where('invoice_number', $invoice_number)->get();
        $table_id = FactureDetail::where('invoice_number', $invoice_number)->value('table_id');

        foreach($datas as $data){
            $valeurStockInitial = DrinkSmallStoreDetail::where('code',$data->code_store)->where('drink_id', $data->drink_id)->value('total_cump_value');
            $quantityStockInitial = DrinkSmallStoreDetail::where('code',$data->code_store)->where('drink_id', $data->drink_id)->value('quantity_bottle');
            $cump = Drink::where('id', $data->drink_id)->value('cump');

            $quantityRestant = $quantityStockInitial - $data->item_quantity;
                      
                $reportData = array(
                    'drink_id' => $data->drink_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'code_store' => $data->code_store,
                    'quantity_sold' => $data->item_quantity,
                    'value_sold' => $data->item_quantity * $data->item_price,
                    'quantity_stock_final' => $quantityRestant,
                    'value_stock_final' => $quantityRestant * $cump,
                    'invoice_no' => $data->invoice_number,
                    'date' => $data->invoice_date,
                    'commande_boisson_no' => $data->drink_order_no,
                    'type_transaction' => 'VENTE',
                    'document_no' => $data->invoice_number,
                    'cump' => $cump,
                    'created_by' => $this->user->name,
                    'employe_id' => $data->employe_id,
                    'origine_facture' => 'BOISSON',
                    'description' => "SORTIE DES BOISSONS APRES VENTE",
                    'created_at' => \Carbon\Carbon::now()
                );
                $report[] = $reportData;
                
                    $donnees = array(
                        'drink_id' => $data->drink_id,
                        'quantity_bottle' => $quantityRestant,
                        'total_cump_value' => $quantityRestant * $cump,
                        'created_by' => $this->user->name,
                        'verified' => true
                    );
                    
                    if ($data->item_quantity <= $quantityStockInitial) {
                        
                        DrinkSmallStoreDetail::where('code',$data->code_store)->where('drink_id',$data->drink_id)
                        ->update($donnees);
                        $flag = 0;
                        /*
                        $theUrl = config('app.guzzle_test_url').'/ebms_api/login/';
                        $response = Http::post($theUrl, [
                            'username'=> config('app.obr_test_username'),
                            'password'=> config('app.obr_test_pwd')

                        ]);
                        $data1 =  json_decode($response);
                        $data2 = ($data1->result);       
    
                        $token = $data2->token;

                        $theUrl = config('app.guzzle_test_url').'/ebms_api/AddStockMovement';  
                        $response = Http::withHeaders([
                        'Authorization' => 'Bearer '.$token,
                        'Accept' => 'application/json'])->post($theUrl, [
                            'system_or_device_id'=> "wsconfig('app.tin_number_company')00565",
                            'item_code'=> $data->drink->code,
                            'item_designation'=>$data->drink->name,
                            'item_quantity'=>$data->item_quantity,
                            'item_measurement_unit'=>$data->drink->unit,
                            'item_purchase_or_sale_price'=>$data->drink->purchase_price,
                            'item_purchase_or_sale_currency'=> "BIF",
                            'item_movement_type'=> 'SN',
                            'item_movement_invoice_ref'=> "",
                            'item_movement_description'=> 'SORTIES NORMALES DE VENTE DES MARCHANDISE',
                            'item_movement_date'=> Carbon::parse($data->updated_at)->format('Y-m-d H:i:s'),

                        ]);
                        */
                        
                    }else{

                        foreach ($datas as $data) {
                            $valeurStockInitial = DrinkSmallStoreDetail::where('code',$data->code_store)->where('drink_id', $data->drink_id)->value('total_cump_value');
                            $quantityStockInitial = DrinkSmallStoreDetail::where('code',$data->code_store)->where('drink_id', $data->drink_id)->where('verified',true)->value('quantity_bottle');
                            $cump = Drink::where('id', $data->drink_id)->value('cump');

                            $quantityTotal = $quantityStockInitial + $data->item_quantity;
                      
                
                            $returnData = array(
                                'drink_id' => $data->drink_id,
                                'quantity_bottle' => $quantityTotal,
                                'total_cump_value' => $quantityTotal * $cump,
                                'created_by' => $this->user->name,
                                'verified' => false
                            );

                            $status = DrinkSmallStoreDetail::where('code',$data->code_store)->where('drink_id', $data->drink_id)->value('verified');
                    

                        
                                DrinkSmallStoreDetail::where('code',$data->code_store)->where('drink_id',$data->drink_id)->where('verified',true)
                                ->update($returnData);
                                $flag = 1;
                            
                        }

                        DrinkSmallStoreDetail::where('drink_id','!=','')->update(['verified' => false]);

                        session()->flash('error', $this->user->name.' ,why do you want selling a quantity that you do not have!');
                        return redirect()->back();
                    }
        }
        
        if ($flag != 1) {
            DrinkSmallReport::insert($report);
        }

        foreach($datas as $data){
                $orderData = array(
                    'confirmed_by' => $this->user->name,
                    'status' => 3,
                );

                OrderDrink::where('order_no', '=', $data->drink_order_no)
                    ->update($orderData);
                OrderDrinkDetail::where('order_no', '=', $data->drink_order_no)
                    ->update($orderData);
        }

        $item_total_amount = DB::table('facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');
            
        $total_amount_paying = DB::table('tables')
            ->where('id', '=', $table_id)
            ->sum('total_amount_paying');
        
        $in_pending = count(OrderDrinkDetail::where('table_id',$table_id)->where('status','!=',3)->where('status','!=',2)->where('status','!=',-1)->get());

        if ($in_pending < 1 && $item_total_amount >= $total_amount_paying) {
            Table::where('id',$table_id)->update(['etat' => 0,'waiter_name' => '','opened_by' => '','total_amount_paying' => 0]);
        }else {
            $total_amount_remaining = $total_amount_paying - $item_total_amount;
            Table::where('id',$table_id)->update(['total_amount_paying' => $total_amount_remaining]);
        }

        DrinkSmallStoreDetail::where('drink_id','!=','')->update(['verified' => false]);

        Facture::where('invoice_number', '=', $invoice_number)
            ->update(['etat' => '01','etat_recouvrement' => '0','montant_total_credit' => $item_total_amount,'statut_paied' => '0','client_id' => $client_id,'validated_by' => $this->user->name]);
        FactureDetail::where('invoice_number', '=', $invoice_number)
            ->update(['etat' => '01','etat_recouvrement' => '0','montant_total_credit' => $item_total_amount,'statut_paied' => '0','client_id' => $client_id,'validated_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'La Facture  est validée avec succés');
            return redirect()->route('ebms_api.invoices.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

        
    }

    public function validerFactureBarrist($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        try {DB::beginTransaction();

        $datas = FactureDetail::where('invoice_number', $invoice_number)->get();

        $table_id = FactureDetail::where('invoice_number', $invoice_number)->value('table_id');

        foreach($datas as $data){
            $valeurStockInitial = BarristProductionStore::where('barrist_item_id', $data->barrist_item_id)->value('total_cump_value');
            $quantityStockInitial = BarristProductionStore::where('barrist_item_id', $data->barrist_item_id)->value('quantity');
            $cump = BarristProductionStore::where('barrist_item_id', $data->barrist_item_id)->value('cump');

            $quantityRestant = $quantityStockInitial - $data->item_quantity;
                      
                $reportData = array(
                    'barrist_item_id' => $data->barrist_item_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'quantity_sold' => $data->item_quantity,
                    'value_sold' => $data->item_quantity * $data->item_price,
                    'quantity_stock_final' => $quantityRestant,
                    'value_stock_final' => $quantityRestant * $data->item_price,
                    'invoice_no' => $data->invoice_number,
                    'commande_boisson_no' => $data->barrist_order_no,
                    'created_by' => $this->user->name,
                    'employe_id' => $data->employe_id,
                    'origine_facture' => 'BOISSON',
                    'created_at' => \Carbon\Carbon::now()
                );
                $report[] = $reportData;
                    /*
                    $donnees = array(
                        'barrist_item_id' => $data->barrist_item_id,
                        'quantity' => $quantityRestant,
                        'total_cump_value' => $quantityRestant * $cump,
                        'created_by' => $this->user->name,
                        'verified' => false
                    );
                    
                    if ($data->item_quantity <= $quantityStockInitial) {

                        BarristSmallReport::insert($report);
                        
                        BarristProductionStore::where('barrist_item_id',$data->barrist_item_id)
                        ->update($donnees);

                        
                    }else{
                        session()->flash('error', $this->user->name.' ,why do you want selling a quantity that you do not have!');
                        return redirect()->back();
                    }
                    */
        }


        foreach($datas as $data){
                $orderData = array(
                    'confirmed_by' => $this->user->name,
                    'status' => 3,
                );

                BarristOrder::where('order_no', '=', $data->barrist_order_no)
                    ->update($orderData);
                BarristOrderDetail::where('order_no', '=', $data->barrist_order_no)
                    ->update($orderData);
        }

        $item_total_amount = DB::table('facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');

        $total_amount_paying = DB::table('tables')
            ->where('id', '=', $table_id)
            ->sum('total_amount_paying');

        $in_pending = count(BarristOrderDetail::where('table_id',$table_id)->where('status','!=',3)->where('status','!=',2)->where('status','!=',-1)->get());

        if ($in_pending < 1 && $item_total_amount >= $total_amount_paying) {
            Table::where('id',$table_id)->update(['etat' => 0,'waiter_name' => '','opened_by' => '','total_amount_paying' => 0]);
        }else {
            $total_amount_remaining = $total_amount_paying - $item_total_amount;
            Table::where('id',$table_id)->update(['total_amount_paying' => $total_amount_remaining]);
        }

        Facture::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => 1,'statut_paied' => '0','validated_by' => $this->user->name]);
        FactureDetail::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => 1,'statut_paied' => '0','validated_by' => $this->user->name]);
        BarristSmallReport::insert($report);

        DB::commit();
            session()->flash('success', 'La Facture  est validée avec succés');
            return redirect()->route('admin.barrist-invoices.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function validerFactureBarristCredit(Request  $request,$invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        $request->validate([
            'client_id' => 'required'
        ]);

        try {DB::beginTransaction();

        $client_id = $request->client_id;

        $datas = FactureDetail::where('invoice_number', $invoice_number)->get();

        $table_id = FactureDetail::where('invoice_number', $invoice_number)->value('table_id');

        foreach($datas as $data){
            $valeurStockInitial = BarristProductionStore::where('barrist_item_id', $data->barrist_item_id)->value('total_cump_value');
            $quantityStockInitial = BarristProductionStore::where('barrist_item_id', $data->barrist_item_id)->value('quantity');
            $cump = BarristProductionStore::where('barrist_item_id', $data->barrist_item_id)->value('cump');

            $quantityRestant = $quantityStockInitial - $data->item_quantity;
                      
                $reportData = array(
                    'barrist_item_id' => $data->barrist_item_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'quantity_sold' => $data->item_quantity,
                    'value_sold' => $data->item_quantity * $data->item_price,
                    'quantity_stock_final' => $quantityRestant,
                    'value_stock_final' => $quantityRestant * $data->item_price,
                    'invoice_no' => $data->invoice_number,
                    'commande_boisson_no' => $data->barrist_order_no,
                    'type_transaction' => 'VENTE',
                    'document_no' => $data->invoice_number,
                    'created_by' => $this->user->name,
                    'employe_id' => $data->employe_id,
                    'origine_facture' => 'BOISSON',
                    'created_at' => \Carbon\Carbon::now()
                );
                $report[] = $reportData;
                    /*
                    $donnees = array(
                        'barrist_item_id' => $data->barrist_item_id,
                        'quantity' => $quantityRestant,
                        'total_cump_value' => $quantityRestant * $cump,
                        'created_by' => $this->user->name,
                        'verified' => false
                    );
                    
                    if ($data->item_quantity <= $quantityStockInitial) {

                        BarristSmallReport::insert($report);
                        
                        BarristProductionStore::where('barrist_item_id',$data->barrist_item_id)
                        ->update($donnees);

                        
                    }else{
                        session()->flash('error', $this->user->name.' ,why do you want selling a quantity that you do not have!');
                        return redirect()->back();
                    }
                    */
        }

        $item_total_amount = DB::table('facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');

        foreach($datas as $data){
                $orderData = array(
                    'confirmed_by' => $this->user->name,
                    'status' => 3,
                );

                BarristOrder::where('order_no', '=', $data->barrist_order_no)
                    ->update($orderData);
                BarristOrderDetail::where('order_no', '=', $data->barrist_order_no)
                    ->update($orderData);
        }

        $total_amount_paying = DB::table('tables')
            ->where('id', '=', $table_id)
            ->sum('total_amount_paying');

        $in_pending = count(BarristOrderDetail::where('table_id',$table_id)->where('status','!=',3)->where('status','!=',2)->where('status','!=',-1)->get());

        if ($in_pending < 1 && $item_total_amount >= $total_amount_paying) {
            Table::where('id',$table_id)->update(['etat' => 0,'waiter_name' => '','opened_by' => '','total_amount_paying' => 0]);
        }else {
            $total_amount_remaining = $total_amount_paying - $item_total_amount;
            Table::where('id',$table_id)->update(['total_amount_paying' => $total_amount_remaining]);
        }

        Facture::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => '01','etat_recouvrement' => '0','montant_total_credit' => $item_total_amount,'statut_paied' => '0','client_id' => $client_id,'validated_by' => $this->user->name]);
        FactureDetail::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => '01','etat_recouvrement' => '0','montant_total_credit' => $item_total_amount,'statut_paied' => '0','client_id' => $client_id,'validated_by' => $this->user->name]);
        BarristSmallReport::insert($report);

        DB::commit();
            session()->flash('success', 'La Facture  est validée avec succés');
            return redirect()->route('admin.barrist-invoices.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }


    public function validerFactureBartender($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        try {DB::beginTransaction();

        $datas = FactureDetail::where('invoice_number', $invoice_number)->get();

        $table_id = FactureDetail::where('invoice_number', $invoice_number)->value('table_id');

        foreach($datas as $data){
            $valeurStockInitial = BartenderProductionStore::where('bartender_item_id', $data->bartender_item_id)->value('total_cump_value');
            $quantityStockInitial = BartenderProductionStore::where('bartender_item_id', $data->bartender_item_id)->value('quantity');
            $cump = BartenderProductionStore::where('bartender_item_id', $data->bartender_item_id)->value('cump');

            $quantityRestant = $quantityStockInitial - $data->item_quantity;
                      
                $reportData = array(
                    'bartender_item_id' => $data->bartender_item_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'quantity_sold' => $data->item_quantity,
                    'value_sold' => $data->item_quantity * $data->item_price,
                    'quantity_stock_final' => $quantityRestant,
                    'value_stock_final' => $quantityRestant * $data->item_price,
                    'invoice_no' => $data->invoice_number,
                    'date' => $data->invoice_date,
                    'commande_boisson_no' => $data->bartender_order_no,
                    'type_transaction' => 'VENTE',
                    'document_no' => $data->invoice_number,
                    'created_by' => $this->user->name,
                    'employe_id' => $data->employe_id,
                    'origine_facture' => 'BOISSON',
                    'created_at' => \Carbon\Carbon::now()
                );
                $report[] = $reportData;

                $donnees = array(
                        'bartender_item_id' => $data->bartender_item_id,
                        'quantity' => $quantityRestant,
                        'total_selling_value' => $quantityRestant * $data->item_price,
                        'created_by' => $this->user->name,
                        'verified' => false
                    );
                    
                    if ($data->item_quantity <= $quantityStockInitial) {
                        
                        BartenderProductionStore::where('bartender_item_id',$data->bartender_item_id)
                        ->update($donnees);

                        /*
                        $theUrl = config('app.guzzle_test_url').'/ebms_api/login/';
                        $response = Http::post($theUrl, [
                            'username'=> config('app.obr_test_username'),
                            'password'=> config('app.obr_test_pwd')

                        ]);
                        $data1 =  json_decode($response);
                        $data2 = ($data1->result);       
    
                        $token = $data2->token;

                        $theUrl = config('app.guzzle_test_url').'/ebms_api/AddStockMovement';  
                        $response = Http::withHeaders([
                        'Authorization' => 'Bearer '.$token,
                        'Accept' => 'application/json'])->post($theUrl, [
                            'system_or_device_id'=> "wsconfig('app.tin_number_company')00565",
                            'item_code'=> $data->bartenderItem->code,
                            'item_designation'=>$data->bartenderItem->name,
                            'item_quantity'=>$data->item_quantity,
                            'item_measurement_unit'=>$data->bartenderItem->unit,
                            'item_purchase_or_sale_price'=> "",
                            'item_purchase_or_sale_currency'=> "BIF",
                            'item_movement_type'=> 'SN',
                            'item_movement_invoice_ref'=> "",
                            'item_movement_description'=> 'SORTIES NORMALES DE VENTE DES MARCHANDISES',
                            'item_movement_date'=> Carbon::parse($data->updated_at)->format('Y-m-d H:i:s'),

                        ]);

                    */
                        
                    }else{
                        session()->flash('error', $this->user->name.' ,why do you want selling a quantity that you do not have!');
                        return redirect()->back();
                    }
        }

        BartenderSmallReport::insert($report);

        $item_total_amount = DB::table('facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');

        foreach($datas as $data){
                $orderData = array(
                    'confirmed_by' => $this->user->name,
                    'status' => 3,
                );

                BartenderOrder::where('order_no', '=', $data->bartender_order_no)
                    ->update($orderData);
                BartenderOrderDetail::where('order_no', '=', $data->bartender_order_no)
                    ->update($orderData);
        }

        $total_amount_paying = DB::table('tables')
            ->where('id', '=', $table_id)
            ->sum('total_amount_paying');

        $in_pending = count(BartenderOrderDetail::where('table_id',$table_id)->where('status','!=',3)->where('status','!=',2)->where('status','!=',-1)->get());

        if ($in_pending < 1 && $item_total_amount >= $total_amount_paying) {
            Table::where('id',$table_id)->update(['etat' => 0,'waiter_name' => '','opened_by' => '','total_amount_paying' => 0]);
        }else {
            $total_amount_remaining = $total_amount_paying - $item_total_amount;
            Table::where('id',$table_id)->update(['total_amount_paying' => $total_amount_remaining]);
        }
        
        Facture::where('invoice_number', '=', $invoice_number)
            ->update(['etat' => 1,'statut_paied' => '0','validated_by' => $this->user->name]);
        FactureDetail::where('invoice_number', '=', $invoice_number)
            ->update(['etat' => 1,'statut_paied' => '0','validated_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'La Facture  est validée avec succés');
            return redirect()->route('admin.bartender-invoices.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function validerFactureBartenderCredit(Request  $request,$invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        $request->validate([
            'client_id' => 'required'
        ]);

        try {DB::beginTransaction();

        $client_id = $request->client_id;

        $datas = FactureDetail::where('invoice_number', $invoice_number)->get();

        $table_id = FactureDetail::where('invoice_number', $invoice_number)->value('table_id');

        foreach($datas as $data){
            $valeurStockInitial = BartenderProductionStore::where('bartender_item_id', $data->bartender_item_id)->value('total_cump_value');
            $quantityStockInitial = BartenderProductionStore::where('bartender_item_id', $data->bartender_item_id)->value('quantity');
            $cump = BartenderProductionStore::where('bartender_item_id', $data->bartender_item_id)->value('cump');

            $quantityRestant = $quantityStockInitial - $data->item_quantity;
                      
                $reportData = array(
                    'bartender_item_id' => $data->bartender_item_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'quantity_sold' => $data->item_quantity,
                    'value_sold' => $data->item_quantity * $data->item_price,
                    'quantity_stock_final' => $quantityRestant,
                    'value_stock_final' => $quantityRestant * $data->item_price,
                    'invoice_no' => $data->invoice_number,
                    'date' => $data->invoice_date,
                    'commande_boisson_no' => $data->bartender_order_no,
                    'type_transaction' => 'VENTE',
                    'document_no' => $data->invoice_number,
                    'created_by' => $this->user->name,
                    'employe_id' => $data->employe_id,
                    'origine_facture' => 'BOISSON',
                    'created_at' => \Carbon\Carbon::now()
                );
                $report[] = $reportData;

                $donnees = array(
                        'bartender_item_id' => $data->bartender_item_id,
                        'quantity' => $quantityRestant,
                        'total_selling_value' => $quantityRestant * $data->item_price,
                        'created_by' => $this->user->name,
                        'verified' => false
                    );
                    
                    if ($data->item_quantity <= $quantityStockInitial) {
                        
                        BartenderProductionStore::where('bartender_item_id',$data->bartender_item_id)
                        ->update($donnees);

                        /*
                        $theUrl = config('app.guzzle_test_url').'/ebms_api/login/';
                        $response = Http::post($theUrl, [
                            'username'=> config('app.obr_test_username'),
                            'password'=> config('app.obr_test_pwd')

                        ]);
                        $data1 =  json_decode($response);
                        $data2 = ($data1->result);       
    
                        $token = $data2->token;

                        $theUrl = config('app.guzzle_test_url').'/ebms_api/AddStockMovement';  
                        $response = Http::withHeaders([
                        'Authorization' => 'Bearer '.$token,
                        'Accept' => 'application/json'])->post($theUrl, [
                            'system_or_device_id'=> "wsconfig('app.tin_number_company')00565",
                            'item_code'=> $data->bartenderItem->code,
                            'item_designation'=>$data->bartenderItem->name,
                            'item_quantity'=>$data->item_quantity,
                            'item_measurement_unit'=>$data->bartenderItem->unit,
                            'item_purchase_or_sale_price'=> "",
                            'item_purchase_or_sale_currency'=> "BIF",
                            'item_movement_type'=> 'SN',
                            'item_movement_invoice_ref'=> "",
                            'item_movement_description'=> 'SORTIES NORMALES DE VENTE DES MARCHANDISES',
                            'item_movement_date'=> Carbon::parse($data->updated_at)->format('Y-m-d H:i:s'),

                        ]);

                    */
                        
                    }else{
                        session()->flash('error', $this->user->name.' ,why do you want selling a quantity that you do not have!');
                        return redirect()->back();
                    }
        }

        BartenderSmallReport::insert($report);

        $item_total_amount = DB::table('facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');

        foreach($datas as $data){
                $orderData = array(
                    'confirmed_by' => $this->user->name,
                    'status' => 3,
                );

                BartenderOrder::where('order_no', '=', $data->bartender_order_no)
                    ->update($orderData);
                BartenderOrderDetail::where('order_no', '=', $data->bartender_order_no)
                    ->update($orderData);
        }


        $total_amount_paying = DB::table('tables')
            ->where('id', '=', $table_id)
            ->sum('total_amount_paying');

        $in_pending = count(BartenderOrderDetail::where('table_id',$table_id)->where('status','!=',3)->where('status','!=',2)->where('status','!=',-1)->get());

        if ($in_pending < 1 && $item_total_amount >= $total_amount_paying) {
            Table::where('id',$table_id)->update(['etat' => 0,'waiter_name' => '','opened_by' => '','total_amount_paying' => 0]);
        }else {
            $total_amount_remaining = $total_amount_paying - $item_total_amount;
            Table::where('id',$table_id)->update(['total_amount_paying' => $total_amount_remaining]);
        }

        Facture::where('invoice_number', '=', $invoice_number)
            ->update(['etat' => '01','etat_recouvrement' => '0','montant_total_credit' => $item_total_amount,'statut_paied' => '0','client_id' => $client_id,'validated_by' => $this->user->name]);
        FactureDetail::where('invoice_number', '=', $invoice_number)
            ->update(['etat' => '01','etat_recouvrement' => '0','montant_total_credit' => $item_total_amount,'statut_paied' => '0','client_id' => $client_id,'validated_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'La Facture  est validée avec succés');
            return redirect()->route('admin.bartender-invoices.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function validerFactureBooking($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        try {DB::beginTransaction();

        $data = Facture::where('invoice_number',$invoice_number)->first();

        Facture::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => 1,'statut_paied' => '0','validated_by' => $this->user->name]);
        FactureDetail::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => 1,'statut_paied' => '0','validated_by' => $this->user->name]);
        BookingBooking::where('booking_no', '=', $data->booking_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
        BookingBookingDetail::where('booking_no', '=', $data->booking_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'La Facture  est validée avec succés');
            return redirect()->route('admin.booking-invoices.choose');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function validerFactureBookingCredit(Request  $request,$invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        $request->validate([
            'client_id' => 'required'
        ]);

        try {DB::beginTransaction();

        $client_id = $request->client_id;

        $data = Facture::where('invoice_number',$invoice_number)->first();

        $item_total_amount = DB::table('facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');

        Facture::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => '01','etat_recouvrement' => '0','montant_total_credit' => $item_total_amount,'statut_paied' => '0','client_id' => $client_id,'validated_by' => $this->user->name]);
        FactureDetail::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => '01','etat_recouvrement' => '0','montant_total_credit' => $item_total_amount,'statut_paied' => '0','client_id' => $client_id,'validated_by' => $this->user->name]);
        BookingBooking::where('booking_no', '=', $data->booking_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
        BookingBookingDetail::where('booking_no', '=', $data->booking_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'La Facture  est validée avec succés');
            return redirect()->route('admin.booking-invoices.choose');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

    public function validerFactureCuisine($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('invoice_kitchen.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        try {DB::beginTransaction();

        $datas = FactureDetail::where('invoice_number', $invoice_number)->get();
        $data = Facture::where('invoice_number', $invoice_number)->first();

        $table_id = FactureDetail::where('invoice_number', $invoice_number)->value('table_id');
        /*
        foreach($datas as $data){
            $valeurStockInitial = FoodStore::where('food_item_id', $data->food_item_id)->value('total_cump_value');
            $quantityStockInitial = FoodStore::where('food_item_id', $data->food_item_id)->value('quantity');
            $cump = FoodStore::where('food_item_id', $data->food_item_id)->value('cump');

            $quantityRestant = $quantityStockInitial - $data->item_quantity;
                      
                $reportData = array(
                    'food_item_id' => $data->food_item_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'quantity_sold' => $data->item_quantity,
                    'value_sold' => $data->item_quantity * $data->item_price,
                    'invoice_no' => $data->invoice_number,
                    'date' => $data->invoice_date,
                    'quantity_stock_final' => $quantityRestant,
                    'value_stock_final' => $quantityRestant * $data->item_price,
                    'commande_cuisine_no' => $data->food_order_no,
                    'type_transaction' => 'VENTE',
                    'document_no' => $data->invoice_number,
                    'created_by' => $this->user->name,
                    'employe_id' => $data->employe_id,
                    'origine_facture' => 'CUISINE',
                    'created_at' => \Carbon\Carbon::now()
                );
                $report[] = $reportData;
                
            $foods = FoodItemDetail::where('code', $data->foodItem->code)->get();
            $food = FoodItemDetail::where('code', $data->foodItem->code)->first();
            foreach($foods as $food){

                $quantityStockInitial = FoodSmallStoreDetail::where('food_id','!=', '')->where('food_id', $food->food_id)->value('quantity');

                $quantiteSortie = $data->item_quantity * $food->quantity;
                
                $quantityRestantBigStore = $quantityStockInitial - $quantiteSortie;
                $reportBigStore = array(
                    'food_id' => $food->food_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $quantityStockInitial * $food->food->purchase_price,
                    'invoice_no' => $data->invoice_number,
                    'date' => $data->invoice_date,
                    'quantity_stockout' => $quantiteSortie,
                    'value_stockout' => $quantiteSortie * $food->food->purchase_price,
                    'quantity_stock_final' => $quantityRestantBigStore,
                    'value_stock_final' => $quantityRestantBigStore * $food->food->purchase_price,
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportBigStoreData[] = $reportBigStore;

                    $bigStore = array(
                        'food_id' => $food->food_id,
                        'quantity' => $quantityRestantBigStore,
                        'total_purchase_value' => $quantityRestantBigStore * $food->food->purchase_price,
                        'total_cump_value' => $quantityRestantBigStore * $food->food->purchase_price,
                        'verified' => true,
                        'created_at' => \Carbon\Carbon::now()
                    );
                    
                    
                    if ($quantiteSortie <= $quantityStockInitial) {
                        
                        FoodSmallStoreDetail::where('food_id',$food->food_id)
                        ->update($bigStore);
                        $flag = 0;
                       
                    }else{

                        foreach($foods as $food){

                        $quantityStockInitial = FoodSmallStoreDetail::where('food_id','!=', '')->where('food_id', $food->food_id)->value('quantity');

                        $quantiteSortie = $data->item_quantity * $food->quantity;
                
                        $quantityTotal = $quantityStockInitial + $quantiteSortie;

                        $returnData = array(
                            'food_id' => $food->food_id,
                            'quantity' => $quantityTotal,
                            'total_purchase_value' => $quantityTotal * $food->food->purchase_price,
                            'total_cump_value' => $quantityTotal * $food->food->purchase_price,
                            'verified' => false,
                            'created_at' => \Carbon\Carbon::now()
                        );
                    
                        $status = FoodSmallStoreDetail::where('food_id','!=', '')->where('food_id', $food->food_id)->value('verified');
                        if ($status == true) {
                        
                            FoodSmallStoreDetail::where('food_id',$food->food_id)
                            ->update($returnData);
                            $flag = 1;
                         }
                     }
                        FoodSmallStoreDetail::where('food_id','!=','')->update(['verified' => false]);

                        session()->flash('error', 'Quantite des ingredients insuffisante au stock intermediaire des nourritures!('.$food->food->name.':'.$food->name.')');
                        return redirect()->back();
                    }
                    
                    
                    
            }
        }

        if (!empty($food->food_id) && $flag != 1) {
            FoodSmallReport::insert($reportBigStoreData);
        }

        */
        foreach($datas as $data){
                $orderData = array(
                    'confirmed_by' => $this->user->name,
                    'status' => 3,
                );

                OrderKitchen::where('order_no', '=', $data->food_order_no)
                    ->update($orderData);
                OrderKitchenDetail::where('order_no', '=', $data->food_order_no)
                    ->update($orderData);
        }

        $item_total_amount = DB::table('facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');

        $total_amount_paying = DB::table('tables')
            ->where('id', '=', $table_id)
            ->sum('total_amount_paying');

        $in_pending = count(OrderKitchenDetail::where('table_id',$table_id)->where('status','!=',3)->where('status','!=',2)->where('status','!=',-1)->get());

        if ($in_pending < 1 && $item_total_amount >= $total_amount_paying) {
            Table::where('id',$table_id)->update(['etat' => 0,'waiter_name' => '','opened_by' => '','total_amount_paying' => 0]);
        }else {
            $total_amount_remaining = $total_amount_paying - $item_total_amount;
            Table::where('id',$table_id)->update(['total_amount_paying' => $total_amount_remaining]);
        }
        
        FoodSmallStoreDetail::where('food_id','!=','')->update(['verified' => false]);

        Facture::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => 1,'statut_paied' => '0','validated_by' => $this->user->name]);
        FactureDetail::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => 1,'statut_paied' => '0','validated_by' => $this->user->name]);
        //FoodStoreReport::insert($report);

        DB::commit();
            session()->flash('success', 'La Facture  est validée avec succés');
            return redirect()->route('admin.invoice-kitchens.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function validerFactureCuisineCredit(Request  $request,$invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('invoice_kitchen.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        $request->validate([
            'client_id' => 'required'
        ]);

        try {DB::beginTransaction();

        $client_id = $request->client_id;

        $datas = FactureDetail::where('invoice_number', $invoice_number)->get();
        $data = Facture::where('invoice_number', $invoice_number)->first();

        $table_id = FactureDetail::where('invoice_number', $invoice_number)->value('table_id');
        /*
        foreach($datas as $data){
            $valeurStockInitial = FoodStore::where('food_item_id', $data->food_item_id)->value('total_cump_value');
            $quantityStockInitial = FoodStore::where('food_item_id', $data->food_item_id)->value('quantity');
            $cump = FoodStore::where('food_item_id', $data->food_item_id)->value('cump');

            $quantityRestant = $quantityStockInitial - $data->item_quantity;
                      
                $reportData = array(
                    'food_item_id' => $data->food_item_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'quantity_sold' => $data->item_quantity,
                    'value_sold' => $data->item_quantity * $data->item_price,
                    'invoice_no' => $data->invoice_number,
                    'date' => $data->invoice_date,
                    'quantity_stock_final' => $quantityRestant,
                    'value_stock_final' => $quantityRestant * $data->item_price,
                    'commande_cuisine_no' => $data->food_order_no,
                    'type_transaction' => 'VENTE',
                    'document_no' => $data->invoice_number,
                    'created_by' => $this->user->name,
                    'employe_id' => $data->employe_id,
                    'origine_facture' => 'CUISINE',
                    'created_at' => \Carbon\Carbon::now()
                );
                $report[] = $reportData;
                
            $foods = FoodItemDetail::where('code', $data->foodItem->code)->get();
            $food = FoodItemDetail::where('code', $data->foodItem->code)->first();
            foreach($foods as $food){

                $quantityStockInitial = FoodSmallStoreDetail::where('food_id','!=', '')->where('food_id', $food->food_id)->value('quantity');

                $quantiteSortie = $data->item_quantity * $food->quantity;
                
                $quantityRestantBigStore = $quantityStockInitial - $quantiteSortie;
                $reportBigStore = array(
                    'food_id' => $food->food_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $quantityStockInitial * $food->food->purchase_price,
                    'invoice_no' => $data->invoice_number,
                    'date' => $data->invoice_date,
                    'quantity_stockout' => $quantiteSortie,
                    'value_stockout' => $quantiteSortie * $food->food->purchase_price,
                    'quantity_stock_final' => $quantityRestantBigStore,
                    'value_stock_final' => $quantityRestantBigStore * $food->food->purchase_price,
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );
                $reportBigStoreData[] = $reportBigStore;

                    $bigStore = array(
                        'food_id' => $food->food_id,
                        'quantity' => $quantityRestantBigStore,
                        'total_purchase_value' => $quantityRestantBigStore * $food->food->purchase_price,
                        'total_cump_value' => $quantityRestantBigStore * $food->food->purchase_price,
                        'verified' => true,
                        'created_at' => \Carbon\Carbon::now()
                    );
                    
                    
                    if ($quantiteSortie <= $quantityStockInitial) {
                        
                        FoodSmallStoreDetail::where('food_id',$food->food_id)
                        ->update($bigStore);
                        $flag = 0;
                       
                    }else{

                        foreach($foods as $food){

                        $quantityStockInitial = FoodSmallStoreDetail::where('food_id','!=', '')->where('food_id', $food->food_id)->value('quantity');

                        $quantiteSortie = $data->item_quantity * $food->quantity;
                
                        $quantityTotal = $quantityStockInitial + $quantiteSortie;

                        $returnData = array(
                            'food_id' => $food->food_id,
                            'quantity' => $quantityTotal,
                            'total_purchase_value' => $quantityTotal * $food->food->purchase_price,
                            'total_cump_value' => $quantityTotal * $food->food->purchase_price,
                            'verified' => false,
                            'created_at' => \Carbon\Carbon::now()
                        );
                    
                        $status = FoodSmallStoreDetail::where('food_id','!=', '')->where('food_id', $food->food_id)->value('verified');
                        if ($status == true) {
                        
                            FoodSmallStoreDetail::where('food_id',$food->food_id)
                            ->update($returnData);
                            $flag = 1;
                         }
                     }
                        FoodSmallStoreDetail::where('food_id','!=','')->update(['verified' => false]);

                        session()->flash('error', 'Quantite des ingredients insuffisante au stock intermediaire des nourritures!('.$food->food->name.':'.$food->name.')');
                        return redirect()->back();
                    }
                    
                    
                    
            }
        }

        if (!empty($food->food_id) && $flag != 1) {
            FoodSmallReport::insert($reportBigStoreData);
        }

        */

        foreach($datas as $data){
                $orderData = array(
                    'confirmed_by' => $this->user->name,
                    'status' => 3,
                );

                OrderKitchen::where('order_no', '=', $data->food_order_no)
                    ->update($orderData);
                OrderKitchenDetail::where('order_no', '=', $data->food_order_no)
                    ->update($orderData);
        }

        $item_total_amount = DB::table('facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');

        $total_amount_paying = DB::table('tables')
            ->where('id', '=', $table_id)
            ->sum('total_amount_paying');

        $in_pending = count(OrderKitchenDetail::where('table_id',$table_id)->where('status','!=',3)->where('status','!=',2)->where('status','!=',-1)->get());

        if ($in_pending < 1 && $item_total_amount >= $total_amount_paying) {
            Table::where('id',$table_id)->update(['etat' => 0,'waiter_name' => '','opened_by' => '','total_amount_paying' => 0]);
        }else {
            $total_amount_remaining = $total_amount_paying - $item_total_amount;
            Table::where('id',$table_id)->update(['total_amount_paying' => $total_amount_remaining]);
        }
        
        FoodSmallStoreDetail::where('food_id','!=','')->update(['verified' => false]);

        $item_total_amount = DB::table('facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');
        
        Facture::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => '01','etat_recouvrement' => '0','montant_total_credit' => $item_total_amount,'statut_paied' => '0','client_id' => $client_id,'validated_by' => $this->user->name]);
        FactureDetail::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => '01','etat_recouvrement' => '0','montant_total_credit' => $item_total_amount,'statut_paied' => '0','client_id' => $client_id,'validated_by' => $this->user->name]);
        //FoodStoreReport::insert($report);

        DB::commit();
            session()->flash('success', 'La Facture  est validée avec succés');
            return redirect()->route('admin.invoice-kitchens.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function annulerFacture(Request $request,$invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any invoice !');
        }


        $request->validate([
            'cn_motif' => 'required|min:10|max:500'
        ]);

        try {DB::beginTransaction();

        $cn_motif = $request->cn_motif;

        $invoice_signature = Facture::where('invoice_number', $invoice_number)->value('invoice_signature');
        
            $email1 = 'ambazamarcellin2001@gmail.com';
            $email2 = 'frangiye@gmail.com';
            //$email3 = 'khaembamartin@gmail.com';
            $email4 = 'munyembari_mp@yahoo.fr';
            $auteur = $this->user->name;
            $mailData = [
                    'title' => 'Système de facturation électronique, Akiwacu',
                    'invoice_number' => $invoice_number,
                    'auteur' => $auteur,
                    'cn_motif' => $cn_motif,
                    ];
         
            Mail::to($email1)->send(new InvoiceResetedMail($mailData));
            Mail::to($email2)->send(new InvoiceResetedMail($mailData));
            //Mail::to($email3)->send(new InvoiceResetedMail($mailData));
            Mail::to($email4)->send(new InvoiceResetedMail($mailData));
            
        
            Facture::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => -1,'statut' => -1,'cn_motif' => $cn_motif,'reseted_by' => $this->user->name]);
            FactureDetail::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => -1,'statut' => -1,'cn_motif' => $cn_motif,'reseted_by' => $this->user->name]);
               
            DB::commit();
            session()->flash('success', 'La Facture  est annulée avec succés');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function validerAnnulerFacture($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any invoice !');
        }

        try {DB::beginTransaction();

        Facture::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => -1,'reseted_by' => $this->user->name]);
        FactureDetail::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => -1,'reseted_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'La Facture  est annulée avec succés');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    } 

    public function facture($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('invoice_booking.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        try {DB::beginTransaction();
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $datas = FactureDetail::where('invoice_number', $invoice_number)->get();
        $facture = Facture::where('invoice_number', $invoice_number)->first();
        $invoice_signature = Facture::where('invoice_number', $invoice_number)->value('invoice_signature');
        $data = Facture::where('invoice_number', $invoice_number)->first();
        $totalValue = DB::table('facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_price_nvat');
        $item_total_amount = DB::table('facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');
        $totalVat = DB::table('facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('vat');
        $client = Facture::where('invoice_number', $invoice_number)->value('customer_name');
        $date = Facture::where('invoice_number', $invoice_number)->value('invoice_date');
       
        $pdf = PDF::loadView('backend.pages.document.facture',compact('datas','invoice_number','totalValue','item_total_amount','client','setting','date','data','invoice_signature','facture','totalVat'))->setPaper('a6', 'portrait');

        Storage::put('public/factures/'.$invoice_number.'.pdf', $pdf->output());

        
        $factures = Facture::where('invoice_number', $invoice_number)->get();

        $datas = FactureDetail::where('invoice_number', $invoice_number)->get();

        Facture::where('invoice_number', '=', $invoice_number)
                ->update(['statut' => 1]);
            FactureDetail::where('invoice_number', '=', $invoice_number)
                ->update(['statut' => 1]);

            
            
            // download pdf file
        DB::commit();

        return view('backend.pages.document.facture',compact('datas','invoice_number','totalValue','item_total_amount','client','setting','date','data','invoice_signature','facture','totalVat'));
            

        //return $pdf->download('FACTURE_'.$invoice_number.'.pdf');

        /*
        $theUrl = config('app.guzzle_test_url').'/ebms_api/login/';
        $response = Http::post($theUrl, [
            'username'=> config('app.obr_test_username'),
            'password'=> config('app.obr_test_pwd')

        ]);
        $data =  json_decode($response);
        $data2 = ($data->result);
        
    
        $token = $data2->token;

        foreach($datas as $data){
            if (!empty($data->food_item_id)) {
                $invoice_items = array(
                'item_designation'=>$data->foodItem->name,
                'item_quantity'=>$data->item_quantity,
                'item_price'=>$data->item_price,
                'item_ct'=>$data->item_ct,
                'item_tl'=>$data->item_tl,
                'item_price_nvat'=>$data->item_price_nvat,
                'vat'=>$data->vat,
                'item_price_wvat'=>$data->item_price_wvat,
                'item_total_amount'=>$data->item_total_amount
                );

                $factureDetail[] = $invoice_items;
            }elseif(!empty($data->drink_id)){
                $invoice_items = array(
                'item_designation'=>$data->drink->name,
                'item_quantity'=>$data->item_quantity,
                'item_price'=>$data->item_price,
                'item_ct'=>$data->item_ct,
                'item_tl'=>$data->item_tl,
                'item_price_nvat'=>$data->item_price_nvat,
                'vat'=>$data->vat,
                'item_price_wvat'=>$data->item_price_wvat,
                'item_total_amount'=>$data->item_total_amount
                );

                $factureDetail[] = $invoice_items;
            }elseif(!empty($data->bartender_item_id)){
                $invoice_items = array(
                'item_designation'=>$data->bartenderItem->name,
                'item_quantity'=>$data->item_quantity,
                'item_price'=>$data->item_price,
                'item_ct'=>$data->item_ct,
                'item_tl'=>$data->item_tl,
                'item_price_nvat'=>$data->item_price_nvat,
                'vat'=>$data->vat,
                'item_price_wvat'=>$data->item_price_wvat,
                'item_total_amount'=>$data->item_total_amount
                );

                $factureDetail[] = $invoice_items;
            }elseif(!empty($data->barrist_item_id)){
                $invoice_items = array(
                'item_designation'=>$data->barristItem->name,
                'item_quantity'=>$data->item_quantity,
                'item_price'=>$data->item_price,
                'item_ct'=>$data->item_ct,
                'item_tl'=>$data->item_tl,
                'item_price_nvat'=>$data->item_price_nvat,
                'vat'=>$data->vat,
                'item_price_wvat'=>$data->item_price_wvat,
                'item_total_amount'=>$data->item_total_amount
                );

                $factureDetail[] = $invoice_items;
            }elseif(!empty($data->salle_id)){
                $invoice_items = array(
                'item_designation'=>$data->salle->name,
                'item_quantity'=>$data->item_quantity,
                'item_price'=>$data->item_price,
                'item_ct'=>$data->item_ct,
                'item_tl'=>$data->item_tl,
                'item_price_nvat'=>$data->item_price_nvat,
                'vat'=>$data->vat,
                'item_price_wvat'=>$data->item_price_wvat,
                'item_total_amount'=>$data->item_total_amount
                );

                $factureDetail[] = $invoice_items;
            }elseif(!empty($data->service_id)){
                $invoice_items = array(
                'item_designation'=>$data->service->name,
                'item_quantity'=>$data->item_quantity,
                'item_price'=>$data->item_price,
                'item_ct'=>$data->item_ct,
                'item_tl'=>$data->item_tl,
                'item_price_nvat'=>$data->item_price_nvat,
                'vat'=>$data->vat,
                'item_price_wvat'=>$data->item_price_wvat,
                'item_total_amount'=>$data->item_total_amount
                );

                $factureDetail[] = $invoice_items;
            }else{
                $invoice_items = array(
                'item_designation'=>$data->table->name,
                'item_quantity'=>$data->item_quantity,
                'item_price'=>$data->item_price,
                'item_ct'=>$data->item_ct,
                'item_tl'=>$data->item_tl,
                'item_price_nvat'=>$data->item_price_nvat,
                'vat'=>$data->vat,
                'item_price_wvat'=>$data->item_price_wvat,
                'item_total_amount'=>$data->item_total_amount
                );

                $factureDetail[] = $invoice_items;
            }

        }


        foreach($factures as $facture){
        $theUrl = config('app.guzzle_test_url').'/ebms_api/addInvoice';  
        $response = Http::withHeaders([
        'Authorization' => 'Bearer '.$token,
        'Accept' => 'application/json'])->post($theUrl, [
            'invoice_number'=>$facture->invoice_number,
            'invoice_date'=> $facture->invoice_date,
            'tp_type'=>$facture->tp_type,
            'tp_name'=>$facture->tp_name,
            'tp_TIN'=>$facture->tp_TIN,
            'tp_trade_number'=>$facture->tp_trade_number,
            'tp_phone_number'=>$facture->tp_phone_number,
            'tp_address_province'=>$facture->tp_address_province,
            'tp_address_commune'=>$facture->tp_address_commune,
            'tp_address_quartier'=>$facture->tp_address_quartier,
            'tp_address_avenue'=>$facture->tp_address_avenue,
            'tp_address_rue'=>$facture->tp_address_rue,
            'vat_taxpayer'=>$facture->vat_taxpayer,
            'ct_taxpayer'=>$facture->ct_taxpayer,
            'tl_taxpayer'=>$facture->tl_taxpayer,
            'tp_fiscal_center'=>$facture->tp_fiscal_center,
            'tp_activity_sector'=>$facture->tp_activity_sector,
            'tp_legal_form'=>$facture->tp_legal_form,
            'payment_type'=>$facture->payment_type,
            'customer_name'=>$facture->customer_name,
            'customer_TIN'=>$facture->customer_TIN,
            'customer_address'=>$facture->customer_address,
            'invoice_signature'=> $facture->invoice_signature,
            'invoice_currency'=> $facture->invoice_currency,
            'cancelled_invoice_ref'=> $facture->cancelled_invoice_ref,
            'cancelled_invoice'=> $facture->cancelled_invoice,
            'invoice_ref'=> $facture->invoice_ref,
            'invoice_signature_date'=> $facture->invoice_signature_date,
            'invoice_items' => $factureDetail,

        ]); 

        }

        $data =  json_decode($response);
        $done = $data->success;
        $msg = $data->msg;


        if ($done == true) {
            Facture::where('invoice_number', '=', $invoice_number)
                ->update(['statut' => 1]);
            FactureDetail::where('invoice_number', '=', $invoice_number)
                ->update(['statut' => 1]);

            // download pdf file
        return $pdf->download('FACTURE_'.$invoice_number.'.pdf');

        }else{
            return $response->json();
        }
        */
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

    public function factureBrouillon($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to print invoice !more information you have to contact Marcellin');
        }


        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $datas = FactureDetail::where('invoice_number', $invoice_number)->get();
        $facture = Facture::where('invoice_number', $invoice_number)->first();
        $invoice_signature = Facture::where('invoice_number', $invoice_number)->value('invoice_signature');
        $data = Facture::where('invoice_number', $invoice_number)->first();
        $totalValue = DB::table('facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_price_nvat');
        $item_total_amount = DB::table('facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');
        $totalVat = DB::table('facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('vat');
        $client = Facture::where('invoice_number', $invoice_number)->value('customer_name');
        $date = Facture::where('invoice_number', $invoice_number)->value('invoice_date');
        
        return view('backend.pages.document.facture',compact('datas','invoice_number','totalValue','item_total_amount','client','setting','date','data','invoice_signature','facture','totalVat'));
        
        //$pdf = PDF::loadView('backend.pages.document.facture',compact('datas','invoice_number','totalValue','item_total_amount','client','setting','date','data','invoice_signature','facture','totalVat'))->setPaper('a6', 'portrait');

            // download pdf file
        //return $pdf->download('FACTURE_'.$invoice_number.'.pdf');

    }

    public function voirFactureAnnuler($invoice_number){
        $facture = FactureDetail::where('invoice_number',$invoice_number)->first();
        $datas = FactureDetail::where('invoice_number', $invoice_number)->get();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();
        return view('backend.pages.invoice.reset', compact('facture','datas','clients'));
    }

    public function voirFactureCash($invoice_number){
        $facture = FactureDetail::where('invoice_number',$invoice_number)->first();
        $datas = FactureDetail::where('invoice_number', $invoice_number)->get();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();
        return view('backend.pages.invoice.cash', compact('facture','datas','clients'));
    }

    public function voirFactureCredit($invoice_number){
        $facture = FactureDetail::where('invoice_number',$invoice_number)->first();
        $datas = FactureDetail::where('invoice_number', $invoice_number)->get();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();
        return view('backend.pages.invoice.credit', compact('facture','datas','clients'));
    }

    public function getCancelInvoice($invoice_number){
        $facture = Facture::where('invoice_number',$invoice_number)->first();
        $datas = FactureDetail::where('invoice_number', $invoice_number)->get();
        return view('backend.pages.invoice.cancel', compact('facture','datas'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $invoice_number
     * @return \Illuminate\Http\Response
     */
    public function show($invoice_number)
    {
        //
        if (is_null($this->user) || !$this->user->can('invoice_drink.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factureDetails = FactureDetail::where('invoice_number',$invoice_number)->get();
        $facture = Facture::with('employe')->where('invoice_number',$invoice_number)->first();
        $total_amount = DB::table('facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');
        return view('backend.pages.invoice.show',compact('facture','factureDetails','total_amount'));
    }


     public function payerCredit(Request  $request,$invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('recouvrement.create')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        $request->validate([
            'client_id' => 'required',
            'statut_paied' => 'required',
            'etat_recouvrement' => 'required',
            'date_recouvrement' => 'required',
            'nom_recouvrement' => 'required',
            'note_recouvrement' => 'required',
            'montant_total_credit' => 'required',
            'montant_recouvre' => 'required',
        ]);

        try {DB::beginTransaction();

        $client_id = $request->client_id;
        $statut_paied = $request->statut_paied;
        $customer_address = $request->customer_address;
        $customer_TIN = $request->customer_TIN;
        $etat_recouvrement = $request->etat_recouvrement;
        $date_recouvrement = $request->date_recouvrement;
        $nom_recouvrement = $request->nom_recouvrement;
        $note_recouvrement = $request->note_recouvrement;
        $bank_name = $request->bank_name;
        $cheque_no = $request->cheque_no;
        $montant_total_credit = $request->montant_total_credit;
        $montant_recouvre_input = $request->montant_recouvre;

        $montant_recouvre = DB::table('factures')
            ->where('invoice_number',$invoice_number)->where('etat','01')->sum('montant_recouvre');

        if ($montant_total_credit >= $montant_recouvre_input) {

            $montant_total_recouvre = $montant_recouvre_input + $montant_recouvre;
            $reste_credit = $montant_total_credit - $montant_total_recouvre;

            if ($reste_credit == 0) {
                $etat_recouvrement = 2;
                Facture::where('invoice_number', '=', $invoice_number)
                    ->update([
                        'client_id' => $client_id,
                        'statut_paied' => $statut_paied,
                        'customer_address' => $customer_address,
                        'customer_TIN' => $customer_TIN,
                        'etat_recouvrement' => $etat_recouvrement,
                        'date_recouvrement' => $date_recouvrement,
                        'nom_recouvrement' => $nom_recouvrement,
                        'note_recouvrement' => $note_recouvrement,
                        'bank_name' => $bank_name,
                        'cheque_no' => $cheque_no,
                        'montant_total_credit' => $montant_total_credit,
                        'montant_recouvre' => $montant_total_recouvre,
                        'reste_credit' => $reste_credit,
                        'confirmed_by' => $this->user->name
                    ]);
                FactureDetail::where('invoice_number', '=', $invoice_number)
                    ->update([
                        'client_id' => $client_id,
                        'statut_paied' => $statut_paied,
                        'customer_address' => $customer_address,
                        'customer_TIN' => $customer_TIN,
                        'etat_recouvrement' => $etat_recouvrement,
                        'date_recouvrement' => $date_recouvrement,
                        'nom_recouvrement' => $nom_recouvrement,
                        'note_recouvrement' => $note_recouvrement,
                        'bank_name' => $bank_name,
                        'cheque_no' => $cheque_no,
                        'montant_total_credit' => $montant_total_credit,
                        'montant_recouvre' => $montant_total_recouvre,
                        'reste_credit' => $reste_credit,
                        'confirmed_by' => $this->user->name
                    ]);

                //session()->flash('success', 'Le credit  est payé avec succés');
                //return back();
            }elseif ($reste_credit < 0) {
                session()->flash('error', $this->user->name.' ,je vous prie de bien vouloir saisir les donnees exactes s\'il te plait! plus d\'info contacte IT Musumba Holding Marcellin ');
                return back();
            }
            else{
                $etat_recouvrement = 1;
                Facture::where('invoice_number', '=', $invoice_number)
                    ->update([
                        'client_id' => $client_id,
                        'statut_paied' => $statut_paied,
                        'customer_address' => $customer_address,
                        'customer_TIN' => $customer_TIN,
                        'etat_recouvrement' => $etat_recouvrement,
                        'date_recouvrement' => $date_recouvrement,
                        'nom_recouvrement' => $nom_recouvrement,
                        'note_recouvrement' => $note_recouvrement,
                        'bank_name' => $bank_name,
                        'cheque_no' => $cheque_no,
                        'montant_total_credit' => $montant_total_credit,
                        'montant_recouvre' => $montant_total_recouvre,
                        'reste_credit' => $reste_credit,
                        'confirmed_by' => $this->user->name
                    ]);
                FactureDetail::where('invoice_number', '=', $invoice_number)
                    ->update([
                        'client_id' => $client_id,
                        'statut_paied' => $statut_paied,
                        'customer_address' => $customer_address,
                        'customer_TIN' => $customer_TIN,
                        'etat_recouvrement' => $etat_recouvrement,
                        'date_recouvrement' => $date_recouvrement,
                        'nom_recouvrement' => $nom_recouvrement,
                        'note_recouvrement' => $note_recouvrement,
                        'bank_name' => $bank_name,
                        'cheque_no' => $cheque_no,
                        'montant_total_credit' => $montant_total_credit,
                        'montant_recouvre' => $montant_total_recouvre,
                        'reste_credit' => $reste_credit,
                        'confirmed_by' => $this->user->name
                    ]);

                //session()->flash('success', 'Le credit  est payé avec succés');
               // return redirect()->route('admin.credit-invoices.list');
            }
        }else{
            session()->flash('error', 'Le montant saisi doit etre inferieur ou egal au montant total de la facture');
            return redirect()->route('admin.credit-invoices.list');
        }

            DB::commit();
            session()->flash('success', 'Le credit  est payé avec succés');
            return redirect()->route('admin.credit-invoices.list');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $invoice_number
     * @return \Illuminate\Http\Response
     */

    public function edit($drink_order_no)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any invoice !');
        }

        $invoice_number = Facture::where('drink_order_no',$drink_order_no)->value('invoice_number');

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $drinks =  Drink::orderBy('name','asc')->get();
        $datas =  OrderDrinkDetail::where('order_no',$drink_order_no)->get();
        $drink_small_stores = DrinkSmallStore::all();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();

        $data =  Facture::where('invoice_number',$invoice_number)->first();
        return view('backend.pages.invoice.edit',compact('drinks','data','setting','datas','invoice_number','drink_small_stores','clients'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $invoice_number
     * @return \Illuminate\Http\Response
     */
    public function update(Request  $request,$invoice_number)
    {

        if (is_null($this->user) || !$this->user->can('invoice_drink.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any invoice !');
        }

        $rules = array(
                'invoice_date' => 'required',
                'tp_type' => 'required',
                'tp_name' => 'required|max:100|min:3',
                'tp_TIN' => 'required|max:30|min:4',
                'tp_trade_number' => 'required|max:20|min:4',
                'tp_phone_number' => 'required|max:20|min:6',
                'tp_address_commune' => 'required|max:50|min:5',
                'tp_address_quartier' => 'required|max:50|min:5',
                //'client_id' => 'required|max:100|min:3',
                //'customer_TIN' => 'required|max:30|min:4',
                //'customer_address' => 'required|max:100|min:5',
                //'invoice_signature' => 'required|max:90|min:10',
                //'invoice_signature_date' => 'required|max: |min:',
                'code_store' => 'required',
                'drink_id.*'  => 'required',
                'item_quantity.*'  => 'required',
                'item_ct.*'  => 'required',
                'item_tl.*'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $drink_id = $request->drink_id;
            $item_quantity = $request->item_quantity;
            $item_price = $request->item_price;
            $item_ct = $request->item_ct;
            $item_tl =$request->item_tl; 

            $employe_id = $request->employe_id;

        try {DB::beginTransaction();

        for( $count = 0; $count < count($drink_id); $count++ )
        {
            $taux_tva = Drink::where('id', $drink_id[$count])->value('vat');
            $brarudi_price = Drink::where('id', $drink_id[$count])->value('brarudi_price');

            if($request->vat_taxpayer == 1){

                if (!empty($brarudi_price) || $brarudi_price != 0) {
                    $d_prix_tva = $item_price[$count] - $brarudi_price;
                    $item_total_amount = ($item_price[$count]*$item_quantity[$count]);
                    $item_total_amount_brarudi = ($d_prix_tva*$item_quantity[$count]);
                    $item_price_nvat = ($item_total_amount_brarudi * 100)/110;
                    $vat = ($item_price_nvat * $taux_tva)/100;
                    $item_price_wvat = ($item_price_nvat + $vat); 
                }else{

                    $item_total_amount = ($item_price[$count]*$item_quantity[$count]);
                    
                    $item_price_nvat = ($item_total_amount* 100)/110;
                    $vat = ($item_price_nvat * $taux_tva)/100;
                    $item_price_wvat = ($item_price_nvat + $vat); 
                }

            }else{
                $item_price_nvat = ($item_price[$count]*$item_quantity[$count])+$item_ct[$count];
                $vat = 0;
                $item_price_wvat = ($item_price_nvat + $vat);
                $item_total_amount = $item_price_wvat + $item_tl[$count];
            }

          $data = array(
            'invoice_date'=> $request->invoice_date,
            'invoice_number'=> $invoice_number,
            'tp_type'=>$request->tp_type,
            'tp_name'=>$request->tp_name,
            'tp_TIN'=>$request->tp_TIN,
            'tp_trade_number'=>$request->tp_trade_number,
            'tp_phone_number'=>$request->tp_phone_number,
            'tp_address_province'=>$request->tp_address_province,
            'tp_address_commune'=>$request->tp_address_commune,
            'tp_address_quartier'=>$request->tp_address_quartier,
            'tp_address_avenue'=>$request->tp_address_avenue,
            'tp_address_rue'=>$request->tp_address_rue,
            'vat_taxpayer'=>$request->vat_taxpayer,
            'ct_taxpayer'=>$request->ct_taxpayer,
            'tl_taxpayer'=>$request->tl_taxpayer,
            'tp_fiscal_center'=>$request->tp_fiscal_center,
            'tp_activity_sector'=>$request->tp_activity_sector,
            'tp_legal_form'=>$request->tp_legal_form,
            'payment_type'=>$request->payment_type,
            'client_id'=>$request->client_id,
            'customer_TIN'=>$request->customer_TIN,
            'customer_address'=>$request->customer_address,
            'drink_order_no'=>$request->drink_order_no,
            'cancelled_invoice_ref'=>$request->cancelled_invoice_ref,
            'cancelled_invoice'=>$request->cancelled_invoice,
            'invoice_currency'=>$request->invoice_currency,
            'invoice_ref'=>$request->invoice_ref,
            'code_store'=>$request->code_store,
            'invoice_signature_date'=> Carbon::now(),
            'drink_id'=>$drink_id[$count],
            'item_quantity'=>$item_quantity[$count],
            'item_price'=>$item_price[$count],
            'item_ct'=>$item_ct[$count],
            'item_tl'=>$item_tl[$count],
            'item_price_nvat'=>$item_price_nvat,
            'vat'=>$vat,
            'item_price_wvat'=>$item_price_wvat,
            'item_total_amount'=>$item_total_amount,
            'employe_id'=> $employe_id,
        );

          $data1[] = $data;

          FactureDetail::where('invoice_number',$invoice_number)->delete();
      }

      FactureDetail::insert($data1);

            //create facture
            $facture = Facture::where('invoice_number',$invoice_number)->first();
            $facture->invoice_date = $request->invoice_date;
            $facture->invoice_date =  $request->invoice_date;
            $facture->tp_type = $request->tp_type;
            $facture->tp_name = $request->tp_name;
            $facture->tp_TIN = $request->tp_TIN;
            $facture->tp_trade_number = $request->tp_trade_number;
            $facture->tp_phone_number = $request->tp_phone_number;
            $facture->tp_address_province = $request->tp_address_province;
            $facture->tp_address_commune = $request->tp_address_commune;
            $facture->tp_address_quartier = $request->tp_address_quartier;
            $facture->drink_order_no = $request->drink_order_no;
            $facture->vat_taxpayer = $request->vat_taxpayer;
            $facture->ct_taxpayer = $request->ct_taxpayer;
            $facture->tl_taxpayer = $request->tl_taxpayer;
            $facture->tp_fiscal_center = $request->tp_fiscal_center;
            $facture->tp_activity_sector = $request->tp_activity_sector;
            $facture->tp_legal_form = $request->tp_legal_form;
            $facture->invoice_currency = $request->invoice_currency;
            $facture->payment_type = $request->payment_type;
            $facture->client_id = $request->client_id;
            $facture->customer_TIN = $request->customer_TIN;
            $facture->customer_address = $request->customer_address;
            $facture->cancelled_invoice_ref = $request->cancelled_invoice_ref;
            $facture->cancelled_invoice = $request->cancelled_invoice;
            $facture->invoice_ref = $request->invoice_ref;
            $facture->code_store = $request->code_store;
            $facture->employe_id = $employe_id;
            $facture->save();

            OrderDrink::where('order_no', '=', $facture->drink_order_no)
                ->update(['status' => 2,'confirmed_by' => $this->user->name]);
            OrderDrinkDetail::where('order_no', '=', $facture->drink_order_no)
                ->update(['status' => 2,'confirmed_by' => $this->user->name]);

            DB::commit();
            session()->flash('success', 'Le facture est modifié avec succés!!');
            return redirect()->route('ebms_api.invoices.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function rapportBoisson(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any report !');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        $datas = FactureDetail::select(
                        DB::raw('id,drink_id,invoice_number,invoice_date,item_quantity,item_price,vat,item_price_nvat,customer_name,drink_order_no,client_id,item_total_amount'))->where('drink_order_no','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','drink_id','invoice_date','invoice_number','item_quantity','item_price','vat','item_price_nvat','customer_name','drink_order_no','client_id','item_total_amount')->orderBy('invoice_number','asc')->get();
        $total_amount = DB::table('facture_details')->where('drink_order_no','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
        $total_vat = DB::table('facture_details')->where('drink_order_no','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
        $total_item_price_nvat = DB::table('facture_details')->where('drink_order_no','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');

        $credits = FactureDetail::select(
                        DB::raw('id,drink_id,invoice_number,invoice_date,item_quantity,item_price,vat,item_price_nvat,customer_name,drink_order_no,client_id,item_total_amount'))->where('drink_order_no','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','drink_id','invoice_date','invoice_number','item_quantity','item_price','vat','item_price_nvat','customer_name','drink_order_no','client_id','item_total_amount')->orderBy('invoice_number','asc')->get();
        $total_amount_credit = DB::table('facture_details')->where('drink_order_no','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
        $total_vat_credit = DB::table('facture_details')->where('drink_order_no','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
        $total_item_price_nvat_credit = DB::table('facture_details')->where('drink_order_no','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        //note de credit

        $note_credits = NoteCreditDetail::select(
                        DB::raw('id,drink_id,invoice_ref,invoice_date,item_quantity,item_price,vat,item_price_nvat,customer_name,invoice_number,client_id,item_total_amount'))->where('drink_id','!=','')->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','drink_id','invoice_date','invoice_ref','item_quantity','item_price','vat','item_price_nvat','customer_name','invoice_number','client_id','item_total_amount')->orderBy('invoice_number','asc')->get();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.rapport_facture_boisson',compact('datas','dateTime','setting','end_date','start_date','total_amount','total_amount_credit','credits','total_vat','total_item_price_nvat','total_vat_credit','total_item_price_nvat_credit','note_credits'))->setPaper('a4', 'landscape');
        /*
            $email1 = 'ambazamarcellin2001@gmail.com';
            $email2 = 'frankirakoze77@gmail.com';
            $mailData = [
                    'title' => 'Système de facturation électronique, edenSoft',
                    'email1' => $email1,
                    'email2' => $email2,
                    'total_amount' => $total_amount,
                    'total_amount_credit' => $total_amount_credit,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    ];
         
            Mail::to($email1)->send(new ReportDrinkMail($mailData));
            Mail::to($email2)->send(new ReportDrinkMail($mailData));
        */
        Storage::put('public/rapport_facture_boisson/'.$d1.'_'.$d2.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download("rapport_facture_boisson_".$dateTime.'.pdf');

        
    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $invoice_number
     * @return \Illuminate\Http\Response
     */
    public function destroy($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any invoice !');
        }

        try {DB::beginTransaction();

        $facture = Facture::where('invoice_number',$invoice_number)->first();
        if (!is_null($facture)) {
            $facture->delete();
            FactureDetail::where('invoice_number',$invoice_number)->delete();

            $email = 'ambazamarcellin2001@gmail.com';
            $auteur = $this->user->name;
            $mailData = [
                    'title' => 'Suppression de facture',
                    'email' => $email,
                    'invoice_number' => $invoice_number,
                    'auteur' => $auteur,
                    ];
         
            Mail::to($email)->send(new DeleteFactureMail($mailData));
        }

        DB::commit();
            session()->flash('success', 'La facture est supprimée !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }
    
}

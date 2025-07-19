<?php

namespace App\Http\Controllers\Backend\F;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use PDF;
use Excel;
use Mail;
use Validator;
use App\Models\F\FBill;
use App\Models\F\FBillDetail;
use App\Models\Setting;
use App\Models\F\FDrinkOrder;
use App\Models\F\FDrinkOrderDetail;
use App\Models\F\FFoodOrderDetail;
use App\Models\F\FFoodOrder;
use App\Models\F\FBarristaOrder;
use App\Models\F\FBarristaOrderDetail;
use App\Models\F\FBartenderOrder;
use App\Models\F\FBartenderOrderDetail;
use App\Models\Drink;
use App\Models\DrinkSmallStore;
use App\Models\FoodBigStoreDetail;
use App\Models\BarristItem;
use App\Models\FoodItem;
use App\Models\FoodItemDetail;
use App\Models\BartenderItem;
use App\Models\Employe;
use App\Models\F\FBooking;
use App\Models\F\FBookingDetail;
use App\Models\BookingSalle;
use App\Models\BookingRoom;
use App\Models\BookingService;
use App\Models\BookingEGRClient;
use App\Models\EGRClient;
use App\Models\F\FTable;
use App\Models\BookingTable;
use App\Models\KidnessSpace;
use App\Models\BreakFast;
use App\Models\SwimingPool;
use App\Exports\TurnoverExport;
use App\Mail\DeleteFactureMail;
use App\Mail\InvoiceResetedMail;
use App\Mail\ReportDrinkMail;

class FBillController extends Controller
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

    public function indexDrink()
    {
        if (is_null($this->user) || !$this->user->can('f_bill.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factures = FBill::where('drink_order_no','!=','')->take(1000)->orderBy('id','desc')->get();
        return view('backend.pages.f.bill.index_drink',compact('factures'));
    }

    public function indexFood()
    {
        if (is_null($this->user) || !$this->user->can('f_bill.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factures = FBill::where('food_order_no','!=','')->take(1000)->orderBy('id','desc')->get();
        return view('backend.pages.f.bill.index_food',compact('factures'));
    }

    public function indexBarrista()
    {
        if (is_null($this->user) || !$this->user->can('f_bill.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factures = FBill::where('barrist_order_no','!=','')->take(1000)->orderBy('id','desc')->get();
        return view('backend.pages.f.bill.index_barrista',compact('factures'));
    }

    public function indexBartender()
    {
        if (is_null($this->user) || !$this->user->can('f_bill.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factures = FBill::where('bartender_order_no','!=','')->take(500)->orderBy('id','desc')->get();
        return view('backend.pages.f.bill.index_bartender',compact('factures'));
    }

    public function indexBookingSalle()
    {
        if (is_null($this->user) || !$this->user->can('f_bill.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factures = FBillDetail::where('salle_id','!=','')->take(100)->orderBy('id','desc')->get();
        return view('backend.pages.f.bill.salle',compact('factures'));
    }

    public function indexBookingRoom()
    {
        if (is_null($this->user) || !$this->user->can('f_bill.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factures = FBillDetail::where('room_id','!=','')->take(100)->orderBy('id','desc')->get();
        return view('backend.pages.f.bill.room',compact('factures'));
    }

    public function indexBookingService()
    {
        if (is_null($this->user) || !$this->user->can('f_bill.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factures = FBillDetail::where('service_id','!=','')->take(100)->orderBy('id','desc')->get();
        return view('backend.pages.f.bill.service',compact('factures'));
    }

    public function indexBookingKidnessSpace()
    {
        if (is_null($this->user) || !$this->user->can('f_bill.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factures = FBillDetail::where('kidness_space_id','!=','')->take(100)->orderBy('id','desc')->get();
        return view('backend.pages.f.bill.kidness_space',compact('factures'));
    }

    public function indexBookingSwimingPool()
    {
        if (is_null($this->user) || !$this->user->can('f_bill.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factures = FBillDetail::where('swiming_pool_id','!=','')->take(100)->orderBy('id','desc')->get();
        return view('backend.pages.f.bill.swiming_pool',compact('factures'));
    }

    public function choose()
    {
        if (is_null($this->user) || !$this->user->can('f_bill.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        return view('backend.pages.f.bill.choose');
    }


    public function createDrink($order_no)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $drinks =  Drink::orderBy('name','asc')->get();
        $drink_small_stores =  DrinkSmallStore::orderBy('name','asc')->get();
        $orders =  FDrinkOrderDetail::where('order_no',$order_no)->orderBy('id','desc')->get();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();

        $table_id = FDrinkOrder::where('order_no',$order_no)->value('table_id');

        $data =  FDrinkOrderDetail::where('order_no',$order_no)->orderBy('id','desc')->first();
        $total_amount = DB::table('f_drink_order_details')
            ->where('order_no',$order_no)
            ->sum('total_amount_selling');

        return view('backend.pages.f.bill.create_drink',compact('drinks','data','setting','orders','order_no','clients','drink_small_stores','table_id','total_amount'));
    }

    public function createByTableDrink($table_id)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $drinks =  Drink::orderBy('name','asc')->get();
        $drink_small_stores =  DrinkSmallStore::orderBy('name','asc')->get();
        $orders =  FDrinkOrderDetail::where('table_id',$table_id)->where('status',1)->orderBy('id','desc')->get();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();

        $data =  FDrinkOrderDetail::where('table_id',$table_id)->where('status',1)->orderBy('id','desc')->first();
        $total_amount = DB::table('f_drink_order_details')
            ->where('table_id',$table_id)->where('status',1)
            ->sum('total_amount_selling');

        return view('backend.pages.f.bill.create_drink',compact('drinks','data','setting','orders','table_id','drink_small_stores','clients','total_amount'));
    }

    public function createFood($order_no)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $food_items =  FoodItem::orderBy('name','asc')->get();
        $orders =  FFoodOrderDetail::where('order_no',$order_no)->orderBy('id','desc')->get();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();
        $data =  FFoodOrder::where('order_no',$order_no)->first();
        $table_id = FFoodOrder::where('order_no',$order_no)->value('table_id');

        $total_amount = DB::table('f_food_order_details')
            ->where('order_no', '=', $order_no)
            ->sum('total_amount_selling');

        return view('backend.pages.f.bill.create_food',compact('food_items','data','setting','orders','order_no','clients','table_id','total_amount'));
    }

    public function createByTableFood($table_id)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $food_items =  FoodItem::orderBy('name','asc')->get();
        $orders =  FFoodOrderDetail::where('table_id',$table_id)->where('status',1)->orderBy('id','desc')->get();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();
        $data =  FFoodOrderDetail::where('table_id',$table_id)->where('status',1)->orderBy('id','desc')->first();

        $total_amount = DB::table('f_food_order_details')
            ->where('table_id',$table_id)->where('status',1)
            ->sum('total_amount_selling');

        return view('backend.pages.f.bill.create_food',compact('food_items','data','setting','orders','clients','table_id','total_amount'));
    }

    public function createBarrista($order_no)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $barrist_items =  BarristItem::orderBy('name','asc')->get();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();
        $orders =  FBarristaOrderDetail::where('order_no',$order_no)->orderBy('order_no','asc')->get();

        $data =  FBarristaOrder::where('order_no',$order_no)->first();
        $table_id = FBarristaOrder::where('order_no',$order_no)->value('table_id');
        $total_amount = DB::table('f_barrista_order_details')
            ->where('order_no',$order_no)
            ->sum('total_amount_selling');

        return view('backend.pages.f.bill.create_barrista',compact('barrist_items','data','setting','orders','order_no','clients','table_id','total_amount'));
    }

    public function createByTableBarrista($table_id)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $barrist_items =  BarristItem::orderBy('name','asc')->get();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();
        $orders =  FBarristaOrderDetail::where('table_id',$table_id)->where('status',1)->orderBy('id','desc')->get();

        $data =  FBarristaOrderDetail::where('table_id',$table_id)->where('status',1)->orderBy('id','desc')->first();
        $total_amount = DB::table('f_barrista_order_details')
            ->where('table_id',$table_id)->where('status',1)
            ->sum('total_amount_selling');

        return view('backend.pages.f.bill.create_barrista',compact('barrist_items','data','setting','orders','table_id','clients','total_amount'));
    }

    public function createBartender($order_no)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $bartender_items =  BartenderItem::orderBy('name','asc')->get();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();
        $orders =  FBartenderOrderDetail::where('order_no',$order_no)->orderBy('order_no','asc')->get();

        $data =  FBartenderOrder::where('order_no',$order_no)->first();
        $table_id = FBartenderOrder::where('order_no',$order_no)->value('table_id');
        $total_amount = DB::table('f_bartender_order_details')
            ->where('order_no',$order_no)
            ->sum('total_amount_selling');

        return view('backend.pages.f.bill.create_bartender',compact('bartender_items','data','setting','orders','order_no','clients','table_id','total_amount'));
    }

    public function createByTableBartender($table_id)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $bartender_items =  BartenderItem::orderBy('name','asc')->get();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();
        $orders =  FBartenderOrderDetail::where('table_id',$table_id)->where('status',1)->orderBy('id','desc')->get();

        $data =  FBartenderOrderDetail::where('table_id',$table_id)->where('status',1)->orderBy('id','desc')->first();
        $total_amount = DB::table('f_bartender_order_details')
            ->where('table_id',$table_id)->where('status',1)
            ->sum('total_amount_selling');

        return view('backend.pages.f.bill.create_bartender',compact('bartender_items','data','setting','orders','table_id','clients','total_amount'));
    }

    public function createBooking($booking_no)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $bookings =  FBookingDetail::where('booking_no',$booking_no)->orderBy('id','asc')->get();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();

        $data =  FBooking::where('booking_no',$booking_no)->first();
        return view('backend.pages.f.bill.create_booking',compact('bookings','data','setting','booking_no','clients'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeDrink(Request  $request)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.create')) {
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
                'client_id' => 'required',
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
            

               $invoice_number = date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6); 
            
            $invoice_date = Carbon::now();
            

            $invoice_signature = $request->tp_TIN."/".config('app.obr_test_username')."/".Carbon::parse($invoice_date)->format('YmdHis')."/".$invoice_number;

        $client = EGRClient::where('id',$request->client_id)->first();

        for( $count = 0; $count < count($drink_id); $count++ )
        {
            $taux_tva = Drink::where('id', $drink_id[$count])->value('vat');
            $brarudi_price = Drink::where('id', $drink_id[$count])->value('brarudi_price');
            $cump = Drink::where('id', $drink_id[$count])->value('cump');

            if($request->vat_taxpayer == 1){

                if ($request->tl_taxpayer == 1) {
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

                    if ($taux_tva <= 0) {
                        $item_price_nvat = ($item_price[$count]*$item_quantity[$count])+$item_ct[$count];
                        $vat = 0;
                        $item_price_wvat = ($item_price_nvat + $vat);
                        $item_total_amount = $item_price_wvat + $item_tl[$count];
                    }else{
                        $item_total_amount = ($item_price[$count]*$item_quantity[$count]);
                    
                        $item_price_nvat = ($item_total_amount* 100)/110;
                        $vat = ($item_price_nvat * $taux_tva)/100;
                        $item_price_wvat = ($item_price_nvat + $vat); 
                    }
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
            'invoice_type'=>$request->invoice_type,
            'client_id'=>$request->client_id,
            'customer_TIN'=>$client->customer_TIN,
            'customer_address'=>$client->customer_address,
            'vat_customer_payer'=>$client->vat_customer_payer,
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

        FBillDetail::insert($data1);

            $order_no = FBillDetail::where('invoice_number',$invoice_number)->value('drink_order_no');

            //create facture
            $facture = new FBill();
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
            $facture->invoice_type = $request->invoice_type;
            $facture->client_id = $request->client_id;
            $facture->customer_TIN = $client->customer_TIN;
            $facture->customer_address = $client->customer_address;
            $facture->vat_customer_payer = $client->vat_customer_payer;
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

                FDrinkOrder::where('order_no', '=', $request->drink_order_no[$count])
                    ->update($orderData);
                FDrinkOrderDetail::where('order_no', '=', $request->drink_order_no[$count])
                    ->update($orderData);
            }


            DB::commit();
            session()->flash('success', 'Le vente est fait avec succés!!');
            return redirect()->route('admin.f-drink-bills.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function storeBarrista(Request  $request)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.create')) {
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
                'client_id' => 'required',
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

            $invoice_number = date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6); 
            


            $invoice_date = Carbon::now();

            $invoice_signature = $request->tp_TIN."/".config('app.obr_test_username')."/".Carbon::parse($invoice_date)->format('YmdHis')."/".$invoice_number;

            $client = EGRClient::where('id',$request->client_id)->first();

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
            'invoice_type'=>$request->invoice_type,
            'client_id'=>$request->client_id,
            'customer_TIN'=>$client->customer_TIN,
            'customer_address'=>$client->customer_address,
            'vat_customer_payer'=>$client->vat_customer_payer,
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


        FBillDetail::insert($data1);

        $order_no = FBillDetail::where('invoice_number',$invoice_number)->value('barrist_order_no');

            //create facture
            $facture = new FBill();
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
            $facture->invoice_type = $request->invoice_type;
            $facture->client_id = $request->client_id;
            $facture->customer_TIN = $client->customer_TIN;
            $facture->customer_address = $client->customer_address;
            $facture->vat_customer_payer = $client->vat_customer_payer;
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

                FBarristaOrder::where('order_no', '=', $request->barrist_order_no[$count])
                    ->update($orderData);
                FBarristaOrderDetail::where('order_no', '=', $request->barrist_order_no[$count])
                    ->update($orderData);
            }

        DB::commit();
            session()->flash('success', 'Le vente est fait avec succés!!');
            return redirect()->route('admin.f-barrista-bills.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function storeFood(Request  $request)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.create')) {
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
                'client_id' => 'required',
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
            
            $invoice_number = date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6); 
            


            $invoice_date = Carbon::now();

            $invoice_signature = $request->tp_TIN."/".config('app.obr_test_username')."/".Carbon::parse($invoice_date)->format('YmdHis')."/".$invoice_number;

            $client = EGRClient::where('id',$request->client_id)->first();

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

            $code = FoodItem::where('id', $food_item_id[$count])->value('code');
            $foods = FoodItemDetail::where('code', $code)->get();
            $cumpData = [];
            foreach($foods as $food){
                $foodM = FoodBigStoreDetail::where('food_id','!=', '')->where('food_id', $food->food_id)->first();
                $cumpMediumStore = DB::table('food_big_store_details')->where('food_id','!=', '')->where('food_id', $food->food_id)->value('cump');
                $cumpSmallStore = ($cumpMediumStore / $foodM->food->foodMeasurement->equivalent) * $foodM->food->foodMeasurement->sub_equivalent;
                $cump = array(
                    'cump' => $cumpSmallStore,
                );
                $cumpData[] = $cump;
            }

            $cmp1 = collect($cumpData)->sum('cump');
            $inflation = $cmp1 * 15/100;
            $miscelius = $cmp1 * 5/100;
            $cmp = $cmp1 + $inflation + $miscelius;

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
            'invoice_type'=>$request->invoice_type,
            'client_id'=>$request->client_id,
            'customer_TIN'=>$client->customer_TIN,
            'customer_address'=>$client->customer_address,
            'vat_customer_payer'=>$client->vat_customer_payer,
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
            'cump'=> $cmp,
            'employe_id'=> $employe_id,
            'created_at'=> Carbon::now(),
        );
          $data1[] = $data;
      }


        FBillDetail::insert($data1);

        $order_no = FBillDetail::where('invoice_number',$invoice_number)->value('food_order_no');
            //create facture
            $facture = new FBill();
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
            $facture->invoice_type = $request->invoice_type;
            $facture->client_id = $request->client_id;
            $facture->customer_TIN = $client->customer_TIN;
            $facture->customer_address = $client->customer_address;
            $facture->vat_customer_payer = $client->vat_customer_payer;
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

                FFoodOrder::where('order_no', '=', $request->food_order_no[$count])
                    ->update($orderData);
                FFoodOrderDetail::where('order_no', '=', $request->food_order_no[$count])
                    ->update($orderData);
            }

            DB::commit();
            session()->flash('success', 'Le vente est fait avec succés!!');
            return redirect()->route('admin.f-food-bills.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function storeBartender(Request  $request)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.create')) {
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
                'client_id' => 'required',
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

            $invoice_number = date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6); 

            $invoice_date = Carbon::now();
            

            $invoice_signature = $request->tp_TIN."/".config('app.obr_test_username')."/".Carbon::parse($invoice_date)->format('YmdHis')."/".$invoice_number;

            $client = EGRClient::where('id',$request->client_id)->first();

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
            'invoice_type'=>$request->invoice_type,
            'client_id'=>$request->client_id,
            'customer_TIN'=>$client->customer_TIN,
            'customer_address'=>$client->customer_address,
            'vat_customer_payer'=>$client->vat_customer_payer,
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


        FBillDetail::insert($data1);

        $order_no = FBillDetail::where('invoice_number',$invoice_number)->value('bartender_order_no');
            //create facture
            $facture = new FBill();
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
            $facture->invoice_type = $request->invoice_type;
            $facture->client_id = $request->client_id;
            $facture->customer_TIN = $client->customer_TIN;
            $facture->customer_address = $client->customer_address;
            $facture->vat_customer_payer = $client->vat_customer_payer;
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

                FBartenderOrder::where('order_no', '=', $request->bartender_order_no[$count])
                    ->update($orderData);
                FBartenderOrderDetail::where('order_no', '=', $request->bartender_order_no[$count])
                    ->update($orderData);
            }

            DB::commit();
            session()->flash('success', 'Le vente est fait avec succés!!');
            return redirect()->route('admin.f-bartender-bills.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function storeBooking(Request  $request)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.create')) {
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
                'client_id' => 'required',
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
            $room_id = $request->room_id;
            $item_quantity = $request->item_quantity;
            $item_price = $request->item_price;
            $item_ct = $request->item_ct;
            $item_tl =$request->item_tl; 

            $employe_id = $request->employe_id;
            
            $invoice_number = date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6); 
            

            $invoice_date = Carbon::now();
            

            $invoice_signature = $request->tp_TIN."/".config('app.obr_test_username')."/".Carbon::parse($invoice_date)->format('YmdHis')."/".$invoice_number;

            $client = EGRClient::where('id',$request->client_id)->first();

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
            'invoice_type'=>$request->invoice_type,
            'client_id'=>$request->client_id,
            'customer_TIN'=>$client->customer_TIN,
            'customer_address'=>$client->customer_address,
            'vat_customer_payer'=>$client->vat_customer_payer,
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


        FBillDetail::insert($data1);


            //create facture
            $facture = new FBill();
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
            $facture->invoice_type = $request->invoice_type;
            $facture->client_id = $request->client_id;
            $facture->customer_TIN = $client->customer_TIN;
            $facture->customer_address = $client->customer_address;
            $facture->vat_customer_payer = $client->vat_customer_payer;
            $facture->invoice_signature = $invoice_signature;
            $facture->cancelled_invoice_ref = $request->cancelled_invoice_ref;
            $facture->cancelled_invoice = $request->cancelled_invoice;
            $facture->invoice_ref = $request->invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->created_at = Carbon::now();
            $facture->save();

            FBooking::where('booking_no', '=', $request->booking_no)
                ->update(['status' => 2]);
            FBookingDetail::where('booking_no', '=', $request->booking_no)
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
            'invoice_type'=>$request->invoice_type,
            'client_id'=>$request->client_id,
            'customer_TIN'=>$client->customer_TIN,
            'customer_address'=>$client->customer_address,
            'vat_customer_payer'=>$client->vat_customer_payer,
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


        FBillDetail::insert($data1);


            //create facture
            $facture = new FBill();
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
            $facture->invoice_type = $request->invoice_type;
            $facture->client_id = $request->client_id;
            $facture->customer_TIN = $client->customer_TIN;
            $facture->customer_address = $client->customer_address;
            $facture->vat_customer_payer = $client->vat_customer_payer;
            $facture->invoice_signature = $invoice_signature;
            $facture->cancelled_invoice_ref = $request->cancelled_invoice_ref;
            $facture->cancelled_invoice = $request->cancelled_invoice;
            $facture->invoice_ref = $request->invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->created_at = Carbon::now();
            $facture->save();

            FBooking::where('booking_no', '=', $request->booking_no)
                ->update(['status' => 2]);
            FBookingDetail::where('booking_no', '=', $request->booking_no)
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
            'invoice_type'=>$request->invoice_type,
            'client_id'=>$request->client_id,
            'customer_TIN'=>$client->customer_TIN,
            'customer_address'=>$client->customer_address,
            'vat_customer_payer'=>$client->vat_customer_payer,
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


        FBillDetail::insert($data1);


            //create facture
            $facture = new FBill();
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
            $facture->invoice_type = $request->invoice_type;
            $facture->client_id = $request->client_id;
            $facture->customer_TIN = $client->customer_TIN;
            $facture->customer_address = $client->customer_address;
            $facture->vat_customer_payer = $client->vat_customer_payer;
            $facture->invoice_signature = $invoice_signature;
            $facture->cancelled_invoice_ref = $request->cancelled_invoice_ref;
            $facture->cancelled_invoice = $request->cancelled_invoice;
            $facture->invoice_ref = $request->invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->created_at = Carbon::now();
            $facture->save();

            FBooking::where('booking_no', '=', $request->booking_no)
                ->update(['status' => 2]);
            FBookingDetail::where('booking_no', '=', $request->booking_no)
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
            'invoice_type'=>$request->invoice_type,
            'client_id'=>$request->client_id,
            'customer_TIN'=>$client->customer_TIN,
            'customer_address'=>$client->customer_address,
            'vat_customer_payer'=>$client->vat_customer_payer,
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


        FBillDetail::insert($data1);


            //create facture
            $facture = new FBill();
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
            $facture->invoice_type = $request->invoice_type;
            $facture->client_id = $request->client_id;
            $facture->customer_TIN = $client->customer_TIN;
            $facture->customer_address = $client->customer_address;
            $facture->vat_customer_payer = $client->vat_customer_payer;
            $facture->invoice_signature = $invoice_signature;
            $facture->cancelled_invoice_ref = $request->cancelled_invoice_ref;
            $facture->cancelled_invoice = $request->cancelled_invoice;
            $facture->invoice_ref = $request->invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->created_at = Carbon::now();
            $facture->save();

            FBooking::where('booking_no', '=', $request->booking_no)
                ->update(['status' => 2]);
            FBookingDetail::where('booking_no', '=', $request->booking_no)
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
            'invoice_type'=>$request->invoice_type,
            'client_id'=>$request->client_id,
            'customer_TIN'=>$client->customer_TIN,
            'customer_address'=>$client->customer_address,
            'vat_customer_payer'=>$client->vat_customer_payer,
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


        FBillDetail::insert($data1);


            //create facture
            $facture = new FBill();
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
            $facture->invoice_type = $request->invoice_type;
            $facture->client_id = $request->client_id;
            $facture->customer_TIN = $client->customer_TIN;
            $facture->customer_address = $client->customer_address;
            $facture->vat_customer_payer = $client->vat_customer_payer;
            $facture->invoice_signature = $invoice_signature;
            $facture->cancelled_invoice_ref = $request->cancelled_invoice_ref;
            $facture->cancelled_invoice = $request->cancelled_invoice;
            $facture->invoice_ref = $request->invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->created_at = Carbon::now();
            $facture->save();

            FBooking::where('booking_no', '=', $request->booking_no)
                ->update(['status' => 2]);
            FBookingDetail::where('booking_no', '=', $request->booking_no)
                ->update(['status' => 2]);

        }elseif(!empty($room_id)){
            for( $count = 0; $count < count($room_id); $count++ )
        {
            $taux_tva = BookingRoom::where('id', $room_id[$count])->value('vat');
            $item_tsce_tax = BookingRoom::where('id', $room_id[$count])->value('item_tsce_tax');

            if($request->vat_taxpayer == 1){
                if ($request->ct_taxpayer == 1) {
                    $item_total_amount = ($item_price[$count]*$item_quantity[$count]);
                    $item_price_nvat1 = ($item_total_amount * 100)/110;
                    $item_price_nvat = ($item_price_nvat1 * 100)/105;
                    $item_tsce_tax = ($item_price_nvat * $item_tsce_tax)/100;
                    $vat = ($item_price_nvat1 * $taux_tva)/100;
                    $item_price_wvat = ($item_price_nvat + $vat + $item_tsce_tax);
                    $item_total_amount = ($item_price_nvat + $vat + $item_tsce_tax);
                }else{
                    $item_total_amount = ($item_price[$count]*$item_quantity[$count]);
                    $item_price_nvat = ($item_total_amount * 100)/110;
                    $vat = ($item_price_nvat * $taux_tva)/100;
                    $item_price_wvat = ($item_price_nvat + $vat);
                }
            }else{
                $item_price_nvat = ($item_price[$count]*$item_quantity[$count])+$item_ct[$count];
                $vat = 0;
                $item_price_wvat = ($item_price_nvat + $vat);
                $item_total_amount = $item_price_wvat + $item_tl[$count];
                $item_tsce_tax = ($item_total_amount * $taux_tva)/100;
                $item_total_amount = ($item_price_nvat + $vat + $item_tsce_tax);
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
            'invoice_type'=>$request->invoice_type,
            'client_id'=>$request->client_id,
            'customer_TIN'=>$client->customer_TIN,
            'customer_address'=>$client->customer_address,
            'vat_customer_payer'=>$client->vat_customer_payer,
            'invoice_signature'=> $invoice_signature,
            'booking_no'=>$request->booking_no,
            'cancelled_invoice_ref'=>$request->cancelled_invoice_ref,
            'cancelled_invoice'=>$request->cancelled_invoice,
            'invoice_currency'=>$request->invoice_currency,
            'auteur' => $this->user->name,
            'invoice_ref'=>$request->invoice_ref,
            'invoice_signature_date'=> Carbon::now(),
            'room_id'=>$room_id[$count],
            'item_quantity'=>$item_quantity[$count],
            'item_price'=>$item_price[$count],
            'item_ct'=> $item_tsce_tax,
            'item_tl'=>$item_tl[$count],
            'item_tsce_tax'=> 0,
            'item_price_nvat'=>$item_price_nvat,
            'vat'=>$vat,
            'item_price_wvat'=>$item_price_wvat,
            'item_total_amount'=>$item_total_amount,
            'employe_id'=> $employe_id,
            'created_at'=> Carbon::now(),
        );
          $data1[] = $data;
      }


        FBillDetail::insert($data1);


            //create facture
            $facture = new FBill();
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
            $facture->invoice_type = $request->invoice_type;
            $facture->client_id = $request->client_id;
            $facture->customer_TIN = $client->customer_TIN;
            $facture->customer_address = $client->customer_address;
            $facture->vat_customer_payer = $client->vat_customer_payer;
            $facture->invoice_signature = $invoice_signature;
            $facture->cancelled_invoice_ref = $request->cancelled_invoice_ref;
            $facture->cancelled_invoice = $request->cancelled_invoice;
            $facture->invoice_ref = $request->invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->created_at = Carbon::now();
            $facture->save();

            FBooking::where('booking_no', '=', $request->booking_no)
                ->update(['status' => 2]);
            FBookingDetail::where('booking_no', '=', $request->booking_no)
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
            'invoice_type'=>$request->invoice_type,
            'client_id'=>$request->client_id,
            'customer_TIN'=>$client->customer_TIN,
            'customer_address'=>$client->customer_address,
            'vat_customer_payer'=>$client->vat_customer_payer,
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


        FBillDetail::insert($data1);


            //create facture
            $facture = new FBill();
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
            $facture->invoice_type = $request->invoice_type;
            $facture->client_id = $request->client_id;
            $facture->customer_TIN = $client->customer_TIN;
            $facture->customer_address = $client->customer_address;
            $facture->vat_customer_payer = $client->vat_customer_payer;
            $facture->invoice_signature = $invoice_signature;
            $facture->cancelled_invoice_ref = $request->cancelled_invoice_ref;
            $facture->cancelled_invoice = $request->cancelled_invoice;
            $facture->invoice_ref = $request->invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->created_at = Carbon::now();
            $facture->save();

            FBooking::where('booking_no', '=', $request->booking_no)
                ->update(['status' => 2]);
            FBookingDetail::where('booking_no', '=', $request->booking_no)
                ->update(['status' => 2]);

        }

        DB::commit();
            session()->flash('success', 'Le vente est fait avec succés!!');
            return redirect()->route('admin.f-booking-bills.choose');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function validerFactureBoisson($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        try {DB::beginTransaction();

        $datas = FBillDetail::where('invoice_number', $invoice_number)->get();

        $table_id = FBillDetail::where('invoice_number', $invoice_number)->value('table_id');

        foreach($datas as $data){
                $orderData = array(
                    'confirmed_by' => $this->user->name,
                    'status' => 3,
                );

                FDrinkOrder::where('order_no', '=', $data->drink_order_no)
                    ->update($orderData);
                FDrinkOrderDetail::where('order_no', '=', $data->drink_order_no)
                    ->update($orderData);
        }

        $item_total_amount = DB::table('f_bill_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');

        $total_amount_paying = DB::table('f_tables')
            ->where('id', '=', $table_id)
            ->sum('total_amount_paying');

        $in_pending = count(FDrinkOrderDetail::where('table_id',$table_id)->where('status','!=',3)->where('status','!=',2)->where('status','!=',-1)->get());

        if ($in_pending < 1 && $item_total_amount >= $total_amount_paying) {
            FTable::where('id',$table_id)->update(['etat' => 0,'waiter_name' => '','opened_by' => '','total_amount_paying' => 0]);
        }else {
            $total_amount_remaining = $total_amount_paying - $item_total_amount;
            FTable::where('id',$table_id)->update(['total_amount_paying' => $total_amount_remaining]);
        }
        
        FBill::where('invoice_number', '=', $invoice_number)
            ->update(['etat' => 1,'statut_paied' => '0','validated_by' => $this->user->name]);
        FBillDetail::where('invoice_number', '=', $invoice_number)
            ->update(['etat' => 1,'statut_paied' => '0','validated_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'La Facture  est validée avec succés');
            return redirect()->route('admin.f-drink-bills.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }


    }

    public function validerFactureBoissonCredit(Request  $request,$invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        $request->validate([
            'client_id' => 'required'
        ]);

        try {DB::beginTransaction();

        $client_id = $request->client_id;

        $datas = FBillDetail::where('invoice_number', $invoice_number)->get();
        $table_id = FBillDetail::where('invoice_number', $invoice_number)->value('table_id');

        foreach($datas as $data){
                $orderData = array(
                    'confirmed_by' => $this->user->name,
                    'status' => 3,
                );

                FDrinkOrder::where('order_no', '=', $data->drink_order_no)
                    ->update($orderData);
                FDrinkOrderDetail::where('order_no', '=', $data->drink_order_no)
                    ->update($orderData);
        }

        $item_total_amount = DB::table('f_bill_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');
            
        $total_amount_paying = DB::table('f_tables')
            ->where('id', '=', $table_id)
            ->sum('total_amount_paying');
        
        $in_pending = count(OrderDrinkDetail::where('table_id',$table_id)->where('status','!=',3)->where('status','!=',2)->where('status','!=',-1)->get());

        if ($in_pending < 1 && $item_total_amount >= $total_amount_paying) {
            FTable::where('id',$table_id)->update(['etat' => 0,'waiter_name' => '','opened_by' => '','total_amount_paying' => 0]);
        }else {
            $total_amount_remaining = $total_amount_paying - $item_total_amount;
            FTable::where('id',$table_id)->update(['total_amount_paying' => $total_amount_remaining]);
        }

        FBill::where('invoice_number', '=', $invoice_number)
            ->update(['etat' => '01','etat_recouvrement' => '0','montant_total_credit' => $item_total_amount,'statut_paied' => '0','client_id' => $client_id,'validated_by' => $this->user->name]);
        FBillDetail::where('invoice_number', '=', $invoice_number)
            ->update(['etat' => '01','etat_recouvrement' => '0','montant_total_credit' => $item_total_amount,'statut_paied' => '0','client_id' => $client_id,'validated_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'La Facture  est validée avec succés');
            return redirect()->route('admin.f-drink-bills.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

        
    }

    public function validerFactureBarrista($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        try {DB::beginTransaction();

        $datas = FBillDetail::where('invoice_number', $invoice_number)->get();

        $table_id = FBillDetail::where('invoice_number', $invoice_number)->value('table_id');

        foreach($datas as $data){
                $orderData = array(
                    'confirmed_by' => $this->user->name,
                    'status' => 3,
                );

                FBarristaOrder::where('order_no', '=', $data->barrist_order_no)
                    ->update($orderData);
                FBarristaOrderDetail::where('order_no', '=', $data->barrist_order_no)
                    ->update($orderData);
        }

        $item_total_amount = DB::table('f_bill_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');

        $total_amount_paying = DB::table('f_tables')
            ->where('id', '=', $table_id)
            ->sum('total_amount_paying');

        $in_pending = count(FBarristaOrderDetail::where('table_id',$table_id)->where('status','!=',3)->where('status','!=',2)->where('status','!=',-1)->get());

        if ($in_pending < 1 && $item_total_amount >= $total_amount_paying) {
            FTable::where('id',$table_id)->update(['etat' => 0,'waiter_name' => '','opened_by' => '','total_amount_paying' => 0]);
        }else {
            $total_amount_remaining = $total_amount_paying - $item_total_amount;
            FTable::where('id',$table_id)->update(['total_amount_paying' => $total_amount_remaining]);
        }

        FBill::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => 1,'statut_paied' => '0','validated_by' => $this->user->name]);
        FBillDetail::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => 1,'statut_paied' => '0','validated_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'La Facture  est validée avec succés');
            return redirect()->route('admin.f-barrista-bills.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function validerFactureBarristaCredit(Request  $request,$invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        $request->validate([
            'client_id' => 'required'
        ]);

        try {DB::beginTransaction();

        $client_id = $request->client_id;

        $datas = FBillDetail::where('invoice_number', $invoice_number)->get();

        $table_id = FBillDetail::where('invoice_number', $invoice_number)->value('table_id');

        $item_total_amount = DB::table('f_bill_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');

        foreach($datas as $data){
                $orderData = array(
                    'confirmed_by' => $this->user->name,
                    'status' => 3,
                );

                FBarristaOrder::where('order_no', '=', $data->barrist_order_no)
                    ->update($orderData);
                FBarristaOrderDetail::where('order_no', '=', $data->barrist_order_no)
                    ->update($orderData);
        }

        $total_amount_paying = DB::table('f_tables')
            ->where('id', '=', $table_id)
            ->sum('total_amount_paying');

        $in_pending = count(FBarristaOrderDetail::where('table_id',$table_id)->where('status','!=',3)->where('status','!=',2)->where('status','!=',-1)->get());

        if ($in_pending < 1 && $item_total_amount >= $total_amount_paying) {
            FTable::where('id',$table_id)->update(['etat' => 0,'waiter_name' => '','opened_by' => '','total_amount_paying' => 0]);
        }else {
            $total_amount_remaining = $total_amount_paying - $item_total_amount;
            FTable::where('id',$table_id)->update(['total_amount_paying' => $total_amount_remaining]);
        }

        FBill::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => '01','etat_recouvrement' => '0','montant_total_credit' => $item_total_amount,'statut_paied' => '0','client_id' => $client_id,'validated_by' => $this->user->name]);
        FBillDetail::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => '01','etat_recouvrement' => '0','montant_total_credit' => $item_total_amount,'statut_paied' => '0','client_id' => $client_id,'validated_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'La Facture  est validée avec succés');
            return redirect()->route('admin.f-barrista-bills.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }


    public function validerFactureBartender($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        try {DB::beginTransaction();

        $datas = FBillDetail::where('invoice_number', $invoice_number)->get();

        $table_id = FBillDetail::where('invoice_number', $invoice_number)->value('table_id');


        $item_total_amount = DB::table('f_bill_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');

        foreach($datas as $data){
                $orderData = array(
                    'confirmed_by' => $this->user->name,
                    'status' => 3,
                );

                FBartenderOrder::where('order_no', '=', $data->bartender_order_no)
                    ->update($orderData);
                FBartenderOrderDetail::where('order_no', '=', $data->bartender_order_no)
                    ->update($orderData);
        }

        $total_amount_paying = DB::table('f_tables')
            ->where('id', '=', $table_id)
            ->sum('total_amount_paying');

        $in_pending = count(FBartenderOrderDetail::where('table_id',$table_id)->where('status','!=',3)->where('status','!=',2)->where('status','!=',-1)->get());

        if ($in_pending < 1 && $item_total_amount >= $total_amount_paying) {
            FTable::where('id',$table_id)->update(['etat' => 0,'waiter_name' => '','opened_by' => '','total_amount_paying' => 0]);
        }else {
            $total_amount_remaining = $total_amount_paying - $item_total_amount;
            FTable::where('id',$table_id)->update(['total_amount_paying' => $total_amount_remaining]);
        }
        
        FBill::where('invoice_number', '=', $invoice_number)
            ->update(['etat' => 1,'statut_paied' => '0','validated_by' => $this->user->name]);
        FBillDetail::where('invoice_number', '=', $invoice_number)
            ->update(['etat' => 1,'statut_paied' => '0','validated_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'La Facture  est validée avec succés');
            return redirect()->route('admin.f-bartender-bills.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function validerFactureBartenderCredit(Request  $request,$invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        $request->validate([
            'client_id' => 'required'
        ]);

        try {DB::beginTransaction();

        $client_id = $request->client_id;

        $datas = FBillDetail::where('invoice_number', $invoice_number)->get();

        $table_id = FBillDetail::where('invoice_number', $invoice_number)->value('table_id');


        $item_total_amount = DB::table('f_bill_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');

        foreach($datas as $data){
                $orderData = array(
                    'confirmed_by' => $this->user->name,
                    'status' => 3,
                );

                FBartenderOrder::where('order_no', '=', $data->bartender_order_no)
                    ->update($orderData);
                FBartenderOrderDetail::where('order_no', '=', $data->bartender_order_no)
                    ->update($orderData);
        }


        $total_amount_paying = DB::table('f_tables')
            ->where('id', '=', $table_id)
            ->sum('total_amount_paying');

        $in_pending = count(FBartenderOrderDetail::where('table_id',$table_id)->where('status','!=',3)->where('status','!=',2)->where('status','!=',-1)->get());

        if ($in_pending < 1 && $item_total_amount >= $total_amount_paying) {
            FTable::where('id',$table_id)->update(['etat' => 0,'waiter_name' => '','opened_by' => '','total_amount_paying' => 0]);
        }else {
            $total_amount_remaining = $total_amount_paying - $item_total_amount;
            FTable::where('id',$table_id)->update(['total_amount_paying' => $total_amount_remaining]);
        }

        FBill::where('invoice_number', '=', $invoice_number)
            ->update(['etat' => '01','etat_recouvrement' => '0','montant_total_credit' => $item_total_amount,'statut_paied' => '0','client_id' => $client_id,'validated_by' => $this->user->name]);
        FBillDetail::where('invoice_number', '=', $invoice_number)
            ->update(['etat' => '01','etat_recouvrement' => '0','montant_total_credit' => $item_total_amount,'statut_paied' => '0','client_id' => $client_id,'validated_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'La Facture  est validée avec succés');
            return redirect()->route('admin.f-bartender-bills.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function validerFactureBooking($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        try {DB::beginTransaction();

        $data = FBill::where('invoice_number',$invoice_number)->first();

        FBill::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => 1,'statut_paied' => '0','validated_by' => $this->user->name]);
        FBillDetail::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => 1,'statut_paied' => '0','validated_by' => $this->user->name]);
        FBooking::where('booking_no', '=', $data->booking_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
        FBookingDetail::where('booking_no', '=', $data->booking_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'La Facture  est validée avec succés');
            return redirect()->route('admin.f-booking-bills.choose');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function validerFactureBookingCredit(Request  $request,$invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        $request->validate([
            'client_id' => 'required'
        ]);

        try {DB::beginTransaction();

        $client_id = $request->client_id;

        $data = FBill::where('invoice_number',$invoice_number)->first();

        $item_total_amount = DB::table('f_bill_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');

        FBill::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => '01','etat_recouvrement' => '0','montant_total_credit' => $item_total_amount,'statut_paied' => '0','client_id' => $client_id,'validated_by' => $this->user->name]);
        FBillDetail::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => '01','etat_recouvrement' => '0','montant_total_credit' => $item_total_amount,'statut_paied' => '0','client_id' => $client_id,'validated_by' => $this->user->name]);
        FBooking::where('booking_no', '=', $data->booking_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
        FBookingDetail::where('booking_no', '=', $data->booking_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'La Facture  est validée avec succés');
            return redirect()->route('admin.f-booking-bills.choose');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

    public function validerFactureCuisine($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        try {DB::beginTransaction();

        $datas = FBillDetail::where('invoice_number', $invoice_number)->get();
        $data = FBill::where('invoice_number', $invoice_number)->first();

        $table_id = FBillDetail::where('invoice_number', $invoice_number)->value('table_id');
 
        foreach($datas as $data){
                $orderData = array(
                    'confirmed_by' => $this->user->name,
                    'status' => 3,
                );

                FFoodOrder::where('order_no', '=', $data->food_order_no)
                    ->update($orderData);
                FFoodOrderDetail::where('order_no', '=', $data->food_order_no)
                    ->update($orderData);
        }

        $item_total_amount = DB::table('f_bill_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');

        $total_amount_paying = DB::table('f_tables')
            ->where('id', '=', $table_id)
            ->sum('total_amount_paying');

        $in_pending = count(FFoodOrderDetail::where('table_id',$table_id)->where('status','!=',3)->where('status','!=',2)->where('status','!=',-1)->get());

        if ($in_pending < 1 && $item_total_amount >= $total_amount_paying) {
            FTable::where('id',$table_id)->update(['etat' => 0,'waiter_name' => '','opened_by' => '','total_amount_paying' => 0]);
        }else {
            $total_amount_remaining = $total_amount_paying - $item_total_amount;
            FTable::where('id',$table_id)->update(['total_amount_paying' => $total_amount_remaining]);
        }
        

        FBill::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => 1,'statut_paied' => '0','validated_by' => $this->user->name]);
        FBillDetail::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => 1,'statut_paied' => '0','validated_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'La Facture  est validée avec succés');
            return redirect()->route('admin.f-food-bills.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function validerFactureCuisineCredit(Request  $request,$invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        $request->validate([
            'client_id' => 'required'
        ]);

        try {DB::beginTransaction();

        $client_id = $request->client_id;

        $datas = FBillDetail::where('invoice_number', $invoice_number)->get();
        $data = FBill::where('invoice_number', $invoice_number)->first();

        $table_id = FBillDetail::where('invoice_number', $invoice_number)->value('table_id');

        foreach($datas as $data){
                $orderData = array(
                    'confirmed_by' => $this->user->name,
                    'status' => 3,
                );

                FFoodOrder::where('order_no', '=', $data->food_order_no)
                    ->update($orderData);
                FFoodOrderDetail::where('order_no', '=', $data->food_order_no)
                    ->update($orderData);
        }

        $item_total_amount = DB::table('f_bill_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');

        $total_amount_paying = DB::table('f_tables')
            ->where('id', '=', $table_id)
            ->sum('total_amount_paying');

        $in_pending = count(FFoodOrderDetail::where('table_id',$table_id)->where('status','!=',3)->where('status','!=',2)->where('status','!=',-1)->get());

        if ($in_pending < 1 && $item_total_amount >= $total_amount_paying) {
            FTable::where('id',$table_id)->update(['etat' => 0,'waiter_name' => '','opened_by' => '','total_amount_paying' => 0]);
        }else {
            $total_amount_remaining = $total_amount_paying - $item_total_amount;
            FTable::where('id',$table_id)->update(['total_amount_paying' => $total_amount_remaining]);
        }
        

        $item_total_amount = DB::table('f_bill_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');
        
        FBill::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => '01','etat_recouvrement' => '0','montant_total_credit' => $item_total_amount,'statut_paied' => '0','client_id' => $client_id,'validated_by' => $this->user->name]);
        FBillDetail::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => '01','etat_recouvrement' => '0','montant_total_credit' => $item_total_amount,'statut_paied' => '0','client_id' => $client_id,'validated_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'La Facture  est validée avec succés');
            return redirect()->route('admin.f-food-bills.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function annulerFacture(Request $request,$invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any invoice !');
        }


        $request->validate([
            'cn_motif' => 'required|min:10|max:500'
        ]);

        try {DB::beginTransaction();

        $cn_motif = $request->cn_motif;

        $invoice_signature = FBill::where('invoice_number', $invoice_number)->value('invoice_signature');

            FBill::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => -1,'statut' => -1,'cn_motif' => $cn_motif,'reseted_by' => $this->user->name]);
            FBillDetail::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => -1,'statut' => -1,'cn_motif' => $cn_motif,'reseted_by' => $this->user->name]);
            
            DB::commit();
            session()->flash('success', 'La Facture  est annulée');
            return back();

        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function bill($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        try {DB::beginTransaction();
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $datas = FBillDetail::where('invoice_number', $invoice_number)->get();
        $facture = FBill::where('invoice_number', $invoice_number)->first();
        $invoice_signature = FBill::where('invoice_number', $invoice_number)->value('invoice_signature');
        $data = FBill::where('invoice_number', $invoice_number)->first();
        $totalValue = DB::table('f_bill_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_price_nvat');
        $item_total_amount = DB::table('f_bill_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');

        $totalVat = DB::table('f_bill_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('vat');

        $total_tsce_tax = DB::table('f_bill_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_ct');

        $client = FBill::where('invoice_number', $invoice_number)->value('customer_name');
        $date = FBill::where('invoice_number', $invoice_number)->value('invoice_date');
       
        $pdf = PDF::loadView('backend.pages.f.document.facture',compact('datas','invoice_number','totalValue','item_total_amount','client','setting','date','data','invoice_signature','facture','totalVat','total_tsce_tax'))->setPaper('a6', 'portrait');

        Storage::put('public/f/factures/'.$invoice_number.'.pdf', $pdf->output());

        
        $factures = FBill::where('invoice_number', $invoice_number)->get();

        $datas = FBillDetail::where('invoice_number', $invoice_number)->get();
            

            FBill::where('invoice_number', '=', $invoice_number)
                ->update(['statut' => 1]);
            FBillDetail::where('invoice_number', '=', $invoice_number)
                ->update(['statut' => 1]);

            
            DB::commit();

            return view('backend.pages.f.document.facture',compact('datas','invoice_number','totalValue','item_total_amount','client','setting','date','data','invoice_signature','facture','totalVat','total_tsce_tax'));
        
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

    public function draftBill($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('facture.reimprimer')) {
            abort(403, 'Sorry !! You are Unauthorized to print invoice !more information you have to contact Marcellin');
        }


        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $datas = FBillDetail::where('invoice_number', $invoice_number)->get();
        $facture = FBill::where('invoice_number', $invoice_number)->first();
        $invoice_signature = FBill::where('invoice_number', $invoice_number)->value('invoice_signature');
        $data = FBill::where('invoice_number', $invoice_number)->first();
        $totalValue = DB::table('f_bill_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_price_nvat');
        $item_total_amount = DB::table('f_bill_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');
        $totalVat = DB::table('f_bill_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('vat');
        $total_tsce_tax = DB::table('f_bill_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_ct');

        $client = FBill::where('invoice_number', $invoice_number)->value('customer_name');
        $date = FBill::where('invoice_number', $invoice_number)->value('invoice_date');
        
        return view('backend.pages.f.document.facture',compact('datas','invoice_number','totalValue','item_total_amount','client','setting','date','data','invoice_signature','facture','totalVat','total_tsce_tax'));

    }

    public function expedition($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('facture.reimprimer')) {
            abort(403, 'Sorry !! You are Unauthorized to print invoice !more information you have to contact Marcellin');
        }


        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $datas = FBillDetail::where('invoice_number', $invoice_number)->get();
        $facture = FBill::where('invoice_number', $invoice_number)->first();
        $invoice_signature = FBill::where('invoice_number', $invoice_number)->value('invoice_signature');
        $data = FBill::where('invoice_number', $invoice_number)->first();
        $totalValue = DB::table('f_bill_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_price_nvat');
        $item_total_amount = DB::table('f_bill_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');
        $totalVat = DB::table('f_bill_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('vat');
        $total_tsce_tax = DB::table('f_bill_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_ct');

        $client = FBill::where('invoice_number', $invoice_number)->value('customer_name');
        $date = FBill::where('invoice_number', $invoice_number)->value('invoice_date');
        
        return view('backend.pages.f.document.expedition',compact('datas','invoice_number','totalValue','item_total_amount','client','setting','date','data','invoice_signature','facture','totalVat','total_tsce_tax'));

    }

    public function voirFactureAnnuler($invoice_number){
        $facture = FBillDetail::where('invoice_number',$invoice_number)->first();
        $datas = FBillDetail::where('invoice_number', $invoice_number)->get();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();
        return view('backend.pages.f.bill.reset', compact('facture','datas','clients'));
    }

    public function voirFactureCash($invoice_number){
        $facture = FBillDetail::where('invoice_number',$invoice_number)->first();
        $datas = FBillDetail::where('invoice_number', $invoice_number)->get();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();
        return view('backend.pages.f.bill.cash', compact('facture','datas','clients'));
    }

    public function voirFactureCredit($invoice_number){
        $facture = FBillDetail::where('invoice_number',$invoice_number)->first();
        $datas = FBillDetail::where('invoice_number', $invoice_number)->get();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();
        return view('backend.pages.f.bill.credit', compact('facture','datas','clients'));
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
        if (is_null($this->user) || !$this->user->can('f_bill.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factureDetails = FBillDetail::where('invoice_number',$invoice_number)->get();
        $facture = FBill::with('employe')->where('invoice_number',$invoice_number)->first();
        $total_amount = DB::table('f_bill_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');
        return view('backend.pages.f.bill.show',compact('facture','factureDetails','total_amount'));
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

        $montant_recouvre = DB::table('f_bills')
            ->where('invoice_number',$invoice_number)->where('etat','01')->sum('montant_recouvre');

        if ($montant_total_credit >= $montant_recouvre_input) {

            $montant_total_recouvre = $montant_recouvre_input + $montant_recouvre;
            $reste_credit = $montant_total_credit - $montant_total_recouvre;

            if ($reste_credit == 0) {
                $etat_recouvrement = 2;
                FBill::where('invoice_number', '=', $invoice_number)
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
                FBillDetail::where('invoice_number', '=', $invoice_number)
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
                FBill::where('invoice_number', '=', $invoice_number)
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
                FBillDetail::where('invoice_number', '=', $invoice_number)
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
        if (is_null($this->user) || !$this->user->can('f_bill.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any invoice !');
        }

        
    }

    public function turnoverExportToExcel(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('dashboard.view')) {
            abort(403, 'Sorry !! You are Unauthorized to export any bill !');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');

        return Excel::download(new TurnoverExport, 'RAPPORT DU CHIFFRE D AFFAIRES DU '.$d1.' AU '.$d2.'.xlsx');
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

        if (is_null($this->user) || !$this->user->can('f_bill.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any invoice !');
        }

        

    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $invoice_number
     * @return \Illuminate\Http\Response
     */
    public function destroy($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('f_bill.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any invoice !');
        }
        /*
        try {DB::beginTransaction();

        $facture = FBill::where('invoice_number',$invoice_number)->first();
        if (!is_null($facture)) {
            $facture->delete();
            FBillDetail::where('invoice_number',$invoice_number)->delete();

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
        */
    }
}

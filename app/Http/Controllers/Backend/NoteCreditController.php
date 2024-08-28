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
//use GuzzleHttp\Client;
use App\Models\Facture;
use App\Models\FactureDetail;
use App\Models\NoteCredit;
use App\Models\NoteCreditDetail;
use App\Models\Setting;
use App\Models\Drink;
use App\Models\BarristItem;
use App\Models\FoodItem;
use App\Models\FoodItemDetail;
use App\Models\BartenderItem;
use App\Models\Employe;
use App\Models\BookingSalle;
use App\Models\BookingService;
use App\Models\BookingClient;
use App\Models\EGRClient;
use App\Models\Table;
use App\Models\BookingTable;
use App\Models\KidnessSpace;
use App\Models\BreakFast;
use App\Models\SwimingPool;
use App\Models\DrinkSmallStore;
use App\Models\DrinkSmallStoreDetail;
use App\Models\DrinkSmallReport;
use App\Mail\DeleteFactureMail;
use App\Mail\InvoiceResetedMail;
use App\Mail\ReportDrinkMail;

class NoteCreditController extends Controller
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

        $factures = NoteCredit::orderBy('id','desc')->get();
        return view('backend.pages.note_credit.index',compact('factures'));
    }


    public function noteCreditBoisson($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $drinks =  Drink::orderBy('name','asc')->get();
        $datas =  FactureDetail::where('invoice_number',$invoice_number)->orderBy('invoice_number','asc')->get();
        $drink_small_stores = DrinkSmallStore::all();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();

        $data =  facture::where('invoice_number',$invoice_number)->first();
        $total_amount = DB::table('facture_details')
            ->where('invoice_number',$invoice_number)
            ->sum('item_total_amount');

        return view('backend.pages.note_credit.create_drink',compact('drinks','data','setting','datas','invoice_number','drink_small_stores','clients','total_amount'));
    }

    public function noteCreditBartender($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $bartender_items =  BartenderItem::orderBy('name','asc')->get();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();
        $datas =  FactureDetail::where('invoice_number',$invoice_number)->orderBy('invoice_number','asc')->get();
        $data =  facture::where('invoice_number',$invoice_number)->first();
        $total_amount = DB::table('facture_details')
            ->where('invoice_number',$invoice_number)
            ->sum('item_total_amount');

        return view('backend.pages.note_credit.create_bartender',compact('bartender_items','data','setting','datas','invoice_number','clients','total_amount'));
    }

    public function noteCreditBarrista($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $barrista_items =  BarristItem::orderBy('name','asc')->get();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();
        $datas =  FactureDetail::where('invoice_number',$invoice_number)->orderBy('invoice_number','asc')->get();
        $data =  facture::where('invoice_number',$invoice_number)->first();
        $total_amount = DB::table('facture_details')
            ->where('invoice_number',$invoice_number)
            ->sum('item_total_amount');

        return view('backend.pages.note_credit.create_barrista',compact('barrista_items','data','setting','datas','invoice_number','clients','total_amount'));
    }

    public function noteCreditNourriture($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $food_items =  FoodItem::orderBy('name','asc')->get();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();
        $datas =  FactureDetail::where('invoice_number',$invoice_number)->orderBy('invoice_number','asc')->get();
        $data =  facture::where('invoice_number',$invoice_number)->first();
        $total_amount = DB::table('facture_details')
            ->where('invoice_number',$invoice_number)
            ->sum('item_total_amount');

        return view('backend.pages.note_credit.create_food',compact('food_items','data','setting','datas','invoice_number','clients','total_amount'));
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
                //'invoice_number' => 'required',
                'tp_type' => 'required',
                'tp_name' => 'required|max:100|min:3',
                'tp_TIN' => 'required|max:30|min:4',
                'tp_trade_number' => 'required|max:20|min:4',
                'tp_phone_number' => 'required|max:20|min:6',
                'tp_address_commune' => 'required|max:50|min:5',
                'tp_address_quartier' => 'required|max:50|min:5',
                'cn_motif' => 'required|max:255',
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
            $cn_motif = $request->cn_motif;
            $etat = $request->etat;

            $invoice_date = Carbon::now();

            if ($cn_motif == 1) {
                $cn_motif_detail = "Erreur sur la facture";
            }elseif ($cn_motif == 2) {
                $cn_motif_detail = "Retour marchandises";
            }elseif ($cn_motif == 3) {
                $cn_motif_detail = "Rabais";
            }elseif ($cn_motif == 4) {
                $cn_motif_detail = "Reduction hors facture";
            }else{
                $cn_motif_detail = "Erreur sur la facture";
            }




            $cancelled_invoice_ref = $request->invoice_number;

            $drink_order_no = $request->drink_order_no;

            
            $latest = NoteCredit::orderBy('id','desc')->first();
            if ($latest) {
               $invoice_number = 'FA' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $invoice_number = 'FA' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }
            

            $cancelled_invoice = 1;
            

            $invoice_signature = $request->tp_TIN."/wsconfig('app.tin_number_company')/".Carbon::parse($invoice_date)->format('YmdHis')."/".$invoice_number;
            $invoice_ref = $invoice_number;

        for( $count = 0; $count < count($drink_id); $count++ )
        {
            $taux_tva = Drink::where('id', $drink_id[$count])->value('vat');
            $brarudi_price = Drink::where('id', $drink_id[$count])->value('brarudi_price');
            $cump = Drink::where('id', $drink_id[$count])->value('cump');

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
            'cancelled_invoice_ref'=>$cancelled_invoice_ref,
            'cn_motif'=>$cn_motif,
            'invoice_currency'=>$request->invoice_currency,
            'invoice_ref'=>$invoice_ref,
            'code_store'=>$request->code_store,
            'auteur' => $this->user->name,
            'etat' => $etat,
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
        );
          $data1[] = $data;
      }


        NoteCreditDetail::insert($data1);

            $order_no = NoteCreditDetail::where('invoice_number',$invoice_number)->value('drink_order_no');

            //create facture
            $facture = new NoteCredit();
            $facture->invoice_date = $invoice_date;
            $facture->invoice_number = $invoice_number;
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
            $facture->cancelled_invoice_ref = $cancelled_invoice_ref;
            $facture->cn_motif = $cn_motif;
            $facture->etat = $etat;
            $facture->cancelled_invoice = $cancelled_invoice;
            $facture->invoice_ref = $invoice_ref;
            $facture->code_store = $request->code_store;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->save();

            Facture::where('invoice_number', '=', $cancelled_invoice_ref)
                ->update(['cancelled_invoice' => 1,'cn_motif' => $cn_motif_detail,'invoice_ref' => $invoice_ref,'reseted_by' => $this->user->name]);
            FactureDetail::where('invoice_number', '=', $cancelled_invoice_ref)
                ->update(['cancelled_invoice' => 1,'cn_motif' => $cn_motif_detail,'invoice_ref' => $invoice_ref,'reseted_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'La note de credit est faite avec succés!!');
            return redirect()->route('admin.note-de-credit.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

    public function storeBarrista(Request  $request)
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
            $cn_motif = $request->cn_motif;
            $etat = $request->etat;

            $invoice_date = Carbon::now();

            if ($cn_motif == 1) {
                $cn_motif_detail = "Erreur sur la facture";
            }elseif ($cn_motif == 2) {
                $cn_motif_detail = "Retour marchandises";
            }elseif ($cn_motif == 3) {
                $cn_motif_detail = "Rabais";
            }elseif ($cn_motif == 4) {
                $cn_motif_detail = "Reduction hors facture";
            }else{
                $cn_motif_detail = "Erreur sur la facture";
            }




            $cancelled_invoice_ref = $request->invoice_number;

            $barrist_order_no = $request->barrist_order_no;

            
            $latest = NoteCredit::orderBy('id','desc')->first();
            if ($latest) {
               $invoice_number = 'FA' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $invoice_number = 'FA' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }
            

            $cancelled_invoice = 1;
            

            $invoice_signature = $request->tp_TIN."/wsconfig('app.tin_number_company')/".Carbon::parse($invoice_date)->format('YmdHis')."/".$invoice_number;
            $invoice_ref = $invoice_number;

        for( $count = 0; $count < count($barrist_item_id); $count++ )
        {
            $taux_tva = BarristItem::where('id', $barrist_item_id[$count])->value('vat');

            if($request->vat_taxpayer == 1){

                    $item_total_amount = ($item_price[$count]*$item_quantity[$count]);
                    
                    $item_price_nvat = ($item_total_amount* 100)/110;
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
            'barrist_order_no'=>$request->barrist_order_no[$count],
            'cancelled_invoice_ref'=>$cancelled_invoice_ref,
            'cn_motif'=>$cn_motif,
            'invoice_currency'=>$request->invoice_currency,
            'invoice_ref'=>$invoice_ref,
            'auteur' => $this->user->name,
            'etat' => $etat,
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
        );
          $data1[] = $data;
      }


        NoteCreditDetail::insert($data1);

            $order_no = NoteCreditDetail::where('invoice_number',$invoice_number)->value('barrist_order_no');

            //create facture
            $facture = new NoteCredit();
            $facture->invoice_date = $invoice_date;
            $facture->invoice_number = $invoice_number;
            $facture->barrist_order_no = $order_no;
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
            $facture->cancelled_invoice_ref = $cancelled_invoice_ref;
            $facture->cn_motif = $cn_motif;
            $facture->etat = $etat;
            $facture->cancelled_invoice = $cancelled_invoice;
            $facture->invoice_ref = $invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->save();

            Facture::where('invoice_number', '=', $cancelled_invoice_ref)
                ->update(['cancelled_invoice' => 1,'cn_motif' => $cn_motif_detail,'invoice_ref' => $invoice_ref,'reseted_by' => $this->user->name]);
            FactureDetail::where('invoice_number', '=', $cancelled_invoice_ref)
                ->update(['cancelled_invoice' => 1,'cn_motif' => $cn_motif_detail,'invoice_ref' => $invoice_ref,'reseted_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'La note de credit est faite avec succés!!');
            return redirect()->route('admin.note-de-credit.index');
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
            $cn_motif = $request->cn_motif;
            $etat = $request->etat;

            $invoice_date = Carbon::now();

            if ($cn_motif == 1) {
                $cn_motif_detail = "Erreur sur la facture";
            }elseif ($cn_motif == 2) {
                $cn_motif_detail = "Retour marchandises";
            }elseif ($cn_motif == 3) {
                $cn_motif_detail = "Rabais";
            }elseif ($cn_motif == 4) {
                $cn_motif_detail = "Reduction hors facture";
            }else{
                $cn_motif_detail = "Erreur sur la facture";
            }




            $cancelled_invoice_ref = $request->invoice_number;

            $food_order_no = $request->food_order_no;

            
            $latest = NoteCredit::orderBy('id','desc')->first();
            if ($latest) {
               $invoice_number = 'FA' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $invoice_number = 'FA' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }
            

            $cancelled_invoice = 1;
            

            $invoice_signature = $request->tp_TIN."/wsconfig('app.tin_number_company')/".Carbon::parse($invoice_date)->format('YmdHis')."/".$invoice_number;
            $invoice_ref = $invoice_number;

        for( $count = 0; $count < count($food_item_id); $count++ )
        {
            $taux_tva = FoodItem::where('id', $food_item_id[$count])->value('vat');

            if($request->vat_taxpayer == 1){

                    $item_total_amount = ($item_price[$count]*$item_quantity[$count]);
                    
                    $item_price_nvat = ($item_total_amount* 100)/110;
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
            'food_order_no'=>$request->food_order_no[$count],
            'cancelled_invoice_ref'=>$cancelled_invoice_ref,
            'cn_motif'=>$cn_motif,
            'invoice_currency'=>$request->invoice_currency,
            'invoice_ref'=>$invoice_ref,
            'auteur' => $this->user->name,
            'etat' => $etat,
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
        );
          $data1[] = $data;
      }


        NoteCreditDetail::insert($data1);

            $order_no = NoteCreditDetail::where('invoice_number',$invoice_number)->value('food_order_no');

            //create facture
            $facture = new NoteCredit();
            $facture->invoice_date = $invoice_date;
            $facture->invoice_number = $invoice_number;
            $facture->food_order_no = $order_no;
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
            $facture->cancelled_invoice_ref = $cancelled_invoice_ref;
            $facture->cn_motif = $cn_motif;
            $facture->etat = $etat;
            $facture->cancelled_invoice = $cancelled_invoice;
            $facture->invoice_ref = $invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->save();

            Facture::where('invoice_number', '=', $cancelled_invoice_ref)
                ->update(['cancelled_invoice' => 1,'cn_motif' => $cn_motif_detail,'invoice_ref' => $invoice_ref,'reseted_by' => $this->user->name]);
            FactureDetail::where('invoice_number', '=', $cancelled_invoice_ref)
                ->update(['cancelled_invoice' => 1,'cn_motif' => $cn_motif_detail,'invoice_ref' => $invoice_ref,'reseted_by' => $this->user->name]);

            DB::commit();
            session()->flash('success', 'La note de credit est faite avec succés!!');
            return redirect()->route('admin.note-de-credit.index');
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
            $cn_motif = $request->cn_motif;
            $etat = $request->etat;

            $invoice_date = Carbon::now();

            if ($cn_motif == 1) {
                $cn_motif_detail = "Erreur sur la facture";
            }elseif ($cn_motif == 2) {
                $cn_motif_detail = "Retour marchandises";
            }elseif ($cn_motif == 3) {
                $cn_motif_detail = "Rabais";
            }elseif ($cn_motif == 4) {
                $cn_motif_detail = "Reduction hors facture";
            }else{
                $cn_motif_detail = "Erreur sur la facture";
            }




            $cancelled_invoice_ref = $request->invoice_number;

            $bartender_order_no = $request->bartender_order_no;

            
            $latest = NoteCredit::orderBy('id','desc')->first();
            if ($latest) {
               $invoice_number = 'FA' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $invoice_number = 'FA' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }
            

            $cancelled_invoice = 1;
            

            $invoice_signature = $request->tp_TIN."/wsconfig('app.tin_number_company')/".Carbon::parse($invoice_date)->format('YmdHis')."/".$invoice_number;
            $invoice_ref = $invoice_number;

        for( $count = 0; $count < count($bartender_item_id); $count++ )
        {
            $taux_tva = BartenderItem::where('id', $bartender_item_id[$count])->value('vat');

            if($request->vat_taxpayer == 1){

                    $item_total_amount = ($item_price[$count]*$item_quantity[$count]);
                    
                    $item_price_nvat = ($item_total_amount* 100)/110;
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
            'bartender_order_no'=>$request->bartender_order_no[$count],
            'cancelled_invoice_ref'=>$cancelled_invoice_ref,
            'cn_motif'=>$cn_motif,
            'invoice_currency'=>$request->invoice_currency,
            'invoice_ref'=>$invoice_ref,
            'auteur' => $this->user->name,
            'etat' => $etat,
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
        );
          $data1[] = $data;
      }


        NoteCreditDetail::insert($data1);

            $order_no = NoteCreditDetail::where('invoice_number',$invoice_number)->value('bartender_order_no');

            //create facture
            $facture = new NoteCredit();
            $facture->invoice_date = $invoice_date;
            $facture->invoice_number = $invoice_number;
            $facture->bartender_order_no = $order_no;
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
            $facture->cancelled_invoice_ref = $cancelled_invoice_ref;
            $facture->cn_motif = $cn_motif;
            $facture->etat = $etat;
            $facture->cancelled_invoice = $cancelled_invoice;
            $facture->invoice_ref = $invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->save();

            Facture::where('invoice_number', '=', $cancelled_invoice_ref)
                ->update(['cancelled_invoice' => 1,'cn_motif' => $cn_motif_detail,'invoice_ref' => $invoice_ref,'reseted_by' => $this->user->name]);
            FactureDetail::where('invoice_number', '=', $cancelled_invoice_ref)
                ->update(['cancelled_invoice' => 1,'cn_motif' => $cn_motif_detail,'invoice_ref' => $invoice_ref,'reseted_by' => $this->user->name]);

            DB::commit();
            session()->flash('success', 'La note de credit est faite avec succés!!');
            return redirect()->route('admin.note-de-credit.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

    public function storeBooking(Request  $request)
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
            
            $cn_motif = $request->cn_motif;
            $etat = $request->etat;

            $invoice_date = Carbon::now();

            if ($cn_motif == 1) {
                $cn_motif_detail = "Erreur sur la facture";
            }elseif ($cn_motif == 2) {
                $cn_motif_detail = "Retour marchandises";
            }elseif ($cn_motif == 3) {
                $cn_motif_detail = "Rabais";
            }elseif ($cn_motif == 4) {
                $cn_motif_detail = "Reduction hors facture";
            }else{
                $cn_motif_detail = "Erreur sur la facture";
            }


            $cancelled_invoice_ref = $request->invoice_number;

            $booking_no = $request->booking_no;

            
            $latest = NoteCredit::orderBy('id','desc')->first();
            if ($latest) {
               $invoice_number = 'FA' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $invoice_number = 'FA' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }
            

            $cancelled_invoice = 1;
            

            $invoice_signature = $request->tp_TIN."/wsconfig('app.tin_number_company')/".Carbon::parse($invoice_date)->format('YmdHis')."/".$invoice_number;
            $invoice_ref = $invoice_number;

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
            'booking_no'=>$request->booking_no[$count],
            'cancelled_invoice_ref'=>$cancelled_invoice_ref,
            'cn_motif'=>$cn_motif,
            'invoice_currency'=>$request->invoice_currency,
            'invoice_ref'=>$invoice_ref,
            'auteur' => $this->user->name,
            'etat' => $etat,
            'invoice_signature_date'=> Carbon::now(),
            'salle_id'=>$bartender_item_id[$count],
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
      }


        NoteCreditDetail::insert($data1);

            $order_no = NoteCreditDetail::where('invoice_number',$invoice_number)->value('booking_no');

            //create facture
            $facture = new NoteCredit();
            $facture->invoice_date = $invoice_date;
            $facture->invoice_number = $invoice_number;
            $facture->booking_no = $order_no;
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
            $facture->cancelled_invoice_ref = $cancelled_invoice_ref;
            $facture->cn_motif = $cn_motif;
            $facture->etat = $etat;
            $facture->cancelled_invoice = $cancelled_invoice;
            $facture->invoice_ref = $invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->save();

            Facture::where('invoice_number', '=', $cancelled_invoice_ref)
                ->update(['cancelled_invoice' => 1,'cn_motif' => $cn_motif_detail,'invoice_ref' => $invoice_ref,'reseted_by' => $this->user->name]);
            FactureDetail::where('invoice_number', '=', $cancelled_invoice_ref)
                ->update(['cancelled_invoice' => 1,'cn_motif' => $cn_motif_detail,'invoice_ref' => $invoice_ref,'reseted_by' => $this->user->name]);

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
            'booking_no'=>$request->booking_no[$count],
            'cancelled_invoice_ref'=>$cancelled_invoice_ref,
            'cn_motif'=>$cn_motif,
            'invoice_currency'=>$request->invoice_currency,
            'invoice_ref'=>$invoice_ref,
            'auteur' => $this->user->name,
            'etat' => $etat,
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
        );
          $data1[] = $data;
      }


        NoteCreditDetail::insert($data1);

            $order_no = NoteCreditDetail::where('invoice_number',$invoice_number)->value('booking_no');

            //create facture
            $facture = new NoteCredit();
            $facture->invoice_date = $invoice_date;
            $facture->invoice_number = $invoice_number;
            $facture->booking_no = $order_no;
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
            $facture->cancelled_invoice_ref = $cancelled_invoice_ref;
            $facture->cn_motif = $cn_motif;
            $facture->etat = $etat;
            $facture->cancelled_invoice = $cancelled_invoice;
            $facture->invoice_ref = $invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->save();

            Facture::where('invoice_number', '=', $cancelled_invoice_ref)
                ->update(['cancelled_invoice' => 1,'cn_motif' => $cn_motif_detail,'invoice_ref' => $invoice_ref,'reseted_by' => $this->user->name]);
            FactureDetail::where('invoice_number', '=', $cancelled_invoice_ref)
                ->update(['cancelled_invoice' => 1,'cn_motif' => $cn_motif_detail,'invoice_ref' => $invoice_ref,'reseted_by' => $this->user->name]);

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
            'booking_no'=>$request->booking_no[$count],
            'cancelled_invoice_ref'=>$cancelled_invoice_ref,
            'cn_motif'=>$cn_motif,
            'invoice_currency'=>$request->invoice_currency,
            'invoice_ref'=>$invoice_ref,
            'auteur' => $this->user->name,
            'etat' => $etat,
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
        );
          $data1[] = $data;
      }


        NoteCreditDetail::insert($data1);

            $order_no = NoteCreditDetail::where('invoice_number',$invoice_number)->value('booking_no');

            //create facture
            $facture = new NoteCredit();
            $facture->invoice_date = $invoice_date;
            $facture->invoice_number = $invoice_number;
            $facture->booking_no = $order_no;
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
            $facture->cancelled_invoice_ref = $cancelled_invoice_ref;
            $facture->cn_motif = $cn_motif;
            $facture->etat = $etat;
            $facture->cancelled_invoice = $cancelled_invoice;
            $facture->invoice_ref = $invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->save();

            Facture::where('invoice_number', '=', $cancelled_invoice_ref)
                ->update(['cancelled_invoice' => 1,'cn_motif' => $cn_motif_detail,'invoice_ref' => $invoice_ref,'reseted_by' => $this->user->name]);
            FactureDetail::where('invoice_number', '=', $cancelled_invoice_ref)
                ->update(['cancelled_invoice' => 1,'cn_motif' => $cn_motif_detail,'invoice_ref' => $invoice_ref,'reseted_by' => $this->user->name]);

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
            'booking_no'=>$request->booking_no[$count],
            'cancelled_invoice_ref'=>$cancelled_invoice_ref,
            'cn_motif'=>$cn_motif,
            'invoice_currency'=>$request->invoice_currency,
            'invoice_ref'=>$invoice_ref,
            'auteur' => $this->user->name,
            'etat' => $etat,
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
        );
          $data1[] = $data;
      }


        NoteCreditDetail::insert($data1);

            $order_no = NoteCreditDetail::where('invoice_number',$invoice_number)->value('booking_no');

            //create facture
            $facture = new NoteCredit();
            $facture->invoice_date = $invoice_date;
            $facture->invoice_number = $invoice_number;
            $facture->booking_no = $order_no;
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
            $facture->cancelled_invoice_ref = $cancelled_invoice_ref;
            $facture->cn_motif = $cn_motif;
            $facture->etat = $etat;
            $facture->cancelled_invoice = $cancelled_invoice;
            $facture->invoice_ref = $invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->save();

            Facture::where('invoice_number', '=', $cancelled_invoice_ref)
                ->update(['cancelled_invoice' => 1,'cn_motif' => $cn_motif_detail,'invoice_ref' => $invoice_ref,'reseted_by' => $this->user->name]);
            FactureDetail::where('invoice_number', '=', $cancelled_invoice_ref)
                ->update(['cancelled_invoice' => 1,'cn_motif' => $cn_motif_detail,'invoice_ref' => $invoice_ref,'reseted_by' => $this->user->name]);

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
            'booking_no'=>$request->booking_no[$count],
            'cancelled_invoice_ref'=>$cancelled_invoice_ref,
            'cn_motif'=>$cn_motif,
            'invoice_currency'=>$request->invoice_currency,
            'invoice_ref'=>$invoice_ref,
            'auteur' => $this->user->name,
            'etat' => $etat,
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
        );
          $data1[] = $data;
      }


        NoteCreditDetail::insert($data1);

            $order_no = NoteCreditDetail::where('invoice_number',$invoice_number)->value('booking_no');

            //create facture
            $facture = new NoteCredit();
            $facture->invoice_date = $invoice_date;
            $facture->invoice_number = $invoice_number;
            $facture->booking_no = $order_no;
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
            $facture->cancelled_invoice_ref = $cancelled_invoice_ref;
            $facture->cn_motif = $cn_motif;
            $facture->etat = $etat;
            $facture->cancelled_invoice = $cancelled_invoice;
            $facture->invoice_ref = $invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->save();

            Facture::where('invoice_number', '=', $cancelled_invoice_ref)
                ->update(['cancelled_invoice' => 1,'cn_motif' => $cn_motif_detail,'invoice_ref' => $invoice_ref,'reseted_by' => $this->user->name]);
            FactureDetail::where('invoice_number', '=', $cancelled_invoice_ref)
                ->update(['cancelled_invoice' => 1,'cn_motif' => $cn_motif_detail,'invoice_ref' => $invoice_ref,'reseted_by' => $this->user->name]);

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
            'booking_no'=>$request->booking_no[$count],
            'cancelled_invoice_ref'=>$cancelled_invoice_ref,
            'cn_motif'=>$cn_motif,
            'invoice_currency'=>$request->invoice_currency,
            'invoice_ref'=>$invoice_ref,
            'auteur' => $this->user->name,
            'etat' => $etat,
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
        );
          $data1[] = $data;
      }


        NoteCreditDetail::insert($data1);

            $order_no = NoteCreditDetail::where('invoice_number',$invoice_number)->value('booking_no');

            //create facture
            $facture = new NoteCredit();
            $facture->invoice_date = $invoice_date;
            $facture->invoice_number = $invoice_number;
            $facture->booking_no = $order_no;
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
            $facture->cancelled_invoice_ref = $cancelled_invoice_ref;
            $facture->cn_motif = $cn_motif;
            $facture->etat = $etat;
            $facture->cancelled_invoice = $cancelled_invoice;
            $facture->invoice_ref = $invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->save();

            Facture::where('invoice_number', '=', $cancelled_invoice_ref)
                ->update(['cancelled_invoice' => 1,'cn_motif' => $cn_motif_detail,'invoice_ref' => $invoice_ref,'reseted_by' => $this->user->name]);
            FactureDetail::where('invoice_number', '=', $cancelled_invoice_ref)
                ->update(['cancelled_invoice' => 1,'cn_motif' => $cn_motif_detail,'invoice_ref' => $invoice_ref,'reseted_by' => $this->user->name]);

        }

            DB::commit();
            session()->flash('success', 'La note de credit est faite avec succés!!');
            return redirect()->route('admin.note-de-credit.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

    public function validerFactureDrink($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        try {DB::beginTransaction();

        $datas = NoteCreditDetail::where('invoice_number', $invoice_number)->get();

        foreach($datas as $data){
            if ($data->cn_motif == 2) {
                $valeurStockInitial = DrinkSmallStoreDetail::where('code',$data->code_store)->where('drink_id', $data->drink_id)->value('total_cump_value');
                $quantityStockInitial = DrinkSmallStoreDetail::where('code',$data->code_store)->where('drink_id', $data->drink_id)->value('quantity_bottle');
                $cump = Drink::where('id', $data->drink_id)->value('cump');

                $quantityTotal = $quantityStockInitial + $data->item_quantity;
                      
                $reportData = array(
                    'drink_id' => $data->drink_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'code_store' => $data->code_store,
                    'quantity_stockin' => $data->item_quantity,
                    'value_stockin' => $data->item_quantity * $cump,
                    'quantity_stock_final' => $quantityTotal,
                    'value_stock_final' => $quantityTotal * $data->item_price,
                    'invoice_no' => $data->invoice_number,
                    'date' => $data->invoice_date,
                    'commande_boisson_no' => $data->drink_order_no,
                    'type_transaction' => 'NOTE DE CREDIT',
                    'document_no' => $data->invoice_number,
                    'cump' => $cump,
                    'created_by' => $this->user->name,
                    'employe_id' => $data->employe_id,
                    'origine_facture' => 'BOISSON',
                    'description' => "RETOUR DES MARCHANDISES",
                    'created_at' => \Carbon\Carbon::now()
                );
                $report[] = $reportData;
                
                    $donnees = array(
                        'drink_id' => $data->drink_id,
                        'quantity_bottle' => $quantityTotal,
                        'total_cump_value' => $quantityTotal * $cump,
                        'created_by' => $this->user->name,
                        'verified' => true
                    );

                    $noteCreditdata = array(
                        'drink_id' => $data->drink_id,
                        'invoice_number'=>$data->invoice_number,
                        'invoice_date'=> $data->invoice_date,
                        'tp_type'=>$data->tp_type,
                        'tp_name'=>$data->tp_name,
                        'tp_TIN'=>$data->tp_TIN,
                        'tp_trade_number'=>$data->tp_trade_number,
                        'tp_phone_number'=>$data->tp_phone_number,
                        'tp_address_province'=>$data->tp_address_province,
                        'tp_address_commune'=>$data->tp_address_commune,
                        'tp_address_quartier'=>$data->tp_address_quartier,
                        'tp_address_avenue'=>$data->tp_address_avenue,
                        'tp_address_rue'=>$data->tp_address_rue,
                        'vat_taxpayer'=>$data->vat_taxpayer,
                        'ct_taxpayer'=>$data->ct_taxpayer,
                        'tl_taxpayer'=>$data->tl_taxpayer,
                        'tp_fiscal_center'=>$data->tp_fiscal_center,
                        'tp_activity_sector'=>$data->tp_activity_sector,
                        'tp_legal_form'=>$data->tp_legal_form,
                        'payment_type'=>$data->payment_type,
                        'client_id'=>$data->client_id,
                        'customer_TIN'=>$data->customer_TIN,
                        'customer_address'=>$data->customer_address,
                        'invoice_signature'=> $invoice_signature,
                        'drink_order_no'=>$data->drink_order_no,
                        'cancelled_invoice_ref'=>$data->cancelled_invoice_ref,
                        'cn_motif'=>$data->cn_motif,
                        'invoice_currency'=>$data->invoice_currency,
                        'invoice_ref'=>$data->invoice_ref,
                        'etat' => $data->etat,
                        'auteur' => $this->user->name,
                        'invoice_signature_date'=> Carbon::now(),
                        //'table_id'=>$data->table_id,
                        'item_quantity'=>$data->item_quantity,
                        'item_price'=>-$data->item_price,
                        'item_ct'=>$data->item_ct,
                        'item_tl'=>$data->item_tl,
                        'item_price_nvat'=>-$data->item_price_nvat,
                        'vat'=>-$data->vat,
                        'item_price_wvat'=>-$data->item_price_wvat,
                        'item_total_amount'=>-$data->item_total_amount,
                        'employe_id'=> $data->employe_id,
                    );

                    $noteCredit[] = $noteCreditdata;
                        
                        DrinkSmallStoreDetail::where('code',$data->code_store)->where('drink_id',$data->drink_id)
                        ->update($donnees);
                        $flag = 0;
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

                    $noteCreditdata = array(
                        'drink_id' => $data->drink_id,
                        'invoice_number'=>$data->invoice_number,
                        'invoice_date'=> $data->invoice_date,
                        'tp_type'=>$data->tp_type,
                        'tp_name'=>$data->tp_name,
                        'tp_TIN'=>$data->tp_TIN,
                        'tp_trade_number'=>$data->tp_trade_number,
                        'tp_phone_number'=>$data->tp_phone_number,
                        'tp_address_province'=>$data->tp_address_province,
                        'tp_address_commune'=>$data->tp_address_commune,
                        'tp_address_quartier'=>$data->tp_address_quartier,
                        'tp_address_avenue'=>$data->tp_address_avenue,
                        'tp_address_rue'=>$data->tp_address_rue,
                        'vat_taxpayer'=>$data->vat_taxpayer,
                        'ct_taxpayer'=>$data->ct_taxpayer,
                        'tl_taxpayer'=>$data->tl_taxpayer,
                        'tp_fiscal_center'=>$data->tp_fiscal_center,
                        'tp_activity_sector'=>$data->tp_activity_sector,
                        'tp_legal_form'=>$data->tp_legal_form,
                        'payment_type'=>$data->payment_type,
                        'client_id'=>$data->client_id,
                        'customer_TIN'=>$data->customer_TIN,
                        'customer_address'=>$data->customer_address,
                        'invoice_signature'=> $invoice_signature,
                        'drink_order_no'=>$data->drink_order_no,
                        'cancelled_invoice_ref'=>$data->cancelled_invoice_ref,
                        'cn_motif'=>$data->cn_motif,
                        'invoice_currency'=>$data->invoice_currency,
                        'invoice_ref'=>$data->invoice_ref,
                        'auteur' => $this->user->name,
                        'etat' => $data->etat,
                        'invoice_signature_date'=> Carbon::now(),
                        //'table_id'=>$data->table_id,
                        'item_quantity'=>$data->item_quantity,
                        'item_price'=>-$data->item_price,
                        'item_ct'=>$data->item_ct,
                        'item_tl'=>$data->item_tl,
                        'item_price_nvat'=>$data->item_price_nvat,
                        'vat'=>$data->vat,
                        'item_price_wvat'=>$data->item_price_wvat,
                        'item_total_amount'=>$data->item_total_amount,
                        'employe_id'=> $data->employe_id,
                    );

                    $noteCredit[] = $noteCreditdata;

                $flag = 1;
            }
        }

        if ($flag != 1) {
            DrinkSmallReport::insert($report);
        }

        FactureDetail::insert($noteCredit);

        DrinkSmallStoreDetail::where('drink_id','!=','')->update(['verified' => false]);
        NoteCredit::where('invoice_number', '=', $invoice_number)
            ->update(['statut' => 2,'validated_by' => $this->user->name]);
        NoteCreditDetail::where('invoice_number', '=', $invoice_number)
            ->update(['statut' => 2,'validated_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'La note de credit a été validé avec succés!!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function validerFactureBarrista($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        try {DB::beginTransaction();

        $datas = NoteCreditDetail::where('invoice_number', $invoice_number)->get();

        foreach ($datas as $data) {
                $noteCreditdata = array(
                        'barrist_item_id' => $data->barrist_item_id,
                        'invoice_number'=>$data->invoice_number,
                        'invoice_date'=> $data->invoice_date,
                        'tp_type'=>$data->tp_type,
                        'tp_name'=>$data->tp_name,
                        'tp_TIN'=>$data->tp_TIN,
                        'tp_trade_number'=>$data->tp_trade_number,
                        'tp_phone_number'=>$data->tp_phone_number,
                        'tp_address_province'=>$data->tp_address_province,
                        'tp_address_commune'=>$data->tp_address_commune,
                        'tp_address_quartier'=>$data->tp_address_quartier,
                        'tp_address_avenue'=>$data->tp_address_avenue,
                        'tp_address_rue'=>$data->tp_address_rue,
                        'vat_taxpayer'=>$data->vat_taxpayer,
                        'ct_taxpayer'=>$data->ct_taxpayer,
                        'tl_taxpayer'=>$data->tl_taxpayer,
                        'tp_fiscal_center'=>$data->tp_fiscal_center,
                        'tp_activity_sector'=>$data->tp_activity_sector,
                        'tp_legal_form'=>$data->tp_legal_form,
                        'payment_type'=>$data->payment_type,
                        'client_id'=>$data->client_id,
                        'customer_TIN'=>$data->customer_TIN,
                        'customer_address'=>$data->customer_address,
                        'invoice_signature'=> $invoice_signature,
                        'barrist_order_no'=>$data->barrist_order_no,
                        'cancelled_invoice_ref'=>$data->cancelled_invoice_ref,
                        'cn_motif'=>$data->cn_motif,
                        'invoice_currency'=>$data->invoice_currency,
                        'invoice_ref'=>$data->invoice_ref,
                        'auteur' => $this->user->name,
                        'etat' => $data->etat,
                        'invoice_signature_date'=> Carbon::now(),
                        //'table_id'=>$data->table_id,
                        'item_quantity'=>$data->item_quantity,
                        'item_price'=>$data->item_price,
                        'item_ct'=>$data->item_ct,
                        'item_tl'=>$data->item_tl,
                        'item_price_nvat'=>$data->item_price_nvat,
                        'vat'=>$data->vat,
                        'item_price_wvat'=>$data->item_price_wvat,
                        'item_total_amount'=>$data->item_total_amount,
                        'employe_id'=> $data->employe_id,
                    );

                $noteCredit[] = $noteCreditdata;
        }

        FactureDetail::insert($noteCredit);

        NoteCredit::where('invoice_number', '=', $invoice_number)
            ->update(['statut' => 2,'validated_by' => $this->user->name]);
        NoteCreditDetail::where('invoice_number', '=', $invoice_number)
            ->update(['statut' => 2,'validated_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'La note de credit a été validé avec succés!!');
            return back();
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

        $datas = NoteCreditDetail::where('invoice_number', $invoice_number)->get();


        foreach($datas as $data){
         if ($data->cn_motif == 2) {
            $valeurStockInitial = BartenderProductionStore::where('bartender_item_id', $data->bartender_item_id)->value('total_cump_value');
            $quantityStockInitial = BartenderProductionStore::where('bartender_item_id', $data->bartender_item_id)->value('quantity');
            $cump = BartenderProductionStore::where('bartender_item_id', $data->bartender_item_id)->value('cump');

            $quantityTotal = $quantityStockInitial + $data->item_quantity;
                      
                $reportData = array(
                    'bartender_item_id' => $data->bartender_item_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'quantity_stockin' => $data->item_quantity,
                    'value_stockin' => $data->item_quantity * $data->item_price,
                    'quantity_stock_final' => $quantityTotal,
                    'value_stock_final' => $quantityTotal * $data->item_price,
                    'invoice_no' => $data->invoice_number,
                    'date' => $data->invoice_date,
                    'commande_boisson_no' => $data->bartender_order_no,
                    'type_transaction' => 'RETOUR DES MARCHANDISES',
                    'document_no' => $data->invoice_number,
                    'created_by' => $this->user->name,
                    'employe_id' => $data->employe_id,
                    'origine_facture' => 'BOISSON',
                    'created_at' => \Carbon\Carbon::now()
                );
                $report[] = $reportData;

                $donnees = array(
                        'bartender_item_id' => $data->bartender_item_id,
                        'quantity' => $quantityTotal,
                        'total_selling_value' => $quantityTotal * $data->item_price,
                        'created_by' => $this->user->name,
                        'verified' => false
                    );


                    $noteCreditdata = array(
                        'bartender_item_id' => $data->bartender_item_id,
                        'invoice_number'=>$data->invoice_number,
                        'invoice_date'=> $data->invoice_date,
                        'tp_type'=>$data->tp_type,
                        'tp_name'=>$data->tp_name,
                        'tp_TIN'=>$data->tp_TIN,
                        'tp_trade_number'=>$data->tp_trade_number,
                        'tp_phone_number'=>$data->tp_phone_number,
                        'tp_address_province'=>$data->tp_address_province,
                        'tp_address_commune'=>$data->tp_address_commune,
                        'tp_address_quartier'=>$data->tp_address_quartier,
                        'tp_address_avenue'=>$data->tp_address_avenue,
                        'tp_address_rue'=>$data->tp_address_rue,
                        'vat_taxpayer'=>$data->vat_taxpayer,
                        'ct_taxpayer'=>$data->ct_taxpayer,
                        'tl_taxpayer'=>$data->tl_taxpayer,
                        'tp_fiscal_center'=>$data->tp_fiscal_center,
                        'tp_activity_sector'=>$data->tp_activity_sector,
                        'tp_legal_form'=>$data->tp_legal_form,
                        'payment_type'=>$data->payment_type,
                        'client_id'=>$data->client_id,
                        'customer_TIN'=>$data->customer_TIN,
                        'customer_address'=>$data->customer_address,
                        'invoice_signature'=> $invoice_signature,
                        'drink_order_no'=>$data->drink_order_no,
                        'cancelled_invoice_ref'=>$data->cancelled_invoice_ref,
                        'cn_motif'=>$data->cn_motif,
                        'invoice_currency'=>$data->invoice_currency,
                        'invoice_ref'=>$data->invoice_ref,
                        'auteur' => $this->user->name,
                        'etat' => $data->etat,
                        'invoice_signature_date'=> Carbon::now(),
                        //'table_id'=>$data->table_id,
                        'item_quantity'=>$data->item_quantity,
                        'item_price'=>-$data->item_price,
                        'item_ct'=>$data->item_ct,
                        'item_tl'=>$data->item_tl,
                        'item_price_nvat'=>$data->item_price_nvat,
                        'vat'=>$data->vat,
                        'item_price_wvat'=>$data->item_price_wvat,
                        'item_total_amount'=>$data->item_total_amount,
                        'employe_id'=> $data->employe_id,
                    );

                    $noteCredit[] = $noteCreditdata;
                    
                        
                        BartenderProductionStore::where('bartender_item_id',$data->bartender_item_id)
                        ->update($donnees);

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
            }

        }

        BartenderSmallReport::insert($report);

        FactureDetail::insert($noteCredit);

        NoteCredit::where('invoice_number', '=', $invoice_number)
            ->update(['statut' => 2,'validated_by' => $this->user->name]);
        NoteCreditDetail::where('invoice_number', '=', $invoice_number)
            ->update(['statut' => 2,'validated_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'La note de credit a été validé avec succés!!');
            return back();
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

        $datas = NoteCreditDetail::where('invoice_number', $invoice_number)->get();

        foreach ($datas as $data) {
            if (!empty($data->salle_id)) {
                $noteCreditdata = array(
                        'salle_id' => $data->salle_id,
                        'invoice_number'=>$data->invoice_number,
                        'invoice_date'=> $data->invoice_date,
                        'tp_type'=>$data->tp_type,
                        'tp_name'=>$data->tp_name,
                        'tp_TIN'=>$data->tp_TIN,
                        'tp_trade_number'=>$data->tp_trade_number,
                        'tp_phone_number'=>$data->tp_phone_number,
                        'tp_address_province'=>$data->tp_address_province,
                        'tp_address_commune'=>$data->tp_address_commune,
                        'tp_address_quartier'=>$data->tp_address_quartier,
                        'tp_address_avenue'=>$data->tp_address_avenue,
                        'tp_address_rue'=>$data->tp_address_rue,
                        'vat_taxpayer'=>$data->vat_taxpayer,
                        'ct_taxpayer'=>$data->ct_taxpayer,
                        'tl_taxpayer'=>$data->tl_taxpayer,
                        'tp_fiscal_center'=>$data->tp_fiscal_center,
                        'tp_activity_sector'=>$data->tp_activity_sector,
                        'tp_legal_form'=>$data->tp_legal_form,
                        'payment_type'=>$data->payment_type,
                        'client_id'=>$data->client_id,
                        'customer_TIN'=>$data->customer_TIN,
                        'customer_address'=>$data->customer_address,
                        'invoice_signature'=> $invoice_signature,
                        'booking_no'=>$data->booking_no,
                        'cancelled_invoice_ref'=>$data->cancelled_invoice_ref,
                        'cn_motif'=>$data->cn_motif,
                        'invoice_currency'=>$data->invoice_currency,
                        'invoice_ref'=>$data->invoice_ref,
                        'auteur' => $this->user->name,
                        'etat' => $data->etat,
                        'invoice_signature_date'=> Carbon::now(),
                        //'table_id'=>$data->table_id,
                        'item_quantity'=>$data->item_quantity,
                        'item_price'=>$data->item_price,
                        'item_ct'=>$data->item_ct,
                        'item_tl'=>$data->item_tl,
                        'item_price_nvat'=>$data->item_price_nvat,
                        'vat'=>$data->vat,
                        'item_price_wvat'=>$data->item_price_wvat,
                        'item_total_amount'=>$data->item_total_amount,
                        'employe_id'=> $data->employe_id,
                    );
            }elseif (!empty($data->swiming_pool_id)) {
                $noteCreditdata = array(
                        'swiming_pool_id' => $data->swiming_pool_id,
                        'invoice_number'=>$data->invoice_number,
                        'invoice_date'=> $data->invoice_date,
                        'tp_type'=>$data->tp_type,
                        'tp_name'=>$data->tp_name,
                        'tp_TIN'=>$data->tp_TIN,
                        'tp_trade_number'=>$data->tp_trade_number,
                        'tp_phone_number'=>$data->tp_phone_number,
                        'tp_address_province'=>$data->tp_address_province,
                        'tp_address_commune'=>$data->tp_address_commune,
                        'tp_address_quartier'=>$data->tp_address_quartier,
                        'tp_address_avenue'=>$data->tp_address_avenue,
                        'tp_address_rue'=>$data->tp_address_rue,
                        'vat_taxpayer'=>$data->vat_taxpayer,
                        'ct_taxpayer'=>$data->ct_taxpayer,
                        'tl_taxpayer'=>$data->tl_taxpayer,
                        'tp_fiscal_center'=>$data->tp_fiscal_center,
                        'tp_activity_sector'=>$data->tp_activity_sector,
                        'tp_legal_form'=>$data->tp_legal_form,
                        'payment_type'=>$data->payment_type,
                        'client_id'=>$data->client_id,
                        'customer_TIN'=>$data->customer_TIN,
                        'customer_address'=>$data->customer_address,
                        'invoice_signature'=> $invoice_signature,
                        'booking_no'=>$data->booking_no,
                        'cancelled_invoice_ref'=>$data->cancelled_invoice_ref,
                        'cn_motif'=>$data->cn_motif,
                        'invoice_currency'=>$data->invoice_currency,
                        'invoice_ref'=>$data->invoice_ref,
                        'auteur' => $this->user->name,
                        'etat' => $data->etat,
                        'invoice_signature_date'=> Carbon::now(),
                        //'table_id'=>$data->table_id,
                        'item_quantity'=>$data->item_quantity,
                        'item_price'=>$data->item_price,
                        'item_ct'=>$data->item_ct,
                        'item_tl'=>$data->item_tl,
                        'item_price_nvat'=>$data->item_price_nvat,
                        'vat'=>$data->vat,
                        'item_price_wvat'=>$data->item_price_wvat,
                        'item_total_amount'=>$data->item_total_amount,
                        'employe_id'=> $data->employe_id,
                    );
            }elseif (!empty($data->service_id)) {
                $noteCreditdata = array(
                        'service_id' => $data->service_id,
                        'invoice_number'=>$data->invoice_number,
                        'invoice_date'=> $data->invoice_date,
                        'tp_type'=>$data->tp_type,
                        'tp_name'=>$data->tp_name,
                        'tp_TIN'=>$data->tp_TIN,
                        'tp_trade_number'=>$data->tp_trade_number,
                        'tp_phone_number'=>$data->tp_phone_number,
                        'tp_address_province'=>$data->tp_address_province,
                        'tp_address_commune'=>$data->tp_address_commune,
                        'tp_address_quartier'=>$data->tp_address_quartier,
                        'tp_address_avenue'=>$data->tp_address_avenue,
                        'tp_address_rue'=>$data->tp_address_rue,
                        'vat_taxpayer'=>$data->vat_taxpayer,
                        'ct_taxpayer'=>$data->ct_taxpayer,
                        'tl_taxpayer'=>$data->tl_taxpayer,
                        'tp_fiscal_center'=>$data->tp_fiscal_center,
                        'tp_activity_sector'=>$data->tp_activity_sector,
                        'tp_legal_form'=>$data->tp_legal_form,
                        'payment_type'=>$data->payment_type,
                        'client_id'=>$data->client_id,
                        'customer_TIN'=>$data->customer_TIN,
                        'customer_address'=>$data->customer_address,
                        'invoice_signature'=> $invoice_signature,
                        'booking_no'=>$data->booking_no,
                        'cancelled_invoice_ref'=>$data->cancelled_invoice_ref,
                        'cn_motif'=>$data->cn_motif,
                        'invoice_currency'=>$data->invoice_currency,
                        'invoice_ref'=>$data->invoice_ref,
                        'auteur' => $this->user->name,
                        'etat' => $data->etat,
                        'invoice_signature_date'=> Carbon::now(),
                        //'table_id'=>$data->table_id,
                        'item_quantity'=>$data->item_quantity,
                        'item_price'=>$data->item_price,
                        'item_ct'=>$data->item_ct,
                        'item_tl'=>$data->item_tl,
                        'item_price_nvat'=>$data->item_price_nvat,
                        'vat'=>$data->vat,
                        'item_price_wvat'=>$data->item_price_wvat,
                        'item_total_amount'=>$data->item_total_amount,
                        'employe_id'=> $data->employe_id,
                    );
            }elseif (!empty($data->breakfast_id)) {
                $noteCreditdata = array(
                        'breakfast_id' => $data->breakfast_id,
                        'invoice_number'=>$data->invoice_number,
                        'invoice_date'=> $data->invoice_date,
                        'tp_type'=>$data->tp_type,
                        'tp_name'=>$data->tp_name,
                        'tp_TIN'=>$data->tp_TIN,
                        'tp_trade_number'=>$data->tp_trade_number,
                        'tp_phone_number'=>$data->tp_phone_number,
                        'tp_address_province'=>$data->tp_address_province,
                        'tp_address_commune'=>$data->tp_address_commune,
                        'tp_address_quartier'=>$data->tp_address_quartier,
                        'tp_address_avenue'=>$data->tp_address_avenue,
                        'tp_address_rue'=>$data->tp_address_rue,
                        'vat_taxpayer'=>$data->vat_taxpayer,
                        'ct_taxpayer'=>$data->ct_taxpayer,
                        'tl_taxpayer'=>$data->tl_taxpayer,
                        'tp_fiscal_center'=>$data->tp_fiscal_center,
                        'tp_activity_sector'=>$data->tp_activity_sector,
                        'tp_legal_form'=>$data->tp_legal_form,
                        'payment_type'=>$data->payment_type,
                        'client_id'=>$data->client_id,
                        'customer_TIN'=>$data->customer_TIN,
                        'customer_address'=>$data->customer_address,
                        'invoice_signature'=> $invoice_signature,
                        'booking_no'=>$data->booking_no,
                        'cancelled_invoice_ref'=>$data->cancelled_invoice_ref,
                        'cn_motif'=>$data->cn_motif,
                        'invoice_currency'=>$data->invoice_currency,
                        'invoice_ref'=>$data->invoice_ref,
                        'auteur' => $this->user->name,
                        'etat' => $data->etat,
                        'invoice_signature_date'=> Carbon::now(),
                        //'table_id'=>$data->table_id,
                        'item_quantity'=>$data->item_quantity,
                        'item_price'=>$data->item_price,
                        'item_ct'=>$data->item_ct,
                        'item_tl'=>$data->item_tl,
                        'item_price_nvat'=>$data->item_price_nvat,
                        'vat'=>$data->vat,
                        'item_price_wvat'=>$data->item_price_wvat,
                        'item_total_amount'=>$data->item_total_amount,
                        'employe_id'=> $data->employe_id,
                    );
            }elseif (!empty($data->kidness_space_id)) {
                $noteCreditdata = array(
                        'kidness_space_id' => $data->kidness_space_id,
                        'invoice_number'=>$data->invoice_number,
                        'invoice_date'=> $data->invoice_date,
                        'tp_type'=>$data->tp_type,
                        'tp_name'=>$data->tp_name,
                        'tp_TIN'=>$data->tp_TIN,
                        'tp_trade_number'=>$data->tp_trade_number,
                        'tp_phone_number'=>$data->tp_phone_number,
                        'tp_address_province'=>$data->tp_address_province,
                        'tp_address_commune'=>$data->tp_address_commune,
                        'tp_address_quartier'=>$data->tp_address_quartier,
                        'tp_address_avenue'=>$data->tp_address_avenue,
                        'tp_address_rue'=>$data->tp_address_rue,
                        'vat_taxpayer'=>$data->vat_taxpayer,
                        'ct_taxpayer'=>$data->ct_taxpayer,
                        'tl_taxpayer'=>$data->tl_taxpayer,
                        'tp_fiscal_center'=>$data->tp_fiscal_center,
                        'tp_activity_sector'=>$data->tp_activity_sector,
                        'tp_legal_form'=>$data->tp_legal_form,
                        'payment_type'=>$data->payment_type,
                        'client_id'=>$data->client_id,
                        'customer_TIN'=>$data->customer_TIN,
                        'customer_address'=>$data->customer_address,
                        'invoice_signature'=> $invoice_signature,
                        'booking_no'=>$data->booking_no,
                        'cancelled_invoice_ref'=>$data->cancelled_invoice_ref,
                        'cn_motif'=>$data->cn_motif,
                        'invoice_currency'=>$data->invoice_currency,
                        'invoice_ref'=>$data->invoice_ref,
                        'auteur' => $this->user->name,
                        'etat' => $data->etat,
                        'invoice_signature_date'=> Carbon::now(),
                        //'table_id'=>$data->table_id,
                        'item_quantity'=>$data->item_quantity,
                        'item_price'=>$data->item_price,
                        'item_ct'=>$data->item_ct,
                        'item_tl'=>$data->item_tl,
                        'item_price_nvat'=>$data->item_price_nvat,
                        'vat'=>$data->vat,
                        'item_price_wvat'=>$data->item_price_wvat,
                        'item_total_amount'=>$data->item_total_amount,
                        'employe_id'=> $data->employe_id,
                    );
            }

                $noteCredit[] = $noteCreditdata;
        }

        FactureDetail::insert($noteCredit);

        NoteCredit::where('invoice_number', '=', $invoice_number)
            ->update(['statut' => 2,'validated_by' => $this->user->name]);
        NoteCreditDetail::where('invoice_number', '=', $invoice_number)
            ->update(['statut' => 2,'validated_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'La note de credit a été validé avec succés!!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

    public function validerFactureNourriture($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('invoice_kitchen.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        try {DB::beginTransaction();

        $datas = NoteCreditDetail::where('invoice_number', $invoice_number)->get();

        foreach ($datas as $data) {
                $noteCreditdata = array(
                        'food_item_id' => $data->food_item_id,
                        'invoice_number'=>$data->invoice_number,
                        'invoice_date'=> $data->invoice_date,
                        'tp_type'=>$data->tp_type,
                        'tp_name'=>$data->tp_name,
                        'tp_TIN'=>$data->tp_TIN,
                        'tp_trade_number'=>$data->tp_trade_number,
                        'tp_phone_number'=>$data->tp_phone_number,
                        'tp_address_province'=>$data->tp_address_province,
                        'tp_address_commune'=>$data->tp_address_commune,
                        'tp_address_quartier'=>$data->tp_address_quartier,
                        'tp_address_avenue'=>$data->tp_address_avenue,
                        'tp_address_rue'=>$data->tp_address_rue,
                        'vat_taxpayer'=>$data->vat_taxpayer,
                        'ct_taxpayer'=>$data->ct_taxpayer,
                        'tl_taxpayer'=>$data->tl_taxpayer,
                        'tp_fiscal_center'=>$data->tp_fiscal_center,
                        'tp_activity_sector'=>$data->tp_activity_sector,
                        'tp_legal_form'=>$data->tp_legal_form,
                        'payment_type'=>$data->payment_type,
                        'client_id'=>$data->client_id,
                        'customer_TIN'=>$data->customer_TIN,
                        'customer_address'=>$data->customer_address,
                        'invoice_signature'=> $invoice_signature,
                        'food_order_no'=>$data->food_order_no,
                        'cancelled_invoice_ref'=>$data->cancelled_invoice_ref,
                        'cn_motif'=>$data->cn_motif,
                        'invoice_currency'=>$data->invoice_currency,
                        'invoice_ref'=>$data->invoice_ref,
                        'auteur' => $this->user->name,
                        'etat' => $data->etat,
                        'invoice_signature_date'=> Carbon::now(),
                        //'table_id'=>$data->table_id,
                        'item_quantity'=>$data->item_quantity,
                        'item_price'=>$data->item_price,
                        'item_ct'=>$data->item_ct,
                        'item_tl'=>$data->item_tl,
                        'item_price_nvat'=>$data->item_price_nvat,
                        'vat'=>$data->vat,
                        'item_price_wvat'=>$data->item_price_wvat,
                        'item_total_amount'=>$data->item_total_amount,
                        'employe_id'=> $data->employe_id,
                    );

                $noteCredit[] = $noteCreditdata;
        }

        FactureDetail::insert($noteCredit);

        NoteCredit::where('invoice_number', '=', $invoice_number)
            ->update(['statut' => 2,'validated_by' => $this->user->name]);
        NoteCreditDetail::where('invoice_number', '=', $invoice_number)
            ->update(['statut' => 2,'validated_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'La note de credit a été validé avec succés!!');
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
        if (is_null($this->user) || !$this->user->can('invoice_drink.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }


        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $datas = NoteCreditDetail::where('invoice_number', $invoice_number)->get();
        $facture = NoteCredit::where('invoice_number', $invoice_number)->first();
        $invoice_signature = NoteCredit::where('invoice_number', $invoice_number)->value('invoice_signature');
        $data = NoteCredit::where('invoice_number', $invoice_number)->first();
        $totalValue = DB::table('note_credit_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_price_nvat');
        $item_total_amount = DB::table('note_credit_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');
        $totalVat = DB::table('note_credit_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('vat');
        $client = NoteCredit::where('invoice_number', $invoice_number)->value('customer_name');
        $date = NoteCredit::where('invoice_number', $invoice_number)->value('invoice_date');
       
        $pdf = PDF::loadView('backend.pages.note_credit.facture',compact('datas','invoice_number','totalValue','item_total_amount','client','setting','date','data','invoice_signature','facture','totalVat'))->setPaper('a4', 'portrait');

        Storage::put('public/note_credit/'.$invoice_number.'.pdf', $pdf->output());


        $factures = NoteCredit::where('invoice_number', $invoice_number)->get();

        $datas = NoteCreditDetail::where('invoice_number', $invoice_number)->get();

        NoteCredit::where('invoice_number', '=', $invoice_number)
                ->update(['statut' => 1]);
            NoteCreditDetail::where('invoice_number', '=', $invoice_number)
                ->update(['statut' => 1]);

            // download pdf file
        return $pdf->download('FACTURE_D_AVOIR_'.$invoice_number.'.pdf');

        /*
        $theUrl = config('app.guzzle_test_url').'/ebms_api/login/';
        $response = Http::post($theUrl, [
            'username'=> "wsconfig('app.tin_number_company')00565",
            'password'=> "5VS(GO:p"

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

        $factureDetails = NoteCreditDetail::where('invoice_number',$invoice_number)->get();
        $facture = NoteCredit::with('employe')->where('invoice_number',$invoice_number)->first();
        $total_amount = DB::table('note_credit_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');
        return view('backend.pages.note_credit.show',compact('facture','factureDetails','total_amount'));
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
        $clients =  Client::orderBy('customer_name','asc')->get();

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

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.rapport_facture_boisson',compact('datas','dateTime','setting','end_date','start_date','total_amount','total_amount_credit','credits','total_vat','total_item_price_nvat','total_vat_credit','total_item_price_nvat_credit'))->setPaper('a4', 'landscape');
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

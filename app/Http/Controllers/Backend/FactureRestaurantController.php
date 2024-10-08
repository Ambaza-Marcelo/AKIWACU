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
//use GuzzleHttp\EGRClient;
use App\Models\Facture;
use App\Models\FactureDetail;
use App\Models\OrderKitchenDetail;
use App\Models\OrderKitchen;
use App\Models\DrinkSmallStore;
use App\Models\DrinkBigStore;
use App\Models\FoodBigStore;
use App\Models\MaterialBigStore;
use App\Models\FoodItem;
use App\Models\Employe;
use App\Models\EGRClient;
use App\Models\Table;
use App\Models\NoteCreditDetail;
use App\Models\NoteCredit;
use App\Mail\ReportFoodMail;
use App\Exports\ChiffreAffaireExport;
use App\Exports\FactureCreditExport;
use App\Exports\FacturePayeExport;
use App\Exports\FactureAnnuleExport;
use App\Exports\FactureEncoursExport;
use App\Exports\FactureArecouvreExport;
use App\Exports\FactureRecouvreExport;

class FactureRestaurantController extends Controller
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
        if (is_null($this->user) || !$this->user->can('invoice_kitchen.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factures = Facture::where('food_order_no','!=','')->take(300)->orderBy('id','desc')->get();
        return view('backend.pages.invoice_kitchen.index',compact('factures'));
    }

    public function voirFacturePayercredit(Request $request,$invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('recouvrement.view')) {
            abort(403, 'Sorry !! You are Unauthorized to do this ! more information,contact IT Msumba Holding Marcellin');
        }


        $facture = Facture::where('invoice_number',$invoice_number)->where('etat','01')->first();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();
        $datas = FactureDetail::where('invoice_number',$invoice_number)->where('etat','01')->get();
        $total_amount = DB::table('facture_details')
            ->where('invoice_number',$invoice_number)->where('etat','01')->sum('item_total_amount');
        $r_credit = DB::table('factures')
            ->where('invoice_number',$invoice_number)->where('etat','01')->sum('reste_credit');
        if (!empty($r_credit)) {
            $reste_credit = $r_credit;
        }else{
            $reste_credit = $total_amount;
        }

        $montant_recouvre = DB::table('factures')
            ->where('invoice_number',$invoice_number)->where('etat','01')->sum('montant_recouvre');

        return view('backend.pages.invoice_all.payer-credit',compact('datas','facture','total_amount','clients','reste_credit','montant_recouvre'));
    }

    public function voirFactureAcredit()
    {
        if (is_null($this->user) || !$this->user->can('recouvrement.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view this ! more information,contact Marcellin');
        }


        $factures = Facture::where('etat','01')->orderBy('id','desc')->get();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();
        return view('backend.pages.invoice_all.credit',compact('factures','clients'));
    }

    public function creditPayes()
    {
        if (is_null($this->user) || !$this->user->can('recouvrement.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factures = Facture::where('statut_paied','!=','0')->orderBy('id','desc')->get();
        return view('backend.pages.invoice_all.credit_paid',compact('factures'));
    }

    public function validerPaye($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('recouvrement.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        try {DB::beginTransaction();

        Facture::where('invoice_number', '=', $invoice_number)
                ->update(['paid_either' => '1','approuved_by' => $this->user->name]);
        FactureDetail::where('invoice_number', '=', $invoice_number)
                ->update(['paid_either' => '1','approuved_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'vous avez approuvé le paiement du crédit avec succés');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function exporterChiffreAffaire(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.view')) {
            abort(403, 'Sorry !! You are Unauthorized to export this ! more information you have to contact Marcellin');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        $datas = FactureDetail::select(
                        DB::raw('sum(item_total_amount) as item_total_amount,sum(item_price_nvat) as item_price_nvat,sum(vat) as vat'))->where('etat','!=','0')->where('etat','!=','-1')->whereBetween('invoice_date',[$start_date,$end_date])->get();

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.chiffre_affaire',compact('datas','dateTime','setting','end_date','start_date'))->setPaper('a4', 'portrait');

        //Storage::put('public/journal_general/'.$d1.'_'.$d2.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download("chiffre_affaire".$dateTime.'.pdf');

        
    }

    public function chiffreAffaire()
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view this ! more information,contact Marcellin');
        }

        $item_total_amount = DB::table('facture_details')->where('etat','1')->sum('item_total_amount');
        $total_vat = DB::table('facture_details')->where('etat','1')->sum('vat');
        $total_item_price_nvat = DB::table('facture_details')->where('etat','1')->sum('item_price_nvat');

        $item_total_amount_credit = DB::table('facture_details')->where('etat','01')->sum('item_total_amount');
        $total_vat_credit = DB::table('facture_details')->where('etat','01')->sum('vat');
        $total_item_price_nvat_credit = DB::table('facture_details')->where('etat','01')->sum('item_price_nvat');

        $datas = FactureDetail::select(
                        DB::raw('id,drink_id,food_item_id,barrist_item_id,bartender_item_id,service_id,salle_id,sum(item_total_amount) as item_total_amount'))->where('etat','1')->groupBy('id','drink_id','food_item_id','barrist_item_id','bartender_item_id','service_id','salle_id')->orderBy('item_total_amount','desc')->take(10)->get();
        $drinksmstores = DrinkSmallStore::all();
        $drinkbgstores = DrinkBigStore::all();
        $foodbgstores = FoodBigStore::all();
        $materialbgstores = MaterialBigStore::all();

        return view('backend.pages.invoice.chiffre_affaire',compact('item_total_amount','total_vat','total_item_price_nvat','item_total_amount_credit','total_vat_credit','total_item_price_nvat_credit','datas','drinksmstores','foodbgstores','materialbgstores','drinkbgstores'));
    }


    public function create($order_no)
    {
        if (is_null($this->user) || !$this->user->can('invoice_kitchen.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $food_items =  FoodItem::orderBy('name','asc')->get();
        $orders =  OrderKitchenDetail::where('order_no',$order_no)->orderBy('id','desc')->get();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();
        $data =  OrderKitchen::where('order_no',$order_no)->first();
        $table_id = OrderKitchen::where('order_no',$order_no)->value('table_id');

        $total_amount = DB::table('order_kitchen_details')
            ->where('order_no', '=', $order_no)
            ->sum('total_amount_selling');

        return view('backend.pages.invoice_kitchen.create',compact('food_items','data','setting','orders','order_no','clients','table_id','total_amount'));
    }

    public function createByTable($table_id)
    {
        if (is_null($this->user) || !$this->user->can('invoice_kitchen.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $food_items =  FoodItem::orderBy('name','asc')->get();
        $orders =  OrderKitchenDetail::where('table_id',$table_id)->where('status',1)->orderBy('id','desc')->get();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();
        $data =  OrderKitchenDetail::where('table_id',$table_id)->where('status',1)->orderBy('id','desc')->first();

        $total_amount = DB::table('order_kitchen_details')
            ->where('table_id',$table_id)->where('status',1)
            ->sum('total_amount_selling');

        return view('backend.pages.invoice_kitchen.create',compact('food_items','data','setting','orders','clients','table_id','total_amount'));
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
        if (is_null($this->user) || !$this->user->can('invoice_kitchen.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factureDetails = FactureDetail::where('invoice_number',$invoice_number)->get();
        $facture = Facture::with('employe')->where('invoice_number',$invoice_number)->first();
        $total_amount = DB::table('facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');
        return view('backend.pages.invoice_kitchen.show',compact('facture','factureDetails','total_amount'));
    }


    public function edit($food_order_no)
    {
        if (is_null($this->user) || !$this->user->can('invoice_kitchen.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $invoice_number = Facture::where('food_order_no',$food_order_no)->value('invoice_number');

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $food_items =  FoodItem::orderBy('name','asc')->get();
        $datas =  OrderKitchenDetail::where('order_no',$food_order_no)->orderBy('order_no','asc')->get();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();
        $data =  Facture::where('invoice_number',$invoice_number)->first();
        $table_id = OrderKitchen::where('order_no',$order_no)->value('table_id');
        return view('backend.pages.invoice_kitchen.edit',compact('food_items','data','setting','datas','food_order_no','clients','invoice_number'));
    }

    public function update(Request  $request,$invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('invoice_kitchen.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any invoice !');
        }

        $rules = array(
                'invoice_date' => 'required',
                //'invoice_number' => 'required',
                'tp_type' => 'required',
                'tp_name' => 'required|max:100|min:3',
                'tp_TIN' => 'required|max:30|min:4',
                'tp_trade_number' => 'required|max:20|min:4',
                'tp_phone_number' => 'required|max:20|min:6',
                'tp_address_commune' => 'required|max:50|min:5',
                'tp_address_quartier' => 'required|max:50|min:5',
                //'EGRClient_id' => 'required|max:100|min:3',
                //'customer_TIN' => 'required|max:30|min:4',
                //'customer_address' => 'required|max:100|min:5',
                //'invoice_signature' => 'required|max:90|min:10',
                //'invoice_signature_date' => 'required|max: |min:',
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

            $food_item_id = $request->food_item_id;
            $item_quantity = $request->item_quantity;
            $item_price = $request->item_price;
            $item_ct = $request->item_ct;
            $item_tl =$request->item_tl; 

            $employe_id = $request->employe_id;

            try {DB::beginTransaction();

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
            'EGRClient_id'=>$request->EGRClient_id,
            'customer_TIN'=>$request->customer_TIN,
            'customer_address'=>$request->customer_address,
            'food_order_no'=>$request->food_order_no,
            'cancelled_invoice_ref'=>$request->cancelled_invoice_ref,
            'cancelled_invoice'=>$request->cancelled_invoice,
            'invoice_currency'=>$request->invoice_currency,
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
        );
          $data1[] = $data;
          FactureDetail::where('invoice_number',$invoice_number)->delete();
      }


        FactureDetail::insert($data1);


            //create facture
            $facture = Facture::where('invoice_number',$invoice_number)->first();
            $facture->invoice_date =  $request->invoice_date;
            $facture->tp_type = $request->tp_type;
            $facture->tp_name = $request->tp_name;
            $facture->tp_TIN = $request->tp_TIN;
            $facture->tp_trade_number = $request->tp_trade_number;
            $facture->tp_phone_number = $request->tp_phone_number;
            $facture->tp_address_province = $request->tp_address_province;
            $facture->tp_address_commune = $request->tp_address_commune;
            $facture->tp_address_quartier = $request->tp_address_quartier;
            $facture->food_order_no = $request->food_order_no;
            $facture->vat_taxpayer = $request->vat_taxpayer;
            $facture->ct_taxpayer = $request->ct_taxpayer;
            $facture->tl_taxpayer = $request->tl_taxpayer;
            $facture->tp_fiscal_center = $request->tp_fiscal_center;
            $facture->tp_activity_sector = $request->tp_activity_sector;
            $facture->tp_legal_form = $request->tp_legal_form;
            $facture->invoice_currency = $request->invoice_currency;
            $facture->payment_type = $request->payment_type;
            $facture->EGRClient_id = $request->EGRClient_id;
            $facture->customer_TIN = $request->customer_TIN;
            $facture->customer_address = $request->customer_address;
            $facture->cancelled_invoice_ref = $request->cancelled_invoice_ref;
            $facture->cancelled_invoice = $request->cancelled_invoice;
            $facture->invoice_ref = $request->invoice_ref;
            $facture->employe_id = $employe_id;
            $facture->invoice_signature_date = Carbon::now();
            $facture->save();

            OrderKitchen::where('order_no', '=', $facture->food_order_no)
                ->update(['status' => 2,'confirmed_by' => $this->user->name]);
            OrderKitchenDetail::where('order_no', '=', $facture->food_order_no)
                ->update(['status' => 2,'confirmed_by' => $this->user->name]);

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


    public function report()
    {
        if (is_null($this->user) || !$this->user->can('invoice_kitchen.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factures = FactureDetail::orderBy('id','desc')->take(10000)->get();
        $clients = EGRClient::orderBy('customer_name')->get();
        return view('backend.pages.invoice.report',compact('factures','clients'));
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function numberToWord($num = '')
    {
        $num    = ( string ) ( ( int ) $num );
        
        if( ( int ) ( $num ) && ctype_digit( $num ) )
        {
            $words  = array( );
             
            $num    = str_replace( array( ',' , ' ' ) , '' , trim( $num ) );
             
            $list1  = array('','one','two','three','four','five','six','seven',
                'eight','nine','ten','eleven','twelve','thirteen','fourteen',
                'fifteen','sixteen','seventeen','eighteen','nineteen');
             
            $list2  = array('','ten','twenty','thirty','forty','fifty','sixty',
                'seventy','eighty','ninety','hundred');
             
            $list3  = array('','thousand','million','billion','trillion',
                'quadrillion','quintillion','sextillion','septillion',
                'octillion','nonillion','decillion','undecillion',
                'duodecillion','tredecillion','quattuordecillion',
                'quindecillion','sexdecillion','septendecillion',
                'octodecillion','novemdecillion','vigintillion');
             
            $num_length = strlen( $num );
            $levels = ( int ) ( ( $num_length + 2 ) / 3 );
            $max_length = $levels * 3;
            $num    = substr( '00'.$num , -$max_length );
            $num_levels = str_split( $num , 3 );
             
            foreach( $num_levels as $num_part )
            {
                $levels--;
                $hundreds   = ( int ) ( $num_part / 100 );
                $hundreds   = ( $hundreds ? ' ' . $list1[$hundreds] . ' Hundred' . ( $hundreds == 1 ? '' : 's' ) . ' ' : '' );
                $tens       = ( int ) ( $num_part % 100 );
                $singles    = '';
                 
                if( $tens < 20 ) { $tens = ( $tens ? ' ' . $list1[$tens] . ' ' : '' ); } else { $tens = ( int ) ( $tens / 10 ); $tens = ' ' . $list2[$tens] . ' '; $singles = ( int ) ( $num_part % 10 ); $singles = ' ' . $list1[$singles] . ' '; } $words[] = $hundreds . $tens . $singles . ( ( $levels && ( int ) ( $num_part ) ) ? ' ' . $list3[$levels] . ' ' : '' ); } $commas = count( $words ); if( $commas > 1 )
            {
                $commas = $commas - 1;
            }
             
            $words  = implode( ', ' , $words );
             
            $words  = trim( str_replace( ' ,' , ',' , ucwords( $words ) )  , ', ' );
            if( $commas )
            {
                $words  = str_replace( ',' , ' and' , $words );
            }
             
            return $words;
        }
        else if( ! ( ( int ) $num ) )
        {
            return 'Zero';
        }
        return '';
    }

    public function factureGlobale(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('invoice_kitchen.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view this ! more information,contact Marcellin');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');
        $client_id = $request->query('client_id');

        $data = Facture::where('client_id',$client_id)->first();

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        $item_total_amount = DB::table('facture_details')->where('etat','1')->where('client_id',$client_id)->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
        $total_vat = DB::table('facture_details')->where('etat','1')->where('client_id',$client_id)->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');

        $item_total_amount_credit = DB::table('facture_details')->where('etat','01')->where('client_id',$client_id)->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
        $total_vat_credit = DB::table('facture_details')->where('etat','01')->where('client_id',$client_id)->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');


        $item_total_amount_kitchen = DB::table('facture_details')->where('etat','01')->where('food_item_id','!=','')->where('client_id',$client_id)->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
        $total_vat_kitchen = DB::table('facture_details')->where('etat','01')->where('food_item_id','!=','')->where('client_id',$client_id)->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
        $item_total_nvat_kitchen = DB::table('facture_details')->where('etat','01')->where('food_item_id','!=','')->where('client_id',$client_id)->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');

        $item_total_amount_drink = DB::table('facture_details')->where('etat','01')->where('drink_id','!=','')->where('client_id',$client_id)->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
        $total_vat_drink = DB::table('facture_details')->where('etat','01')->where('drink_id','!=','')->where('client_id',$client_id)->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
        $item_total_nvat_drink = DB::table('facture_details')->where('etat','01')->where('drink_id','!=','')->where('client_id',$client_id)->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');

        $item_total_amount_barrista = DB::table('facture_details')->where('etat','01')->where('barrist_item_id','!=','')->where('client_id',$client_id)->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
        $total_vat_barrista = DB::table('facture_details')->where('etat','01')->where('barrist_item_id','!=','')->where('client_id',$client_id)->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
        $item_total_nvat_barrista = DB::table('facture_details')->where('etat','01')->where('barrist_item_id','!=','')->where('client_id',$client_id)->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');

        $item_total_amount_bartender = DB::table('facture_details')->where('etat','01')->where('bartender_item_id','!=','')->where('client_id',$client_id)->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
        $total_vat_bartender = DB::table('facture_details')->where('etat','01')->where('bartender_item_id','!=','')->where('client_id',$client_id)->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
        $item_total_nvat_bartender = DB::table('facture_details')->where('etat','01')->where('bartender_item_id','!=','')->where('client_id',$client_id)->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');

        $item_total_amount_service = DB::table('facture_details')->where('etat','01')->where('booking_no','!=','')->where('client_id',$client_id)->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
        $total_vat_service = DB::table('facture_details')->where('etat','01')->where('booking_no','!=','')->where('client_id',$client_id)->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
        $item_total_nvat_service = DB::table('facture_details')->where('etat','01')->where('booking_no','!=','')->where('client_id',$client_id)->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');

        $montant_total_global = ($item_total_amount_drink + $item_total_amount_kitchen + $item_total_amount_barrista + $item_total_amount_bartender + $item_total_amount_service);

        $montant_total_global_en_lettre = $this->numberToWord($montant_total_global);

        if (!empty($data->client_id)) {
            $customer_name = $data->client->customer_name;
        }else{
            $customer_name = $data->customer_name;
        }

        $datas = FactureDetail::select(
                        DB::raw('id,drink_id,food_item_id,barrist_item_id,bartender_item_id,service_id,salle_id,invoice_number,invoice_date,sum(item_quantity) as item_quantity,sum(item_price) as item_price,sum(vat) as vat,sum(item_price_nvat) as item_price_nvat,sum(item_total_amount) as item_total_amount'))->where('etat','1')->where('client_id',$client_id)->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','drink_id','food_item_id','barrist_item_id','bartender_item_id','service_id','salle_id')->orderBy('invoice_number')->get();
        $credits = FactureDetail::select(
                        DB::raw('id,drink_id,food_item_id,barrist_item_id,bartender_item_id,service_id,salle_id,invoice_number,invoice_date,sum(item_quantity) as item_quantity,sum(item_price) as item_price,sum(vat) as vat,sum(item_price_nvat) as item_price_nvat,sum(item_total_amount) as item_total_amount'))->where('etat','01')->where('client_id',$client_id)->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','drink_id','food_item_id','barrist_item_id','bartender_item_id','service_id','salle_id')->orderBy('invoice_number')->get();

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $pdf = PDF::loadView('backend.pages.invoice.facture_globale',compact(
        'item_total_amount',
        'total_vat',
        'item_total_amount_credit',
        'total_vat_credit',
        'datas',
        'credits',
        'data',
        'start_date',
        'end_date',
        'setting',
        'item_total_amount_kitchen',
        'total_vat_kitchen',
        'item_total_nvat_kitchen',
        'item_total_amount_drink',
        'total_vat_drink',
        'item_total_nvat_drink',
        'item_total_amount_barrista',
        'total_vat_barrista',
        'item_total_nvat_barrista',
        'item_total_amount_bartender',
        'total_vat_bartender',
        'item_total_nvat_bartender',
        'item_total_amount_service',
        'total_vat_service',
        'item_total_nvat_service',
        'montant_total_global_en_lettre'
        ));//->setPaper('a4', 'landscape');

        return $pdf->download("FACTURE_GLOBALE".$customer_name.'.pdf');

    }

    public function rapportNourriture(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stock !');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        $datas = FactureDetail::select(
                        DB::raw('id,food_item_id,invoice_number,invoice_date,item_quantity,vat,item_price_nvat,item_price,food_order_no,customer_name,client_id,item_total_amount'))->where('food_order_no','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','food_item_id','invoice_date','invoice_number','item_quantity','item_price','vat','item_price_nvat','customer_name','client_id','item_total_amount','food_order_no')->orderBy('invoice_number','asc')->get();
        $total_amount = DB::table('facture_details')->where('food_order_no','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
        $total_vat = DB::table('facture_details')->where('food_order_no','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
        $total_item_price_nvat = DB::table('facture_details')->where('food_order_no','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');

        $credits = FactureDetail::select(
                        DB::raw('id,food_item_id,invoice_number,invoice_date,item_quantity,vat,item_price_nvat,item_price,food_order_no,customer_name,client_id,item_total_amount'))->where('food_order_no','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','food_item_id','invoice_date','invoice_number','item_quantity','item_price','vat','item_price_nvat','customer_name','food_order_no','client_id','item_total_amount')->orderBy('invoice_number','asc')->get();
        $total_amount_credit = DB::table('facture_details')->where('food_order_no','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
        $total_vat_credit = DB::table('facture_details')->where('food_order_no','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
        $total_item_price_nvat_credit = DB::table('facture_details')->where('food_order_no','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');


        $note_credits = NoteCreditDetail::select(
                        DB::raw('id,client_id,service_id,food_item_id,drink_id,barrist_item_id,bartender_item_id,salle_id,swiming_pool_id,kidness_space_id,invoice_number,invoice_date,item_quantity,item_price,vat,item_price_nvat,cancelled_invoice_ref,customer_name,item_total_amount'))->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','service_id','invoice_date','invoice_number','item_quantity','item_price','vat','item_price_nvat','customer_name','cancelled_invoice_ref','client_id','food_item_id','drink_id','barrist_item_id','bartender_item_id','salle_id','swiming_pool_id','kidness_space_id','item_total_amount')->orderBy('id','asc')->get();
            $total_amount_note_credit = DB::table('note_credit_details')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
            $total_vat_note_credit = DB::table('note_credit_details')->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
            $total_item_price_nvat_note_credit = DB::table('note_credit_details')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.rapport_facture_restaurant',compact('datas','dateTime','setting','end_date','start_date','total_amount','total_amount_credit','credits','total_vat','total_item_price_nvat','total_vat_credit','total_item_price_nvat_credit','note_credits','total_vat_note_credit','total_item_price_nvat_note_credit','total_amount_note_credit'))->setPaper('a4', 'landscape');
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
         
            Mail::to($email1)->send(new ReportFoodMail($mailData));
            Mail::to($email2)->send(new ReportFoodMail($mailData));
        */
        Storage::put('public/rapport_facture_restaurant/'.$d1.'_'.$d2.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download("rapport_facture_restaurant_".$dateTime.'.pdf');

        
    }

    public function exportToPdfReportOne(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stock !');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        $datas = FactureDetail::select(
                        DB::raw('food_item_id,salle_id,service_id,drink_id,barrist_item_id,bartender_item_id,sum(item_quantity) as quantity'))->where('etat','!=','0')->where('etat','!=','-1')->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('drink_id','food_item_id','bartender_item_id','barrist_item_id','salle_id','service_id')->get();

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.rapport_vente_un',compact('datas','dateTime','setting','end_date','start_date'));//->setPaper('a6', 'portrait');

        //Storage::put('public/journal_general/'.$d1.'_'.$d2.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download("rapport_vente_".$dateTime.'.pdf');

        
    }

    public function exportToPdfReportTwo(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stock !');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        $datas = FactureDetail::select(
                        DB::raw('invoice_number,invoice_date,sum(item_quantity) as item_quantity,customer_name,client_id,drink_order_no,food_order_no,bartender_order_no,barrist_order_no,sum(item_total_amount) as item_total_amount'))->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('invoice_date','invoice_number','item_quantity','drink_order_no','food_order_no','bartender_order_no','barrist_order_no','customer_name','client_id','item_total_amount')->orderBy('invoice_number','asc')->get();
        $total_amount = DB::table('facture_details')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');

        $credits = FactureDetail::select(
                        DB::raw('invoice_number,invoice_date,sum(item_quantity) as item_quantity,customer_name,client_id,drink_order_no,food_order_no,bartender_order_no,barrist_order_no,sum(item_total_amount) as item_total_amount'))->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('invoice_date','invoice_number','item_quantity','drink_order_no','food_order_no','bartender_order_no','barrist_order_no','customer_name','client_id','item_total_amount')->orderBy('invoice_number','asc')->get();
        $total_amount_credit = DB::table('facture_details')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.rapport_vente_deux',compact('datas','dateTime','setting','end_date','start_date','total_amount','total_amount_credit','credits'))->setPaper('a4', 'landscape');

        //Storage::put('public/journal_general/'.$d1.'_'.$d2.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download("rapport_vente_".$dateTime.'.pdf');

        
    }

    public function rapportCredit(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('recouvrement.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stock !');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        $datas = FactureDetail::select(
                        DB::raw('id,food_item_id,drink_id,barrist_item_id,bartender_item_id,invoice_number,invoice_date,item_quantity,customer_name,client_id,drink_order_no,food_order_no,bartender_order_no,barrist_order_no,item_total_amount,vat,item_price_nvat'))->where('etat','01')->where('statut_paied','0')->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','drink_id','food_item_id','bartender_item_id','barrist_item_id','invoice_date','invoice_number','item_quantity','drink_order_no','food_order_no','bartender_order_no','barrist_order_no','customer_name','client_id','item_total_amount','vat','item_price_nvat')->orderBy('customer_name','asc')->get();
        $total_amount = DB::table('facture_details')->where('etat','01')->where('statut_paied','0')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
        $total_vat = DB::table('facture_details')->where('etat','01')->where('statut_paied','0')->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
        $total_item_price_nvat = DB::table('facture_details')->where('etat','01')->where('statut_paied','0')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.rapport_facture_credit',compact('datas','dateTime','setting','end_date','start_date','total_amount','total_vat','total_item_price_nvat'))->setPaper('a4', 'landscape');

        // download pdf file
        return $pdf->download("rapport_facture_credit_".$dateTime.'.pdf');

        
    }


    public function rapportCreditPaye(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('recouvrement.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stock !');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        $datas = FactureDetail::select(
                        DB::raw('id,food_item_id,drink_id,barrist_item_id,bartender_item_id,invoice_number,invoice_date,item_quantity,customer_name,client_id,drink_order_no,food_order_no,bartender_order_no,barrist_order_no,item_total_amount,vat,item_price_nvat'))->where('statut_paied','!=','0')->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','drink_id','food_item_id','bartender_item_id','barrist_item_id','invoice_date','invoice_number','item_quantity','drink_order_no','food_order_no','bartender_order_no','barrist_order_no','customer_name','client_id','item_total_amount','vat','item_price_nvat')->orderBy('customer_name','asc')->get();
        $total_amount = DB::table('facture_details')->where('statut_paied','!=','0')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
        $total_vat = DB::table('facture_details')->where('statut_paied','!=','0')->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
        $total_item_price_nvat = DB::table('facture_details')->where('statut_paied','!=','0')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();
        

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.rapport_facture_credit_paye',compact('datas','dateTime','setting','end_date','start_date','total_amount','total_vat','total_item_price_nvat'))->setPaper('a4', 'landscape');

        // download pdf file
        return $pdf->download("rapport_facture_credit_paye_".$dateTime.'.pdf');

        
    }

    public function reportServer(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('employe.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any employe !');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        $datas = Facture::select(
                        DB::raw('employe_id,count(drink_order_no) as drink_order_no,count(food_order_no) as food_order_no,count(bartender_order_no) as bartender_order_no,count(barrist_order_no) as barrist_order_no'))->whereBetween('created_at',[$start_date,$end_date])->groupBy('employe_id')->get();

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.rapport_commande_serveur',compact('datas','dateTime','setting','end_date','start_date'))->setPaper('a6', 'portrait');

        // download pdf file
        return $pdf->download("rapport_serveur".$dateTime.'.pdf');

        
    }

    public function exporterChiffreAffaireEnExcel(Request $request)
    {
        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');

        return Excel::download(new ChiffreAffaireExport, 'RAPPORT_CHIFFRE_AFFAIRE DU '.$d1.' AU '.$d2.'.xlsx');
    }

    public function creditExportToExcel(Request $request)
    {
        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');

        return Excel::download(new FactureArecouvreExport, 'RAPPORT_FACTURE_CREDIT DU '.$d1.' AU '.$d2.'.xlsx');
    }

    public function recouvrementExportToExcel(Request $request)
    {
        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');
        return Excel::download(new FactureRecouvreExport, 'RAPPORT_FACTURE_RECOUVRE DU '.$d1.' AU '.$d2.'.xlsx');
    }

    public function exporterCreditEnExcel(Request $request)
    {
        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');
        return Excel::download(new FactureCreditExport, 'RAPPORT_FACTURE_CREDIT DU '.$d1.' AU '.$d2.'.xlsx');
    }

    public function exporterCashEnExcel(Request $request)
    {
        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');
        return Excel::download(new FacturePayeExport, 'RAPPORT_FACTURE_PAYE DU '.$d1.' AU '.$d2.'.xlsx');
    }

    public function exporterFactureAnnule(Request $request)
    {
        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');
        return Excel::download(new FactureAnnuleExport, 'RAPPORT_FACTURE_ANNULE DU '.$d1.' AU '.$d2.'.xlsx');
    }

    public function exporterFactureEncours(Request $request)
    {
        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');
        return Excel::download(new FactureEncoursExport, 'RAPPORT_FACTURE_ENCOURS DU '.$d1.' AU '.$d2.'.xlsx');
    }

    
}
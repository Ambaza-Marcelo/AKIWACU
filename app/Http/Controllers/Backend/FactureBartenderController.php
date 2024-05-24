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
use App\Models\Setting;
use App\Models\BartenderOrder;
use App\Models\BartenderOrderDetail;
use App\Models\BartenderItem;
use App\Models\Employe;
use App\Models\Client;
use App\Models\Table;
use App\Mail\ReportBartenderMail;

class FactureBartenderController extends Controller
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

        $factures = Facture::where('bartender_order_no','!=','')->take(100)->orderBy('id','desc')->get();
        return view('backend.pages.invoice_bartender.index',compact('factures'));
    }


    public function create($order_no)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $bartender_items =  BartenderItem::orderBy('name','asc')->get();
        $clients =  Client::orderBy('customer_name','asc')->get();
        $orders =  BartenderOrderDetail::where('order_no',$order_no)->orderBy('order_no','asc')->get();

        $data =  BartenderOrder::where('order_no',$order_no)->first();
        $table_id = BartenderOrder::where('order_no',$order_no)->value('table_id');

        return view('backend.pages.invoice_bartender.create',compact('bartender_items','data','setting','orders','order_no','clients','table_id'));
    }

    public function createByTable($table_id)
    {
        if (is_null($this->user) || !$this->user->can('invoice_drink.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $bartender_items =  BartenderItem::orderBy('name','asc')->get();
        $clients =  Client::orderBy('customer_name','asc')->get();
        $orders =  BartenderOrderDetail::where('table_id',$table_id)->where('status',1)->orderBy('id','asc')->get();

        $data =  BartenderOrder::where('table_id',$table_id)->first();

        return view('backend.pages.invoice_bartender.create',compact('bartender_items','data','setting','orders','table_id','clients'));
    }

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
        return view('backend.pages.invoice_bartender.show',compact('facture','factureDetails','total_amount'));
    }

    public function rapportBartender(Request $request)
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
                        DB::raw('id,bartender_item_id,invoice_number,invoice_date,item_quantity,item_price,vat,item_price_nvat,customer_name,bartender_order_no,client_id,item_total_amount'))->where('bartender_order_no','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','bartender_item_id','invoice_date','invoice_number','item_quantity','item_price','vat','item_price_nvat','customer_name','bartender_order_no','client_id','item_total_amount')->orderBy('invoice_number','asc')->get();
        $total_amount = DB::table('facture_details')->where('bartender_order_no','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
        $total_vat = DB::table('facture_details')->where('bartender_order_no','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
        $total_item_price_nvat = DB::table('facture_details')->where('bartender_order_no','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');

        $credits = FactureDetail::select(
                        DB::raw('id,bartender_item_id,invoice_number,invoice_date,item_quantity,item_price,vat,item_price_nvat,customer_name,bartender_order_no,client_id,item_total_amount'))->where('bartender_order_no','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','bartender_item_id','invoice_date','invoice_number','item_quantity','item_price','vat','item_price_nvat','customer_name','bartender_order_no','client_id','item_total_amount')->orderBy('invoice_number','asc')->get();
        $total_amount_credit = DB::table('facture_details')->where('bartender_order_no','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
        $total_vat_credit = DB::table('facture_details')->where('bartender_order_no','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
        $total_item_price_nvat_credit = DB::table('facture_details')->where('bartender_order_no','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.rapport_facture_bartender',compact('datas','dateTime','setting','end_date','start_date','total_amount','total_amount_credit','credits','total_vat','total_item_price_nvat','total_vat_credit','total_item_price_nvat_credit'))->setPaper('a4', 'landscape');
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
         
            Mail::to($email1)->send(new ReportBartenderMail($mailData));
            Mail::to($email2)->send(new ReportBartenderMail($mailData));
            */

        Storage::put('public/rapport_facture_bartender/'.$d1.'_'.$d2.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download("rapport_facture_bartender_".$dateTime.'.pdf');

        
    }
}

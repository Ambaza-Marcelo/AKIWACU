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
use App\Models\Facture;
use App\Models\FactureDetail;
use App\Models\Setting;
use App\Models\BookingSalle;
use App\Models\BookingService;
use App\Models\BreakFast;
use App\Models\SwimingPool;
use App\Models\KidnessSpace;
use App\Models\EGRClient;
use App\Models\BookingBookingDetail;
use App\Models\BookingBooking;
use App\Models\NoteCreditDetail;
use App\Models\NoteCredit;

class FactureBookingController extends Controller
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

    public function indexSalle()
    {
        if (is_null($this->user) || !$this->user->can('invoice_booking.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factures = FactureDetail::where('salle_id','!=','')->take(100)->orderBy('id','desc')->get();
        return view('backend.pages.invoice_booking.salle',compact('factures'));
    }

    public function indexRoom()
    {
        if (is_null($this->user) || !$this->user->can('invoice_booking.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factures = FactureDetail::where('room_id','!=','')->take(100)->orderBy('id','desc')->get();
        return view('backend.pages.invoice_booking.room',compact('factures'));
    }

    public function indexService()
    {
        if (is_null($this->user) || !$this->user->can('invoice_booking.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factures = FactureDetail::where('service_id','!=','')->take(100)->orderBy('id','desc')->get();
        return view('backend.pages.invoice_booking.service',compact('factures'));
    }

    public function indexKidnessSpace()
    {
        if (is_null($this->user) || !$this->user->can('invoice_kidness_space.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factures = FactureDetail::where('kidness_space_id','!=','')->take(100)->orderBy('id','desc')->get();
        return view('backend.pages.invoice_booking.kidness_space',compact('factures'));
    }

    public function indexSwimingPool()
    {
        if (is_null($this->user) || !$this->user->can('invoice_swiming_pool.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factures = FactureDetail::where('swiming_pool_id','!=','')->take(100)->orderBy('id','desc')->get();
        return view('backend.pages.invoice_booking.swiming_pool',compact('factures'));
    }

    public function indexBreakFast()
    {
        if (is_null($this->user) || !$this->user->can('invoice_breakfast.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factures = FactureDetail::where('breakfast_id','!=','')->take(100)->orderBy('id','desc')->get();
        return view('backend.pages.invoice_booking.breakfast',compact('factures'));
    }

    public function choose()
    {
        if (is_null($this->user) || !$this->user->can('invoice_booking.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        return view('backend.pages.invoice_booking.choose');
    }


    public function create($booking_no)
    {
        if (is_null($this->user) || !$this->user->can('invoice_booking.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $salles =  BookingSalle::orderBy('name','asc')->get();
        $bookings =  BookingBookingDetail::where('booking_no',$booking_no)->orderBy('id','asc')->get();
        $clients =  EGRClient::orderBy('customer_name','asc')->get();

        $data =  BookingBooking::where('booking_no',$booking_no)->first();
        return view('backend.pages.invoice_booking.create',compact('bookings','data','setting','salles','booking_no','clients'));
    }

    public function show($invoice_number)
    {
        //
        if (is_null($this->user) || !$this->user->can('invoice_booking.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factureDetails = FactureDetail::where('invoice_number',$invoice_number)->get();
        $facture = Facture::where('invoice_number',$invoice_number)->first();
        $total_amount = DB::table('facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');
        return view('backend.pages.invoice_booking.show',compact('facture','factureDetails','total_amount'));
    }

    public function rapportReservation(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('invoice_booking.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any report !');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);

        if (!empty($request->query('salle_id'))) {
            $type = $request->query('salle_id');
            $datas = FactureDetail::select(
                        DB::raw('id,salle_id,client_id,booking_client_id,invoice_number,invoice_date,item_quantity,item_price,vat,item_price_nvat,customer_name,booking_no,item_total_amount'))->where('salle_id','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','salle_id','invoice_date','invoice_number','item_quantity','item_price','vat','item_price_nvat','customer_name','booking_no','client_id','booking_client_id','item_total_amount')->orderBy('id','asc')->get();
            $total_amount = DB::table('facture_details')->where('salle_id','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
            $total_vat = DB::table('facture_details')->where('salle_id','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
            $total_item_price_nvat = DB::table('facture_details')->where('salle_id','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');


            $credits = FactureDetail::select(
                        DB::raw('id,client_id,booking_client_id,salle_id,invoice_number,invoice_date,item_quantity,item_price,vat,item_price_nvat,customer_name,booking_no,item_total_amount'))->where('salle_id','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','salle_id','invoice_date','invoice_number','item_quantity','item_price','vat','item_price_nvat','customer_name','booking_no','client_id','booking_client_id','item_total_amount')->orderBy('id','asc')->get();
            $total_amount_credit = DB::table('facture_details')->where('salle_id','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
            $total_vat_credit = DB::table('facture_details')->where('salle_id','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
            $total_item_price_nvat_credit = DB::table('facture_details')->where('salle_id','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');
        }elseif (!empty($request->query('service_id'))) {
            $type = $request->query('service_id');
            $datas = FactureDetail::select(
                        DB::raw('id,client_id,booking_client_id,service_id,invoice_number,invoice_date,item_quantity,item_price,vat,item_price_nvat,booking_no,customer_name,item_total_amount'))->where('service_id','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','service_id','invoice_date','invoice_number','item_quantity','item_price','vat','item_price_nvat','customer_name','booking_no','client_id','booking_client_id','item_total_amount')->orderBy('id','asc')->get();
            $total_amount = DB::table('facture_details')->where('service_id','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
            $total_vat = DB::table('facture_details')->where('service_id','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
            $total_item_price_nvat = DB::table('facture_details')->where('service_id','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');


            $credits = FactureDetail::select(
                        DB::raw('id,client_id,booking_client_id,service_id,invoice_number,invoice_date,item_quantity,item_price,vat,item_price_nvat,booking_no,customer_name,item_total_amount'))->where('service_id','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','service_id','invoice_date','invoice_number','item_quantity','item_price','vat','item_price_nvat','customer_name','booking_no','client_id','booking_client_id','item_total_amount')->orderBy('id','asc')->get();
            $total_amount_credit = DB::table('facture_details')->where('service_id','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
            $total_vat_credit = DB::table('facture_details')->where('service_id','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
            $total_item_price_nvat_credit = DB::table('facture_details')->where('service_id','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');
        }elseif (!empty($request->query('breakfast_id'))) {
            $type = $request->query('breakfast_idk');
            $datas = FactureDetail::select(
                        DB::raw('id,client_id,booking_client_id,breakfast_id,invoice_number,invoice_date,item_quantity,item_price,vat,item_price_nvat,booking_no,customer_name,item_total_amount'))->where('breakfast_id','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','breakfast_id','invoice_date','invoice_number','item_quantity','item_price','vat','item_price_nvat','customer_name','booking_no','client_id','booking_client_id','item_total_amount')->orderBy('id','asc')->get();
            $total_amount = DB::table('facture_details')->where('breakfast_id','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
            $total_vat = DB::table('facture_details')->where('breakfast_id','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
            $total_item_price_nvat = DB::table('facture_details')->where('breakfast_id','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');


            $credits = FactureDetail::select(
                        DB::raw('id,client_id,booking_client_id,breakfast_id,invoice_number,invoice_date,item_quantity,item_price,vat,item_price_nvat,customer_name,booking_no,item_total_amount'))->where('breakfast_id','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','breakfast_id','invoice_date','invoice_number','item_quantity','item_price','vat','item_price_nvat','customer_name','booking_no','client_id','booking_client_id','item_total_amount')->orderBy('id','asc')->get();
            $total_amount_credit = DB::table('facture_details')->where('breakfast_id','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
            $total_vat_credit = DB::table('facture_details')->where('breakfast_id','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
            $total_item_price_nvat_credit = DB::table('facture_details')->where('breakfast_id','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');
        }elseif (!empty($request->query('swiming_pool_id'))) {
            $type = $request->query('swiming_pool_id');
            $datas = FactureDetail::select(
                        DB::raw('id,client_id,booking_client_id,swiming_pool_id,invoice_number,invoice_date,item_quantity,item_price,vat,item_price_nvat,customer_name,item_total_amount'))->where('swiming_pool_id','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','swiming_pool_id','invoice_date','invoice_number','item_quantity','item_price','vat','item_price_nvat','customer_name','client_id','booking_client_id','item_total_amount')->orderBy('id','asc')->get();
            $total_amount = DB::table('facture_details')->where('swiming_pool_id','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
            $total_vat = DB::table('facture_details')->where('swiming_pool_id','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
            $total_item_price_nvat = DB::table('facture_details')->where('swiming_pool_id','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');


            $credits = FactureDetail::select(
                        DB::raw('id,client_id,booking_client_id,swiming_pool_id,invoice_number,invoice_date,item_quantity,item_price,vat,item_price_nvat,customer_name,item_total_amount'))->where('swiming_pool_id','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','swiming_pool_id','invoice_date','invoice_number','item_quantity','item_price','vat','item_price_nvat','customer_name','client_id','booking_client_id','item_total_amount')->orderBy('id','asc')->get();
            $total_amount_credit = DB::table('facture_details')->where('swiming_pool_id','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
            $total_vat_credit = DB::table('facture_details')->where('swiming_pool_id','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
            $total_item_price_nvat_credit = DB::table('facture_details')->where('swiming_pool_id','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');
        }elseif (!empty($request->query('kidness_space_id'))) {
            $type = $request->query('kidness_space_id');
            $datas = FactureDetail::select(
                        DB::raw('id,client_id,booking_client_id,kidness_space_id,invoice_number,invoice_date,item_quantity,item_price,vat,item_price_nvat,customer_name,item_total_amount'))->where('kidness_space_id','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','kidness_space_id','invoice_date','invoice_number','item_quantity','item_price','vat','item_price_nvat','customer_name','client_id','booking_client_id','item_total_amount')->orderBy('id','asc')->get();
            $total_amount = DB::table('facture_details')->where('kidness_space_id','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
            $total_vat = DB::table('facture_details')->where('kidness_space_id','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
            $total_item_price_nvat = DB::table('facture_details')->where('kidness_space_id','!=','')->where('etat','1')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');


            $credits = FactureDetail::select(
                        DB::raw('id,client_id,booking_client_id,kidness_space_id,invoice_number,invoice_date,item_quantity,item_price,vat,item_price_nvat,customer_name,item_total_amount'))->where('kidness_space_id','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','kidness_space_id','invoice_date','invoice_number','item_quantity','item_price','vat','item_price_nvat','customer_name','client_id','booking_client_id','item_total_amount')->orderBy('id','asc')->get();
            $total_amount_credit = DB::table('facture_details')->where('kidness_space_id','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
            $total_vat_credit = DB::table('facture_details')->where('kidness_space_id','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
            $total_item_price_nvat_credit = DB::table('facture_details')->where('kidness_space_id','!=','')->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');
        }else{
           abort(501, 'Sorry !! You have to choose type !more information contact Marcellin'); 
        }

        

        $pdf = PDF::loadView('backend.pages.document.rapport_facture_reservation',compact('datas','dateTime','setting','end_date','start_date','total_amount','total_vat','total_item_price_nvat','total_amount_credit','credits','total_vat_credit','total_item_price_nvat_credit','type'))->setPaper('a4', 'portrait');

        Storage::put('public/rapport_facture_booking_/'.$d1.'_'.$d2.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download($type." RAPPORT VENTE AU ".$dateTime.'.pdf');

        
    }
}

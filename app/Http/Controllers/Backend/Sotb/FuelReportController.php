<?php

namespace App\Http\Controllers\Backend\Sotb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\FuelStockinDetail;
use App\Models\FuelStockoutDetail;
use App\Models\FuelPump;
use App\Models\Fuel;
use App\Models\FuelReport;
use App\Models\Setting;
use Carbon\Carbon;
use Excel;
use PDF;
use App\Exports\FuelMovementExport;

class FuelReportController extends Controller
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


    public function fuelDayReport(){
         
         if (is_null($this->user) || !$this->user->can('fuel_report.view')) {
            abort(403, 'Muradutunge !! Ntaburenganzira mufise bwo kuraba raporo,mufise ico mubaza murashobora guhamagara kuri 122 !');
        }
        
        $reportDays = FuelReport::select(
                        DB::raw('DAY(created_at) as day,MONTH(created_at) as month,YEAR(created_at) as year'),
                        DB::raw('fuel_pump_id,driver_car_id,quantite_stock_initiale,bon_entree,stock_totale,bon_sortie,auteur,sum(quantite_entree) as quantite_e,sum(quantite_sortie) as quantite_s'))->groupBy('fuel_pump_id','driver_car_id','quantite_stock_initiale','bon_entree','stock_totale','bon_sortie','auteur','day','month','year')->get();
       return view('backend.pages.fuel.fuel_report.day_report.index',compact('reportDays'));


    }

    public function exportTopdf()
    {
        if (is_null($this->user) || !$this->user->can('fuel_report.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stock !');
        }

        $datas = FuelReport::all();
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.fuel.documents.rapport',compact('datas','dateTime','setting'));//->setPaper('a4', 'landscape');

        Storage::put('public/carburant/rapport/'.$dateTime.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download($dateTime.'.pdf');
    }

       public function fuelMovement(){
        if (is_null($this->user) || !$this->user->can('fuel_report.view')) {
            abort(403, 'Muradutunge !! Ntaburenganzira mufise bwo kuraba raporo,mufise ico mubaza murashobora guhamagara kuri 122 !');
        }

        $cars = FuelStockoutDetail::select(
                        DB::raw('driver_car_id,sum(quantite) as qtite'))->groupBy('driver_car_id')->orderBy('qtite','desc')->get();

        $fuelMovements = FuelReport::select(
                        DB::raw('created_at,fuel_pump_id,driver_car_id,quantite_stock_initiale,bon_entree,stock_totale,bon_sortie,auteur,quantite_entree,quantite_sortie'))->groupBy('created_at','fuel_pump_id','driver_car_id','quantite_stock_initiale','bon_entree','bon_sortie','auteur','quantite_entree','stock_totale','quantite_sortie')->orderBy('created_at','desc')->get();
        return view('backend.pages.fuel.fuel_report.fuel_movement.index',compact('fuelMovements','cars'));
    }

     public function monthReportToPdf(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('fuel_report.view')) {
            abort(403, 'Muradutunge !! Ntaburenganzira mufise bwo kuraba raporo,mufise ico mubaza murashobora guhamagara kuri 122 !');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';


        $datas = FuelReport::select(
                        DB::raw('fuel_pump_id,sum(quantite_entree) as quantity_in,sum(stock_totale) as stock_tot,sum(quantite_sortie) as quantity_out'))->whereBetween('created_at',[$start_date,$end_date])->groupBy('fuel_pump_id')->get();

        $quantiteStockInitial = FuelReport::where('created_at',$end_date)->value('quantite_stock_initiale');
        $valeurStockInitial = FuelReport::where('created_at',$end_date)->value('valeur_stock_initiale');

        $total_quantite_entree = FuelReport::select(
                        DB::raw('sum(quantite_entree) as entree_total'))->whereBetween('created_at',[$start_date,$end_date])->get();
        $derniere_quantite_sortie = FuelReport::select(
                        DB::raw('quantite_sortie'))->whereBetween('created_at',[$start_date,$end_date])->get();
        $total_quantite_sortie = FuelReport::select(
                        DB::raw('sum(quantite_sortie) as sortie_total'))->whereBetween('created_at',[$start_date,$end_date])->get();

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.fuel.documents.month_report',compact('datas','dateTime','setting','total_quantite_entree','total_quantite_sortie','start_date','end_date'));//->setPaper('a4', 'landscape');

        Storage::put('public/pdf/carburant/rapport/'.$d1.'_'.$d2.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download($d1.'_'.$d2.'.pdf');
    }

     public function movementFuelToPdf(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('fuel_report.view')) {
            abort(403, 'Muradutunge !! Ntaburenganzira mufise bwo kuraba raporo,mufise ico mubaza murashobora guhamagara kuri 122 !');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';


        $datas = FuelReport::select(
                        DB::raw('driver_car_id,created_at,fuel_pump_id,quantite_stock_initiale,sum(quantite_entree) as quantity_in,sum(stock_totale) as stock_tot,sum(quantite_sortie) as quantity_out'))->whereBetween('created_at',[$start_date,$end_date])->groupBy('driver_car_id','fuel_pump_id','created_at','quantite_stock_initiale')->orderBy('created_at')->get();

        $quantiteStockInitial = FuelReport::where('created_at',$end_date)->value('quantite_stock_initiale');
        $valeurStockInitial = FuelReport::where('created_at',$end_date)->value('valeur_stock_initiale');

        $total_quantite_entree = FuelReport::select(
                        DB::raw('sum(quantite_entree) as entree_total'))->whereBetween('created_at',[$start_date,$end_date])->get();
        $derniere_quantite_sortie = FuelReport::select(
                        DB::raw('quantite_sortie'))->whereBetween('created_at',[$start_date,$end_date])->get();
        $total_quantite_sortie = FuelReport::select(
                        DB::raw('sum(quantite_sortie) as sortie_total'))->whereBetween('created_at',[$start_date,$end_date])->get();

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.fuel.documents.movement',compact('datas','dateTime','setting','total_quantite_entree','total_quantite_sortie','start_date','end_date'))->setPaper('a4', 'landscape');

        Storage::put('public/pdf/carburant/mouvement/'.$d1.'_'.$d2.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download($d1.'_'.$d2.'.pdf');
    }



    public function FuelMovementExport(Request $request)
    {
        return Excel::download(new FuelMovementExport, 'rapports.xlsx');
    }
}

<?php

namespace App\Http\Controllers\Backend\MusumbaSteel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\MsFuelStockoutDetail;
use App\Models\FuelPump;
use App\Models\Fuel;
use App\Models\MsFuelReport;
use App\Models\Setting;
use Carbon\Carbon;
use Excel;
use PDF;
use App\Exports\MusumbaSteel\FuelReportExport;

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

    public function index()
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_inventory.view')) {
            abort(403, 'Muradutunge !! Ntaburenganzira mufise bwo kuraba raporo,mufise ico mubaza murashobora guhamagara kuri 122 !');
        }

        $cars = MsFuelStockoutDetail::select(
                        DB::raw('car_id,sum(quantity) as qtite'))->groupBy('car_id')->orderBy('qtite','desc')->get();

        $fuelMovements = MsFuelReport::select(
                        DB::raw('id,created_at,date,pump_id,quantity_inventory,car_id,quantity_stock_initial,description,quantity_stockout,cump,created_by,quantity_stockin,quantity_reception,driver_id'))->where('quantity_stock_initial','!=','')->groupBy('id','created_at','date','pump_id','car_id','quantity_stock_initial','description','cump','created_by','quantity_stockin','quantity_reception','quantity_stockout','driver_id','quantity_inventory')->orderBy('id','desc')->take(200)->get();
        return view('backend.pages.musumba_steel.fuel.report.index',compact('fuelMovements','cars'));
    }

    public function exportToPdf(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_fuel_inventory.view')) {
            abort(403, 'Muradutunge !! Ntaburenganzira mufise bwo kuraba raporo,mufise ico mubaza murashobora guhamagara kuri 122 !');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';


        $datas = MsFuelReport::select(
                        DB::raw('id,created_at,date,type_transaction,document_no,pump_id,quantity_inventory,car_id,quantity_stock_initial,description,stock_total,cump,created_by,quantity_stockin,quantity_reception,quantity_stockout,driver_id,start_index,end_index'))->whereBetween('created_at',[$start_date,$end_date])->where('quantity_stock_initial','!=','')->groupBy('id','created_at','date','type_transaction','document_no','pump_id','car_id','quantity_stock_initial','description','cump','created_by','quantity_stockin','quantity_stockout','stock_total','driver_id','quantity_inventory','quantity_reception','start_index','end_index')->orderBy('id','asc')->get();


        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.musumba_steel.fuel.document.report',compact('datas','dateTime','setting','start_date','end_date'))->setPaper('a4', 'landscape');

        Storage::put('public/musumba_steel/carburant/rapport/'.$d1.'_'.$d2.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download($d1.'_'.$d2.'.pdf');
    }



    public function exportToExcel(Request $request)
    {
        return Excel::download(new FuelReportExport, 'rapports.xlsx');
    }
}

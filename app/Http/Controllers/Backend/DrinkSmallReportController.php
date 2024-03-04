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
use App\Models\DrinkSmallStoreDetail;
use App\Models\DrinkSmallStore;
use App\Models\DrinkSmallReport;
use App\Exports\DrinkSmStoreReportExport;
use Carbon\Carbon;
use PDF;
use Validator;
use Excel;
use Mail;

class DrinkSmallReportController extends Controller
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


    public function index(){
        if (is_null($this->user) || !$this->user->can('drink_small_report.view')) {
            abort(403, 'Muradutunge !! Ntaburenganzira mufise bwo kuraba raporo,mufise ico mubaza murashobora guhamagara kuri 122 !');
        }
            $datas = DrinkSmallReport::select(
                        DB::raw('id,created_at,drink_id,quantity_stock_initial,value_stock_initial,quantity_stockin,value_stockin,quantity_inventory,value_inventory,quantity_inventory_ml,value_inventory_ml,quantity_reception,value_reception,quantity_transfer,value_transfer,quantity_stockout,value_stockout,quantity_sold,value_sold,quantity_stock_final,value_stock_final'))->groupBy('id','created_at','drink_id','quantity_stock_initial','value_stock_initial','quantity_stockin','value_stockin','quantity_inventory','quantity_inventory_ml','value_inventory','value_inventory_ml','quantity_reception','value_reception','quantity_transfer','value_transfer','quantity_stockout','value_stockout','quantity_sold','value_sold','quantity_stock_final','value_stock_final')->get();

            $stores = DrinkSmallStore::all();

        return view('backend.pages.drink_small_store_report.index',compact('datas','stores'));
       
    }

    public function exportToPdf(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('drink_small_report.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any report !');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');
        $code_store = $request->query('code_store');
        $store_signature = DrinkSmallStore::where('code',$code_store)->value('store_signature');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        $datas = DrinkSmallReport::select(
                        DB::raw('id,created_at,drink_id,quantity_stock_initial,value_stock_initial,quantity_stockin,value_stockin,quantity_inventory,value_inventory,quantity_inventory_ml,value_inventory_ml,quantity_reception,value_reception,quantity_transfer,value_transfer,quantity_stockout,value_stockout,quantity_sold,value_sold,quantity_stock_final,value_stock_final'))->whereBetween('created_at',[$start_date,$end_date])/*->where('code_store',$code_store)*/->groupBy('id','created_at','drink_id','quantity_stock_initial','value_stock_initial','quantity_stockin','value_stockin','quantity_inventory','quantity_inventory_ml','value_inventory','value_inventory_ml','quantity_reception','value_reception','quantity_transfer','value_transfer','quantity_stockout','value_stockout','quantity_sold','value_sold','quantity_stock_final','value_stock_final')->orderBy('created_at','desc')->get();

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.drink_small_store_report',compact('datas','dateTime','setting','start_date','end_date','code_store','store_signature'))->setPaper('a4', 'landscape');

        Storage::put('public/drink_small_store_report/'.'RAPPORT_PETIT_STOCK_BOISSON_'.$d1.'_'.$d2.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('RAPPORT_PETIT_STOCK_BOISSON_'.$dateTime.'.pdf');

        
    }

    public function exportToExcel(Request $request)
    {
        return Excel::download(new DrinkSmStoreReportExport, 'RAPPORT_PETIT_STOCK_DES_BOISSONS.xlsx');
    }
}

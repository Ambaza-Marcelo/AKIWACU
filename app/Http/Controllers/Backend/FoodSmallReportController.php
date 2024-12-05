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
use App\Models\Food;
use App\Models\FoodTransfer;
use App\Models\FoodTransferDetail;
use App\Models\FoodBigStoreDetail;
use App\Models\FoodSmallStoreDetail;
use App\Models\FoodSmallStore;
use App\Models\FoodRequisitionDetail;
use App\Models\FoodRequisition;
use App\Models\FoodSmallReport;
use App\Exports\FoodSmStoreReportExport;
use Carbon\Carbon;
use PDF;
use Validator;
use Excel;
use Mail;

class FoodSmallReportController extends Controller
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
        if (is_null($this->user) || !$this->user->can('food_small_report.view')) {
            abort(403, 'Muradutunge !! Ntaburenganzira mufise bwo kuraba raporo,mufise ico mubaza murashobora guhamagara kuri 122 !');
        }
            $datas = FoodSmallReport::select(
                        DB::raw('id,created_at,food_id,quantity_stock_initial,quantity_stock_initial_portion,value_stock_initial,value_stock_initial_portion,quantity_stockin,value_stockin,quantity_portion,value_portion,quantity_inventory,value_inventory,quantity_inventory_portion,value_inventory_portion,quantity_transfer,value_transfer,quantity_stockout,value_stockout,quantity_stock_final,quantity_stock_final_portion'))->groupBy('id','created_at','food_id','quantity_stock_initial','quantity_stock_initial_portion','value_stock_initial','value_stock_initial_portion','quantity_stockin','quantity_portion','value_portion','quantity_inventory','value_inventory','quantity_inventory_portion','value_inventory_portion','value_stockin','quantity_transfer','value_transfer','quantity_stockout','value_stockout','quantity_stock_final','quantity_stock_final_portion')->orderBy('id','desc')->take(500)->get();

            $stores = FoodSmallStore::all();

        return view('backend.pages.food_small_store_report.index',compact('datas','stores'));
       
    }

    public function exportToPdf(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('food_small_report.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any report !');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');
        $code_store = $request->query('code_store');
        $store_signature = FoodSmallStore::where('code',$code_store)->value('store_signature');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        $datas = FoodSmallReport::select(
                        DB::raw('id,date,created_at,food_id,quantity_stock_initial,quantity_stock_initial_portion,value_stock_initial,value_stock_initial_portion,quantity_stockin,value_stockin,quantity_portion,value_portion,quantity_inventory,value_inventory,quantity_inventory_portion,value_inventory_portion,quantity_transfer,value_transfer,quantity_stockout,value_stockout,quantity_stock_final,quantity_stock_final_portion,type_transaction,document_no,created_portion_by,created_by'))->whereBetween('created_at',[$start_date,$end_date])->groupBy('id','created_at','date','food_id','quantity_stock_initial','quantity_stock_initial_portion','value_stock_initial','value_stock_initial_portion','quantity_stockin','quantity_portion','value_portion','quantity_inventory','value_inventory','quantity_inventory_portion','value_inventory_portion','value_stockin','quantity_transfer','value_transfer','quantity_stockout','value_stockout','quantity_stock_final','quantity_stock_final_portion','type_transaction','document_no','created_portion_by','created_by')->orderBy('created_at')->get();

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.food_small_store_report',compact('datas','dateTime','setting','start_date','end_date','code_store','store_signature'))->setPaper('a4', 'landscape');

        Storage::put('public/food_small_store_report/'.'RAPPORT__STOCK_NOURRITURE_'.$d1.'_'.$d2.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('RAPPORT DU PETIT STOCK DES NOURRITURES'.$dateTime.'.pdf');

        
    }

    public function exportToExcel(Request $request)
    {
        return Excel::download(new FoodSmStoreReportExport, 'RAPPORT DU PETIT STOCK DES NOURRITURES.xlsx');
    }
}

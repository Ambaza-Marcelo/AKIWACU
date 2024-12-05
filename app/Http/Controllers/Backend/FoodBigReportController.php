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
use App\Models\FoodBigStoreDetail;
use App\Models\FoodBigStore;
use App\Models\FoodBigReport;
use App\Exports\FoodMdStoreReportExport;
use Carbon\Carbon;
use PDF;
use Validator;
use Excel;
use Mail;

class FoodBigReportController extends Controller
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
        if (is_null($this->user) || !$this->user->can('food_big_report.view')) {
            abort(403, 'Muradutunge !! Ntaburenganzira mufise bwo kuraba raporo,mufise ico mubaza murashobora guhamagara kuri 122 !');
        }
            $datas = FoodBigReport::select(
                        DB::raw('id,created_at,food_id,quantity_stock_initial,value_stock_initial,quantity_stockin,value_stockin,quantity_reception,value_reception,quantity_transfer,value_transfer,quantity_stockout,value_stockout,quantity_stock_final,value_stock_final'))->groupBy('id','created_at','food_id','quantity_stock_initial','value_stock_initial','quantity_stockin','value_stockin','quantity_reception','value_reception','quantity_transfer','value_transfer','quantity_stockout','value_stockout','quantity_stock_final','value_stock_final')->orderBy('id','desc')->take(200)->get();

            $stores = FoodBigStore::all();

        return view('backend.pages.food_big_store_report.index',compact('datas','stores'));
       
    }

    public function exportToPdf(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('food_big_report.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any report !');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');
        $code_store = $request->query('code_store');
        $store_signature = FoodBigStore::where('code',$code_store)->value('store_signature');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        $datas = FoodBigReport::select(
                        DB::raw('id,created_at,created_by,food_id,date,document_no,type_transaction,quantity_stock_initial,cump,value_stock_initial,quantity_stockin,value_stockin,quantity_reception,value_reception,quantity_transfer,value_transfer,transfer_no,quantity_stockout,value_stockout,quantity_stock_final,value_stock_final'))->whereBetween('created_at',[$start_date,$end_date])/*->where('code_store',$code_store)*/->groupBy('id','created_at','created_by','document_no','date','type_transaction','food_id','quantity_stock_initial','cump','value_stock_initial','quantity_stockin','value_stockin','quantity_reception','value_reception','quantity_transfer','transfer_no','value_transfer','quantity_stockout','value_stockout','quantity_stock_final','value_stock_final')->orderBy('created_at','asc')->get();

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.food_big_store_report',compact('datas','dateTime','setting','start_date','end_date','code_store','store_signature'))->setPaper('a4', 'landscape');

        Storage::put('public/food_big_store_report/'.'RAPPORT_GRAND_STOCK_NOURRITURE_'.$d1.'_'.$d2.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('RAPPORT_GRAND_STOCK_NOURRITURE_'.$dateTime.'.pdf');

        
    }

    public function exportToExcel(Request $request)
    {
        return Excel::download(new FoodMdStoreReportExport, 'RAPPORT DE STOCK INTERMEDIAIRE DES NOURRITURES.xlsx');
    }
}

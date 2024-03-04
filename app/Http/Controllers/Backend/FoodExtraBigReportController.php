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
use App\Models\FoodExtraBigStore;
use App\Models\FoodSmallStore;
use App\Models\FoodRequisitionDetail;
use App\Models\FoodRequisition;
use App\Models\FoodExtraBigReport;
use App\Models\FoodSmallReport;
use Carbon\Carbon;
use PDF;
use Validator;
use Excel;
use Mail;

class FoodExtraBigReportController extends Controller
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
        if (is_null($this->user) || !$this->user->can('food_extra_big_report.view')) {
            abort(403, 'Muradutunge !! Ntaburenganzira mufise bwo kuraba raporo,mufise ico mubaza murashobora guhamagara kuri 122 !');
        }
            $datas = FoodExtraBigReport::select(
                        DB::raw('created_at,food_id,quantity_stock_initial,value_stock_initial,quantity_stockin,value_stockin,quantity_reception,value_reception,quantity_transfer,value_transfer,quantity_stockout,value_stockout,quantity_stock_final,value_stock_final'))->groupBy('created_at','food_id','quantity_stock_initial','value_stock_initial','quantity_stockin','value_stockin','quantity_reception','value_reception','quantity_transfer','value_transfer','quantity_stockout','value_stockout','quantity_stock_final','value_stock_final')->get();

            $stores = FoodExtraBigStore::all();

        return view('backend.pages.food_extra_big_store_report.index',compact('datas','stores'));
       
    }

    public function exportToPdf(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('food_extra_big_report.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any report !');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');
        $code_store = $request->query('code_store');
        $store_signature = FoodExtraBigStore::where('code',$code_store)->value('store_signature');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        $datas = FoodExtraBigReport::select(
                        DB::raw('created_at,food_id,quantity_stock_initial,value_stock_initial,quantity_stockin,value_stockin,quantity_reception,value_reception,quantity_transfer,value_transfer,transfer_no,quantity_stockout,value_stockout,quantity_stock_final,value_stock_final'))->whereBetween('created_at',[$start_date,$end_date])/*->where('code_store',$code_store)*/->groupBy('created_at','food_id','quantity_stock_initial','value_stock_initial','quantity_stockin','value_stockin','quantity_reception','value_reception','quantity_transfer','transfer_no','value_transfer','quantity_stockout','value_stockout','quantity_stock_final','value_stock_final')->orderBy('created_at','asc')->orderBy('transfer_no','asc')->get();

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.food_extra_big_store_report',compact('datas','dateTime','setting','start_date','end_date','code_store','store_signature'))->setPaper('a4', 'landscape');

        Storage::put('public/food_extra_big_store_report/'.'RAPPORT_EXTRA_GRAND_STOCK_NOURRITURE_'.$d1.'_'.$d2.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('RAPPORT_EXTRA_GRAND_STOCK_NOURRITURE_'.$dateTime.'.pdf');

        
    }
}

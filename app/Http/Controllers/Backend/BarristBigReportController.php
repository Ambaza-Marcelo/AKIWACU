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
use App\Models\Drink;
use App\Models\food;
use App\Models\DrinkTransfer;
use App\Models\DrinkTransferDetail;
use App\Models\FoodTransfer;
use App\Models\FoodTransferDetail;
use App\Models\DrinkBigStoreDetail;
use App\Models\DrinkSmallStoreDetail;
use App\Models\FoodBigStoreDetail;
use App\Models\FoodSmallStoreDetail;
use App\Models\DrinkBigStore;
use App\Models\DrinkSmallStore;
use App\Models\DrinkRequisitionDetail;
use App\Models\DrinkRequisition;
use App\Models\BarristBigReport;
use App\Models\BarristSmallReport;
use Carbon\Carbon;
use PDF;
use Validator;
use Excel;
use Mail;

class BarristBigReportController extends Controller
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


    public function indexDrink(){
        if (is_null($this->user) || !$this->user->can('barrist_report.view')) {
            abort(403, 'Muradutunge !! Ntaburenganzira mufise bwo kuraba raporo,mufise ico mubaza murashobora guhamagara kuri 122 !');
        }
            $datas = BarristBigReport::select(
                        DB::raw('created_at,drink_id,food_id,quantity_stock_initial,value_stock_initial,quantity_stockin,value_stockin,quantity_reception,value_reception,quantity_transfer,value_transfer,quantity_stockout,value_stockout,quantity_stock_final,value_stock_final'))->where('drink_id','!=','')->groupBy('created_at','drink_id','food_id','quantity_stock_initial','value_stock_initial','quantity_stockin','value_stockin','quantity_reception','value_reception','quantity_transfer','value_transfer','quantity_stockout','value_stockout','quantity_stock_final','value_stock_final')->orderBy('created_at')->get();

            $stores = DrinkBigStore::all();

        return view('backend.pages.barrist_drink_big_report.index',compact('datas','stores'));
       
    }

    public function indexFood(){
        if (is_null($this->user) || !$this->user->can('barrist_report.view')) {
            abort(403, 'Muradutunge !! Ntaburenganzira mufise bwo kuraba raporo,mufise ico mubaza murashobora guhamagara kuri 122 !');
        }
            $datas = BarristBigReport::select(
                        DB::raw('created_at,drink_id,food_id,quantity_stock_initial,value_stock_initial,quantity_stockin,value_stockin,quantity_reception,value_reception,quantity_transfer,value_transfer,quantity_stockout,value_stockout,quantity_stock_final,value_stock_final'))->where('food_id','!=','')->groupBy('created_at','drink_id','food_id','quantity_stock_initial','value_stock_initial','quantity_stockin','value_stockin','quantity_reception','value_reception','quantity_transfer','value_transfer','quantity_stockout','value_stockout','quantity_stock_final','value_stock_final')->orderBy('created_at')->get();

            $stores = DrinkBigStore::all();

        return view('backend.pages.barrist_food_big_report.index',compact('datas','stores'));
       
    }

    public function exportToPdfDrink(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('barrist_report.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any report !');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');
        $code_store = $request->query('code_store');
        $store_signature = DrinkBigStore::where('code',$code_store)->value('store_signature');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        $dataDrinks = BarristBigReport::select(
                        DB::raw('created_at,drink_id,unit,quantity_stock_initial,value_stock_initial,quantity_stockin,value_stockin,quantity_reception,value_reception,quantity_transfer,value_transfer,quantity_stockout,value_stockout,quantity_stock_final,value_stock_final'))->whereBetween('created_at',[$start_date,$end_date])->where('drink_id','!=','')/*->where('code_store',$code_store)*/->groupBy('created_at','drink_id','unit','quantity_stock_initial','value_stock_initial','quantity_stockin','value_stockin','quantity_reception','value_reception','quantity_transfer','value_transfer','quantity_stockout','value_stockout','quantity_stock_final','value_stock_final')->orderBy('created_at','desc')->get();

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.barrist_drink_big_report',compact('dataDrinks','dateTime','setting','start_date','end_date','code_store','store_signature'))->setPaper('a4', 'landscape');

        Storage::put('public/barrist_drink_big_report/'.'RAPPORT_GRAND_STOCK_BOISSON_BARRIST_'.$d1.'_'.$d2.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('RAPPORT_GRAND_STOCK_BOISSON_BARRIST_'.$dateTime.'.pdf');

        
    }

    public function exportToPdfFood(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('barrist_report.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any report !');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');
        $code_store = $request->query('code_store');
        $store_signature = DrinkBigStore::where('code',$code_store)->value('store_signature');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        $dataFoods = BarristBigReport::select(
                        DB::raw('created_at,food_id,unit,quantity_stock_initial,value_stock_initial,quantity_stockin,value_stockin,quantity_reception,value_reception,quantity_transfer,value_transfer,quantity_stockout,value_stockout,quantity_stock_final,value_stock_final'))->whereBetween('created_at',[$start_date,$end_date])->where('food_id','!=','')/*->where('code_store',$code_store)*/->groupBy('created_at','food_id','unit','quantity_stock_initial','value_stock_initial','quantity_stockin','value_stockin','quantity_reception','value_reception','quantity_transfer','value_transfer','quantity_stockout','value_stockout','quantity_stock_final','value_stock_final')->orderBy('created_at','desc')->get();

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.barrist_food_big_report',compact('dataFoods','dateTime','setting','start_date','end_date','code_store','store_signature'))->setPaper('a4', 'landscape');

        Storage::put('public/barrist_food_big_report/'.'RAPPORT_GRAND_STOCK_NOURRITURE_BARRIST_'.$d1.'_'.$d2.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('RAPPORT_GRAND_STOCK_NOURRITURE_BARRIST_'.$dateTime.'.pdf');

        
    }
}

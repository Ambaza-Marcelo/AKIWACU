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
use App\Models\PrivateStoreReport;

class PrivateStoreReportController extends Controller
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
        if (is_null($this->user) || !$this->user->can('private_drink_stockin.view')) {
            abort(403, 'Muradutunge !! Ntaburenganzira mufise bwo kuraba raporo,mufise ico mubaza murashobora guhamagara kuri 122 !');
        }
            $datas = PrivateStoreReport::select(
                        DB::raw('id,created_at,private_store_item_id,quantity_stock_initial,value_stock_initial,quantity_stockin,value_stockin,quantity_inventory,value_inventory,cump,quantity_stockout,value_stockout,quantity_sold,value_sold,quantity_stock_final,value_stock_final'))->groupBy('id','created_at','private_store_item_id','quantity_stock_initial','value_stock_initial','quantity_stockin','value_stockin','quantity_inventory','value_inventory','quantity_stockout','value_stockout','quantity_sold','value_sold','quantity_stock_final','value_stock_final','cump')->orderBy('id','desc')->take(200)->get();


        return view('backend.pages.private_store_report.index',compact('datas'));
       
    }

    public function exportToPdf(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('private_drink_stockin.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any report !');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');
        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        $datas = PrivateStoreReport::select(
                        DB::raw('id,created_at,private_store_item_id,quantity_stock_initial,value_stock_initial,quantity_stockin,value_stockin,quantity_inventory,value_inventory,quantity_stockout,value_stockout,quantity_sold,value_sold,quantity_stock_final,value_stock_final,type_transaction,created_by,document_no,cump'))->groupBy('id','created_at','private_store_item_id','quantity_stock_initial','value_stock_initial','quantity_stockin','value_stockin','quantity_inventory','value_inventory','quantity_stockout','value_stockout','quantity_sold','value_sold','quantity_stock_final','value_stock_final','type_transaction','created_by','document_no','cump')->orderBy('id','asc')->get();

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.private_store_report',compact('datas','dateTime','setting','start_date','end_date'))->setPaper('a4', 'landscape');

        Storage::put('public/private_store_report/'.'RAPPORT_MAGASIN_EGR_'.$d1.'_'.$d2.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('RAPPORT_MAGASIN_EGR_'.$dateTime.'.pdf');

        
    }

    public function exportToExcel(Request $request)
    {
        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');

        return Excel::download(new DrinkSmStoreReportExport, 'RAPPORT_MAGASIN_EGR_DU '.$d1.' AU '.$d2.'.xlsx');
    }
}

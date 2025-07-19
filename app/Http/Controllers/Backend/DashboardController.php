<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Events\RealTimeMessage;
use App\Models\Facture;
use App\Models\FactureDetail;
use App\Models\Table;
use App\Models\F\FTable;

class DashboardController extends Controller
{
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
        if (is_null($this->user) || !$this->user->can('dashboard.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view dashboard !');
        }


        //$month = ['01','02','03','04','05','06','07','08','09','10','11','12'];
        $year = ['2023','2024','2025','2026','2027','2028','2029','2030'];
        
        $food = [];
        foreach ($year as $key => $value) {
            $food[] = FactureDetail::where(\DB::raw("DATE_FORMAT(invoice_date, '%Y')"),$value)->where('etat','!=','0')->where('etat','!=','-1')->where('food_item_id','!=','')->sum('item_total_amount');
        }

        $beverage = [];
        foreach ($year as $key => $value) {
            $beverage[] = FactureDetail::where(\DB::raw("DATE_FORMAT(invoice_date, '%Y')"),$value)->where('etat','!=','0')->where('etat','!=','-1')->where('drink_id','!=','')->sum('item_total_amount');
        }

        $barrist = [];
        foreach ($year as $key => $value) {
            $barrist[] = FactureDetail::where(\DB::raw("DATE_FORMAT(invoice_date, '%Y')"),$value)->where('etat','!=','0')->where('etat','!=','-1')->where('barrist_item_id','!=','')->sum('item_total_amount');
        }

        $bartender = [];
        foreach ($year as $key => $value) {
            $bartender[] = FactureDetail::where(\DB::raw("DATE_FORMAT(invoice_date, '%Y')"),$value)->where('etat','!=','0')->where('etat','!=','-1')->where('bartender_item_id','!=','')->sum('item_total_amount');
        }

        $salle = [];
        foreach ($year as $key => $value) {
            $salle[] = FactureDetail::where(\DB::raw("DATE_FORMAT(invoice_date, '%Y')"),$value)->where('etat','!=','0')->where('etat','!=','-1')->where('salle_id','!=','')->sum('item_total_amount');
        }

        $kidness_space = [];
        foreach ($year as $key => $value) {
            $kidness_space[] = FactureDetail::where(\DB::raw("DATE_FORMAT(invoice_date, '%Y')"),$value)->where('etat','!=','0')->where('etat','!=','-1')->where('kidness_space_id','!=','')->sum('item_total_amount');
        }

        $swiming_pool = [];
        foreach ($year as $key => $value) {
            $swiming_pool[] = FactureDetail::where(\DB::raw("DATE_FORMAT(invoice_date, '%Y')"),$value)->where('etat','!=','0')->where('etat','!=','-1')->where('swiming_pool_id','!=','')->sum('item_total_amount');
        }

        $service = [];
        foreach ($year as $key => $value) {
            $service[] = FactureDetail::where(\DB::raw("DATE_FORMAT(invoice_date, '%Y')"),$value)->where('etat','!=','0')->where('etat','!=','-1')->where('service_id','!=','')->sum('item_total_amount');
        }

        $total_roles = count(Role::select('id')->get());
        $total_admins = count(Admin::select('id')->get());
        $total_permissions = count(Permission::select('id')->get());
        $tables = Table::all();
        $f_tables = FTable::all();
        $employes = Facture::select(
                        DB::raw('employe_id,count(invoice_number) as invoice_number'))->where('etat','-1')->groupBy('employe_id')->orderBy('invoice_number','desc')->take(5)->get();

        return view('backend.pages.dashboard.index', 
            compact(
            'total_admins', 
            'total_roles', 
            'total_permissions',
            'employes',
            'tables',
            'f_tables'

            ))->with('year',json_encode($year,JSON_NUMERIC_CHECK))->with('food',json_encode($food,JSON_NUMERIC_CHECK))->with('year',json_encode($year,JSON_NUMERIC_CHECK))->with('beverage',json_encode($beverage,JSON_NUMERIC_CHECK))->with('barrist',json_encode($barrist,JSON_NUMERIC_CHECK))->with('year',json_encode($year,JSON_NUMERIC_CHECK))->with('bartender',json_encode($bartender,JSON_NUMERIC_CHECK))->with('salle',json_encode($salle,JSON_NUMERIC_CHECK))->with('swiming_pool',json_encode($swiming_pool,JSON_NUMERIC_CHECK))->with('year',json_encode($year,JSON_NUMERIC_CHECK))->with('service',json_encode($service,JSON_NUMERIC_CHECK))->with('kidness_space',json_encode($kidness_space,JSON_NUMERIC_CHECK));
    }

    public function changeLang(Request $request){
        \App::setlocale($request->lang);
        session()->put("locale",$request->lang);
        event(new RealTimeMessage('Hello World'));

        return redirect()->back();
    }
}

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
use App\Models\DrinkBigStoreDetail;
use App\Models\DrinkSmallStoreDetail;
use App\Models\FoodBigStoreDetail;
use App\Models\FoodSmallStoreDetail;
use App\Models\MaterialBigStoreDetail;
use App\Models\MaterialSmallStoreDetail;
use App\Models\BarristProductionStore;
use App\Models\DrinkExtraBigStoreDetail;
use App\Models\FoodExtraBigStoreDetail;
use App\Models\MaterialExtraBigStoreDetail;
use App\Models\Facture;
use App\Models\Table;

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
        
        $drink_extra_big_store = [];
        foreach ($year as $key => $value) {
            $drink_extra_big_store[] = DrinkExtraBigStoreDetail::where(\DB::raw("DATE_FORMAT(created_at, '%Y')"),$value)->sum('total_cump_value');
        }

        $drink_big_store = [];
        foreach ($year as $key => $value) {
            $drink_big_store[] = DrinkBigStoreDetail::where(\DB::raw("DATE_FORMAT(created_at, '%Y')"),$value)->sum('total_cump_value');
        }

        $drink_small_store = [];
        foreach ($year as $key => $value) {
            $drink_small_store[] = DrinkSmallStoreDetail::where(\DB::raw("DATE_FORMAT(created_at, '%Y')"),$value)->sum('total_cump_value');
        }

        $food_extra_big_store = [];
        foreach ($year as $key => $value) {
            $food_extra_big_store[] = FoodExtraBigStoreDetail::where(\DB::raw("DATE_FORMAT(created_at, '%Y')"),$value)->sum('total_cump_value');
        }

        $food_big_store = [];
        foreach ($year as $key => $value) {
            $food_big_store[] = FoodBigStoreDetail::where(\DB::raw("DATE_FORMAT(created_at, '%Y')"),$value)->sum('total_cump_value');
        }

        $food_small_store = [];
        foreach ($year as $key => $value) {
            $food_small_store[] = FoodSmallStoreDetail::where(\DB::raw("DATE_FORMAT(created_at, '%Y')"),$value)->sum('total_cump_value');
        }

        $material_extra_big_store = [];
        foreach ($year as $key => $value) {
            $material_extra_big_store[] = MaterialExtraBigStoreDetail::where(\DB::raw("DATE_FORMAT(created_at, '%Y')"),$value)->sum('total_cump_value');
        }

        $material_big_store = [];
        foreach ($year as $key => $value) {
            $material_big_store[] = MaterialBigStoreDetail::where(\DB::raw("DATE_FORMAT(created_at, '%Y')"),$value)->sum('total_cump_value');
        }

        $material_small_store = [];
        foreach ($year as $key => $value) {
            $material_small_store[] = MaterialSmallStoreDetail::where(\DB::raw("DATE_FORMAT(created_at, '%Y')"),$value)->sum('total_cump_value');
        }

        $barrist_store = [];
        foreach ($year as $key => $value) {
            $barrist_store[] = BarristProductionStore::where(\DB::raw("DATE_FORMAT(created_at, '%Y')"),$value)->sum('total_cump_value');
        }



        $total_roles = count(Role::select('id')->get());
        $total_admins = count(Admin::select('id')->get());
        $total_permissions = count(Permission::select('id')->get());
        $tables = Table::all();
       $employes = Facture::select(
                        DB::raw('employe_id,count(invoice_number) as invoice_number'))->where('etat','-1')->groupBy('employe_id')->take(5)->get();

        return view('backend.pages.dashboard.index', 
            compact(
            'total_admins', 
            'total_roles', 
            'total_permissions',
            'employes',
            'tables'

            ))->with('year',json_encode($year,JSON_NUMERIC_CHECK))->with('drink_extra_big_store',json_encode($drink_extra_big_store,JSON_NUMERIC_CHECK))->with('year',json_encode($year,JSON_NUMERIC_CHECK))->with('drink_big_store',json_encode($drink_big_store,JSON_NUMERIC_CHECK))->with('drink_small_store',json_encode($drink_small_store,JSON_NUMERIC_CHECK))->with('year',json_encode($year,JSON_NUMERIC_CHECK))->with('food_extra_big_store',json_encode($food_extra_big_store,JSON_NUMERIC_CHECK))->with('food_big_store',json_encode($food_big_store,JSON_NUMERIC_CHECK))->with('food_small_store',json_encode($food_small_store,JSON_NUMERIC_CHECK))->with('year',json_encode($year,JSON_NUMERIC_CHECK))->with('material_extra_big_store',json_encode($material_extra_big_store,JSON_NUMERIC_CHECK))->with('material_big_store',json_encode($material_big_store,JSON_NUMERIC_CHECK))->with('material_small_store',json_encode($material_small_store,JSON_NUMERIC_CHECK))->with('barrist_store',json_encode($barrist_store,JSON_NUMERIC_CHECK));
    }

    public function changeLang(Request $request){
        \App::setlocale($request->lang);
        session()->put("locale",$request->lang);
        event(new RealTimeMessage('Hello World'));

        return redirect()->back();
    }
}

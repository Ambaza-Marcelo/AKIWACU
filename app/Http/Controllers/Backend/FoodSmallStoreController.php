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
use App\Models\FoodCategory;
use App\Models\FoodSmallStore;
use App\Models\FoodSmallStoreDetail;
use App\Exports\FoodSmallStoreExport;
use Carbon\Carbon;
use Excel;
use PDF;

class FoodSmallStoreController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (is_null($this->user) || !$this->user->can('food_small_store.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any food !');
        }

        $food_small_stores = FoodSmallStore::all();
        return view('backend.pages.food_small_store.index', compact('food_small_stores'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('food_small_store.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any food !');
        }
        return view('backend.pages.food_small_store.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('food_small_store.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any food !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'emplacement' => 'required|max:20',
        ]);

        // Create New store
        $food_small_store = new FoodSmallStore();
        $food_small_store->name = $request->name;
        $reference = strtoupper(substr($request->name, 0, 3));
        $food_small_store->code = $reference.date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);
        $store_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$food_small_store->code;
        $food_small_store->store_signature = $store_signature;
        $food_small_store->emplacement = $request->emplacement;
        $food_small_store->manager = $request->manager;
        $food_small_store->created_by = $this->user->name;
        $food_small_store->save();

        // Create New store
        $food_small_store_detail = new FoodSmallStoreDetail();
        $food_small_store_detail->name = $request->name;
        $food_small_store_detail->code = $food_small_store->code;
        $food_small_store_detail->emplacement = $request->emplacement;
        $food_small_store_detail->manager = $request->manager;
        $food_small_store_detail->created_by = $this->user->name;
        $food_small_store_detail->save();
        session()->flash('success', 'Food Small Store has been created !!');
        return redirect()->route('admin.food-small-store.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $code
     * @return \Illuminate\Http\Response
     */
    public function show($code)
    {
        //
        $food_small_store = FoodSmallStore::where('code',$code)->first();
        $food_small_stores = FoodSmallStoreDetail::where('code',$code)->where('food_id','!=','')->get();
        return view('backend.pages.food_small_store.show', compact(
            'food_small_stores','food_small_store'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($code)
    {
        if (is_null($this->user) || !$this->user->can('food_small_store.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any food store !');
        }

        $food_small_store = FoodSmallStore::where('code',$code)->first();
        return view('backend.pages.food_small_store.edit', compact(
            'food_small_store'));
    }

    public function storeStatus($code)
    {
        if (is_null($this->user) || !$this->user->can('food_small_store.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any food store  !');
        }

        $datas = FoodSmallStoreDetail::where('code',$code)->where('food_id','!=','')->get();
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();
        $totalPrixAchat = DB::table('food_small_store_details')->where('code',$code)->sum('total_cump_value');

        $dateT =  $currentTime->toDateTimeString();

        $totalPrixVente = DB::table('food_small_store_details')->where('code',$code)->sum('total_selling_value');

        $store_signature = FoodSmallStoreDetail::where('code',$code)->value('store_signature');

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.food_small_store_status',compact('datas','dateTime','setting','totalPrixAchat','totalPrixVente','store_signature'));//->setPaper('a4', 'landscape');

        Storage::put('public/food_small_store/Etat_stock/'.'ETAT_DU_STOCK_'.$dateTime.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('ETA DE PETIT STOCK DES NOURRITURES '.$dateTime.'.pdf');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $code)
    {
        if (is_null($this->user) || !$this->user->can('food_small_store.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any food store !');
        }

        // Create New FoodSmallStore
        $food_small_store = FoodSmallStore::where('code',$code)->first();
        $food_small_store_detail = FoodSmallStoreDetail::where('code',$code)->first();

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'emplacement' => 'required',
        ]);


        $food_small_store->name = $request->name;
        $food_small_store->emplacement = $request->emplacement;
        $food_small_store->manager = $request->manager;
        $food_small_store->created_by = $this->user->name;
        $food_small_store->save();

        $food_small_store_detail->name = $request->name;
        $food_small_store_detail->emplacement = $request->emplacement;
        $food_small_store_detail->manager = $request->manager;
        $food_small_store_detail->created_by = $this->user->name;
        $food_small_store_detail->save();

        session()->flash('success', 'Food Small Store has been updated !!');
        return redirect()->route('admin.food-small-store.index');
    }

    public function exportToExcel(Request $request)
    {
        return Excel::download(new FoodSmallStoreExport, 'ETAT DU PETIT STOCK DES NOURRITURES.xlsx');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('food_small_store.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any food store !');
        }

        $food_small_store = FoodSmallStoreDetail::find($id);
        if (!is_null($food_small_store)) {
            $food_small_store->delete();
        }

        session()->flash('success', 'Food Small Store has been deleted !!');
        return back();
    }
}

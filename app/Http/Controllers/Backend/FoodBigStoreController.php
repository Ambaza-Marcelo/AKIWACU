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
use App\Models\FoodBigStore;
use App\Models\FoodBigStoreDetail;
use App\Exports\FoodBigStoreExport;
use Carbon\Carbon;
use Excel;
use PDF;

class FoodBigStoreController extends Controller
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
        if (is_null($this->user) || !$this->user->can('food_big_store.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any food !');
        }

        $food_big_stores = FoodBigStore::all();
        return view('backend.pages.food_big_store.index', compact('food_big_stores'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('food_big_store.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any food !');
        }
        return view('backend.pages.food_big_store.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('food_big_store.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any food !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'emplacement' => 'required|max:20',
        ]);

        // Create New store
        try {DB::beginTransaction();

        $food_big_store = new FoodBigStore();
        $food_big_store->name = $request->name;
        $reference = strtoupper(substr($request->name, 0, 3));
        $food_big_store->code = $reference.date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);
        $store_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$food_big_store->code;
        $food_big_store->store_signature = $store_signature;
        $food_big_store->emplacement = $request->emplacement;
        $food_big_store->manager = $request->manager;
        $food_big_store->created_by = $this->user->name;
        $food_big_store->save();

        $food_big_store_detail = new FoodBigStoreDetail();
        $food_big_store_detail->name = $request->name;
        $food_big_store_detail->code = $food_big_store->code;
        $food_big_store_detail->store_signature = $store_signature;
        $food_big_store_detail->emplacement = $request->emplacement;
        $food_big_store_detail->manager = $request->manager;
        $food_big_store_detail->created_by = $this->user->name;
        $food_big_store_detail->save();

        DB::commit();
            session()->flash('success', 'Food Big Store has been created !!');
            return redirect()->route('admin.food-big-store.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $code
     * @return \Illuminate\Http\Response
     */
    public function show($code)
    {
        $food_big_store = FoodBigStore::where('code',$code)->first();
        $food_big_stores = FoodBigStoreDetail::where('code',$code)->where('food_id','!=','')->get();
        return view('backend.pages.food_big_store.show', compact(
            'food_big_stores','food_big_store'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($code)
    {
        if (is_null($this->user) || !$this->user->can('food_big_store.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any food store !');
        }

        $food_big_store = FoodBigStore::where('code',$code)->first();
        return view('backend.pages.food_big_store.edit', compact(
            'food_big_store'));
    }

    public function storeStatus($code)
    {
        if (is_null($this->user) || !$this->user->can('food_big_store.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any food store  !');
        }

        $datas = FoodBigStoreDetail::where('code',$code)->where('food_id','!=','')->get();
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();
        $totalPrixAchat = DB::table('food_big_store_details')->where('code',$code)->sum('total_cump_value');

        $dateT =  $currentTime->toDateTimeString();

        $totalPrixVente = DB::table('food_big_store_details')->where('code',$code)->sum('total_selling_value');

        $store_signature = FoodBigStoreDetail::where('code',$code)->value('store_signature');

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.food_big_store_status',compact('datas','dateTime','setting','totalPrixAchat','totalPrixVente','store_signature'));//->setPaper('a4', 'landscape');

        Storage::put('public/food_big_store/Etat_stock/'.'ETAT_DU_STOCK_'.$dateTime.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('ETA DE STOCK INTERMEDIAIRE DES NOURRITURES '.$dateTime.'.pdf');
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
        if (is_null($this->user) || !$this->user->can('food_big_store.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any food store !');
        }

        // Create New FoodBigStore
        $food_big_store = FoodBigStore::where('code',$code)->first();
        $food_big_store_detail = FoodBigStoreDetail::where('code',$code)->first();

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'emplacement' => 'required',
        ]);

        try {DB::beginTransaction();

        $food_big_store->name = $request->name;
        $food_big_store->emplacement = $request->emplacement;
        $food_big_store->manager = $request->manager;
        $food_big_store->created_by = $this->user->name;
        $food_big_store->save();

        $food_big_store_detail->name = $request->name;
        $food_big_store_detail->emplacement = $request->emplacement;
        $food_big_store_detail->manager = $request->manager;
        $food_big_store_detail->created_by = $this->user->name;
        $food_big_store_detail->save();

        DB::commit();
            session()->flash('success', 'Drink Big Store has been updated !!');
            return redirect()->route('admin.food-big-store.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function exportToExcel(Request $request,$code)
    {
        return Excel::download(new FoodBigStoreExport($code), 'ETAT DE STOCK INTERMEDIAIRE DES NOURRITURES.xlsx');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('food_big_store.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any food store !');
        }

        $food_big_store = FoodBigStoreDetail::find($id);
        if (!is_null($food_big_store)) {
            $food_big_store->delete();
        }

        session()->flash('success', 'Drink Big Store has been deleted !!');
        return back();
    }
}

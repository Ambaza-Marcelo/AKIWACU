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
use App\Models\DrinkCategory;
use App\Models\DrinkExtraBigStore;
use App\Models\DrinkExtraBigStoreDetail;
use Carbon\Carbon;
use Excel;
use PDF;

class DrinkExtraBigStoreController extends Controller
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
        if (is_null($this->user) || !$this->user->can('drink_extra_big_store.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any drink !');
        }

        $drink_big_stores = DrinkExtraBigStore::all();
        return view('backend.pages.drink_extra_big_store.index', compact('drink_big_stores'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('drink_extra_big_store.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any drink !');
        }
        return view('backend.pages.drink_extra_big_store.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('drink_extra_big_store.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any drink !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'emplacement' => 'required|max:20',
        ]);

        // Create New store
        $drink_big_store = new DrinkExtraBigStore();
        $drink_big_store->name = $request->name;
        $reference = strtoupper(substr($request->name, 0, 3));
        $drink_big_store->code = $reference.date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);
        $store_signature = "4001711615".Carbon::parse(Carbon::now())->format('YmdHis')."/".$drink_big_store->code;
        $drink_big_store->store_signature = $store_signature;
        $drink_big_store->emplacement = $request->emplacement;
        $drink_big_store->manager = $request->manager;
        $drink_big_store->created_by = $this->user->name;
        $drink_big_store->save();

        $drink_big_store_detail = new DrinkExtraBigStoreDetail();
        $drink_big_store_detail->name = $drink_big_store->name;
        $drink_big_store_detail->code = $drink_big_store->code;
        $drink_big_store_detail->store_signature = $store_signature;
        $drink_big_store_detail->emplacement = $drink_big_store->emplacement;
        $drink_big_store_detail->manager = $drink_big_store->manager;
        $drink_big_store_detail->created_by = $this->user->name;
        $drink_big_store_detail->save();
        session()->flash('success', 'Drink Big Store has been created !!');
        return redirect()->route('admin.drink-extra-big-store.index');
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
        $drink_big_store = DrinkExtraBigStore::where('code',$code)->first();
        $drink_big_stores = DrinkExtraBigStoreDetail::where('code',$code)->where('drink_id','!=','')->get();
        return view('backend.pages.drink_extra_big_store.show', compact(
            'drink_big_stores','drink_big_store'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($code)
    {
        if (is_null($this->user) || !$this->user->can('drink_extra_big_store.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any drink store !');
        }

        $drink_big_store = DrinkExtraBigStore::where('code',$code)->first();
        return view('backend.pages.drink_extra_big_store.edit', compact(
            'drink_big_store'));
    }

    public function storeStatus($code)
    {
        if (is_null($this->user) || !$this->user->can('drink_extra_big_store.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any drink store  !');
        }

        $datas = DrinkExtraBigStoreDetail::where('code',$code)->where('drink_id','!=','')->get();
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();
        $totalPrixAchat = DB::table('drink_big_store_details')->where('code',$code)->sum('total_purchase_value');

        $dateT =  $currentTime->toDateTimeString();

        $totalPrixVente = DB::table('drink_big_store_details')->where('code',$code)->sum('total_selling_value');

        $store_signature = DrinkExtraBigStoreDetail::where('code',$code)->value('store_signature');

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.drink_big_store_status',compact('datas','dateTime','setting','totalPrixAchat','totalPrixVente','store_signature'));//->setPaper('a4', 'landscape');

        Storage::put('public/drink_extra_big_store/Etat_stock/'.'ETAT_DU_STOCK_'.$dateTime.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('ETAT_DU_STOCK_'.$dateTime.'.pdf');
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
        if (is_null($this->user) || !$this->user->can('drink_extra_big_store.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any drink store !');
        }

        // Create New DrinkExtraBigStore
        $drink_big_store = DrinkExtraBigStore::where('code',$code)->first();
        $drink_big_store_detail = DrinkExtraBigStoreDetail::where('code',$code)->first();

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'emplacement' => 'required',
        ]);


        $drink_big_store->name = $request->name;
        $drink_big_store->emplacement = $request->emplacement;
        $drink_big_store->manager = $request->manager;
        $drink_big_store->created_by = $this->user->name;
        $drink_big_store->save();

        $drink_big_store_detail->name = $request->name;
        $drink_big_store_detail->emplacement = $request->emplacement;
        $drink_big_store_detail->manager = $request->manager;
        $drink_big_store_detail->created_by = $this->user->name;
        $drink_big_store_detail->save();

        session()->flash('success', 'Drink Big Store has been updated !!');
        return redirect()->route('admin.drink-big-store.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('drink_extra_big_store.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any drink store !');
        }

        $drink_big_store = DrinkExtraBigStoreDetail::find($id);
        if (!is_null($drink_big_store)) {
            $drink_big_store->delete();
        }

        session()->flash('success', 'Drink Big Store has been deleted !!');
        return back();
    }
}

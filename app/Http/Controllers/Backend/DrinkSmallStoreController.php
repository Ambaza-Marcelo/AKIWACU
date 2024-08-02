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
use App\Models\DrinkSmallStore;
use App\Models\DrinkSmallStoreDetail;
use App\Exports\DrinkSmallStoreExport;
use App\Exports\VirtualDrinkSmStoreExport;
use Carbon\Carbon;
use Excel;
use PDF;

class DrinkSmallStoreController extends Controller
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
        if (is_null($this->user) || !$this->user->can('drink_small_store.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any drink !');
        }

        $drink_small_stores = DrinkSmallStore::all();
        return view('backend.pages.drink_small_store.index', compact('drink_small_stores'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('drink_small_store.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any drink !');
        }
        return view('backend.pages.drink_small_store.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('drink_small_store.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any drink !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'emplacement' => 'required|max:20',
        ]);

        // Create New store
        try {DB::beginTransaction();

        $drink_small_store = new DrinkSmallStore();
        $drink_small_store->name = $request->name;
        $reference = strtoupper(substr($request->name, 0, 3));
        $drink_small_store->code = $reference.date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);
        $store_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$drink_small_store->code;
        $drink_small_store->store_signature = $store_signature;
        $drink_small_store->emplacement = $request->emplacement;
        $drink_small_store->manager = $request->manager;
        $drink_small_store->created_by = $this->user->name;
        $drink_small_store->save();


        $drink_small_store_detail = new DrinkSmallStoreDetail();
        $drink_small_store_detail->name = $request->name;
        $drink_small_store_detail->code = $drink_small_store->code;
        $drink_small_store_detail->emplacement = $request->emplacement;
        $drink_small_store_detail->manager = $request->manager;
        $drink_small_store_detail->created_by = $this->user->name;
        $drink_small_store_detail->save();

        DB::commit();
            session()->flash('success', 'DrinkSmallStore has been created !!');
            return redirect()->route('admin.drink-small-store.index');
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
        //
        $drink_small_store = DrinkSmallStore::where('code',$code)->first();
        $drink_small_stores = DrinkSmallStoreDetail::where('code',$code)->where('drink_id','!=','')->get();
        return view('backend.pages.drink_small_store.show', compact(
            'drink_small_stores','drink_small_store'));
    }

    public function storeStatus($code)
    {
        if (is_null($this->user) || !$this->user->can('drink_small_store.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any drink store  !');
        }

        $datas = DrinkSmallStoreDetail::where('code',$code)->where('drink_id','!=','')->get();
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();
        $totalPrixAchat = DB::table('drink_small_store_details')->where('code',$code)->sum('total_purchase_value');

        $dateT =  $currentTime->toDateTimeString();

        $totalPrixVente = DB::table('drink_small_store_details')->where('code',$code)->sum('total_selling_value');

        $store_signature = DrinkSmallStoreDetail::where('code',$code)->value('store_signature');

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.drink_small_store_status',compact('datas','dateTime','setting','totalPrixAchat','totalPrixVente','store_signature'))->setPaper('a4', 'landscape');

        Storage::put('public/drink_small_store/Etat_stock/'.'ETAT_DU_STOCK_'.$dateTime.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('ETAT_DU_STOCK_'.$dateTime.'.pdf');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($code)
    {
        if (is_null($this->user) || !$this->user->can('drink_small_store.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any drink store !');
        }

        $drink_small_store = DrinkSmallStore::where('code',$code)->first();
        return view('backend.pages.drink_small_store.edit', compact(
            'drink_small_store'));
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
        if (is_null($this->user) || !$this->user->can('drink_small_store.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any drink store !');
        }

        // Create New DrinkSmallStore
        $drink_small_store = DrinkSmallStore::where('code',$code)->first();
        $drink_small_store_detail = DrinkSmallStoreDetail::where('code',$code)->first();

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'emplacement' => 'required',
        ]);

        try {DB::beginTransaction();

        $drink_small_store->name = $request->name;
        $drink_small_store->emplacement = $request->emplacement;
        $drink_small_store->manager = $request->manager;
        $drink_small_store->created_by = $this->user->name;
        $drink_small_store->save();

        $drink_small_store_detail->name = $request->name;
        $drink_small_store_detail->emplacement = $request->emplacement;
        $drink_small_store_detail->manager = $request->manager;
        $drink_small_store_detail->created_by = $this->user->name;
        $drink_small_store_detail->save();

        DB::commit();
            session()->flash('success', 'Drink Small Store has been updated !!');
            return redirect()->route('admin.drink-small-store.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function exportToExcel(Request $request,$code)
    {
        $currentTime = Carbon::now();
        $dateT =  $currentTime->toDateTimeString();
        $dateTime = str_replace([' ',':'], '_', $dateT);

        return Excel::download(new DrinkSmallStoreExport($code), 'etat_du_petit_stock_boissons_'.$dateTime.'.xlsx');
    }

    public function virtualExportToExcel(Request $request)
    {
        $currentTime = Carbon::now();
        $dateT =  $currentTime->toDateTimeString();
        $dateTime = str_replace([' ',':'], '_', $dateT);

        return Excel::download(new VirtualDrinkSmStoreExport(), 'etat_du_petit_stock_boissons_'.$dateTime.'.xlsx');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('drink_small_store.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any drink store !');
        }

        $drink_small_store = DrinkSmallStoreDetail::find($id);
        if (!is_null($drink_small_store)) {
            $drink_small_store->delete();
        }

        session()->flash('success', 'Drink Small Store has been deleted !!');
        return back();
    }
}

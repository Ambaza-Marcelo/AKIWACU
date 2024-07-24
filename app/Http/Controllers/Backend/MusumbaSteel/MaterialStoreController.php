<?php

namespace App\Http\Controllers\Backend\MusumbaSteel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Storage;
use App\Models\MsMaterialStore;
use App\Models\MsMaterialStoreDetail;
use Carbon\Carbon;
use Excel;
use PDF;
use App\Exports\MusumbaSteel\MaterialStoreExport;

class MaterialStoreController extends Controller
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_store.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any material !');
        }

        $material_stores = MsMaterialStore::all();
        return view('backend.pages.musumba_steel.material_store.index', compact('material_stores'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_store.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any material !');
        }
        return view('backend.pages.musumba_steel.material_store.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_store.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any material !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'emplacement' => 'required|max:20',
        ]);

        // Create New store
        $material_store = new MsMaterialStore();
        $material_store->name = $request->name;
        $reference = strtoupper(substr($request->name, 0, 3));
        $material_store->code = $reference.date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);
        $store_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$material_store->code;
        $material_store->store_signature = $store_signature;
        $material_store->emplacement = $request->emplacement;
        $material_store->manager = $request->manager;
        $material_store->created_by = $this->user->name;
        $material_store->save();

        $material_store_detail = new MsMaterialStoreDetail();
        $material_store_detail->name = $request->name;
        $material_store_detail->code = $material_store->code;
        $material_store_detail->store_signature = $store_signature;
        $material_store_detail->emplacement = $request->emplacement;
        $material_store_detail->manager = $request->manager;
        $material_store_detail->created_by = $this->user->name;
        $material_store_detail->save();
        session()->flash('success', 'Material Store has been created !!');
        return redirect()->route('admin.ms-material-store.index');
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
        $material_store = MsMaterialStore::where('code',$code)->first();
        $material_stores = MsMaterialStoreDetail::where('code',$code)->where('material_id','!=','')->get();
        return view('backend.pages.musumba_steel.material_store.show', compact(
            'material_stores','material_store'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($code)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_store.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any material store !');
        }

        $material_store = MsMaterialStore::where('code',$code)->first();
        return view('backend.pages.musumba_steel.material_store.edit', compact(
            'material_store'));
    }

    public function storeStatus($code)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_store.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any material store  !');
        }

        $datas = MsMaterialStoreDetail::where('code',$code)->where('material_id','!=','')->get();
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();
        $totalPrixAchat = DB::table('ms_material_store_details')->where('code',$code)->sum('total_purchase_value');

        $dateT =  $currentTime->toDateTimeString();

        $store_signature = MsMaterialStoreDetail::where('code',$code)->value('store_signature');

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.musumba_steel.document.material_store_status',compact('datas','dateTime','setting','totalPrixAchat','store_signature'));//->setPaper('a4', 'landscape');

        Storage::put('public/musumba_steel/material_store/Etat_stock/'.'ETAT_DU_STOCK_'.$dateTime.'.pdf', $pdf->output());

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
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_store.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any material store !');
        }

        // Create New MsMaterialStore
        $material_store = MsMaterialStore::find('code',$code)->first();
        $material_store_detail = MsMaterialStoreDetail::find('code',$code)->first();

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'emplacement' => 'required',
        ]);


        $material_store->name = $request->name;
        $material_store->emplacement = $request->emplacement;
        $material_store->manager = $request->manager;
        $material_store->created_by = $this->user->name;
        $material_store->save();

        $material_store_detail->name = $request->name;
        $material_store_detail->emplacement = $request->emplacement;
        $material_store_detail->manager = $request->manager;
        $material_store_detail->created_by = $this->user->name;
        $material_store_detail->save();

        session()->flash('success', 'Material Big Store has been updated !!');
        return redirect()->route('admin.ms-material-store.index');
    }



    public function exportToExcel($code)
    {
        return Excel::download(new MaterialStoreExport($code), 'Etat_stock.xlsx');
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_material_store.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any material store !');
        }

        $material_store = MsMaterialStoreDetail::find($id);
        if (!is_null($material_store)) {
            $material_store->delete();
        }

        session()->flash('success', 'Material Big Store has been deleted !!');
        return back();
    }
}

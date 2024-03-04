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
use App\Models\PrivateStoreItem;
use App\Exports\PrivateStoreExport;
use Excel;
use Carbon\Carbon;
use PDF;

class PrivateStoreItemController extends Controller
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
        if (is_null($this->user) || !$this->user->can('private_store_item.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any drink !');
        }

        $items = PrivateStoreItem::all();
        return view('backend.pages.private_store_item.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('private_store_item.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any drink !');
        }
        return view('backend.pages.private_store_item.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('private_store_item.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any drink !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'unit' => 'required|max:20',
            'purchase_price' => 'required',
            'selling_price' => 'required',
            'quantity' => 'required',
        ]);

        // Create New PrivateStoreItem
        $private_store_item = new PrivateStoreItem();
        $private_store_item->name = $request->name;
        $artCode = strtoupper(substr($request->name, 0, 3));
        $private_store_item->code = $artCode.date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);
        $private_store_item->unit = $request->unit;
        $private_store_item->purchase_price = $request->purchase_price;
        $private_store_item->selling_price = $request->selling_price;
        $private_store_item->quantity = $request->quantity;
        $private_store_item->specification = $request->specification;
        $private_store_item->vat = $request->vat;
        $private_store_item->expiration_date = $request->expiration_date;
        $private_store_item->threshold_quantity = $request->threshold_quantity;
        $private_store_item->created_by = $this->user->name;
        $private_store_item->save();

        session()->flash('success', 'Item has been created !!');
        return redirect()->route('admin.private-store-items.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    public function uploadArticle(Request $request)
    {
        Excel::import(new ArticlesImport, $request->file('file')->store('temp'));
        return redirect()->back();
    }

    public function exportToPdf()
    {
        if (is_null($this->user) || !$this->user->can('private_store_item.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any drink store  !');
        }

        $datas = PrivateStoreItem::orderBy('name')->get();
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.private_store_status',compact('datas','dateTime','setting'));//->setPaper('a4', 'landscape');

        Storage::put('public/STOCK-PDG/Etat_stock/'.'ETAT_DU_STOCK_'.$dateTime.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('ETAT_DU_STOCK_'.$dateTime.'.pdf');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('private_store_item.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any drink !');
        }

        $item = PrivateStoreItem::find($id);
        return view('backend.pages.private_store_item.edit', compact(
            'item'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('private_store_item.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any drink !');
        }

        // Create New PrivateStoreItem
        $private_store_item = PrivateStoreItem::find($id);

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'unit' => 'required|max:20',
            'purchase_price' => 'required',
            'selling_price' => 'required',
            'quantity' => 'required',
        ]);

        
        $private_store_item->name = $request->name;
        $private_store_item->unit = $request->unit;
        $private_store_item->purchase_price = $request->purchase_price;
        $private_store_item->selling_price = $request->selling_price;
        $private_store_item->quantity = $request->quantity;
        $private_store_item->specification = $request->specification;
        $private_store_item->vat = $request->vat;
        $private_store_item->expiration_date = $request->expiration_date;
        $private_store_item->threshold_quantity = $request->threshold_quantity;
        $private_store_item->created_by = $this->user->name;
        $private_store_item->save();

        session()->flash('success', 'Item has been updated !!');
        return redirect()->route('admin.private-store-items.index');
    }

    public function exportToExcel()
    {
        return Excel::download(new PrivateStoreExport(), 'ETAT_DU_STOCK_BOISSONS_PDG.xlsx');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('private_store_item.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any drink !');
        }

        $private_store_item = PrivateStoreItem::find($id);
        if (!is_null($private_store_item)) {
            $private_store_item->delete();
        }

        session()->flash('success', 'Item has been deleted !!');
        return back();
    }
}

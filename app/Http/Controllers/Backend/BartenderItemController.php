<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\BartenderItem;
use App\Models\BartenderProductionStore;
use Excel;

class BartenderItemController extends Controller
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
        if (is_null($this->user) || !$this->user->can('bartender_item.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any drink !');
        }

        $bartender_items = BartenderItem::all();
        return view('backend.pages.bartender_item.index', compact('bartender_items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('bartender_item.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any drink !');
        }
        return view('backend.pages.bartender_item.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('bartender_item.create')) {
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

        // Create New BartenderItem
        $bartender_item = new BartenderItem();
        $bartender_item->name = $request->name;
        $artCode = strtoupper(substr($request->name, 0, 3));
        $bartender_item->code = $artCode.date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);
        $bartender_item->unit = $request->unit;
        $bartender_item->purchase_price = $request->purchase_price;
        $bartender_item->selling_price = $request->selling_price;
        $bartender_item->quantity = $request->quantity;
        $bartender_item->specification = $request->specification;
        $bartender_item->vat = $request->vat;
        //$bartender_item->taux_marge = $request->taux_marge;
        //$bartender_item->taux_majoration = $request->taux_majoration;
        //$bartender_item->taux_reduction = $request->taux_reduction;
        $bartender_item->expiration_date = $request->expiration_date;
        $bartender_item->threshold_quantity = $request->threshold_quantity;
        $bartender_item->created_by = $this->user->name;
        $bartender_item->save();

        $bartender_store = new BartenderProductionStore();

        $bartender_item_id = BartenderItem::latest()->first()->id;

        $unit = BartenderItem::where('id',$bartender_item_id)->value('unit');
        $quantity = BartenderItem::where('id',$bartender_item_id)->value('quantity');
        $purchase_price = BartenderItem::where('id',$bartender_item_id)->value('purchase_price');
        $selling_price = BartenderItem::where('id',$bartender_item_id)->value('selling_price');
        $threshold_quantity = BartenderItem::where('id',$bartender_item_id)->value('threshold_quantity');
        $vat = BartenderItem::where('id',$bartender_item_id)->value('vat');

        $bartender_store->bartender_item_id = $bartender_item_id;
        $bartender_store->quantity = $quantity;
        $bartender_store->threshold_quantity = $threshold_quantity;
        $bartender_store->purchase_price = $purchase_price;
        $bartender_store->selling_price = $selling_price;
        $bartender_store->vat = $vat;
        $bartender_store->total_purchase_value = $quantity * $purchase_price;
        $bartender_store->total_selling_value = $quantity * $selling_price;
        $bartender_store->unit = $unit;
        $bartender_store->created_by = $this->user->name;
        $bartender_store->save();

        session()->flash('success', 'BartenderItem has been created !!');
        return redirect()->route('admin.bartender-items.index');
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


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('bartender_item.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any drink !');
        }

        $bartender_item = BartenderItem::find($id);
        return view('backend.pages.bartender_item.edit', compact(
            'bartender_item'));
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
        if (is_null($this->user) || !$this->user->can('bartender_item.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any drink !');
        }

        // Create New BartenderItem
        $bartender_item = BartenderItem::find($id);

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'unit' => 'required|max:20',
            'purchase_price' => 'required',
            'selling_price' => 'required',
            'quantity' => 'required',
        ]);

        
        $bartender_item->name = $request->name;
        $bartender_item->unit = $request->unit;
        $bartender_item->purchase_price = $request->purchase_price;
        $bartender_item->selling_price = $request->selling_price;
        $bartender_item->quantity = $request->quantity;
        $bartender_item->specification = $request->specification;
        $bartender_item->vat = $request->vat;
        //$bartender_item->taux_marge = $request->taux_marge;
        //$bartender_item->taux_majoration = $request->taux_majoration;
        //$bartender_item->taux_reduction = $request->taux_reduction;
        $bartender_item->expiration_date = $request->expiration_date;
        $bartender_item->threshold_quantity = $request->threshold_quantity;
        $bartender_item->created_by = $this->user->name;
        $bartender_item->save();

        $unit = BartenderItem::where('id',$id)->value('unit');
        $quantity = BartenderItem::where('id',$id)->value('quantity');
        $purchase_price = BartenderItem::where('id',$id)->value('purchase_price');
        $selling_price = BartenderItem::where('id',$id)->value('selling_price');
        $threshold_quantity = BartenderItem::where('id',$id)->value('threshold_quantity');
        $vat = BartenderItem::where('id',$id)->value('vat');

        $bartender_store = BartenderProductionStore::where('bartender_item_id',$id)->first();
        $bartender_store->bartender_item_id = $id;
        $bartender_store->quantity = $quantity;
        $bartender_store->threshold_quantity = $threshold_quantity;
        $bartender_store->purchase_price = $purchase_price;
        $bartender_store->selling_price = $selling_price;
        $bartender_store->vat = $vat;
        $bartender_store->total_purchase_value = $quantity * $purchase_price;
        $bartender_store->total_selling_value = $quantity * $selling_price;
        $bartender_store->unit = $unit;
        $bartender_store->created_by = $this->user->name;
        $bartender_store->save();

        session()->flash('success', 'BartenderItem has been updated !!');
        return redirect()->route('admin.bartender-items.index');
    }

    public function get_article_data()
    {
        return Excel::download(new ArticleExport, 'articles.xlsx');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('bartender_item.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any drink !');
        }

        $bartender_item = BartenderItem::find($id);
        if (!is_null($bartender_item)) {
            $bartender_item->delete();
        }

        session()->flash('success', 'BartenderItem has been deleted !!');
        return back();
    }
}

<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\BarristItem;
use App\Models\BarristStore;
use App\Models\BarristProductionStore;
use Excel;

class BarristItemController extends Controller
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
        if (is_null($this->user) || !$this->user->can('barrist_item.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any barrist_item !');
        }

        $barrist_items = BarristItem::all();
        return view('backend.pages.barrist_item.index', compact('barrist_items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('barrist_item.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any barrist_item !');
        }
        return view('backend.pages.barrist_item.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('barrist_item.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any barrist_item !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'unit' => 'required|max:20',
            'purchase_price' => 'required',
            'selling_price' => 'required',
            'quantity' => 'required',
        ]);

        // Create New BarristItem
        $barrist_item = new BarristItem();
        $barrist_item->name = $request->name;
        $artCode = strtoupper(substr($request->name, 0, 3));
        $barrist_item->code = $artCode.date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);
        
        $barrist_item->unit = $request->unit;
        $barrist_item->purchase_price = $request->purchase_price;
        $barrist_item->selling_price = $request->selling_price;
        $barrist_item->quantity = $request->quantity;
        $barrist_item->specification = $request->specification;
        $barrist_item->vat = $request->vat;
        $barrist_item->taux_marge = $request->taux_marge;
        $barrist_item->taux_majoration = $request->taux_majoration;
        $barrist_item->taux_reduction = $request->taux_reduction;
        $barrist_item->expiration_date = $request->expiration_date;
        $barrist_item->threshold_quantity = $request->threshold_quantity;
        $barrist_item->created_by = $this->user->name;
        $barrist_item->save();

        $barrist_store = new BarristProductionStore();

        $barrist_item_id = BarristItem::latest()->first()->id;

        $unit = BarristItem::where('id',$barrist_item_id)->value('unit');
        $quantity = BarristItem::where('id',$barrist_item_id)->value('quantity');
        $purchase_price = BarristItem::where('id',$barrist_item_id)->value('purchase_price');
        $selling_price = BarristItem::where('id',$barrist_item_id)->value('selling_price');
        $threshold_quantity = BarristItem::where('id',$barrist_item_id)->value('threshold_quantity');
        $vat = BarristItem::where('id',$barrist_item_id)->value('vat');

        $barrist_store->barrist_item_id = $barrist_item_id;
        $barrist_store->quantity = $quantity;
        $barrist_store->threshold_quantity = $threshold_quantity;
        $barrist_store->purchase_price = $purchase_price;
        $barrist_store->selling_price = $selling_price;
        $barrist_store->vat = $vat;
        $barrist_store->total_purchase_value = $quantity * $purchase_price;
        $barrist_store->total_selling_value = $quantity * $selling_price;
        $barrist_store->unit = $unit;
        $barrist_store->created_by = $this->user->name;
        $barrist_store->save();

        session()->flash('success', 'BarristItem has been created !!');
        return redirect()->route('admin.barrist-items.index');
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
        if (is_null($this->user) || !$this->user->can('barrist_item.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any barrist_item !');
        }

        $barrist_item = BarristItem::find($id);
        return view('backend.pages.barrist_item.edit', compact(
            'barrist_item'));
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
        if (is_null($this->user) || !$this->user->can('barrist_item.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any barrist_item !');
        }

        // Create New BarristItem
        $barrist_item = BarristItem::find($id);

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'unit' => 'required|max:20',
            'purchase_price' => 'required',
            'selling_price' => 'required',
            'quantity' => 'required',
        ]);


        $barrist_item->name = $request->name;
        $barrist_item->unit = $request->unit;
        $barrist_item->purchase_price = $request->purchase_price;
        $barrist_item->selling_price = $request->selling_price;
        $barrist_item->quantity = $request->quantity;
        $barrist_item->specification = $request->specification;
        $barrist_item->vat = $request->vat;
        //$barrist_item->taux_marge = $request->taux_marge;
        //$barrist_item->taux_majoration = $request->taux_majoration;
        //$barrist_item->taux_reduction = $request->taux_reduction;
        $barrist_item->expiration_date = $request->expiration_date;
        $barrist_item->threshold_quantity = $request->threshold_quantity;
        $barrist_item->created_by = $this->user->name;
        $barrist_item->save();

        $unit = BarristItem::where('id',$id)->value('unit');
        $quantity = BarristItem::where('id',$id)->value('quantity');
        $purchase_price = BarristItem::where('id',$id)->value('purchase_price');
        $selling_price = BarristItem::where('id',$id)->value('selling_price');
        $threshold_quantity = BarristItem::where('id',$id)->value('threshold_quantity');
        $vat = BarristItem::where('id',$id)->value('vat');

        $barrist_store = BarristProductionStore::where('barrist_item_id',$id)->first();
        $barrist_store->barrist_item_id = $id;
        $barrist_store->quantity = $quantity;
        $barrist_store->threshold_quantity = $threshold_quantity;
        $barrist_store->purchase_price = $purchase_price;
        $barrist_store->selling_price = $selling_price;
        $barrist_store->vat = $vat;
        $barrist_store->total_purchase_value = $quantity * $purchase_price;
        $barrist_store->total_selling_value = $quantity * $selling_price;
        $barrist_store->unit = $unit;
        $barrist_store->created_by = $this->user->name;
        $barrist_store->save();

        session()->flash('success', 'BarristItem has been updated !!');
        return redirect()->route('admin.barrist-items.index');
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
        if (is_null($this->user) || !$this->user->can('barrist_item.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any barrist_item !');
        }

        $barrist_item = BarristItem::find($id);
        if (!is_null($barrist_item)) {
            $barrist_item->delete();
        }

        session()->flash('success', 'BarristItem has been deleted !!');
        return back();
    }
}

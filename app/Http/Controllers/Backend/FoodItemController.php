<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\FoodItem;
use App\Models\FoodItemDetail;
use App\Models\BarristStore;
use App\Models\FoodStore;
use App\Models\FoodCategory;
use App\Models\Food;
use App\Exports\FicheTechniqueNourritureExport;
use Validator;
use Carbon\Carbon;
use Excel;
use PDF;

class FoodItemController extends Controller
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
        if (is_null($this->user) || !$this->user->can('food_item.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any food_item !');
        }

        $food_items = FoodItem::all();
        return view('backend.pages.food_item.index', compact('food_items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('food_item.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any food_item !');
        }
        $categories = FoodCategory::all();
        $foods = Food::all();

        return view('backend.pages.food_item.create',compact('categories','foods'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('food_item.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any food_item !');
        }

        $rules = array(
            'name' => 'required|max:255',
            'unit' => 'required|max:20',
            'purchase_price' => 'required',
            'selling_price' => 'required',
            'quantity.*' => 'required',
            'food_id.*' => 'required',
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $food_id = $request->food_id;
            $name = $request->name;
            $unit = $request->unit;
            $purchase_price = $request->purchase_price;
            $selling_price = $request->selling_price;
            $quantity = $request->quantity; 
            $specification = $request->specification;
            $vat = $request->vat;
            $created_by = $this->user->name;

            $artCode = strtoupper(substr($name, 0, 3));
            $code = $artCode.date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);


            for( $count = 0; $count < count($food_id); $count++ ){
                    $data = array(
                        'food_id' => $food_id[$count],
                        'name' => $name,
                        'quantity' => $quantity[$count],
                        'unit' => $unit,
                        'purchase_price' => $purchase_price,
                        'selling_price' => $selling_price,
                        'specification' => $specification,
                        'vat' => $vat,
                        'code' => $code,
                        'created_by' => $created_by,
                        'created_at' => \Carbon\Carbon::now()

                    );
                $insert_data[] = $data;
            }

            FoodItemDetail::insert($insert_data);

        // Create New FoodItem
        $food_item = new FoodItem();
        $food_item->name = $name;
        $food_item->code = $code;
        $food_item->unit = $unit;
        $food_item->purchase_price = $purchase_price;
        $food_item->selling_price = $selling_price;
        $food_item->vat = $request->vat;
        //$food_item->taux_marge = $request->taux_marge;
        //$food_item->taux_majoration = $request->taux_majoration;
        //$food_item->taux_reduction = $request->taux_reduction;
        $food_item->threshold_quantity = 0;
        $food_item->created_by = $this->user->name;
        $food_item->save();

        $food_store = new FoodStore();

        $food_item_id = FoodItem::latest()->first()->id;

        $unit = FoodItem::where('id',$food_item_id)->value('unit');
        $quantity = 0;
        $purchase_price = FoodItem::where('id',$food_item_id)->value('purchase_price');
        $selling_price = FoodItem::where('id',$food_item_id)->value('selling_price');
        $threshold_quantity = 0;
        $vat = FoodItem::where('id',$food_item_id)->value('vat');

        $food_store->food_item_id = $food_item_id;
        $food_store->quantity = $quantity;
        $food_store->threshold_quantity = $threshold_quantity;
        $food_store->purchase_price = $purchase_price;
        $food_store->selling_price = $selling_price;
        $food_store->code = $code;
        $food_store->vat = $vat;
        $food_store->total_purchase_value = $quantity * $purchase_price;
        $food_store->total_selling_value = $quantity * $selling_price;
        $food_store->unit = $unit;
        $food_store->created_by = $this->user->name;
        $food_store->save();

        session()->flash('success', 'FoodItem has been created !!');
        return redirect()->route('admin.food-items.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($code)
    {
        //
        if (is_null($this->user) || !$this->user->can('food_item.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any food_item !');
        }

        $datas = FoodItemDetail::where('code',$code)->get();
        return view('backend.pages.food_item.show', compact('datas'));
    }


    public function ficheTechnique(){

        $datas = DB::table('food_item_details')->select('code','name')->distinct()->get();
        $foods = FoodItemDetail::where('food_id','!=','')->get();
        $pdf = PDF::loadView('backend.pages.document.fiche_technique',compact('datas','foods'));//->setPaper('a6', 'portrait');

           // download pdf file
           return $pdf->download('FICHES_TECHNIQUES'.'.pdf');
    }

    public function fiche($code){

        $data = FoodItem::where('code', $code)->first();
        $datas = FoodItemDetail::where('code', $code)->get();
        $pdf = PDF::loadView('backend.pages.food_item.fiche',compact('data','datas'))->setPaper('a6', 'portrait');

           // download pdf file
           return $pdf->download('FICHE_TECHNIQUE_'.$data->name.'.pdf');
    }


    public function uploadArticle(Request $request)
    {
        Excel::import(new ArticlesImport, $request->file('file')->store('temp'));
        return redirect()->back();
    }

    public function exportToExcel(Request $request)
    {
        $currentTime = Carbon::now();
        $dateT =  $currentTime->toDateTimeString();
        $dateTime = str_replace([' ',':'], '_', $dateT);

        return Excel::download(new FicheTechniqueNourritureExport(), 'fiche_technique_stock_nourriture'.$dateTime.'.xlsx');
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($code)
    {
        if (is_null($this->user) || !$this->user->can('food_item.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any food_item !');
        }
        $categories = FoodCategory::all();
        $food_item = FoodItem::where('code',$code)->first();
        $foods = Food::all();
        $datas = FoodItemDetail::where('code',$code)->get();
        return view('backend.pages.food_item.edit', compact(
            'food_item','categories','foods','datas'));
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
        if (is_null($this->user) || !$this->user->can('food_item.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any food_item !');
        }
        

        // Validation Data
        $rules = array(
            'name' => 'required|max:255',
            'unit' => 'required|max:20',
            'purchase_price' => 'required',
            'selling_price' => 'required',
            'quantity.*' => 'required',
            'food_id.*' => 'required',
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }


            $food_id = $request->food_id;
            $name = $request->name;
            $unit = $request->unit;
            $purchase_price = $request->purchase_price;
            $selling_price = $request->selling_price;
            $quantity = $request->quantity; 
            $specification = $request->specification;
            $vat = $request->vat;
            $created_by = $this->user->name;


            for( $count = 0; $count < count($food_id); $count++ ){

                $id_food = FoodItemDetail::where('code',$code)->where('food_id',$food_id[$count])->value('food_id');

                    $data = array(
                        'food_id' => $food_id[$count],
                        'name' => $name,
                        'quantity' => $quantity[$count],
                        'unit' => $unit,
                        'purchase_price' => $purchase_price,
                        'selling_price' => $selling_price,
                        'specification' => $specification,
                        'vat' => $vat,
                        'code' => $code,
                        'created_by' => $created_by,
                        'created_at' => \Carbon\Carbon::now()

                    );

                    $insert_data[] = $data;

                if (!empty($id_food)) {
                    FoodItemDetail::where('code',$code)->delete();
                }
            }

        FoodItemDetail::insert($insert_data);

        $food_item = FoodItem::where('code',$code)->first();

        $food_item->name = $name;
        $food_item->unit = $unit;
        $food_item->purchase_price = $purchase_price;
        $food_item->selling_price = $selling_price;
        $food_item->vat = $request->vat;
        //$food_item->taux_marge = $request->taux_marge;
        //$food_item->taux_majoration = $request->taux_majoration;
        //$food_item->taux_reduction = $request->taux_reduction;
        $food_item->threshold_quantity = 0;
        $food_item->created_by = $this->user->name;
        $food_item->save();
        /*
        $unit = FoodItem::where('code',$code)->value('unit');
        $food_item_id = FoodItem::where('code',$code)->value('id');
        $quantity = 0;
        $purchase_price = FoodItem::where('code',$code)->value('purchase_price');
        $selling_price = FoodItem::where('code',$code)->value('selling_price');
        $threshold_quantity = 0;
        $vat = FoodItem::where('code',$code)->value('vat');

        $food_store = FoodStore::where('code',$code)->first();
        $food_store->food_item_id = $food_item_id;
        $food_store->quantity = $quantity;
        $food_store->threshold_quantity = $threshold_quantity;
        $food_store->purchase_price = $purchase_price;
        $food_store->selling_price = $selling_price;
        $food_store->vat = $vat;
        $food_store->total_purchase_value = $quantity * $purchase_price;
        $food_store->total_selling_value = $quantity * $selling_price;
        $food_store->unit = $unit;
        $food_store->created_by = $this->user->name;
        $food_store->save();
        */

        session()->flash('success', 'FoodItem has been updated !!');
        return redirect()->route('admin.food-items.index');
    }

    public function get_article_data()
    {
        return Excel::download(new ArticleExport, 'articles.xlsx');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $code
     * @return \Illuminate\Http\Response
     */
    public function destroy($code)
    {
        if (is_null($this->user) || !$this->user->can('food_item.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any food_item !');
        }

        $food_item = FoodItem::where('code',$code)->first();
        if (!is_null($food_item)) {
            $food_item->delete();
            FoodItemDetail::where('code',$code)->delete();
        }

        session()->flash('success', 'FoodItem has been deleted !!');
        return back();
    }
}

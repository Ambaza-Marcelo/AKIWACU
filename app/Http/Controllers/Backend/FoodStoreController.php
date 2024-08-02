<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\FoodItem;
use App\Models\FoodStore;
use App\Models\FoodTransfer;
use App\Models\FoodStoreReport;
use App\Models\Setting;
use Excel;
use Carbon\Carbon;
use Validator;
use PDF;

use Mail;
use App\Mail\DeleteStockMail;

class FoodStoreController extends Controller
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
        if (is_null($this->user) || !$this->user->can('food_store.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any food store !');
        }

        $stocks = FoodStore::all();
        return view('backend.pages.food_store.index', compact('stocks'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('food_item.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any item !');
        }
        $food_items = FoodItem::all();
        $transfer_foods =  FoodTransfer::where('origin_store_id','!=','')->where('status',4)->whereNotIn('transfer_no', function($q){
        $q->select('food_transfer_no')->from('food_stores');
        })->orderBy('transfer_no','asc')->get();
        return view('backend.pages.food_store.create', compact(
            'food_items','transfer_foods'));
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
            abort(403, 'Sorry !! You are Unauthorized to create any item !');
        }

        $rules = array(
                'quantity.*'  => 'required',
                //'selling_price.*'  => 'required',
                'unit.*'  => 'required',
                'food_item_id.*'  => 'required',
                'description'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            try {DB::beginTransaction();

            $food_item_id = $request->food_item_id;
            $date = $request->date;
            $quantity = $request->quantity;
            $unit = $request->unit;
            $selling_price =$request->selling_price; 
            $description =$request->description;
            $food_transfer_no =$request->food_transfer_no;
            $created_by = $this->user->name;

            for( $count = 0; $count < count($food_item_id); $count++ ){

                $price = FoodItem::where("id",$food_item_id[$count])->value('selling_price');
                $threshold_quantity = FoodItem::where("id",$food_item_id[$count])->value('threshold_quantity');
                $data = array(
                    'food_item_id' => $food_item_id[$count],
                    'quantity' => $quantity[$count],
                    'unit' => $unit[$count],
                    'selling_price' => $price,
                    'purchase_price' => $price,
                    'food_transfer_no' => $food_transfer_no,
                    'description' => $description,
                    'total_purchase_value' => $price * $quantity[$count],
                    'total_selling_value' => $price * $quantity[$count],
                    'created_by' => $created_by,
                );
                $insert_data[] = $data;


                $valeurStockInitial = FoodStore::where('food_item_id', $food_item_id[$count])->value('total_selling_value');
                $quantityStockInitial = FoodStore::where('food_item_id', $food_item_id[$count])->value('quantity');


                $valeurAcquisition = $quantity[$count] * $price;

                $valeurTotalUnite = $quantity[$count] + $quantityStockInitial;
                $cump = ($valeurStockInitial + $valeurAcquisition) / $valeurTotalUnite;

                $reportData = array(
                    'food_item_id' => $food_item_id[$count],
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'quantity_stockin' => $quantity[$count],
                    'value_stockin' => $price * $quantity[$count],
                    'stock_total' => $quantityStockInitial + $quantity[$count],
                    'quantity_stock_final' => $quantityStockInitial + $quantity[$count],
                    'value_stock_final' => $quantityStockInitial * $price,
                    'created_by' => $this->user->name,
                    'description' => $description,
                    'stockin_no' => $food_transfer_no,
                    'created_at' => \Carbon\Carbon::now()
                );
                $report[] = $reportData;

                    $donnees = array(
                        'food_item_id' => $food_item_id[$count],
                        'quantity' => $valeurTotalUnite,
                        'selling_price' => $price,
                        'purchase_price' => $price,
                        'threshold_quantity' => $threshold_quantity,
                        'food_transfer_no' => $food_transfer_no,
                        'description' => $description,
                        'total_purchase_value' => $price * $quantity[$count],
                        'total_selling_value' => $price * $quantity[$count],
                        'unit' => $unit[$count],
                        'cump' => $cump,
                        'verified' => false,
                        'created_by' => $this->user->name,
                    );
                    $stock[] = $donnees;

                    $artic = FoodStore::where("food_item_id",$food_item_id[$count])->value('food_item_id');
                    if (!empty($artic)) {
                        FoodStoreReport::insert($report);
                        FoodStore::where('food_item_id',$food_item_id[$count])
                        ->update($donnees);
                    }else{
                        FoodStoreReport::insert($report);
                        FoodStore::insert($stock);
                    }
            }
       
        //FoodStore::insert($insert_data);

        DB::commit();
            session()->flash('success', 'Data has been Saved Successfuly !!');
            return redirect()->route('admin.food-store.index');
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function toPdf()
    {
        if (is_null($this->user) || !$this->user->can('food_store.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any food store !');
        }

        $datas = FoodStore::all();
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();
        $totalValue = DB::table('stocks')->sum('total_value');

        $dateT =  $currentTime->toDateTimeString();

        $totalValue = DB::table('stocks')->sum('total_value');

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.stock_status',compact('datas','dateTime','setting','totalValue'));//->setPaper('a4', 'landscape');

        Storage::put('public/pdf/Etat_stock/'.'ETAT_DU_STOCK_'.$dateTime.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('ETAT_DU_STOCK_'.$dateTime.'.pdf');
    }

    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('food_store.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any food_store !');
        }

        $stock = FoodStore::find($id);
        if (!is_null($stock)) {
            $stock->delete();
            /*
            $email = 'ambazamarcellin2001@gmail.com';
            $auteur = $this->user->name;
            $mailData = [
                    'title' => 'suppression du food store fournitures',
                    'email' => $email,
                    'auteur' => $auteur,
                    ];
         
            Mail::to($email)->send(new DeleteStockMail($mailData));
            */
        }

        session()->flash('success', 'food store has been deleted !!');
        return back();
    }
}

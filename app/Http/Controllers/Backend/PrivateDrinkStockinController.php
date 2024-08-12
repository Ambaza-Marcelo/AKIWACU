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
use Illuminate\Support\Facades\Http;
use App\Models\PrivateStoreItem;
use App\Models\PrivateDrinkStockin;
use App\Models\PrivateDrinkStockinDetail;
use App\Exports\PrivateStore\PrivateDrinkStockinExport;
use Carbon\Carbon;
use App\Mail\PrivateStockinMail;
use PDF;
use Validator;
use Excel;
use Mail;

class PrivateDrinkStockinController extends Controller
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
        if (is_null($this->user) || !$this->user->can('private_drink_stockin.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stockin !');
        }

        $stockins = PrivateDrinkStockin::all();
        return view('backend.pages.private_drink_stockin.index', compact('stockins'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('private_drink_stockin.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockin !');
        }

        $items  = PrivateStoreItem::orderBy('name','asc')->get();
        return view('backend.pages.private_drink_stockin.create', compact('items'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('private_drink_stockin.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockin !');
        }

        $rules = array(
                'private_store_item_id.*'  => 'required',
                'date'  => 'required',
                'unit.*'  => 'required',
                'quantity.*'  => 'required',
                'purchase_price.*'  => 'required',
                'handingover'  => 'required',
                'origin'  => 'required',
                'receptionist'  => 'required',
                'item_movement_type'  => 'required',
                'description'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            try {DB::beginTransaction();

            $private_store_item_id = $request->private_store_item_id;
            $date = $request->date;
            $invoice_currency = $request->invoice_currency;
            $handingover = $request->handingover;
            $receptionist = $request->receptionist;
            $origin = $request->origin;
            $description =$request->description; 
            $item_movement_type = $request->item_movement_type;
            $unit = $request->unit;
            $quantity = $request->quantity;
            $purchase_price = $request->purchase_price;
            

            $latest = PrivateDrinkStockin::latest()->first();
            if ($latest) {
               $stockin_no = 'BE' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $stockin_no = 'BE' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            $created_by = $this->user->name;

            $stockin_signature = config('app.tin_number_company').Carbon::parse(Carbon::now())->format('YmdHis')."/".$stockin_no;


            for( $count = 0; $count < count($private_store_item_id); $count++ ){
                $total_amount_purchase = $quantity[$count] * $purchase_price[$count];

                $data = array(
                    'private_store_item_id' => $private_store_item_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'unit' => $unit[$count],
                    'purchase_price' => $purchase_price[$count],
                    'total_amount_purchase' => $total_amount_purchase,
                    'receptionist' => $receptionist,
                    'handingover' => $handingover,
                    'origin' => $origin,
                    'item_movement_type' => $item_movement_type,
                    'stockin_no' => $stockin_no,
                    'stockin_signature' => $stockin_signature,
                    'created_by' => $created_by,
                    'description' => $description,
                    'status' => 1,
                    'created_at' => \Carbon\Carbon::now()

                );
                $insert_data[] = $data;

                
            }
            PrivateDrinkStockinDetail::insert($insert_data);


            //create stockin
            $stockin = new PrivateDrinkStockin();
            $stockin->date = $date;
            $stockin->stockin_no = $stockin_no;
            $stockin->stockin_signature = $stockin_signature;
            $stockin->receptionist = $receptionist;
            $stockin->handingover = $handingover;
            $stockin->origin = $origin;
            $stockin->item_movement_type = $item_movement_type;
            $stockin->created_by = $created_by;
            $stockin->status = 1;
            $stockin->description = $description;
            $stockin->save();

            DB::commit();
            session()->flash('success', 'stockin has been created !!');
            return redirect()->route('admin.private-drink-stockins.index');
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($stockin_no)
    {
        //
        $code = PrivateDrinkStockinDetail::where('stockin_no', $stockin_no)->value('stockin_no');
        $stockins = PrivateDrinkStockinDetail::where('stockin_no', $stockin_no)->get();
        return view('backend.pages.private_drink_stockin.show', compact('stockins','code'));
         
    }

    public function bonEntree($stockin_no)
    {
        if (is_null($this->user) || !$this->user->can('private_drink_stockin.create')) {
            abort(403, 'Sorry !! You are Unauthorized to print handover document!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $code = PrivateDrinkStockin::where('stockin_no', $stockin_no)->value('stockin_no');
        $datas = PrivateDrinkStockinDetail::where('stockin_no', $stockin_no)->get();
        $receptionniste = PrivateDrinkStockin::where('stockin_no', $stockin_no)->value('receptionist');
        $description = PrivateDrinkStockin::where('stockin_no', $stockin_no)->value('description');
        $handingover = PrivateDrinkStockin::where('stockin_no', $stockin_no)->value('handingover');
        $stockin_signature = PrivateDrinkStockin::where('stockin_no', $stockin_no)->value('stockin_signature');
        $date = PrivateDrinkStockin::where('stockin_no', $stockin_no)->value('date');
        $totalValue = DB::table('private_drink_stockin_details')
            ->where('stockin_no', '=', $stockin_no)
            ->sum('total_amount_purchase');
        $pdf = PDF::loadView('backend.pages.document.private_drink_stockin',compact('datas','code','totalValue','receptionniste','description','handingover','setting','date','stockin_signature','stockin_no'));

        Storage::put('public/pdf/private_drink_stockin/'.$stockin_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download('BON_ENTREE_'.$stockin_no.'.pdf');
        
    }

    public function validateStockin($stockin_no)
    {
       if (is_null($this->user) || !$this->user->can('private_drink_stockin.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any stockin !');
        }

        try {DB::beginTransaction();

            PrivateDrinkStockin::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);
            PrivateDrinkStockinDetail::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 2,'validated_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'stockin has been validated !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

    public function reject($stockin_no)
    {
       if (is_null($this->user) || !$this->user->can('private_drink_stockin.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any stockin !');
        }

        try {DB::beginTransaction();

        PrivateDrinkStockin::where('stockin_no', '=', $stockin_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);
        PrivateDrinkStockinDetail::where('stockin_no', '=', $stockin_no)
                ->update(['status' => -1,'rejected_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'Stockin has been rejected !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function reset($stockin_no)
    {
       if (is_null($this->user) || !$this->user->can('private_drink_stockin.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any stockin !');
        }

        try {DB::beginTransaction();

        PrivateDrinkStockin::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);
        PrivateDrinkStockinDetail::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 1,'reseted_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'Stockin has been reseted !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function confirm($stockin_no)
    {
       if (is_null($this->user) || !$this->user->can('private_drink_stockin.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any stockin !');
        }

        try {DB::beginTransaction();

        PrivateDrinkStockin::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);
            PrivateDrinkStockinDetail::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 3,'confirmed_by' => $this->user->name]);

        DB::commit();
            session()->flash('success', 'Stockin has been confirmed !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }

    public function approuve($stockin_no)
    {
       if (is_null($this->user) || !$this->user->can('private_drink_stockin.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any stockin !');
        }

        try {DB::beginTransaction();

        $datas = PrivateDrinkStockinDetail::where('stockin_no', $stockin_no)->get();

        $description = PrivateDrinkStockinDetail::where('stockin_no', $stockin_no)->value('description');
        $totalQuantity = DB::table('private_drink_stockin_details')
            ->where('stockin_no', '=', $stockin_no)
            ->sum('quantity');
        $totalValue = DB::table('private_drink_stockin_details')
            ->where('stockin_no', '=', $stockin_no)
            ->sum('total_amount_purchase');

        foreach($datas as $data){

                $valeurStockInitial = PrivateStoreItem::where('id', $data->private_store_item_id)->value('total_cump_value');
                $quantityStockInitial = PrivateStoreItem::where('id', $data->private_store_item_id)->value('quantity');
                $quantityTotalBigStore = $quantityStockInitial + $data->quantity;


                $valeurAcquisition = $data->quantity * $data->purchase_price;

                $valeurTotalUnite = $data->quantity + $quantityStockInitial;
                $cump = ($valeurStockInitial + $valeurAcquisition) / $valeurTotalUnite;

                    $itemData = array(
                        'id' => $data->private_store_item_id,
                        'quantity' => $quantityTotalBigStore,
                        'total_purchase_value' => $quantityTotalBigStore * $data->purchase_price,
                        'cump' => $cump,
                        'total_cump_value' => $quantityTotalBigStore * $cump
                    );


                    PrivateStoreItem::where('id',$data->private_store_item_id)
                        ->update($itemData);
        }


            PrivateDrinkStockin::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);
            PrivateDrinkStockinDetail::where('stockin_no', '=', $stockin_no)
                ->update(['status' => 4,'approuved_by' => $this->user->name]);

            $email1 = 'ambazamarcellin2001@gmail.com';
            $email2 = 'frangiye@gmail.com';
            //$email3 = 'khaembamartin@gmail.com';
            //$email4 = 'munyembari_mp@yahoo.fr';
            $auteur = $this->user->name;
            $mailData = [
                    'title' => 'ENTREE DES ARTICLES AU MAGASIN EGR',
                    'stockin_no' => $stockin_no,
                    'auteur' => $auteur,
                    'description' => $description,
                    'totalQuantity' => $totalQuantity,
                    'totalValue' => $totalValue,
                    ];
         
            Mail::to($email1)->send(new PrivateStockinMail($mailData));
            Mail::to($email2)->send(new PrivateStockinMail($mailData));
            //Mail::to($email3)->send(new PrivateStockinMail($mailData));
            //Mail::to($email4)->send(new PrivateStockinMail($mailData));

        DB::commit();
            session()->flash('success', 'Stockin has been done successfuly !');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }

    }

    public function exportToExcel(Request $request)
    {
        return Excel::download(new PrivateDrinkStockinExport, 'RAPPORT_ENTEES.xlsx');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $stockin_no
     * @return \Illuminate\Http\Response
     */
    public function destroy($stockin_no)
    {
        if (is_null($this->user) || !$this->user->can('private_drink_stockin.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any stockin !');
        }

        try {DB::beginTransaction();

        $stockin = PrivateDrinkStockin::where('stockin_no',$stockin_no)->first();
        if (!is_null($stockin)) {
            $stockin->delete();
            PrivateDrinkStockinDetail::where('stockin_no',$stockin_no)->delete();
        }

        DB::commit();
            session()->flash('success', 'Stockin has been deleted !!');
            return back();
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }
}

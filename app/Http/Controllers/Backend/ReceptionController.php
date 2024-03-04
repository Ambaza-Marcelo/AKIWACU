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
use App\Models\Article;
use App\Models\Reception;
use App\Models\Stock;
use App\Models\Report;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\ReceptionDetail;
use App\Models\OrderDetail;
use App\Models\Order;
use PDF;
use Validator;
use App\Exports\ReceptionExport;
use Excel;
use Mail;
use App\Mail\ReceptionMail;

class ReceptionController extends Controller
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
        if (is_null($this->user) || !$this->user->can('reception.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any reception !');
        }

        $receptions = Reception::all();
        $reception_partielle = ReceptionDetail::where('status', 1)->value('status');
        $reception_partielle_bon = ReceptionDetail::where('status', 1)->value('reception_no');
        return view('backend.pages.reception.index', compact('receptions','reception_partielle_bon','reception_partielle'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($commande_no)
    {
        if (is_null($this->user) || !$this->user->can('reception.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any reception !');
        }

        $commande_no  = Order::where('commande_no', $commande_no)->value('commande_no');
        $articles  = Article::all();
        $suppliers  = Supplier::all();
        $datas = OrderDetail::where('commande_no', $commande_no)->get();
        return view('backend.pages.reception.create', compact('articles','commande_no','suppliers','datas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('reception.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any reception !');
        }

        $rules = array(
                'article_id.*'  => 'required',
                'date'  => 'required',
                'unit.*'  => 'required',
                'quantity.*'  => 'required',
                'unit_price.*'  => 'required',
                'total_value.*'  => 'required',
                'invoice_no'  => 'required',
                'commande_no'  => 'required',
                'supplier'  => 'required',
                'receptionist'  => 'required',
                'description'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $article_id = $request->article_id;
            $date = $request->date;
            $invoice_no = $request->invoice_no;
            $commande_no = $request->commande_no;
            $description =$request->description; 
            $supplier = $request->supplier;
            $unit = $request->unit;
            $quantity = $request->quantity;
            $unit_price = $request->unit_price;
            //$remaining_quantity = $request->remaining_quantity;
            $receptionist =$request->receptionist; 
            $bon_no = "REC000".date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);
            $created_by = $this->user->name;


            for( $count = 0; $count < count($article_id); $count++ ){
                $total_value = $quantity[$count] * $unit_price[$count];
                $order_quantity = OrderDetail::where("commande_no",$commande_no)->where("article_id",$article_id[$count])->value('quantity');
                $remaining_quantity = $order_quantity - $quantity[$count];

                $status = 0;
                if ($remaining_quantity == 0) {
                	$status = 2;
                }else{
                	$status = 1;
                }
                $data = array(
                    'article_id' => $article_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'unit' => $unit[$count],
                    'unit_price' => $unit_price[$count],
                    'total_value' => $total_value,
                    'invoice_no' => $invoice_no,
                    'commande_no' => $commande_no,
                    'supplier' => $supplier,
                    'remaining_quantity' => $remaining_quantity,
                    'receptionist' => $receptionist,
                    'reception_no' => $bon_no,
                    'created_by' => $created_by,
                    'description' => $description,
                    'status' => $status,
                    'created_at' => \Carbon\Carbon::now()

                );
                $insert_data[] = $data;

                
            }
            ReceptionDetail::insert($insert_data);


            //create reception
            $reception = new Reception();
            $reception->date = $date;
            $reception->reception_no = $bon_no;
            $reception->invoice_no = $invoice_no;
            $reception->commande_no = $commande_no;
            $reception->receptionist = $receptionist;
            $reception->supplier = $supplier;
            $reception->created_by = $created_by;
            $reception->status = 1;
            $reception->description = $description;
            $reception->save();
            
        session()->flash('success', 'reception has been created !!');
        return redirect()->route('admin.receptions.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($bon_no)
    {
        //
        $code = ReceptionDetail::where('reception_no', $bon_no)->value('reception_no');
        $receptions = ReceptionDetail::where('reception_no', $bon_no)->get();
        return view('backend.pages.reception.show', compact('receptions','code'));
         
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($bon_no)
    {
        if (is_null($this->user) || !$this->user->can('reception.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any reception !');
        }

        $commande_no  = Order::where('purchase_bon_no', $bon_no)->value('commande_no');
        $articles  = Article::all();
        $suppliers  = Supplier::all();
        $reception = Reception::where('reception_no', $bon_no)->first();
        $datas = ReceptionDetail::where('reception_no', $bon_no)->get();
        return view('backend.pages.reception.edit', compact('articles','commande_no','suppliers','datas','reception'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $bon_no)
    {
        if (is_null($this->user) || !$this->user->can('reception.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any reception !');
        }

        $rules = array(
                'article_id.*'  => 'required',
                'date'  => 'required',
                'unit.*'  => 'required',
                'quantity.*'  => 'required',
                'unit_price.*'  => 'required',
                'total_value.*'  => 'required',
                'invoice_no'  => 'required',
                'commande_no'  => 'required',
                'supplier'  => 'required',
                //'remaining_quantity'  => 'required',
                'receptionist'  => 'required',
                'description'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $article_id = $request->article_id;
            $date = $request->date;
            $invoice_no = $request->invoice_no;
            $commande_no = $request->commande_no;
            $description =$request->description; 
            $supplier = $request->supplier;
            $unit = $request->unit;
            $quantity = $request->quantity;
            $unit_price = $request->unit_price;
            //$remaining_quantity = $request->remaining_quantity;
            $receptionist =$request->receptionist; 
            $created_by = $this->user->name;


            for( $count = 0; $count < count($article_id); $count++ ){
                $total_value = $quantity[$count] * $unit_price[$count];
                $order_quantity = OrderDetail::where("commande_no",$commande_no)->where("article_id",$article_id[$count])->value('quantity');
                $remaining_quantity = $order_quantity - $quantity[$count];

                $status = 0;
                if ($remaining_quantity == 0) {
                	$status = 2;
                }else{
                	$status = 1;
                }
                $data = array(
                    'article_id' => $article_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'unit' => $unit[$count],
                    'unit_price' => $unit_price[$count],
                    'total_value' => $total_value,
                    'invoice_no' => $invoice_no,
                    'commande_no' => $commande_no,
                    'supplier' => $supplier,
                    'remaining_quantity' => $remaining_quantity,
                    'receptionist' => $receptionist,
                    //'reception_no' => $bon_no,
                    'created_by' => $created_by,
                    'description' => $description,
                    'status' => $status,
                    'created_at' => \Carbon\Carbon::now()

                );
                //$insert_data[] = $data;
                ReceptionDetail::where('article_id',$article_id[$count])
                        ->update($data);
                
            }
            //ReceptionDetail::insert($insert_data);

            //update reception
            $reception = Reception::where('reception_no', $bon_no)->first();
            $reception->date = $date;
            $reception->invoice_no = $invoice_no;
            $reception->commande_no = $commande_no;
            $reception->receptionist = $receptionist;
            $reception->supplier = $supplier;
            $reception->created_by = $created_by;
            $reception->description = $description;
            $reception->save();

            session()->flash('success', 'reception has been updated !!');
        return redirect()->route('admin.receptions.index');
        
    }

    public function bon_reception($reception_no)
    {
        if (is_null($this->user) || !$this->user->can('bon_reception.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $code = Reception::where('reception_no', $reception_no)->value('reception_no');
        $datas = ReceptionDetail::where('reception_no', $reception_no)->get();
        $receptionniste = Reception::where('reception_no', $reception_no)->value('receptionist');
        $description = Reception::where('reception_no', $reception_no)->value('description');
        $supplier = Reception::where('reception_no', $reception_no)->value('supplier');
        $commande_no = Reception::where('reception_no', $reception_no)->value('commande_no');
        $invoice_no = Reception::where('reception_no', $reception_no)->value('invoice_no');
        $date = Reception::where('reception_no', $reception_no)->value('date');
        $totalValue = DB::table('reception_details')
            ->where('reception_no', '=', $reception_no)
            ->sum('total_value');
        $pdf = PDF::loadView('backend.pages.document.bon_reception',compact('datas','code','totalValue','receptionniste','description','supplier','commande_no','invoice_no','setting','date'));

        Storage::put('public/pdf/bon_reception/'.$reception_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download($reception_no.'.pdf');
        
    }

    public function validateReception($reception_no)
    {
       if (is_null($this->user) || !$this->user->can('reception.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any reception !');
        }
            Reception::where('reception_no', '=', $reception_no)
                ->update(['status' => 2]);

        session()->flash('success', 'reception has been validated !!');
        return back();
    }

    public function reject($reception_no)
    {
       if (is_null($this->user) || !$this->user->can('reception.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any reception !');
        }

        Reception::where('reception_no', '=', $reception_no)
                ->update(['status' => -1]);

        session()->flash('success', 'Reception has been rejected !!');
        return back();
    }

    public function reset($reception_no)
    {
       if (is_null($this->user) || !$this->user->can('reception.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any reception !');
        }

        Reception::where('reception_no', '=', $reception_no)
                ->update(['status' => 1]);

        session()->flash('success', 'Reception has been reseted !!');
        return back();
    }

    public function confirm($reception_no)
    {
       if (is_null($this->user) || !$this->user->can('reception.confirm')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any reception !');
        }

        Reception::where('reception_no', '=', $reception_no)
                ->update(['status' => 3]);

        session()->flash('success', 'Reception has been confirmed !!');
        return back();
    }

    public function approuve($reception_no)
    {
       if (is_null($this->user) || !$this->user->can('reception.approuve')) {
            abort(403, 'Sorry !! You are Unauthorized to confirm any reception !');
        }


        $datas = ReceptionDetail::where('reception_no', $reception_no)->get();

        foreach($datas as $data){
            $valeurStockInitial = Stock::where('article_id', $data->article_id)->value('total_value');
                $quantityStockInitial = Stock::where('article_id', $data->article_id)->value('quantity');


                $valeurAcquisition = $data->quantity * $data->unit_price;

                $valeurTotalUnite = $data->quantity + $quantityStockInitial;
                $cump = ($valeurStockInitial + $valeurAcquisition) / $valeurTotalUnite;

                $calcul_cump = array(
                        'unit_price' => $cump,
                    );
                Article::where('id',$data->article_id)
                        ->update($calcul_cump);


                $reportData = array(
                    'article_id' => $data->article_id,
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'quantity_stockin' => $data->quantity,
                    'value_stockin' => $cump * $data->quantity,
                    'stock_total' => $quantityStockInitial + $data->quantity,
                    'created_by' => $this->user->name,
                    'bon_entree' => $data->reception_no,
                    'created_at' => \Carbon\Carbon::now()
                );
                $report[] = $reportData;
                

                    $donnees = array(
                        'article_id' => $data->article_id,
                        'quantity' => $data->quantity,
                        'total_value' => $cump * $data->quantity,
                        'unit' => $data->unit,
                        'verified' => false,
                        'created_by' => $this->user->name,
                    );
                    $stock[] = $donnees;

                    $sto = array(
                        'article_id' => $data->article_id,
                        'quantity' => $valeurTotalUnite,
                        'total_value' => $cump * $data->quantity,
                        'unit' => $data->unit,
                        'verified' => false,
                        'created_by' => $this->user->name,
                    );
                    $artic = Stock::where("article_id",$data->article_id)->value('article_id');
                    if (!empty($artic)) {
                    Report::insert($report);
                    Stock::where('article_id',$data->article_id)
                        ->update($sto);
                    }else{
                    Report::insert($report);
                    Stock::insert($stock);
                    }

                Order::where('commande_no', '=', $data->commande_no)
                ->update(['status' => 5]);
        }


        Reception::where('reception_no', '=', $reception_no)
                ->update(['status' => 4]);
        ReceptionDetail::where('reception_no', '=', $reception_no)
                ->update(['status' => 4]);

        /*
        $data = ReceptionDetail::where('reception_no', $reception_no)->first();

        $mail = Supplier::where('name', $data->supplier)->value('mail');
        $name = $data->supplier;

        $mailData = [
                    'title' => 'LIVRAISON DE LA COMMANDE',
                    'commande_no' => $data->commande_no,
                    'invoice_no' => $data->invoice_no,
                    'name' => $this->user->name,
                    //'body' => 'This is for testing email using smtp.'
                    ];
         
        Mail::to($mail)->send(new ReceptionMail($mailData));
        */

        session()->flash('success', 'Reception has been done successfuly !!');
        return back();
    }

    public function get_reception_data()
    {
        return Excel::download(new ReceptionExport, 'receptions.xlsx');
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('stockin.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any stockin !');
        }

        
    }
}

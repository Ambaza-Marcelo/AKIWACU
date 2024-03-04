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
use App\Models\Stockout;
use App\Models\StockoutDetail;
use App\Models\Stock;
use App\Models\Stockin;
use App\Models\StockinDetail;
use App\Models\Report;
use App\Models\Setting;
use App\Models\MachineRepairingDetail;
use PDF;
use Validator;
use App\Exports\StockoutExport;
use Excel;

use Mail;
use App\Mail\DeleteStockoutMail; 

class StockoutController extends Controller
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
        if (is_null($this->user) || !$this->user->can('stockout.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stockout !');
        }

        $stockouts = Stockout::all();

        return view('backend.pages.stockout.index', compact('stockouts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $articles  = Article::all();
        return view('backend.pages.stockout.create', compact('articles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any stockout !');
        }

        $rules = array(
                'article_id.*'  => 'required',
                'date'  => 'required',
                'unit.*'  => 'required',
                'quantity.*'  => 'required',
                'asker'  => 'required',
                'observation'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $article_id = $request->article_id;
            $date = $request->date;
            $observation =$request->observation;
            $unit = $request->unit;
            $quantity = $request->quantity;

            $unit_price = $request->unit_price;
            $service_id = $request->service_id;
            $asker =$request->asker; 
            $destination = $request->destination;

            $latest = Stockout::latest()->first();
            if ($latest) {
               $bon_no = 'BS' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $bon_no = 'BS' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

            //$bon_no = "BS000".date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);
            $created_by = $this->user->name;



            for( $count = 0; $count < count($article_id); $count++ ){

                $unit_price = Article::where('id', $article_id[$count])->value('unit_price');

                $total_value = $quantity[$count] * $unit_price;

                $valeurStockInitial = Stock::where('article_id', $article_id[$count])->value('total_value');
                $quantityStockInitial = Stock::where('article_id', $article_id[$count])->value('quantity');

                $stockTotal = Report::where('article_id', $article_id[$count])->value('stock_total');

                $quantityRestant = $quantityStockInitial - $quantity[$count];

                    
                   $data = array(
                    'article_id' => $article_id[$count],
                    'date' => $date,
                    'quantity' => $quantity[$count],
                    'unit' => $unit[$count],
                    'total_value' => $total_value,
                    'destination' => $destination[$count],
                    'asker' => $asker,
                    'bon_no' => $bon_no,
                    'created_by' => $created_by,
                    'observation' => $observation,
                    'created_at' => \Carbon\Carbon::now()
                );
                $insert_data[] = $data; 
                
                



                $reportData = array(
                    'article_id' => $article_id[$count],
                    'quantity_stock_initial' => $quantityStockInitial,
                    'value_stock_initial' => $valeurStockInitial,
                    'quantity_stockout' => $quantity[$count],
                    'value_stockout' => $quantity[$count] * $unit_price,
                    'quantity_stock_final' => $stockTotal - $quantity[$count],
                    'destination' => $destination[$count],
                    'created_by' => $created_by,
                    'asker' => $asker,
                    'bon_sortie' => $bon_no,
                    'created_at' => \Carbon\Carbon::now()
                );
                $report[] = $reportData;
                
                    $donnees = array(
                        'article_id' => $article_id[$count],
                        'quantity' => $quantityRestant,
                        'total_value' => $quantityRestant * $unit_price,
                        'unit' => $unit[$count],
                        'created_by' => $this->user->name,
                        'verified' => false
                    );
                    
                    if ($quantity[$count] <= $quantityStockInitial) {

                        Report::insert($report);
                        
                        Stock::where('article_id',$article_id[$count])
                        ->update($donnees);

                        
                    }else{
                        session()->flash('error', 'invalid quantity!!');
                        return redirect()->back();
                    }
                    


                
            }

                        $stockout = new Stockout();
                        $stockout->bon_no = $bon_no;
                        $stockout->date = $date;
                        $stockout->asker = $asker;
                        $stockout->observation = $observation;
                        $stockout->created_by = $this->user->name;
                        $stockout->save();

                        StockoutDetail::insert($insert_data);

            session()->flash('success', 'Stockout has been created !!');
            return redirect()->route('admin.stockouts.index');

        
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
         $code = StockoutDetail::where('bon_no', $bon_no)->value('bon_no');
         $stockouts = StockoutDetail::where('bon_no', $bon_no)->get();
         return view('backend.pages.stockout.show', compact('stockouts','code'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $bon_no
     * @return \Illuminate\Http\Response
     */
    public function edit($bon_no)
    {
        if (is_null($this->user) || !$this->user->can('stockout.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any stockout !');
        }

        $stockout = Stockout::where('bon_no', $bon_no)->first();
        $stockoutDetails = StockoutDetail::where('bon_no' , $bon_no)->get();
        $articles  = Article::all();
        return view('backend.pages.stockout.edit', compact('stockout','stockoutDetails', 'articles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $bon_no
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $bon_no)
    {
        
    }

    public function bon_sortie($bon_no)
    {
        if (is_null($this->user) || !$this->user->can('bon_sortie.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        //$stockout = Stockout::find($bon_no);
        $datas = StockoutDetail::where('bon_no', $bon_no)->get();
        $totalValue = DB::table('stockout_details')
            ->where('bon_no', '=', $bon_no)
            ->sum('total_value');
        $demandeur = Stockout::where('bon_no', $bon_no)->value('asker');
        $date = Stockout::where('bon_no', $bon_no)->value('date');
        $description = Stockout::where('bon_no', $bon_no)->value('observation');
        $gestionnaire = Stockout::where('bon_no', $bon_no)->value('created_by');
        $pdf = PDF::loadView('backend.pages.document.bon_sortie',compact('datas','bon_no','totalValue','demandeur','gestionnaire','setting','description','date'));

        Storage::put('public/pdf/bon_sortie/'.$bon_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download($bon_no.'.pdf');
    }

    public function get_stockout_data()
    {
        return Excel::download(new StockoutExport, 'sorties.xlsx');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $bon_no
     * @return \Illuminate\Http\Response
     */
    public function destroy($bon_no)
    {
        if (is_null($this->user) || !$this->user->can('stockout.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any stockout !');
        }


        $stockout = Stockout::where('bon_no', $bon_no)->first();


        if (!is_null($stockout)) {
    
            StockoutDetail::where('bon_no',$bon_no)->delete();
            Report::where('bon_sortie',$bon_no)->delete();
            $stockout->delete();

            $email = 'ambazamarcellin2001@gmail.com';
            $auteur = $this->user->name;
            $mailData = [
                    'title' => 'Suppression de bon sortie',
                    'email' => $email,
                    'bon_no' => $bon_no,
                    'auteur' => $auteur,
                    ];
         
            Mail::to($email)->send(new DeleteStockoutMail($mailData));
        }

        session()->flash('success', 'Stockout has been deleted !!');
        return back();
    }
}

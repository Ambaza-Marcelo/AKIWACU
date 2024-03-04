<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Stockin;
use App\Models\Stockout;
use App\Models\Stock;
use App\Models\Article;
use App\Models\Category;
use App\Models\Report;
use App\Models\ReceptionDetail;
use App\Models\StockinDetail;
use App\Models\StockoutDetail;
use App\Models\Facture;
use App\Models\FactureDetail;
use App\Models\FactureRestaurantDetail;
use Carbon\Carbon;
use App\Exports\ReportExport;
use App\Exports\JournalVenteExport;
use App\Exports\JournalVenteCuisineExport;
use App\Exports\JournalEntreeExport;
use App\Exports\JournalSortieExport;
use App\Exports\JournalReceptionExport;
use Excel;
use PDF;

class ReportController extends Controller
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

    public function monthReport(){

        if (is_null($this->user) || !$this->user->can('report.view')) {
            abort(403, 'Muradutunge !! Ntaburenganzira mufise bwo kuraba raporo,mufise ico mubaza murashobora guhamagara kuri 122 !');
        }

        $reportMonths = Report::select(
                        DB::raw('article_id,sum(quantity_stock_initial) as quantity_stock_init,sum(quantity_stockin) as quantity_in,sum(stock_total) as stock_tot,sum(quantity_stockout) as quantity_out'))->groupBy('article_id')->get();
        return view('backend.pages.report.month_report.index',compact('reportMonths'));
    }

    public function journalEntree(){

        if (is_null($this->user) || !$this->user->can('report.view')) {
            abort(403, 'Muradutunge !! Ntaburenganzira mufise bwo kuraba raporo,mufise ico mubaza murashobora guhamagara kuri 122 !');
        }

        $journalEntrees = StockinDetail::all();
        return view('backend.pages.report.journal_entree.index',compact('journalEntrees'));
    }

    public function journalVente(){

        if (is_null($this->user) || !$this->user->can('report.view')) {
            abort(403, 'Muradutunge !! Ntaburenganzira mufise bwo kuraba raporo,mufise ico mubaza murashobora guhamagara kuri 122 !');
        }

        $journalVentes = FactureDetail::all();
        return view('backend.pages.report.journal_vente.index',compact('journalVentes'));
    }

    public function journalVenteCuisine(){

        if (is_null($this->user) || !$this->user->can('report.view')) {
            abort(403, 'Muradutunge !! Ntaburenganzira mufise bwo kuraba raporo,mufise ico mubaza murashobora guhamagara kuri 122 !');
        }

        $journalVentes = FactureRestaurantDetail::all();
        return view('backend.pages.report.journal_vente_cuisine.index',compact('journalVentes'));
    }

    public function journalSortie(){

        if (is_null($this->user) || !$this->user->can('report.view')) {
            abort(403, 'Muradutunge !! Ntaburenganzira mufise bwo kuraba raporo,mufise ico mubaza murashobora guhamagara kuri 122 !');
        }

        $journalSorties = StockoutDetail::all();
        return view('backend.pages.report.journal_sortie.index',compact('journalSorties'));
    }

    public function journalReception(){

        if (is_null($this->user) || !$this->user->can('report.view')) {
            abort(403, 'Muradutunge !! Ntaburenganzira mufise bwo kuraba raporo,mufise ico mubaza murashobora guhamagara kuri 122 !');
        }

        $journalReceptions = ReceptionDetail::all();
        return view('backend.pages.report.journal_reception.index',compact('journalReceptions'));
    }


    public function stockMovement(){
        if (is_null($this->user) || !$this->user->can('report.view')) {
            abort(403, 'Muradutunge !! Ntaburenganzira mufise bwo kuraba raporo,mufise ico mubaza murashobora guhamagara kuri 122 !');
        }
            $stockMovements = Report::select(
                        DB::raw('created_at,asker,bon_entree,quantity_stock_initial,quantity_stockin,stock_total,quantity_stockout,quantity_sold,bon_sortie,created_by,article_id'))->groupBy('bon_entree','bon_sortie','created_by','created_at','article_id','asker','quantity_stockin','quantity_stockout','quantity_sold','quantity_stock_initial','stock_total')->get();
        return view('backend.pages.report.stock_movement.index',compact('stockMovements'));
       
    }

    public function stockMovementToPdf(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('stock.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stock !');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        $datas = Report::select(
                        DB::raw('article_id,created_at,facture_no,commande_boisson_no,commande_cuisine_no,quantity_stock_initial,quantity_stockin,quantity_stockout,quantity_sold,value_sold'))->whereBetween('created_at',[$start_date,$end_date])->groupBy('created_at','article_id','quantity_stock_initial','commande_boisson_no','commande_cuisine_no','quantity_stockin','quantity_stockout','quantity_sold','value_sold','facture_no')->orderBy('created_at')->get();

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.stock_movement',compact('datas','dateTime','setting','end_date'))->setPaper('a4', 'landscape');

        Storage::put('public/journal_general/'.$d1.'_'.$d2.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download($dateTime.'.pdf');

        
    }

    public function monthReportToPdf(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('stock.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stock !');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        $datas = Report::select(
                        DB::raw('article_id,sum(quantity_stockin) as quantity_in,sum(stock_total) as stock_tot,sum(quantity_stockout) as quantity_out'))->whereBetween('created_at',[$start_date,$end_date])->groupBy('article_id')->get();

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.month_report',compact('datas','dateTime','setting','end_date','start_date'));//->setPaper('a4', 'landscape');

        Storage::put('public/rapport-simple/'.$d1.'_'.$d2.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download($dateTime.'.pdf');
    }


    public function invoiceSentObrToPdf(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('stock.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stock !');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        $datas = FactureDetail::select(
                        DB::raw('article_id,invoice_number,invoice_date,item_quantity,item_price,customer_name,customer_TIN,item_total_amount'))->where('etat',2)->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('article_id','invoice_date','invoice_number','item_quantity','item_price','customer_name','customer_TIN','item_total_amount')->orderBy('invoice_number','asc')->get();
        $total_amount = DB::table('facture_details')->where('etat',2)->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.rapport_facture',compact('datas','dateTime','setting','end_date','start_date','total_amount'))->setPaper('a4', 'landscape');

        //Storage::put('public/journal_general/'.$d1.'_'.$d2.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download($dateTime.'.pdf');

        
    }


    public function get_report_data(Request $request)
    {
        return Excel::download(new ReportExport, 'rapports.xlsx');
    }

    public function stockMovementExport(Request $request)
    {
        return Excel::download(new ReportExport, 'rapports.xlsx');
    }

    public function journalEntreeExport(Request $request)
    {
        return Excel::download(new JournalEntreeExport, 'journal-entree.xlsx');
    }

    public function journalSortieExport(Request $request)
    {
        return Excel::download(new JournalSortieExport, 'journal-sortie.xlsx');
    }

    public function journalReceptionExport(Request $request)
    {
        return Excel::download(new JournalReceptionExport, 'journal-reception.xlsx');
    }

    public function journalVenteExport(Request $request)
    {
        return Excel::download(new JournalVenteExport, 'journal-vente-boissons.xlsx');
    }

    public function journalVenteCuisineExport(Request $request)
    {
        return Excel::download(new JournalVenteCuisineExport, 'journal-vente-cuisine.xlsx');
    }

}

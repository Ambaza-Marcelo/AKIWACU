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
use App\Models\Article;
use App\Models\Stock;
use App\Models\Setting;
use App\Exports\StockExport;
use Excel;
use Carbon\Carbon;
use PDF;

use Mail;
use App\Mail\DeleteStockMail;

class StockController extends Controller
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
        if (is_null($this->user) || !$this->user->can('stock.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stock !');
        }

        $stocks = Stock::all();
        return view('backend.pages.stock.index', compact('stocks'));
    }

    public function need()
    {
        if (is_null($this->user) || !$this->user->can('stock.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any statement of need !');
        }

        $needs = Stock::whereColumn('quantity', '<=','threshold_quantity')->get();

        /*$needs = DB::table('stocks')
            ->select('article_id','quantity','total_value')
            ->where('quantity', '<=',  5)
            ->get();
            */
        return view('backend.pages.stock.need', compact('needs'));
    }

    public function get_stock_data()
    {
        return Excel::download(new StockExport, 'stocks.xlsx');
    }

    public function toPdf()
    {
        if (is_null($this->user) || !$this->user->can('stock.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any stock !');
        }

        $datas = Stock::all();
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
        if (is_null($this->user) || !$this->user->can('stok.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any stock !');
        }

        $stock = Stock::find($id);
        if (!is_null($stock)) {
            $stock->delete();

            $email = 'ambazamarcellin2001@gmail.com';
            $auteur = $this->user->name;
            $mailData = [
                    'title' => 'suppression du stock fournitures',
                    'email' => $email,
                    'auteur' => $auteur,
                    ];
         
            Mail::to($email)->send(new DeleteStockMail($mailData));
        }

        session()->flash('success', 'Stock has been deleted !!');
        return back();
    }
}

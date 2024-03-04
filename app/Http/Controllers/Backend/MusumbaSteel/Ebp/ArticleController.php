<?php

namespace App\Http\Controllers\Backend\MusumbaSteel\Ebp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\MsEbpArticle;
use App\Models\MsEbpCategory;
use Excel;

class ArticleController extends Controller
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any item !more information you have to contact Marcellin');
        }

        $articles = MsEbpArticle::all();
        return view('backend.pages.musumba_steel.ebp.article.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any item !more information you have to contact Marcellin');
        }
        $categories = MsEbpCategory::all();

        return view('backend.pages.musumba_steel.ebp.article.create',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any item ! more information you have to contact Marcellin');
        }
        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'unit' => 'required|max:20',
            'selling_price' => 'required',
            'quantity' => 'required',
        ]);

        // Create New MsEbpArticle
        $article = new MsEbpArticle();
        $article->name = $request->name;
        $artCode = strtoupper(substr($request->name, 0, 3));
        $article->code = $artCode.date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);
        $article->unit = $request->unit;
        $article->purchase_price = $request->purchase_price;
        $article->selling_price = $request->selling_price;
        $article->quantity = $request->quantity;
        $article->specification = $request->specification;
        $article->vat = $request->vat;
        $article->expiration_date = $request->expiration_date;
        $article->threshold_quantity = $request->threshold_quantity;
        $article->created_by = $this->user->name;
        $article->save();

        session()->flash('success', 'Item has been created !!');
        return redirect()->route('admin.musumba-steel-items.index');
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any item ! more information you have to contact Marcellin');
        }
        $categories = MsEbpCategory::all();
        $article = MsEbpArticle::find($id);
        return view('backend.pages.musumba_steel.ebp.article.edit', compact(
            'article','categories'));
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any item ! more information you have to contact Marcellin');
        }

        // Create New MsEbpArticle
        $article = MsEbpArticle::find($id);

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'unit' => 'required|max:20',
            'purchase_price' => 'required',
            'selling_price' => 'required',
            'quantity' => 'required',
        ]);


        $article->name = $request->name;
        $article->unit = $request->unit;
        $article->purchase_price = $request->purchase_price;
        $article->selling_price = $request->selling_price;
        $article->quantity = $request->quantity;
        $article->specification = $request->specification;
        $article->vat = $request->vat;
        $article->expiration_date = $request->expiration_date;
        $article->threshold_quantity = $request->threshold_quantity;
        $article->created_by = $this->user->name;
        $article->save();

        session()->flash('success', 'Item has been updated !!');
        return redirect()->route('admin.musumba-steel-items.index');
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any item ! more information you have to contact Marcellin');
        }

        $article = MsEbpArticle::find($id);
        if (!is_null($article)) {
            $article->delete();
        }

        session()->flash('success', 'Item has been deleted !!');
        return back();
    }
}

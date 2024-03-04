<?php

namespace App\Http\Controllers\Backend\MusumbaSteel\Ebp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\MsEbpCategory;

class CategoryController extends Controller
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

    public function index()
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any category !');
        }

        $categories = MsEbpCategory::all();
        return view('backend.pages.musumba_steel.ebp.category.index', compact('categories'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any category !');
        }
        return view('backend.pages.musumba_steel.ebp.category.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any category !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:100'
        ]);

        // Create New MsEbpCategory
        $category = new MsEbpCategory();
        $category->name = $request->name;
        //$category->created_by = $this->user->name;
        $category->save();
        return redirect()->back();
    }

    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any category !');
        }

        $category = MsEbpCategory::find($id);
        return view('backend.pages.musumba_steel.ebp.category.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('category.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any category !');
        }

        $category = MsEbpCategory::find($id);

        // Validation Data
        $request->validate([
            'name' => 'required|max:100'
        ]);


        $category->name = $request->name;
        //$category->created_by = $this->user->name;
        $category->save();

        session()->flash('success', 'Category has been updated !!');
        return back();
    }

    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any category !');
        }

        $category = MsEbpCategory::find($id);
        if (!is_null($category)) {
            $category->delete();
        }

        session()->flash('success', 'Category has been deleted !!');
        return back();
    }
}

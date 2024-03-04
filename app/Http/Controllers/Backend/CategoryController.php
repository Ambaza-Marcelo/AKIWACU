<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Category;

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
        if (is_null($this->user) || !$this->user->can('category.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any category !');
        }

        $categories = Category::all();
        return view('backend.pages.category.index', compact('categories'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('category.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any category !');
        }
        return view('backend.pages.category.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('category.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any category !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:100'
        ]);

        // Create New Category
        $category = new Category();
        $category->name = $request->name;
        //$category->created_by = $this->user->name;
        $category->save();
        return redirect()->back();
    }

    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('category.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any category !');
        }

        $category = Category::find($id);
        return view('backend.pages.category.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('category.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any category !');
        }

        $category = Category::find($id);

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
        if (is_null($this->user) || !$this->user->can('category.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any category !');
        }

        $category = Category::find($id);
        if (!is_null($category)) {
            $category->delete();
        }

        session()->flash('success', 'Category has been deleted !!');
        return back();
    }
}

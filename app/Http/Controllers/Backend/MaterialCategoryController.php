<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\MaterialCategory;

class MaterialCategoryController extends Controller
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
        if (is_null($this->user) || !$this->user->can('material_category.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any category !');
        }

        $categories = MaterialCategory::all();
        return view('backend.pages.material_category.index', compact('categories'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('material_category.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any category !');
        }
        return view('backend.pages.material_category.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('material_category.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any category !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:100'
        ]);

        // Create New MaterialCategory
        $material_category = new MaterialCategory();
        $material_category->name = $request->name;
        //$material_category->created_by = $this->user->name;
        $material_category->save();
        session()->flash('success', 'Category has been created successfuly !!');
        return redirect()->route('admin.material-category.index');
    }

    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('material_category.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any category !');
        }

        $material_category = MaterialCategory::find($id);
        return view('backend.pages.material_category.edit', compact('material_category'));
    }

    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('material_category.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any category !');
        }

        $material_category = MaterialCategory::find($id);

        // Validation Data
        $request->validate([
            'name' => 'required|max:100'
        ]);


        $material_category->name = $request->name;
        //$material_category->created_by = $this->user->name;
        $material_category->save();

        session()->flash('success', 'Category has been updated !!');
        return redirect()->route('admin.material-category.index');
    }

    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('material_category.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any category !');
        }

        $material_category = MaterialCategory::find($id);
        if (!is_null($material_category)) {
            $material_category->delete();
        }

        session()->flash('success', 'Category has been deleted !!');
        return back();
    }
}

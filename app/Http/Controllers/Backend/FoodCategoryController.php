<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\FoodCategory;

class FoodCategoryController extends Controller
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
        if (is_null($this->user) || !$this->user->can('food_category.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any category !');
        }

        $categories = FoodCategory::all();
        return view('backend.pages.food_category.index', compact('categories'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('food_category.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any category !');
        }
        return view('backend.pages.food_category.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('food_category.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any category !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:100'
        ]);

        // Create New FoodCategory
        $food_category = new FoodCategory();
        $food_category->name = $request->name;
        //$food_category->created_by = $this->user->name;
        $food_category->save();
        session()->flash('success', 'Category has been updated !!');
        return redirect()->route('admin.food-category.index');
    }

    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('food_category.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any category !');
        }

        $food_category = FoodCategory::find($id);
        return view('backend.pages.food_category.edit', compact('food_category'));
    }

    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('food_category.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any category !');
        }

        $food_category = FoodCategory::find($id);

        // Validation Data
        $request->validate([
            'name' => 'required|max:100'
        ]);


        $food_category->name = $request->name;
        //$food_category->created_by = $this->user->name;
        $food_category->save();
        session()->flash('success', 'Category has been updated !!');
        return redirect()->route('admin.food-category.index');
    }

    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('food_category.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any category !');
        }

        $food_category = FoodCategory::find($id);
        if (!is_null($food_category)) {
            $food_category->delete();
        }

        session()->flash('success', 'Category has been deleted !!');
        return back();
    }
}

<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\DrinkCategory;

class DrinkCategoryController extends Controller
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
        if (is_null($this->user) || !$this->user->can('drink_category.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any category !');
        }

        $categories = DrinkCategory::all();
        return view('backend.pages.drink_category.index', compact('categories'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('drink_category.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any category !');
        }
        return view('backend.pages.drink_category.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('drink_category.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any category !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:100'
        ]);

        // Create New DrinkCategory
        $drink_category = new DrinkCategory();
        $drink_category->name = $request->name;
        //$drink_category->created_by = $this->user->name;
        $drink_category->save();
        session()->flash('success', 'Category has been created successfuly !!');
        return redirect()->route('admin.drink-category.index');

    }

    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('drink_category.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any category !');
        }

        $drink_category = DrinkCategory::find($id);
        return view('backend.pages.drink_category.edit', compact('drink_category'));
    }

    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('drink_category.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any category !');
        }

        $drink_category = DrinkCategory::find($id);

        // Validation Data
        $request->validate([
            'name' => 'required|max:100'
        ]);


        $drink_category->name = $request->name;
        //$drink_category->created_by = $this->user->name;
        $drink_category->save();

        session()->flash('success', 'Category has been updated !!');
        return redirect()->route('admin.drink-category.index');
    }

    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('drink_category.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any category !');
        }

        $drink_category = DrinkCategory::find($id);
        if (!is_null($drink_category)) {
            $drink_category->delete();
        }

        session()->flash('success', 'Category has been deleted !!');
        return back();
    }
}

<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Ingredient;


class IngredientController extends Controller
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
        if (is_null($this->user) || !$this->user->can('barrist_item.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any ingredient !');
        }

        $ingredients = Ingredient::all();
        return view('backend.pages.ingredient.index', compact('ingredients'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('barrist_item.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any ingredient !');
        }
        return view('backend.pages.ingredient.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('barrist_item.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any ingredient !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:100'
        ]);

        // Create New Ingredient
        $ingredient = new Ingredient();
        $ingredient->name = $request->name;
        //$ingredient->created_by = $this->user->name;
        $ingredient->save();
        return redirect()->route('admin.ingredients.index');
    }

    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('barrist_item.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any ingredient !');
        }

        $ingredient = Ingredient::find($id);
        return view('backend.pages.ingredient.edit', compact('ingredient'));
    }

    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('barrist_item.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any ingredient !');
        }

        $ingredient = Ingredient::find($id);

        // Validation Data
        $request->validate([
            'name' => 'required|max:100'
        ]);


        $ingredient->name = $request->name;
        //$ingredient->created_by = $this->user->name;
        $ingredient->save();

        session()->flash('success', 'Ingredient has been updated !!');
        return back();
    }

    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('barrist_item.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any ingredient !');
        }

        $ingredient = Ingredient::find($id);
        if (!is_null($ingredient)) {
            $ingredient->delete();
        }

        session()->flash('success', 'Ingredient has been deleted !!');
        return back();
    }
}

<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\FoodMeasurement;

class FoodMeasurementController extends Controller
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
            abort(403, 'Sorry !! You are Unauthorized to view any unit of measurement !');
        }

        $food_measurements = FoodMeasurement::all();
        return view('backend.pages.food_measurement.index', compact('food_measurements'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('food_category.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any unit of measurement !');
        }
        return view('backend.pages.food_measurement.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('food_category.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any unit of measurement !');
        }

        // Validation Data
        $request->validate([
            'purchase_unit' => 'required|max:255',
            'stockout_unit' => 'required|max:255',
            'production_unit' => 'required|max:255',
            'equivalent' => 'required',
            'sub_equivalent' => 'required'
        ]);

        // Create New FoodMeasurement
        $food_measurement = new FoodMeasurement();
        $food_measurement->purchase_unit = $request->purchase_unit;
        $food_measurement->stockout_unit = $request->stockout_unit;
        $food_measurement->production_unit = $request->production_unit;
        $food_measurement->equivalent = $request->equivalent;
        $food_measurement->sub_equivalent = $request->sub_equivalent;
        $food_measurement->save();
        session()->flash('success', 'unit of measurement has been created successfuly !!');
        return redirect()->route('admin.food-measurement.index');

    }

    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('food_category.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any unit of measurement !');
        }

        $food_measurement = FoodMeasurement::find($id);
        return view('backend.pages.food_measurement.edit', compact('food_measurement'));
    }

    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('food_category.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any unit of measurement !');
        }

        $food_measurement = FoodMeasurement::find($id);

        // Validation Data
        $request->validate([
            'purchase_unit' => 'required|max:255',
            'stockout_unit' => 'required|max:255',
            'production_unit' => 'required|max:255',
            'equivalent' => 'required',
            'sub_equivalent' => 'required'
        ]);


        $food_measurement->purchase_unit = $request->purchase_unit;
        $food_measurement->stockout_unit = $request->stockout_unit;
        $food_measurement->production_unit = $request->production_unit;
        $food_measurement->equivalent = $request->equivalent;
        $food_measurement->sub_equivalent = $request->sub_equivalent;
        $food_measurement->save();

        session()->flash('success', 'unit of measurement has been updated !!');
        return redirect()->route('admin.food-measurement.index');
    }

    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('food_category.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any unit of measurement !');
        }

        $food_measurement = FoodMeasurement::find($id);
        if (!is_null($food_measurement)) {
            $food_measurement->delete();
        }

        session()->flash('success', 'unit of measurement has been deleted !!');
        return back();
    }
}

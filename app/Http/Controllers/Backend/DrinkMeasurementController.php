<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\DrinkMeasurement;

class DrinkMeasurementController extends Controller
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
            abort(403, 'Sorry !! You are Unauthorized to view any unit of measurement !');
        }

        $drink_measurements = DrinkMeasurement::all();
        return view('backend.pages.drink_measurement.index', compact('drink_measurements'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('drink_category.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any unit of measurement !');
        }
        return view('backend.pages.drink_measurement.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('drink_category.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any unit of measurement !');
        }

        // Validation Data
        $request->validate([
            'purchase_unit' => 'required|max:255',
            'stockout_unit' => 'required|max:255',
        ]);

        // Create New DrinkMeasurement
        $drink_measurement = new DrinkMeasurement();
        $drink_measurement->purchase_unit = $request->purchase_unit;
        $drink_measurement->stockout_unit = $request->stockout_unit;
        $drink_measurement->save();
        session()->flash('success', 'unit of measurement has been created successfuly !!');
        return redirect()->route('admin.drink-measurement.index');

    }

    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('drink_category.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any unit of measurement !');
        }

        $drink_measurement = DrinkMeasurement::find($id);
        return view('backend.pages.drink_measurement.edit', compact('drink_measurement'));
    }

    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('drink_category.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any unit of measurement !');
        }

        $drink_measurement = DrinkMeasurement::find($id);

        // Validation Data
        $request->validate([
            'purchase_unit' => 'required|max:255',
            'stockout_unit' => 'required|max:255',
        ]);


        $drink_measurement->purchase_unit = $request->purchase_unit;
        $drink_measurement->stockout_unit = $request->stockout_unit;
        $drink_measurement->save();

        session()->flash('success', 'unit of measurement has been updated !!');
        return redirect()->route('admin.drink-measurement.index');
    }

    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('drink_category.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any unit of measurement !');
        }

        $drink_measurement = DrinkMeasurement::find($id);
        if (!is_null($drink_measurement)) {
            $drink_measurement->delete();
        }

        session()->flash('success', 'unit of measurement has been deleted !!');
        return back();
    }
}

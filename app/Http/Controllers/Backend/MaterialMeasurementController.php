<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\MaterialMeasurement;


class MaterialMeasurementController extends Controller
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
            abort(403, 'Sorry !! You are Unauthorized to view any unit of measurement !');
        }

        $material_measurements = materialMeasurement::all();
        return view('backend.pages.material_measurement.index', compact('material_measurements'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('material_category.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any unit of measurement !');
        }
        return view('backend.pages.material_measurement.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('material_category.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any unit of measurement !');
        }

        // Validation Data
        $request->validate([
            'purchase_unit' => 'required|max:255',
            'stockout_unit' => 'required|max:255',
        ]);

        // Create New materialMeasurement
        $material_measurement = new materialMeasurement();
        $material_measurement->purchase_unit = $request->purchase_unit;
        $material_measurement->stockout_unit = $request->stockout_unit;
        $material_measurement->save();
        session()->flash('success', 'unit of measurement has been created successfuly !!');
        return redirect()->route('admin.material-measurement.index');

    }

    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('material_category.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any unit of measurement !');
        }

        $material_measurement = materialMeasurement::find($id);
        return view('backend.pages.material_measurement.edit', compact('material_measurement'));
    }

    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('material_category.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any unit of measurement !');
        }

        $material_measurement = materialMeasurement::find($id);

        // Validation Data
        $request->validate([
            'purchase_unit' => 'required|max:255',
            'stockout_unit' => 'required|max:255',
        ]);


        $material_measurement->purchase_unit = $request->purchase_unit;
        $material_measurement->stockout_unit = $request->stockout_unit;
        $material_measurement->save();

        session()->flash('success', 'unit of measurement has been updated !!');
        return redirect()->route('admin.material-measurement.index');
    }

    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('material_category.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any unit of measurement !');
        }

        $material_measurement = materialMeasurement::find($id);
        if (!is_null($material_measurement)) {
            $material_measurement->delete();
        }

        session()->flash('success', 'unit of measurement has been deleted !!');
        return back();
    }
}

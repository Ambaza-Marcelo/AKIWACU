<?php

namespace App\Http\Controllers\Backend\Sotb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\SotbFuel;

class FuelController extends Controller
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
        if (is_null($this->user) || !$this->user->can('sotb_fuel.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any fuel !');
        }

        $fuels = SotbFuel::all();
        return view('backend.pages.sotb.fuel.fuel.index', compact('fuels'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('sotb_fuel.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any fuel !');
        }
        return view('backend.pages.sotb.fuel.fuel.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('sotb_fuel.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any fuel !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:100',
            'purchase_price' => 'required|max:100',
        ]);

        // Create New SotbFuel
        $fuel = new SotbFuel();
        $fuel->name = $request->name;
        $fuel->purchase_price = $request->purchase_price;
        $fuel->auteur = $this->user->name;
        $fuel->save();
        session()->flash('success', 'Fuel has been created successfuly !!');
        return redirect()->route('admin.sotb-fuels.index');
    }

    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('sotb_fuel.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any fuel !');
        }

        $fuel = SotbFuel::find($id);
        return view('backend.pages.sotb.fuel.fuel.edit', compact('fuel'));
    }

    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('sotb_fuel.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any fuel !');
        }

        $fuel = SotbFuel::find($id);

        $request->validate([
            'name' => 'required|max:100',
            'purchase_price' => 'required|max:100',
        ]);

        $fuel->name = $request->name;
        $fuel->purchase_price = $request->purchase_price;
        $fuel->auteur = $this->user->name;
        $fuel->save();

        session()->flash('success', 'Fuel has been updated !!');
        return redirect()->route('admin.sotb-fuels.index');
    }

    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('sotb_fuel.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any fuel !');
        }

        $fuel = SotbFuel::find($id);
        if (!is_null($fuel)) {
            $fuel->delete();
        }

        session()->flash('success', 'Fuel has been deleted !!');
        return back();
    }
}

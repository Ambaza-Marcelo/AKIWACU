<?php

namespace App\Http\Controllers\Backend\MusumbaSteel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\MsDriver;

class DriverController extends Controller
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_driver.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any driver !');
        }

        $fuel_drivers = MsDriver::orderBy('firstname')->get();
        return view('backend.pages.musumba_steel.fuel.driver.index', compact('fuel_drivers'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_driver.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any driver !');
        }
        return view('backend.pages.musumba_steel.fuel.driver.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_driver.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any driver !');
        }

        // Validation Data
        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
        ]);

        // Create New Driver
        $fuel_driver = new MsDriver();
        $fuel_driver->firstname = $request->firstname;
        $fuel_driver->lastname = $request->lastname;
        $fuel_driver->telephone = $request->telephone;
        $fuel_driver->gender = $request->gender;
        $fuel_driver->email = $request->email;
        //$fuel_car->auteur = $this->user->name;
        $fuel_driver->save();
        session()->flash('success', 'Driver has been created !!');
        return redirect()->route('admin.ms-drivers.index');
    }

    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_driver.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any driver !');
        }

        $fuel_driver = MsDriver::find($id);
        return view('backend.pages.musumba_steel.fuel.driver.edit', compact('fuel_driver'));
    }

    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_driver.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any driver !');
        }

        $fuel_driver = MsDriver::find($id);

        // Validation Data
        $request->validate([
            'firstname' => 'required',
            'lastname' => 'required',
            'telephone' => 'required',
            'email' => 'required',
        ]);

        $fuel_driver->firstname = $request->firstname;
        $fuel_driver->lastname = $request->lastname;
        $fuel_driver->telephone = $request->telephone;
        $fuel_driver->email = $request->email;
        //$fuel_driver->auteur = $this->user->name;
        $fuel_driver->save();

        session()->flash('success', 'Driver has been updated !!');
        return redirect()->route('admin.ms-drivers.index');
    }

    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_driver.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any driver !');
        }

        $fuel_driver = MsDriver::find($id);
        if (!is_null($fuel_driver)) {
            $fuel_driver->delete();
        }

        session()->flash('success', 'Driver has been deleted !!');
        return back();
    }
}

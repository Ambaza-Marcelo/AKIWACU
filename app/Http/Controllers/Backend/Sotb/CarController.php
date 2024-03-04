<?php

namespace App\Http\Controllers\Backend\Sotb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\SotbCar;
use App\Models\SotbFuel;

class CarController extends Controller
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
        if (is_null($this->user) || !$this->user->can('sotb_car.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any car !');
        }

        $fuel_cars = SotbCar::orderBy('marque')->get();
        return view('backend.pages.sotb.fuel.car.index', compact('fuel_cars'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('sotb_car.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any car !');
        }

        $fuels = SotbFuel::all();
        return view('backend.pages.sotb.fuel.car.create',compact('fuels'));
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('sotb_car.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any car !');
        }

        // Validation Data
        $request->validate([
            'marque' => 'required|max:20',
            //'couleur' => 'required|max:20',
            'immatriculation' => 'required|min:3|max:6',
            //'chassis_no' => 'required',
            //'etat' => 'required',
            'fuel_id' => 'required',
        ]);

        // Create New SotbCar
        $fuel_car = new SotbCar();
        $fuel_car->marque = $request->marque;
        $fuel_car->couleur = $request->couleur;
        $fuel_car->immatriculation = $request->immatriculation;
        $fuel_car->chassis_no = $request->chassis_no;
        $fuel_car->etat = $request->etat;
        $fuel_car->fuel_id = $request->fuel_id;
        //$fuel_car->auteur = $this->user->name;
        $fuel_car->save();
        session()->flash('success', 'Car has been created !!');
        return redirect()->route('admin.sotb-cars.index');
    }

    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('sotb_car.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any car !');
        }

        $fuel_car = SotbCar::find($id);
        $fuels = SotbFuel::all();
        return view('backend.pages.sotb.fuel.car.edit', compact('fuel_car','fuels'));
    }

    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('sotb_car.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any car !');
        }

        $fuel_car = SotbCar::find($id);

        // Validation Data
        $request->validate([
            'marque' => 'required|max:20',
            //'couleur' => 'required|max:20',
            'immatriculation' => 'required|min:3|max:6',
            //'chassis_no' => 'required',
            //'etat' => 'required',
            'fuel_id' => 'required',
        ]);

        $fuel_car->marque = $request->marque;
        $fuel_car->couleur = $request->couleur;
        $fuel_car->immatriculation = $request->immatriculation;
        $fuel_car->chassis_no = $request->chassis_no;
        $fuel_car->etat = $request->etat;
        $fuel_car->fuel_id = $request->fuel_id;
        //$fuel_car->auteur = $this->user->name;
        $fuel_car->save();

        session()->flash('success', 'Car has been updated !!');
        return redirect()->route('admin.sotb-cars.index');
    }

    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('sotb_car.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any car !');
        }

        $fuel_car = SotbCar::find($id);
        if (!is_null($fuel_car)) {
            $fuel_car->delete();
        }

        session()->flash('success', 'Car has been deleted !!');
        return back();
    }
}

<?php

namespace App\Http\Controllers\Backend\Sotb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\SotbFuelIndexPump;

class FuelIndexPumpController extends Controller
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
        if (is_null($this->user) || !$this->user->can('sotb_index_pump.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any index pump !');
        }

        $fuel_index_pumps = SotbFuelIndexPump::orderBy('date','desc')->get();
        return view('backend.pages.sotb.fuel.index_pump.index', compact('fuel_index_pumps'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('sotb_index_pump.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any index pump !');
        }
        return view('backend.pages.sotb.fuel.index_pump.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('sotb_index_pump.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any index pump !');
        }

        // Validation Data
        $request->validate([
            'start_index' => 'required',
            'end_index' => 'required',
            'date' => 'required',
        ]);

        // Create New SotbFuelIndexPump
        $indexPump = new SotbFuelIndexPump();
        $indexPump->start_index = $request->start_index;
        $indexPump->end_index = $request->end_index;
        $indexPump->date = $request->date;
        $indexPump->final_index = $indexPump->end_index - $indexPump->start_index;
        $indexPump->auteur = $this->user->name;
        $indexPump->save();
        return redirect()->route('admin.sotb-fuel-index-pumps.index');
    }

    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('sotb_index_pump.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any index pump !');
        }

        $indexPump = SotbFuelIndexPump::find($id);
        return view('backend.pages.sotb.fuel.index_pump.edit', compact('indexPump'));
    }

    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('sotb_index_pump.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any index pump !');
        }

        $indexPump = SotbFuelIndexPump::find($id);

        // Validation Data
        $request->validate([
            'start_index' => 'required',
            'end_index' => 'required',
            'date' => 'required'
        ]);


        $indexPump->start_index = $request->start_index;
        $indexPump->end_index = $request->end_index;
        $indexPump->date = $request->date;
        $indexPump->final_index = $indexPump->end_index - $indexPump->start_index;
        $indexPump->auteur = $this->user->name;
        $indexPump->save();

        session()->flash('success', 'Index has been updated !!');
        return back();
    }

    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('sotb_index_pump.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any index pump !');
        }

        $indexPump = SotbFuelIndexPump::find($id);
        if (!is_null($indexPump)) {
            $indexPump->delete();
        }

        session()->flash('success', 'Index has been deleted !!');
        return back();
    }
}

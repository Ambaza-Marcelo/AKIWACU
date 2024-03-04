<?php

namespace App\Http\Controllers\Backend\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\BookingTechnique;

class TechniqueController extends Controller
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
        if (is_null($this->user) || !$this->user->can('booking_technique.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any technique !');
        }

        $techniques = BookingTechnique::all();
        return view('backend.pages.booking.technique.index', compact('techniques'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('booking_technique.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any technique !');
        }
        return view('backend.pages.booking.technique.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('booking_technique.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any technique !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:100'
        ]);

        // Create New BookingTechnique
        $technique = new BookingTechnique();
        $technique->name = $request->name;
        $technique->auteur = $this->user->name;
        $technique->save();
        return redirect()->back();
    }

    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('booking_technique.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any technique !');
        }

        $technique = BookingTechnique::find($id);
        return view('backend.pages.booking.technique.edit', compact('technique'));
    }

    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('booking_technique.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any technique !');
        }

        $technique = BookingTechnique::find($id);

        // Validation Data
        $request->validate([
            'name' => 'required|max:100'
        ]);


        $technique->name = $request->name;
        $technique->auteur = $this->user->name;
        $technique->save();

        session()->flash('success', 'BookingTechnique has been updated !!');
        return back();
    }

    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('booking_technique.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any technique !');
        }

        $technique = BookingTechnique::find($id);
        if (!is_null($technique)) {
            $technique->delete();
        }

        session()->flash('success', 'BookingTechnique has been deleted !!');
        return back();
    }
}

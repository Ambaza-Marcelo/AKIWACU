<?php

namespace App\Http\Controllers\Backend\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\BookingTable;

class TableController extends Controller
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
        if (is_null($this->user) || !$this->user->can('booking_table.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any table !');
        }

        $categories = BookingTable::all();
        return view('backend.pages.booking.table.index', compact('categories'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('booking_table.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any table !');
        }
        return view('backend.pages.booking.table.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('booking_table.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any table !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:100'
        ]);

        // Create New BookingTable
        $table = new BookingTable();
        $table->name = $request->name;
        $table->auteur = $this->user->name;
        $table->save();
        return redirect()->back();
    }

    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('booking_table.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any table !');
        }

        $table = BookingTable::find($id);
        return view('backend.pages.booking.table.edit', compact('table'));
    }

    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('booking_table.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any table !');
        }

        $table = BookingTable::find($id);

        // Validation Data
        $request->validate([
            'name' => 'required|max:100'
        ]);


        $table->name = $request->name;
        $table->auteur = $this->user->name;
        $table->save();

        session()->flash('success', 'BookingTable has been updated !!');
        return back();
    }

    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('booking_table.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any table !');
        }

        $table = BookingTable::find($id);
        if (!is_null($table)) {
            $table->delete();
        }

        session()->flash('success', 'BookingTable has been deleted !!');
        return back();
    }
}

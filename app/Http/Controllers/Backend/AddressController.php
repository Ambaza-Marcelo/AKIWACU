<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Address;

class AddressController extends Controller
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
        if (is_null($this->user) || !$this->user->can('address.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any address !');
        }

        $addresses = Address::all();
        return view('backend.pages.address.index', compact('addresses'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('address.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any address !');
        }
        return view('backend.pages.address.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('address.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any address !');
        }

        // Validation Data
        $request->validate([
            'country_name' => 'required|max:100',
            'city' => 'required|max:100',
            'district' => 'required|max:100'
        ]);

        // Create New Address
        $address = new Address();
        $address->country_name = $request->country_name;
        $address->city = $request->city;
        $address->district = $request->district;
        $address->created_by = $this->user->name;
        $address->save();
        return redirect()->back();
    }

    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('address.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any address !');
        }

        $address = Address::find($id);
        return view('backend.pages.address.edit', compact('address'));
    }

    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('address.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any address !');
        }

        $address = Address::find($id);

        // Validation Data
        $request->validate([
            'country_name' => 'required|max:100',
            'city' => 'required|max:100',
            'district' => 'required|max:100'
        ]);


        $address->country_name = $request->country_name;
        $address->city = $request->city;
        $address->district = $request->district;
        $address->created_by = $this->user->name;
        $address->save();

        session()->flash('success', 'Address has been updated !!');
        return back();
    }

    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('address.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any address !');
        }

        $address = Address::find($id);
        if (!is_null($address)) {
            $address->delete();
        }

        session()->flash('success', 'Address has been deleted !!');
        return back();
    }
}

<?php

namespace App\Http\Controllers\Backend\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\KidnessSpace;

class KidnessSpaceController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (is_null($this->user) || !$this->user->can('booking_kidness_space.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any kidness_space !');
        }

        $services = KidnessSpace::all();
        return view('backend.pages.booking.kidness_space.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('booking_kidness_space.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any kidness_space !');
        }
        return view('backend.pages.booking.kidness_space.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('booking_kidness_space.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any kidness_space !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'selling_price' => 'required',
            'quantity' => 'required',
        ]);

        // Create New KidnessSpace
        $kidness_space = new KidnessSpace();
        $kidness_space->name = $request->name;
        $artCode = strtoupper(substr($request->name, 0, 3));
        $kidness_space->code = $artCode.date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);
        $kidness_space->selling_price = $request->selling_price;
        $kidness_space->quantity = $request->quantity;
        $kidness_space->specification = $request->specification;
        $kidness_space->vat = $request->vat;
        $kidness_space->taux_marge = $request->taux_marge;
        $kidness_space->taux_majoration = $request->taux_majoration;
        $kidness_space->taux_reduction = $request->taux_reduction;
        $kidness_space->auteur = $this->user->name;
        $kidness_space->save();

        session()->flash('success', 'Kidness Space has been created !!');
        return redirect()->route('admin.kidness-spaces.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('booking_kidness_space.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any kidness_space !');
        }

        $kidness_space = KidnessSpace::find($id);
        return view('backend.pages.booking.kidness_space.edit', compact(
            'kidness_space'));
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('booking_kidness_space.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any kidness_space !');
        }

        // Create New KidnessSpace
        $kidness_space = KidnessSpace::find($id);

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'selling_price' => 'required',
            'quantity' => 'required',
        ]);


        $kidness_space->name = $request->name;
        $kidness_space->selling_price = $request->selling_price;
        $kidness_space->quantity = $request->quantity;
        $kidness_space->specification = $request->specification;
        $kidness_space->vat = $request->vat;
        $kidness_space->taux_marge = $request->taux_marge;
        $kidness_space->taux_majoration = $request->taux_majoration;
        $kidness_space->taux_reduction = $request->taux_reduction;
        $kidness_space->auteur = $this->user->name;
        $kidness_space->save();

        session()->flash('success', 'Kidness Space has been updated !!');
        return redirect()->route('admin.kidness-spaces.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('booking_kidness_space.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any kidness_space !');
        }

        $kidness_space = KidnessSpace::find($id);
        if (!is_null($kidness_space)) {
            $kidness_space->delete();
        }

        session()->flash('success', 'Kidness Space has been deleted !!');
        return back();
    }
}

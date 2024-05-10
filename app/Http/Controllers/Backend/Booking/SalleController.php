<?php

namespace App\Http\Controllers\Backend\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\BookingSalle;

class SalleController extends Controller
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
        if (is_null($this->user) || !$this->user->can('booking_salle.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any salle !');
        }

        $salles = BookingSalle::all();
        return view('backend.pages.booking.salle.index', compact('salles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('booking_salle.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any salle !');
        }
        return view('backend.pages.booking.salle.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('booking_salle.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any salle !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'selling_price' => 'required',
            'quantity' => 'required',
        ]);

        // Create New BookingSalle
        $salle = new BookingSalle();
        $salle->name = $request->name;
        $artCode = strtoupper(substr($request->name, 0, 3));
        $salle->code = $artCode.date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);
        $salle->selling_price = $request->selling_price;
        $salle->quantity = $request->quantity;
        $salle->specification = $request->specification;
        $salle->vat = $request->vat;
        $salle->taux_marge = $request->taux_marge;
        $salle->taux_majoration = $request->taux_majoration;
        $salle->taux_reduction = $request->taux_reduction;
        $salle->auteur = $this->user->name;
        $salle->save();

        session()->flash('success', 'Salle has been created !!');
        return redirect()->route('admin.salles.index');
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


    public function uploadArticle(Request $request)
    {
        Excel::import(new ArticlesImport, $request->file('file')->store('temp'));
        return redirect()->back();
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('booking_salle.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any salle !');
        }

        $salle = BookingSalle::find($id);
        return view('backend.pages.booking.salle.edit', compact(
            'salle'));
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
        if (is_null($this->user) || !$this->user->can('booking_salle.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any salle !');
        }

        // Create New BookingSalle
        $salle = BookingSalle::find($id);

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'selling_price' => 'required',
            'quantity' => 'required',
        ]);


        $salle->name = $request->name;
        $salle->selling_price = $request->selling_price;
        $salle->quantity = $request->quantity;
        $salle->specification = $request->specification;
        $salle->vat = $request->vat;
        $salle->taux_marge = $request->taux_marge;
        $salle->taux_majoration = $request->taux_majoration;
        $salle->taux_reduction = $request->taux_reduction;
        $salle->auteur = $this->user->name;
        $salle->save();

        session()->flash('success', 'Salle has been updated !!');
        return redirect()->route('admin.salles.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('booking_salle.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any salle !');
        }

        $salle = BookingSalle::find($id);
        if (!is_null($salle)) {
            $salle->delete();
        }

        session()->flash('success', 'Salle has been deleted !!');
        return back();
    }
}

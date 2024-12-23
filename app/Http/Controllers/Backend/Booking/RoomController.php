<?php

namespace App\Http\Controllers\backend\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\BookingRoom;

class RoomController extends Controller
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
        if (is_null($this->user) || !$this->user->can('booking_room.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any room !');
        }

        $rooms = BookingRoom::all();
        return view('backend.pages.booking.room.index', compact('rooms'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('booking_room.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any room !');
        }
        return view('backend.pages.booking.room.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('booking_room.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any room !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'selling_price' => 'required',
            'vat' => 'required',
            'item_tsce_tax' => 'required',
            'quantity' => 'required',
        ]);

        // Create New BookingRoom
        $room = new BookingRoom();
        $room->name = $request->name;
        $artCode = strtoupper(substr($request->name, 0, 3));
        $room->code = $artCode.date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);
        $room->selling_price = $request->selling_price;
        $room->quantity = $request->quantity;
        $room->specification = $request->specification;
        $room->vat = $request->vat;
        $room->item_tsce_tax = $request->item_tsce_tax;
        $room->item_ott_tax = $request->item_ott_tax;
        //$room->taux_marge = $request->taux_marge;
        //$room->taux_majoration = $request->taux_majoration;
        //$room->taux_reduction = $request->taux_reduction;
        $room->auteur = $this->user->name;
        $room->save();

        session()->flash('success', 'room has been created !!');
        return redirect()->route('admin.rooms.index');
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
        if (is_null($this->user) || !$this->user->can('booking_room.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any room !');
        }

        $room = BookingRoom::find($id);
        return view('backend.pages.booking.room.edit', compact(
            'room'));
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
        if (is_null($this->user) || !$this->user->can('booking_room.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any room !');
        }

        // Create New BookingRoom
        $room = BookingRoom::find($id);

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'selling_price' => 'required',
            'quantity' => 'required',
        ]);


        $room->name = $request->name;
        $room->selling_price = $request->selling_price;
        $room->quantity = $request->quantity;
        $room->specification = $request->specification;
        $room->vat = $request->vat;
        //$room->taux_marge = $request->taux_marge;
        //$room->taux_majoration = $request->taux_majoration;
        //$room->taux_reduction = $request->taux_reduction;
        $room->auteur = $this->user->name;
        $room->save();

        session()->flash('success', 'room has been updated !!');
        return redirect()->route('admin.rooms.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('booking_room.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any room !');
        }

        $room = BookingRoom::find($id);
        if (!is_null($room)) {
            $room->delete();
        }

        session()->flash('success', 'room has been deleted !!');
        return back();
    }
}

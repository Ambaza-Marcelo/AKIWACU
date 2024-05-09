<?php

namespace App\Http\Controllers\Backend\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\BreakFast;

class BreakFastController extends Controller
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
        if (is_null($this->user) || !$this->user->can('booking_service.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any break_fast !');
        }

        $services = BreakFast::all();
        return view('backend.pages.booking.break_fast.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('booking_service.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any break_fast !');
        }
        return view('backend.pages.booking.break_fast.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('booking_service.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any break_fast !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'selling_price' => 'required',
            'quantity' => 'required',
        ]);

        // Create New BreakFast
        $break_fast = new BreakFast();
        $break_fast->name = $request->name;
        $artCode = strtoupper(substr($request->name, 0, 3));
        $break_fast->code = $artCode.date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);
        $break_fast->selling_price = $request->selling_price;
        $break_fast->quantity = $request->quantity;
        $break_fast->specification = $request->specification;
        $break_fast->vat = $request->vat;
        $break_fast->auteur = $this->user->name;
        $break_fast->save();

        session()->flash('success', 'Break Fast has been created !!');
        return redirect()->route('admin.break-fasts.index');
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
        if (is_null($this->user) || !$this->user->can('booking_service.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any break_fast !');
        }

        $break_fast = BreakFast::find($id);
        return view('backend.pages.booking.break_fast.edit', compact(
            'break_fast'));
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
        if (is_null($this->user) || !$this->user->can('booking_service.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any break_fast !');
        }

        // Create New BreakFast
        $break_fast = BreakFast::find($id);

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'selling_price' => 'required',
            'quantity' => 'required',
        ]);


        $break_fast->name = $request->name;
        $break_fast->selling_price = $request->selling_price;
        $break_fast->quantity = $request->quantity;
        $break_fast->specification = $request->specification;
        $break_fast->vat = $request->vat;
        $break_fast->auteur = $this->user->name;
        $break_fast->save();

        session()->flash('success', 'Break Fast has been updated !!');
        return redirect()->route('admin.break-fasts.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('booking_service.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any break_fast !');
        }

        $break_fast = BreakFast::find($id);
        if (!is_null($break_fast)) {
            $break_fast->delete();
        }

        session()->flash('success', 'Break Fast has been deleted !!');
        return back();
    }
}

<?php

namespace App\Http\Controllers\Backend\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\BookingService;

class ServiceController extends Controller
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
            abort(403, 'Sorry !! You are Unauthorized to view any service !');
        }

        $services = BookingService::all();
        return view('backend.pages.booking.service.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('booking_service.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any service !');
        }
        return view('backend.pages.booking.service.create');
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
            abort(403, 'Sorry !! You are Unauthorized to create any service !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'selling_price' => 'required',
            'quantity' => 'required',
        ]);

        // Create New BookingService
        $service = new BookingService();
        $service->name = $request->name;
        $artCode = strtoupper(substr($request->name, 0, 3));
        $service->code = $artCode.date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);
        $service->selling_price = $request->selling_price;
        $service->quantity = $request->quantity;
        $service->specification = $request->specification;
        $service->vat = $request->vat;
        $service->taux_marge = $request->taux_marge;
        $service->taux_majoration = $request->taux_majoration;
        $service->taux_reduction = $request->taux_reduction;
        $service->auteur = $this->user->name;
        $service->save();

        session()->flash('success', 'Service has been created !!');
        return redirect()->route('admin.services.index');
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
            abort(403, 'Sorry !! You are Unauthorized to edit any service !');
        }

        $service = BookingService::find($id);
        return view('backend.pages.booking.service.edit', compact(
            'service'));
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
            abort(403, 'Sorry !! You are Unauthorized to edit any service !');
        }

        // Create New BookingService
        $service = BookingService::find($id);

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'selling_price' => 'required',
            'quantity' => 'required',
        ]);


        $service->name = $request->name;
        $service->selling_price = $request->selling_price;
        $service->quantity = $request->quantity;
        $service->specification = $request->specification;
        $service->vat = $request->vat;
        $service->taux_marge = $request->taux_marge;
        $service->taux_majoration = $request->taux_majoration;
        $service->taux_reduction = $request->taux_reduction;
        $service->auteur = $this->user->name;
        $service->save();

        session()->flash('success', 'Service has been updated !!');
        return redirect()->route('admin.services.index');
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
            abort(403, 'Sorry !! You are Unauthorized to delete any service !');
        }

        $service = BookingService::find($id);
        if (!is_null($service)) {
            $service->delete();
        }

        session()->flash('success', 'Service has been deleted !!');
        return back();
    }
}

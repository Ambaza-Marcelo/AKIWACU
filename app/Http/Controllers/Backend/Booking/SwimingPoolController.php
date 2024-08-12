<?php

namespace App\Http\Controllers\Backend\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\SwimingPool;

class SwimingPoolController extends Controller
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
        if (is_null($this->user) || !$this->user->can('swiming_pool.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any swiming_pool !');
        }

        $services = SwimingPool::all();
        return view('backend.pages.booking.swiming_pool.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('swiming_pool.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any swiming_pool !');
        }
        return view('backend.pages.booking.swiming_pool.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('swiming_pool.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any swiming_pool !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'selling_price' => 'required',
            'quantity' => 'required',
        ]);

        // Create New SwimingPool
        $swiming_pool = new SwimingPool();
        $swiming_pool->name = $request->name;
        $artCode = strtoupper(substr($request->name, 0, 3));
        $swiming_pool->code = $artCode.date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);
        $swiming_pool->selling_price = $request->selling_price;
        $swiming_pool->quantity = $request->quantity;
        $swiming_pool->specification = $request->specification;
        $swiming_pool->vat = $request->vat;
        //$swiming_pool->taux_marge = $request->taux_marge;
        //$swiming_pool->taux_majoration = $request->taux_majoration;
        //$swiming_pool->taux_reduction = $request->taux_reduction;
        $swiming_pool->auteur = $this->user->name;
        $swiming_pool->save();

        session()->flash('success', 'Pool has been created !!');
        return redirect()->route('admin.swiming-pools.index');
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
        if (is_null($this->user) || !$this->user->can('swiming_pool.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any swiming_pool !');
        }

        $swiming_pool = SwimingPool::find($id);
        return view('backend.pages.booking.swiming_pool.edit', compact(
            'swiming_pool'));
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
        if (is_null($this->user) || !$this->user->can('swiming_pool.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any swiming_pool !');
        }

        // Create New SwimingPool
        $swiming_pool = SwimingPool::find($id);

        // Validation Data
        $request->validate([
            'name' => 'required|max:255',
            'selling_price' => 'required',
            'quantity' => 'required',
        ]);


        $swiming_pool->name = $request->name;
        $swiming_pool->selling_price = $request->selling_price;
        $swiming_pool->quantity = $request->quantity;
        $swiming_pool->specification = $request->specification;
        $swiming_pool->vat = $request->vat;
        //$swiming_pool->taux_marge = $request->taux_marge;
        //$swiming_pool->taux_majoration = $request->taux_majoration;
        //$swiming_pool->taux_reduction = $request->taux_reduction;
        $swiming_pool->auteur = $this->user->name;
        $swiming_pool->save();

        session()->flash('success', 'Pool has been updated !!');
        return redirect()->route('admin.swiming-pools.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('swiming_pool.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any swiming_pool !');
        }

        $swiming_pool = SwimingPool::find($id);
        if (!is_null($swiming_pool)) {
            $swiming_pool->delete();
        }

        session()->flash('success', 'Pool has been deleted !!');
        return back();
    }
}

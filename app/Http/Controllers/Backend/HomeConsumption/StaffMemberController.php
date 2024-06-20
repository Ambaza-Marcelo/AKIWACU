<?php

namespace App\Http\Controllers\Backend\HomeConsumption;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\StaffMember;
use App\Models\Position;

class StaffMemberController extends Controller
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
        if (is_null($this->user) || !$this->user->can('employe.view')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        } 

        $staff_members = StaffMember::all();
        return view('backend.pages.home_consumption.staff_member.index',compact('staff_members'));
    }
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('employe.create')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        } 
        $positions = Position::where('name','!=','SERVEUR')->where('name','!=','EXTRA')->get();
        return view('backend.pages.home_consumption.staff_member.create',compact('positions'));
    }

    public function choose()
    {
        if (is_null($this->user) || !$this->user->can('drink_order_client.view')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        } 

        $staff_members = StaffMember::all();
        return view('backend.pages.home_consumption.staff_member.choose',compact('staff_members'));
    }

    public function chooseType($staff_member_id)
    {
        if (is_null($this->user) || !$this->user->can('drink_order_client.view')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        } 

         $staff_member = StaffMember::where('id',$staff_member_id)->value('name');

         return view('backend.pages.home_consumption.staff_member.choose_type',compact('staff_member_id'));
            

        
    }

    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'total_amount_authorized' => 'required',
            'position_id' => 'required',

        ]);

        $staff_member = new StaffMember();
        $staff_member->name = $request->name;
        $staff_member->total_amount_authorized = $request->total_amount_authorized;
        $staff_member->total_amount_consumed = 0;
        $staff_member->total_amount_remaining = $request->total_amount_authorized;
        $staff_member->start_date = $request->start_date;
        $staff_member->end_date = $request->end_date;
        $staff_member->position_id = $request->position_id;
        $staff_member->save();

        session()->flash('success', 'StaffMember est créé !!');

        return redirect()->route('admin.staff_members.index');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\StaffMember  $staff_member
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('employe.edit')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        } 
        //
        $staff_member = StaffMember::findOrFail($id);
        $positions = Position::where('name','!=','SERVEUR')->where('name','!=','EXTRA')->get();
        return view('backend.pages.home_consumption.staff_member.edit', compact('staff_member','positions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\StaffMember  $staff_member
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    	if (is_null($this->user) || !$this->user->can('employe.edit')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        }
        //
        $request->validate([
            'name' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'total_amount_authorized' => 'required',
            'position_id' => 'required',

        ]);

        $staff_member = StaffMember::findOrFail($id);

        $staff_member->name = $request->name;
        $staff_member->total_amount_authorized = $request->total_amount_authorized;

        $staff_member->start_date = $request->start_date;
        $staff_member->end_date = $request->end_date;
        $staff_member->position_id = $request->position_id;
        $staff_member->save();
        session()->flash('success', 'staff_member est modifié !!');
        return redirect()->route('admin.staff_members.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\StaffMember  $staff_member
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('employe.delete')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        } 
        //
        $staff_member = StaffMember::findOrFail($id);
        $staff_member->delete();
        session()->flash('success', 'staff_member est supprimé !!');
        return redirect()->back();
    }
}

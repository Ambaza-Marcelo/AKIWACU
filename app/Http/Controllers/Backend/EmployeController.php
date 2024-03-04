<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Employe;
use App\Models\Position;
use App\Models\Address;
use Validator;
use Excel;

class EmployeController extends Controller
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

        $employes = Employe::with(['position','address'])->get();
        return view('backend.pages.employe.index',compact('employes'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('employe.create')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        }

        $positions = Position::all();
        $addresses = Address::all();
        return view('backend.pages.employe.create',compact('positions','addresses'));
    }

    public function store(Request $request)
    {
        //
        $rules = array(
            'name' => 'required',
            //'position_id' => 'required',
            'image' => 'required|mimes:jpeg,jpg,png,svg|max:3072'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

        $name = $request->name;
        $position_id = $request->position_id;
        $address_id = $request->address_id;
        $created_by = $this->user->name;

        $storagepath = $request->file('image')->store('public/employe');
        $fileName = basename($storagepath);

        $picture['image'] = $fileName;

        $data = new Employe();
        $data->name = $name;
        $data->position_id = $position_id;
        $data->address_id = $address_id;
        $data->image = $picture['image'];
        $data->created_by = $created_by;
        $data->save();
        session()->flash('success', 'Employé a été créé!!');

        return redirect()->route('admin.employes.index');
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Employe  $employe
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('employe.edit')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        }
        //
        $addresses = Address::all();
        $positions = Position::all();
        $employe = Employe::findOrFail($id);
        return view('backend.pages.employe.edit', compact('addresses','employe','positions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Employe  $employe
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $request->validate([
            'name' => 'required',
            'position_id' => 'required',

        ]);

        $employe = Employe::findOrFail($id);

        $data = $request->all();

        if($request->hasFile('image')){
            $file_path = "public/employe".$employe->image;
            Storage::delete($file_path);

            $storagepath = $request->file('image')->store('public/employe');
            $fileName = basename($storagepath);
            $data['image'] = $fileName;

        }

        $employe->fill($data);
        $employe->save();
        session()->flash('success', 'Employé a été modifié!!');
        return redirect()->route('admin.employes.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Employe  $employe
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('employe.delete')) {
            abort(403, 'Sorry !! You are Unauthorized !');
        }
        //
        $employe = Employe::findOrFail($id);
        $file_path = "public/employe/".$employe->image;
            Storage::delete($file_path);
        $employe->delete();
        session()->flash('success', 'Employé a été supprimé!!');
        return redirect()->back();
    }
}

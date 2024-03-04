<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\Accompagnement;


class AccompagnementController extends Controller
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
        if (is_null($this->user) || !$this->user->can('food_item.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any accompagnement !');
        }

        $accompagnements = Accompagnement::all();
        return view('backend.pages.accompagnement.index', compact('accompagnements'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('food_item.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any accompagnement !');
        }
        return view('backend.pages.accompagnement.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('food_item.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any accompagnement !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:100'
        ]);

        // Create New Accompagnement
        $accompagnement = new Accompagnement();
        $accompagnement->name = $request->name;
        //$accompagnement->created_by = $this->user->name;
        $accompagnement->save();
        return redirect()->route('admin.accompagnements.index');
    }

    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('food_item.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any accompagnement !');
        }

        $accompagnement = Accompagnement::find($id);
        return view('backend.pages.accompagnement.edit', compact('accompagnement'));
    }

    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('food_item.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any accompagnement !');
        }

        $accompagnement = Accompagnement::find($id);

        // Validation Data
        $request->validate([
            'name' => 'required|max:100'
        ]);


        $accompagnement->name = $request->name;
        //$accompagnement->created_by = $this->user->name;
        $accompagnement->save();

        session()->flash('success', 'Accompagnement has been updated !!');
        return back();
    }

    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('food_item.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any accompagnement !');
        }

        $accompagnement = Accompagnement::find($id);
        if (!is_null($accompagnement)) {
            $accompagnement->delete();
        }

        session()->flash('success', 'Accompagnement has been deleted !!');
        return back();
    }
}

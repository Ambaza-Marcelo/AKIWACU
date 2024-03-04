<?php

namespace App\Http\Controllers\Backend\Hr;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\HrService;

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

    public function index()
    {
        if (is_null($this->user) || !$this->user->can('hr_service.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser le service! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $services = HrService::all();

        return view('backend.pages.hr.service.index',compact('services'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('hr_service.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer le service! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        return view('backend.pages.hr.service.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_service.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer le service! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'name' => 'required',

        ]);

        $latest = HrService::latest()->first();
            if ($latest) {
               $code = 'SE' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $code = 'SE' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }

        $service = new HrService();
        $service->name = $request->name;
        $service->code = $code;
        $service->save();

        session()->flash('success', 'Service est créé !!');

        return redirect()->back();
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrService  $service
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_service.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier le service! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $service = HrService::findOrFail($id);
        return view('backend.pages.hr.service.edit', compact('service'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrService  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_service.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier la service! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'name' => 'required',

        ]);

        $service = HrService::findOrFail($id);

        $service->name = $request->name;
        $service->save();
        session()->flash('success', 'Service est modifié !!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrService  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_service.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer le service! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $service = HrService::findOrFail($id);
        $service->delete();
        session()->flash('success', 'Service est supprimé !!');
        return redirect()->back();
    }
}

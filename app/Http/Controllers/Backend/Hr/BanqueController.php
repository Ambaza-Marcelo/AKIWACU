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
use App\Models\HrBanque;

class BanqueController extends Controller
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
        if (is_null($this->user) || !$this->user->can('hr_banque.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser la banque! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $banques = HrBanque::all();

        return view('backend.pages.hr.banque.index',compact('banques'));
    }


    public function create()
    {
        if (is_null($this->user) || !$this->user->can('hr_banque.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer la banque! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        return view('backend.pages.hr.banque.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_banque.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer la banque! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'name' => 'required',
            'address' => 'required',

        ]);


            $latest = HrBanque::latest()->first();
            if ($latest) {
               $code = 'BA' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $code = 'BA' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }


        $banque = new HrBanque();
        $banque->name = $request->name;
        $banque->address = $request->address;
        $banque->code = $code;
        $banque->currency = $request->currency;
        $banque->save();

        session()->flash('success', 'Banque est créé !!');

        return redirect()->back();
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrBanque  $banque
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_banque.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier la banque! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $banque = HrBanque::findOrFail($id);
        return view('backend.pages.hr.banque.edit', compact('banque'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrBanque  $banque
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_banque.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier la banque! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'name' => 'required',

        ]);

        $banque = HrBanque::findOrFail($id);

        $banque->name = $request->name;
        $banque->address = $request->address;
        $banque->currency = $request->currency;
        $banque->save();
        session()->flash('success', 'Banque est modifié !!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrBanque  $banque
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_banque.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer la banque! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $banque = HrBanque::findOrFail($id);
        $banque->delete();
        session()->flash('success', 'Banque est supprimé !!');
        return redirect()->back();
    }
}

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
use App\Models\HrImpot;
use App\Models\HrGroupeImpot;
use Validator;

class ImpotController extends Controller
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
        if (is_null($this->user) || !$this->user->can('hr_impot.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les indemnités! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $impots = HrImpot::with('groupeImpot')->get();
        $impot = HrImpot::first();

        return view('backend.pages.hr.impot.index',compact('impots','impot'));
    }
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('hr_impot.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les indemnités! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $groupe_impots = HrGroupeImpot::all();


        return view('backend.pages.hr.impot.create',compact('groupe_impots'));
    }

     public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_impot.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les indemnités! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $rules = array(
            'groupe_impot_id.*' => 'required',
            'pourcentage_impot.*' => 'required'

        );

        $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

        $groupe_impot_id = $request->groupe_impot_id;
        $pourcentage_impot = $request->pourcentage_impot;

        for( $count = 0; $count < count($groupe_impot_id); $count++ ){

                $data = array(
                    'groupe_impot_id' => $groupe_impot_id[$count],
                    'pourcentage_impot' => $pourcentage_impot[$count],
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );

                $insert_data[] = $data;
                
            }

        HrImpot::insert($insert_data);
        session()->flash('success', 'Impot est créé !!');

        return redirect()->back();
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Impot  $impot
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_impot.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier l\'indemnité! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $groupe_impots = HrGroupeImpot::all();
        $impot = HrImpot::where('id',$id)->first();
        $datas = HrImpot::where('id',$id)->get();
        return view('backend.pages.hr.impot.edit', compact('impot','groupe_impots','datas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Impot  $impot
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_impot.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier l\'indemnité! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        

        session()->flash('success', 'Impot est modifié !!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrImpot  $impot
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_impot.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer l\'indemnité! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $impot = HrImpot::findOrFail($id);
        $impot->delete();
        session()->flash('success', 'Impot est supprimé !!');
        return redirect()->back();
    }
}

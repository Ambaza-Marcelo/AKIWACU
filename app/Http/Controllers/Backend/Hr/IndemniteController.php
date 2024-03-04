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
use App\Models\HrIndemnite;
use App\Models\HrGroupeIndemnite;
use App\Models\HrTypeIndemnite;
use Validator;

class IndemniteController extends Controller
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
        if (is_null($this->user) || !$this->user->can('hr_indemnite.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les indemnités! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $indemnites = HrIndemnite::with('groupeIndemnite','typeIndemnite')->get();
        $indemnite = HrIndemnite::first();

        return view('backend.pages.hr.indemnite.index',compact('indemnites','indemnite'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('hr_indemnite.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les indemnités! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $type_indemnites = HrTypeIndemnite::all();
        $groupe_indemnites = HrGroupeIndemnite::all();

        return view('backend.pages.hr.indemnite.create',compact('type_indemnites','groupe_indemnites'));
    }

     public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_indemnite.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les indemnités! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $rules = array(
            //'contrat_id' => 'required',
            'type_indemnite_id.*' => 'required',
            'groupe_indemnite_id.*' => 'required',
            'pourcentage_sb.*' => 'required'

        );

        $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

        $type_indemnite_id = $request->type_indemnite_id;
        $groupe_indemnite_id = $request->groupe_indemnite_id;
        $contrat_id = $request->contrat_id;
        $pourcentage_sb = $request->pourcentage_sb;

        $code = "IND".date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);
  

        for( $count = 0; $count < count($type_indemnite_id); $count++ ){

                $data = array(
                    'type_indemnite_id' => $type_indemnite_id[$count],
                    'groupe_indemnite_id' => $groupe_indemnite_id[$count],
                    //'contrat_id' => $contrat_id,
                    'code' => $code,
                    'pourcentage_sb' => $pourcentage_sb[$count],
                    //'somme_indemnite' => $pourcentage_sb[$count] * $somme_salaire,
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );

                $insert_data[] = $data;
                
            }

        HrIndemnite::insert($insert_data);

        session()->flash('success', 'Indemnite est créé !!');

        return redirect()->back();
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrIndemnite  $indemnite
     * @return \Illuminate\Http\Response
     */
    public function edit($code)
    {
        if (is_null($this->user) || !$this->user->can('hr_indemnite.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier l\'indemnité! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $type_indemnites = HrTypeIndemnite::all();
        $groupe_indemnites = HrGroupeIndemnite::all();
        $indemnite = HrIndemnite::where('code',$code)->first();
        $datas = HrIndemnite::where('code',$code)->get();
        $contrats = Contrat::all();
        return view('backend.pages.hr.indemnite.edit', compact('indemnite','type_indemnites','groupe_indemnites','datas','contrats'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrIndemnite  $indemnite
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $code)
    {
        if (is_null($this->user) || !$this->user->can('hr_indemnite.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier l\'indemnité! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $rules = array(
            'groupe_indemnite_id.*' => 'required',
            'type_indemnite_id.*' => 'required',
            'pourcentage_sb.*' => 'required'

        );

        $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

        $type_indemnite_id = $request->type_indemnite_id;
        $groupe_indemnite_id = $request->groupe_indemnite_id;
        $contrat_id = $request->contrat_id;
        $pourcentage_sb = $request->pourcentage_sb;


        for( $count = 0; $count < count($type_indemnite_id); $count++ ){

                $data = array(
                    'type_indemnite_id' => $type_indemnite_id[$count],
                    'groupe_indemnite_id' => $groupe_indemnite_id[$count],
                    //'code' => $code,
                    'pourcentage_sb' => $pourcentage_sb[$count],
                    //'somme_indemnite' => $pourcentage_sb[$count] * $somme_salaire,
                    'auteur' => $this->user->name,
                    'updated_at' => \Carbon\Carbon::now()
                );

                HrIndemnite::where('type_indemnite_id',$type_indemnite_id[$count])
                        ->update($data);
                
            }

        session()->flash('success', 'Indemnite est modifié !!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrIndemnite  $indemnite
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_indemnite.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer l\'indemnité! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //

        $indemnite = HrIndemnite::findOrFail($id);
        $indemnite->delete();
        session()->flash('success', 'Indemnite est supprimé !!');
        return redirect()->back();
    }
}

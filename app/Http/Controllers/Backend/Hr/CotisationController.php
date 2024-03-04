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
use App\Models\HrCotisation;
use App\Models\HrTypeCotisation;
use App\Models\HrGroupeCotisation;
use Validator;


class CotisationController extends Controller
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
        if (is_null($this->user) || !$this->user->can('hr_cotisation.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les cotisations! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $cotisations = HrCotisation::with('groupeCotisation','typeCotisation')->get();
        $cotisation = HrCotisation::first();

        return view('backend.pages.hr.cotisation.index',compact('cotisations','cotisation'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('hr_cotisation.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les cotisations! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        $type_cotisations = HrTypeCotisation::orderBy('name')->get();
        $groupe_cotisations = HrGroupeCotisation::all();
        return view('backend.pages.hr.cotisation.create',compact('type_cotisations','groupe_cotisations'));
    }

     public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_cotisation.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les cotisations! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $rules = array(
            'groupe_cotisation_id.*' => 'required',
            'type_cotisation_id.*' => 'required',
            'pourcentage_sb.*' => 'required'

        );

        $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

        $type_cotisation_id = $request->type_cotisation_id;
        $groupe_cotisation_id = $request->groupe_cotisation_id;
        $pourcentage_sb = $request->pourcentage_sb;
        $pourcentage_employeur = $request->pourcentage_employeur;
        $etat = $request->etat;

        $code = "COT".date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);


        for( $count = 0; $count < count($type_cotisation_id); $count++ ){

                $data = array(
                    'type_cotisation_id' => $type_cotisation_id[$count],
                    'groupe_cotisation_id' => $groupe_cotisation_id[$count],
                    'etat' => $etat[$count],
                    'code' => $code,
                    'pourcentage_sb' => $pourcentage_sb[$count],
                    'pourcentage_employeur' => $pourcentage_employeur[$count],
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );
                $insert_data[] = $data;
                
            }

        HrCotisation::insert($insert_data);

        session()->flash('success', 'Cotisation est créé !!');

        return redirect()->back();
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrCotisation  $cotisation
     * @return \Illuminate\Http\Response
     */
    public function edit($code)
    {
        if (is_null($this->user) || !$this->user->can('hr_cotisation.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier la cotisation! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        $type_cotisations = HrTypeCotisation::all();
        $cotisation = HrCotisation::where('code',$code)->first();
        $datas = HrCotisation::where('code',$code)->get();
        return view('backend.pages.hr.cotisation.edit', compact('cotisation','type_cotisations','contrats','datas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrCotisation  $cotisation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $code)
    {
        if (is_null($this->user) || !$this->user->can('hr_cotisation.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier la cotisation! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $rules = array(
            'groupe_cotisation_id.*' => 'required',
            'type_cotisation_id.*' => 'required',
            'pourcentage_sb.*' => 'required'

        );

        $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

        $type_cotisation_id = $request->type_cotisation_id;
        $groupe_cotisation_id = $request->groupe_cotisation_id;
        $pourcentage_sb = $request->pourcentage_sb;
        $pourcentage_employeur = $request->pourcentage_employeur;
        $etat = $request->etat;


        for( $count = 0; $count < count($type_cotisation_id); $count++ ){

                $data = array(
                    'type_cotisation_id' => $type_cotisation_id[$count],
                    'groupe_cotisation_id' => $groupe_cotisation_id[$count],
                    'etat' => $etat[$count],
                    'pourcentage_sb' => $pourcentage_sb[$count],
                    'pourcentage_employeur' => $pourcentage_employeur[$count],
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );
                HrCotisation::where('type_cotisation_id',$type_cotisation_id)
                        ->update($data);
                
            }

        session()->flash('success', 'Cotisation est modifié !!');
        return redirect()->back();
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrCotisation  $cotisation
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_cotisation.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer la cotisation! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $cotisation = HrCotisation::findOrFail($id);
        $cotisation->delete();
        session()->flash('success', 'Cotisation est supprimé !!');
        return redirect()->back();
    }
}

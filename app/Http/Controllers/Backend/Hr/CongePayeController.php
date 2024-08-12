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
use App\Models\HrCongePaye;
use App\Models\HrJournalCongePaye;
use App\Models\HrEmploye;


class CongePayeController extends Controller
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
        if (is_null($this->user) || !$this->user->can('hr_conge_paye.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les congés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        $journal_conge_paye_encours = count(HrCongePaye::where('etat', 0)->get());

        $conge_payes = DB::table('conge_payes')->orderBy('created_at','desc')->get();
        return view('backend.pages.hr.conge_paye.index',compact('conge_payes','journal_conge_paye_encours'));
    }
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('hr_conge_paye.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les congés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        return view('backend.pages.hr.conge_paye.create');
    }

     public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_conge_paye.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les congés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        //
        $request->validate([
            'nbre_jours' => 'required',
            'session' => 'required'

        ]);

        try {DB::beginTransaction();

        $conge_paye = new HrCongePaye();
        $conge_paye->nbre_jours = $request->nbre_jours;
        $conge_paye->session = $request->session;
        $conge_paye->etat = 0;
        $conge_paye->save();

        $datas = HrEmploye::all();

        foreach($datas as $data){
                    $journalCongePaye = array(
                        'employe_id' => $data->id,
                        'nbre_jours_conge_paye' => $conge_paye->nbre_jours,
                        'nbre_jours_conge_sollicite' => null,
                        'nbre_jours_conge_pris' => null,
                        'nbre_jours_conge_restant' => $conge_paye->nbre_jours,
                        'session' => $conge_paye->session,
                        'etat' => 0,
                        'created_by' => $this->user->name,
                    );

                $insert_data[] = $journalCongePaye;        
            }
                HrJournalCongePaye::insert($insert_data);

            DB::commit();

            session()->flash('success', 'Congé Payé est créé !!');

            return redirect()->back();

            } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrCongePaye  $conge_paye
     * @return \Illuminate\Http\Response
     */
    public function show($session)
    {
        if (is_null($this->user) || !$this->user->can('hr_conge_paye.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les congés! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $conge_paye = HrCongePaye::where('session',$session)->first();
        $datas = HrJournalCongePaye::where('session',$session)->get();
        return view('backend.pages.journal_conge_paye.index', compact('conge_paye','datas'));
    }


    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_conge_paye.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier un congé! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $conge_paye = HrCongePaye::findOrFail($id);
        return view('backend.pages.hr.conge_paye.edit', compact('conge_paye'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrCongePaye  $conge_paye
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_conge_paye.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier un congé! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $request->validate([
            'nbre_jours' => 'required',

        ]);

        $conge_paye = HrCongePaye::findOrFail($id);
        $conge_paye->nbre_jours = $request->nbre_jours;
        $conge_paye->session = $request->session;
        $conge_paye->save();
        session()->flash('success', 'Congé Payé est modifié !!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrCongePaye  $conge_paye
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_conge_paye.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer un congé! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $conge_paye = HrCongePaye::findOrFail($id);
        $conge_paye->delete();
        session()->flash('success', 'Congé Payé est supprimé !!');
        return redirect()->back();
    }
}

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
use App\Models\HrPrime;
use App\Models\HrTypePrime;
use Validator;

class PrimeController extends Controller
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
        if (is_null($this->user) || !$this->user->can('hr_prime.view')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de visualiser les primes! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $primes = HrPrime::with('typePrime')->get();     

        return view('backend.pages.hr.prime.index',compact('primes'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('hr_prime.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les primes! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        $type_primes = HrTypePrime::orderBy('name')->get();
        return view('backend.pages.hr.prime.create',compact('type_primes'));
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('hr_prime.create')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de créer les primes! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $rules = array(
            'type_prime_id.*' => 'required',
            'pourcentage_prime.*' => 'required'

        );

        $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

        $type_prime_id = $request->type_prime_id;
        $pourcentage_prime = $request->pourcentage_prime;

        for( $count = 0; $count < count($type_prime_id); $count++ ){

                $data = array(
                    'type_prime_id' => $type_prime_id[$count],
                    'pourcentage_prime' => $pourcentage_prime[$count],
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );
                $insert_data[] = $data;
                
            }

        HrPrime::insert($insert_data);


        session()->flash('success', 'Prime est créé !!');

        return redirect()->back();
    }



    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\HrPrime  $prime
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_prime.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier la prime! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        $type_primes = HrTypePrime::all();
        $datas = HrPrime::findOrFail($id);
        return view('backend.pages.hr.prime.edit', compact('prime','type_primes','datas'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\HrPrime  $prime
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('hr_prime.edit')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de modifier la prime! Mufise ico mubaza hamagara kuri 130 canke 122');
        }
        //
        $rules = array(
            'type_prime_id.*' => 'required',
            'pourcentage_prime.*' => 'required'

        );

        $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

        $type_prime_id = $request->type_prime_id;
        $pourcentage_prime = $request->pourcentage_prime;


        for( $count = 0; $count < count($type_prime_id); $count++ ){

                $data = array(
                    'type_prime_id' => $type_prime_id[$count],
                    'pourcentage_prime' => $pourcentage_prime[$count],
                    'created_by' => $this->user->name,
                    'created_at' => \Carbon\Carbon::now()
                );
                HrPrime::where('type_prime_id',$type_prime_id)
                        ->update($data);
                
            }

        session()->flash('success', 'Prime est modifié !!');
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\HrPrime  $prime
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('hr_prime.delete')) {
            abort(403, 'Pardon!! vous n\'avez pas l\'autorisation de supprimer la prime! Mufise ico mubaza hamagara kuri 130 canke 122');
        }

        $prime = HrPrime::findOrFail($id);
        $prime->delete();
        session()->flash('success', 'Prime est supprimé !!');
        return redirect()->back();
    }
}

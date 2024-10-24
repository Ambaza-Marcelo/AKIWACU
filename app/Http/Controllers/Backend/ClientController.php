<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\EGRClient;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class ClientController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (is_null($this->user) || !$this->user->can('client.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any client !');
        }

        $clients = EGRClient::orderBy('customer_name','asc')->get();
        return view('backend.pages.client.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('client.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any client !');
        }

        return view('backend.pages.client.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('client.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any client !');
        }

        // Validation Data
        $request->validate([
            //'customer_name' => 'required|max:100',
            'tp_type' => 'required',
            'telephone' => 'required',
        ]);

        $theUrl = config('app.guzzle_test_url').'/ebms_api/login/';
        $response = Http::post($theUrl, [
            'username'=> config('app.obr_test_username'),
            'password'=> config('app.obr_test_pwd')

        ]);

        $data =  json_decode($response);
        $data2 = ($data->result);
        
    
        $token = $data2->token;

        $tp_TIN = $request->customer_TIN;
        /*
        $clients = EGRClient::all();

        foreach ($clients as $client) {
            if ($client->customer_TIN == $tp_TIN) {
                session()->flash('error', 'Le NIF du Contribuable existe déjà');
                return redirect()->back();
            }elseif ($client->customer_name == $request->customer_name) {
                session()->flash('error', 'Le nom du Contribuable existe déjà');
                return redirect()->back();
            }elseif ($client->telephone == $request->telephone) {
                session()->flash('error', 'Le Numero de telephone du Contribuable existe déjà');
                return redirect()->back();
            }elseif ($client->mail == $request->mail) {
                session()->flash('error', 'Le mail du Contribuable existe déjà');
                return redirect()->back();
            }
        }
        */

        if (empty($tp_TIN) && $request->vat_customer_payer == 1) {
            session()->flash('error', 'Le NIF du client est obligatoire');
            return redirect()->back();
        }elseif (!empty($tp_TIN) && strlen($tp_TIN) < 10) {
            session()->flash('error', 'Le NIF du client n\'existe pas');
            return redirect()->back();
        }

        $theUrl = config('app.guzzle_test_url').'/ebms_api/checkTIN/';
        $response = Http::withHeaders([
        'Authorization' => 'Bearer '.$token,
        'Accept' => 'application/json'])->post($theUrl, [
            'tp_TIN'=>$tp_TIN,

        ]); 

        $data =  json_decode($response);
        $data2 = ($data->result);
        
    
        $success = $data->success;
        $msg = $data->msg;

        
        if ($request->vat_customer_payer == 1 && $request->tp_type == 2) {

            $data3 = ($data2->taxpayer);

            $index_one = ($data3['0']);

            $tp_name = $index_one->tp_name;

            $client = new EGRClient();
            $client->date = $request->date;
            $client->customer_name = $tp_name;
            $client->tp_type = $request->tp_type;
            $client->telephone = $request->telephone;
            $client->mail = $request->mail;
            $client->customer_TIN = $request->customer_TIN;
            $client->customer_address = $request->customer_address;
            $client->vat_customer_payer = $request->vat_customer_payer;
            $client->company = $request->company;
            $client->save();
            session()->flash('success', 'Le client a été créé avec succés !!, OBR Message : '.$msg.'('.$tp_name.')');
            return redirect()->route('admin.clients.index');
        }elseif ($success == false && $request->vat_customer_payer == 1) {

            session()->flash('error', 'Le NIF du Contribuable inconnu');
            return redirect()->back();
        }elseif ($success == false && !empty($tp_TIN) && $request->vat_customer_payer == 0) {

            session()->flash('error', 'Le NIF du Contribuable inconnu');
            return redirect()->back();
        }else{
            $client = new EGRClient();
            $client->date = $request->date;
            $client->customer_name = $request->customer_name;
            $client->tp_type = $request->tp_type;
            $client->telephone = $request->telephone;
            $client->mail = $request->mail;
            $client->customer_TIN = $request->customer_TIN;
            $client->customer_address = $request->customer_address;
            $client->vat_customer_payer = $request->vat_customer_payer;
            $client->company = $request->company;
            $client->save();
        }

        session()->flash('success', 'Client has been created !!');
        return redirect()->route('admin.clients.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('client.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any client !');
        }

        $client = EGRClient::find($id);
        return view('backend.pages.client.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('client.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any client !');
        }

        $client = EGRClient::find($id);

        $request->validate([
            //'customer_name' => 'required|max:100',
            'tp_type' => 'required',
            'telephone' => 'required',
        ]);

        $theUrl = config('app.guzzle_test_url').'/ebms_api/login/';
        $response = Http::post($theUrl, [
            'username'=> config('app.obr_test_username'),
            'password'=> config('app.obr_test_pwd')

        ]);

        $data =  json_decode($response);
        $data2 = ($data->result);
        
    
        $token = $data2->token;

        $tp_TIN = $request->customer_TIN;
        /*
        $clients = EGRClient::all();

        foreach ($clients as $client) {
            if ($client->customer_TIN == $tp_TIN) {
                session()->flash('error', 'Le NIF du Contribuable existe déjà');
                return redirect()->back();
            }elseif ($client->customer_name == $request->customer_name) {
                session()->flash('error', 'Le nom du Contribuable existe déjà');
                return redirect()->back();
            }elseif ($client->telephone == $request->telephone) {
                session()->flash('error', 'Le Numero de telephone du Contribuable existe déjà');
                return redirect()->back();
            }elseif ($client->mail == $request->mail) {
                session()->flash('error', 'Le mail du Contribuable existe déjà');
                return redirect()->back();
            }
        }
        */

        if (empty($tp_TIN) && $request->vat_customer_payer == 1) {
            session()->flash('error', 'Le NIF du client est obligatoire');
            return redirect()->back();
        }elseif (!empty($tp_TIN) && strlen($tp_TIN) < 10) {
            session()->flash('error', 'Le NIF du client n\'existe pas');
            return redirect()->back();
        }

        $theUrl = config('app.guzzle_test_url').'/ebms_api/checkTIN/';
        $response = Http::withHeaders([
        'Authorization' => 'Bearer '.$token,
        'Accept' => 'application/json'])->post($theUrl, [
            'tp_TIN'=>$tp_TIN,

        ]); 

        $data =  json_decode($response);
        $data2 = ($data->result);
        
    
        $success = $data->success;
        $msg = $data->msg;

        
        if ($request->vat_customer_payer == 1 && $request->tp_type == 2) {

            $data3 = ($data2->taxpayer);

            $index_one = ($data3['0']);

            $tp_name = $index_one->tp_name;

            $client->date = $request->date;
            $client->customer_name = $tp_name;
            $client->tp_type = $request->tp_type;
            $client->telephone = $request->telephone;
            $client->mail = $request->mail;
            $client->customer_TIN = $request->customer_TIN;
            $client->customer_address = $request->customer_address;
            $client->vat_customer_payer = $request->vat_customer_payer;
            $client->company = $request->company;
            $client->save();
            session()->flash('success', 'Le client a été créé avec succés !!, OBR Message : '.$msg.'('.$tp_name.')');
            return redirect()->route('admin.clients.index');
        }elseif ($success == false && $request->vat_customer_payer == 1) {

            session()->flash('error', 'Le NIF du Contribuable inconnu');
            return redirect()->back();
        }elseif ($success == false && !empty($tp_TIN) && $request->vat_customer_payer == 0) {

            session()->flash('error', 'Le NIF du Contribuable inconnu');
            return redirect()->back();
        }else{
            $client->date = $request->date;
            $client->customer_name = $request->customer_name;
            $client->tp_type = $request->tp_type;
            $client->telephone = $request->telephone;
            $client->mail = $request->mail;
            $client->customer_TIN = $request->customer_TIN;
            $client->customer_address = $request->customer_address;
            $client->vat_customer_payer = $request->vat_customer_payer;
            $client->company = $request->company;
            $client->save();

        session()->flash('success', 'Client has been updated !!');
        return redirect()->route('admin.clients.index');
       }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('client.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any client !');
        }

        $client = EGRClient::find($id);
        if (!is_null($client)) {
            $client->delete();
        }

        session()->flash('success', 'Client has been deleted !!');
        return back();
    }
}

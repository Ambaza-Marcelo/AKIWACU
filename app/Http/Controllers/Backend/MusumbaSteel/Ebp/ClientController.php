<?php

namespace App\Http\Controllers\Backend\MusumbaSteel\Ebp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\MsEbpClient;
use Excel;

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
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any client !');
        }

        $clients = MsEbpClient::orderBy('customer_name','asc')->get();
        return view('backend.pages.musumba_steel.ebp.client.index', compact('clients'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any client !');
        }

        return view('backend.pages.musumba_steel.ebp.client.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any client !');
        }

        // Validation Data
        $request->validate([
            'customer_name' => 'required|max:100',
            //'mail' => 'required|min:10',
            //'telephone' => 'required',
        ]);

        // Create New MsEbpClient
        $client = new MsEbpClient();
        $client->date = $request->date;
        $client->customer_name = $request->customer_name;
        $client->telephone = $request->telephone;
        $client->mail = $request->mail;
        $client->customer_TIN = $request->customer_TIN;
        $client->customer_address = $request->customer_address;
        $client->vat_customer_payer = $request->vat_customer_payer;
        $client->company = $request->company;
        $client->save();

        session()->flash('success', 'Client has been created !!');
        return redirect()->route('admin.musumba-steel-clients.index');
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.create')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any client !');
        }

        $client = MsEbpClient::find($id);
        $addresses  = Address::all();
        return view('backend.pages.musumba_steel.ebp.client.edit', compact('client', 'addresses'));
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.create')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any client !');
        }

        $client = MsEbpClient::find($id);

        $request->validate([
            'customer_name' => 'required|max:100',
            //'mail' => 'required|min:10',
            //'telephone' => 'required',
        ]);

        // update MsEbpClient
        $client->date = $request->date;
        $client->customer_name = $request->customer_name;
        $client->telephone = $request->telephone;
        $client->mail = $request->mail;
        $client->customer_TIN = $request->customer_TIN;
        $client->customer_address = $request->customer_address;
        $client->vat_customer_payer = $request->vat_customer_payer;
        $client->company = $request->company;
        $client->auteur = $this->user->name;
        $client->save();

        session()->flash('success', 'Client has been updated !!');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.create')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any client !');
        }

        $client = MsEbpClient::find($id);
        if (!is_null($client)) {
            $client->delete();
        }

        session()->flash('success', 'Client has been deleted !!');
        return back();
    }
}

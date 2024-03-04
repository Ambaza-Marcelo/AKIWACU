<?php

namespace App\Http\Controllers\Backend\Sotb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\SotbFuelPump;
use App\Models\SotbFuel;

use Mail;
use App\Mail\DeleteFuelPumpMail;
use App\Mail\EditFuelPumpMail;

class FuelPumpController extends Controller
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
        if (is_null($this->user) || !$this->user->can('sotb_fuel_pump.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any pump !');
        }

        $pumps = SotbFuelPump::all();
        return view('backend.pages.sotb.fuel.pump.index', compact('pumps'));
    }

    public function need()
    {
        if (is_null($this->user) || !$this->user->can('sotb_fuel_pump.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any statement of need !');
        }

        $needs = SotbFuelPump::whereColumn('quantite', '<=','quantite_seuil')->get();
        return view('backend.pages.sotb.fuel.pump.need', compact('needs'));
    }


    public function toPdf()
    {
        if (is_null($this->user) || !$this->user->can('sotb_fuel_pump.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any pump !');
        }

        $datas = SotbFuelPump::all();
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.document.stock_status',compact('datas','dateTime','setting'));

        Storage::put('public/pdf/Etat_pompe/'.$dateTime.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download($dateTime.'.pdf');
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('sotb_fuel_pump.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any fuel_pump !');
        }

        $fuels = SotbFuel::all();
        return view('backend.pages.sotb.fuel.pump.create',compact('fuels'));
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('sotb_fuel_pump.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any fuel_pump !');
        }

        // Validation Data
        $request->validate([
            'name' => 'required|max:20',
            'capacity' => 'required',
            'fuel_id' => 'required',
            'quantity' => 'required'
        ]);

        // Create New SotbFuelPump
        $fuel_pump = new SotbFuelPump();
        $fuel_pump->name = $request->name;
        $pumpCode = strtoupper(substr($fuel_pump->name, 0, 3));
        $fuel_pump->code = $pumpCode.date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);
        $fuel_pump->emplacement = $request->emplacement;
        $fuel_pump->capacity = $request->capacity;
        $fuel_pump->quantity = $request->quantity;
        $fuel_pump->fuel_id = $request->fuel_id;

        $purchase_price = SotbFuel::where('id', $fuel_pump->fuel_id)->value('purchase_price');
        $fuel_pump->purchase_price = $purchase_price;
        $fuel_pump->total_purchase_value = $request->quantity * $purchase_price;
        $fuel_pump->quantite_seuil = $request->quantite_seuil;
        $fuel_pump->etat = 0;
        $fuel_pump->auteur = $this->user->name;
        $fuel_pump->save();
        session()->flash('success', 'FuelPump has been created !!');
        return redirect()->back();
    }

    public function edit($id)
    {
        if (is_null($this->user) || !$this->user->can('sotb_fuel_pump.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any fuel_pump !');
        }

        $fuel_pump = SotbFuelPump::find($id);
        $fuels = SotbFuel::all();
        return view('backend.pages.sotb.fuel.pump.edit', compact('fuel_pump','fuels'));
    }

    public function update(Request $request, $id)
    {
        if (is_null($this->user) || !$this->user->can('sotb_fuel_pump.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any fuel_pump !');
        }

        $fuel_pump = SotbFuelPump::find($id);

         // Validation Data
        $request->validate([
            'name' => 'required|max:20',
            'capacity' => 'required',
            'fuel_id' => 'required',
            'quantity' => 'required'
        ]);

        $fuel_pump->name = $request->name;
        $fuel_pump->emplacement = $request->emplacement;
        $fuel_pump->capacity = $request->capacity;
        $fuel_pump->quantity = $request->quantity;
        $fuel_pump->fuel_id = $request->fuel_id;

        $purchase_price = SotbFuel::where('id', $fuel_pump->fuel_id)->value('purchase_price');
        $fuel_pump->purchase_price = $purchase_price;
        $fuel_pump->total_purchase_value = $request->quantity * $purchase_price;
        $fuel_pump->quantite_seuil = $request->quantite_seuil;
        $fuel_pump->etat = 0;
        $fuel_pump->auteur = $this->user->name;
        $fuel_pump->save();

        session()->flash('success', 'FuelPump has been updated !!');
        return back();
    }


    public function destroy($id)
    {
        if (is_null($this->user) || !$this->user->can('sotb_fuel_pump.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any pump !');
        }

        $pump = SotbFuelPump::find($id);
        if (!is_null($pump)) {
            $pump->delete();

            $email = 'ambazamarcellin2001@gmail.com';
            $auteur = $this->user->name;
            $mailData = [
                    'title' => 'Suppression de cuve de carburant',
                    'email' => $email,
                    'auteur' => $auteur,
                    ];
         
            Mail::to($email)->send(new DeleteFuelPumpMail($mailData));
        }

        session()->flash('success', 'FuelPump has been deleted !!');
        return back();
    }
}

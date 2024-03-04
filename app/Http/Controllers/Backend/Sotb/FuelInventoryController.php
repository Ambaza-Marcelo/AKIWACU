<?php

namespace App\Http\Controllers\Backend\Sotb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Storage;
use App\Models\FuelInventory;
use App\Models\FuelInventoryDetail;
use App\Models\FuelPump;
use App\Models\FuelJauge;
use App\Models\Fuel;
use PDF;
use Validator;
use Excel;

class FuelInventoryController extends Controller
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
        if (is_null($this->user) || !$this->user->can('fuel_inventory.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any inventory !');
        }

        $fuel_inventories = FuelInventory::orderBy('date','desc')->get();

        return view('backend.pages.fuel.fuel_inventory.index', compact('fuel_inventories'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (is_null($this->user) || !$this->user->can('fuel_inventory.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any inventory !');
        }

        $fuel_pumps  = FuelPump::all();
        $fuel_jauges  = FuelJauge::all();
        $datas = FuelPump::where('verified','!=',1)->get();
        return view('backend.pages.fuel.fuel_inventory.create', compact('datas','fuel_pumps','fuel_jauges'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if (is_null($this->user) || !$this->user->can('inventory.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any inventory !');
        }
        $rules = array(
            'fuel_pump_id.*' => 'required',
            'date' => 'required|date',
            'quantite.*' => 'required',
            //'jauge_id.*' => 'required',
            'prix_unitaire.*' => 'required',
            'nouvelle_quantite.*' => 'required',
            'nouveau_prix.*' => 'required',
            'description' => 'required',
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $fuel_pump_id = $request->fuel_pump_id;
            $date = $request->date;
            $jauge_id = $request->jauge_id;
            $quantite = $request->quantite;
            $prix_unitaire = $request->prix_unitaire;
            $nouvelle_quantite = $request->nouvelle_quantite;
            $nouveau_prix = $request->nouveau_prix;
            $title = $request->title;  
            $bon_no = "BI000".date("y").substr(number_format(time() * mt_rand(), 0, '', ''), 0, 6);
            $auteur = $this->user->name;
            $description =$request->description; 

            for( $count = 0; $count < count($fuel_pump_id); $count++ ){
                $valeur_totale = $quantite[$count] * $prix_unitaire[$count];
                $nouvelle_valeur_totale = $nouvelle_quantite[$count] * $nouveau_prix[$count];
                $relica = $quantite[$count] - $nouvelle_quantite[$count];

                if (!empty($jauge_id[$count])) {
                    $data = array(
                    'fuel_pump_id' => $fuel_pump_id[$count],
                    'date' => $date,
                    'title' => $title,
                    'quantite' => $quantite[$count],
                    'jauge_id' => $jauge_id[$count],
                    'prix_unitaire' => $prix_unitaire[$count],
                    'valeur_totale' => $valeur_totale,
                    'nouvelle_quantite' => $nouvelle_quantite[$count],
                    'nouveau_prix' => $nouveau_prix[$count],
                    'nouvelle_valeur_totale' => $nouvelle_valeur_totale,
                    'relica' => $relica,
                    'inventory_no' => $bon_no,
                    'auteur' => $auteur,
                    'description' => $description,
                    'created_at' => \Carbon\Carbon::now()
                );
                }else{
                    $data = array(
                    'fuel_pump_id' => $fuel_pump_id[$count],
                    'date' => $date,
                    'title' => $title,
                    'quantite' => $quantite[$count],
                    //'jauge_id' => $jauge_id[$count],
                    'prix_unitaire' => $prix_unitaire[$count],
                    'valeur_totale' => $valeur_totale,
                    'nouvelle_quantite' => $nouvelle_quantite[$count],
                    'nouveau_prix' => $nouveau_prix[$count],
                    'nouvelle_valeur_totale' => $nouvelle_valeur_totale,
                    'relica' => $relica,
                    'inventory_no' => $bon_no,
                    'auteur' => $auteur,
                    'description' => $description,
                    'created_at' => \Carbon\Carbon::now()
                );
                }
                
                $insert_data[] = $data;
                /*

                $valeurStockInitial = FuelPump::where('id', $fuel_pump_id[$count])->value('valeur_totale');

                $valeurAcquisition = $nouvelle_quantite[$count] * $nouveau_prix[$count];

                $valeurTotalUnite = $nouvelle_quantite[$count] + $quantite[$count];
                $cump = ($valeurStockInitial + $valeurAcquisition) / $valeurTotalUnite;

                $calcul_cump = array(
                        'prix_unitaire' => $cump,
                    );*/
                /*FuelPump::where('id',$fuel_pump_id[$count])
                        ->update($calcul_cump);*/
                        /*
                    $carburant = array(
                        'id' => $fuel_pump_id[$count],
                        'quantite' => $nouvelle_quantite[$count],
                        'valeur_totale' => $cump * $nouvelle_quantite[$count],
                        'prix_unitaire' => $nouveau_prix[$count],
                        'auteur' => $this->user->name,
                        'verified' => true,
                    );

                    FuelPump::where('id',$fuel_pump_id[$count])
                        ->update($carburant);
                        */
                
            }
            FuelInventoryDetail::insert($insert_data);
            $fuel_inventory = new FuelInventory();
            $fuel_inventory->date = $date;
            $fuel_inventory->title = $title;
            $fuel_inventory->inventory_no = $bon_no;
            $fuel_inventory->description = $description;
            $fuel_inventory->auteur = $auteur;
            $fuel_inventory->save();
         
        session()->flash('success', 'FuelInventory has been created !!');
        return redirect()->route('admin.fuel_inventories.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $bon_no
     * @return \Illuminate\Http\Response
     */
    public function show($bon_no)
    {
        //
        $code = FuelInventoryDetail::where('inventory_no', $bon_no)->value('inventory_no');
        $fuel_inventories = FuelInventoryDetail::where('inventory_no', $bon_no)->get();
        return view('backend.pages.fuel.fuel_inventory.show', compact('fuel_inventories','code'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($bon_no)
    {
        if (is_null($this->user) || !$this->user->can('fuel_inventory.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any inventory !');
        }

        $bon_no  = FuelInventoryDetail::where('inventory_no', $bon_no)->value('inventory_no');
        $fuel_inventory = FuelInventory::where('inventory_no', $bon_no)->first();
        $datas = FuelInventoryDetail::where('inventory_no', $bon_no)->get();
        $fuel_jauges  = FuelJauge::all();
        $fuel_pumps  = FuelPump::all();
        return view('backend.pages.fuel.fuel_inventory.edit', compact('fuel_inventory', 'bon_no','datas','fuel_jauges','fuel_pumps'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $inventory_no)
    {
        if (is_null($this->user) || !$this->user->can('fuel_inventory.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any inventory !');
        }

        $rules = array(
            'fuel_pump_id.*' => 'required',
            'date' => 'required|date',
            'quantite.*' => 'required',
            //'jauge_id.*' => 'required',
            'prix_unitaire.*' => 'required',
            'nouvelle_quantite.*' => 'required',
            'nouveau_prix.*' => 'required',
            'description' => 'required',
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $fuel_pump_id = $request->fuel_pump_id;
            $date = $request->date;
            $jauge_id = $request->jauge_id;
            $quantite = $request->quantite;
            $prix_unitaire = $request->prix_unitaire;
            $nouvelle_quantite = $request->nouvelle_quantite;
            $nouveau_prix = $request->nouveau_prix;
            $title = $request->title;  
            $auteur = $this->user->name;
            $description =$request->description; 

            for( $count = 0; $count < count($fuel_pump_id); $count++ ){
                $valeur_totale = $quantite[$count] * $prix_unitaire[$count];
                $nouvelle_valeur_totale = $nouvelle_quantite[$count] * $nouveau_prix[$count];
                $relica = $nouvelle_quantite[$count] - $quantite[$count];

                if (!empty($jauge_id[$count])) {
                    $data = array(
                    'fuel_pump_id' => $fuel_pump_id[$count],
                    'date' => $date,
                    'title' => $title,
                    'quantite' => $quantite[$count],
                    'jauge_id' => $jauge_id[$count],
                    'prix_unitaire' => $prix_unitaire[$count],
                    'valeur_totale' => $valeur_totale,
                    'nouvelle_quantite' => $nouvelle_quantite[$count],
                    'nouveau_prix' => $nouveau_prix[$count],
                    'nouvelle_valeur_totale' => $nouvelle_valeur_totale,
                    'relica' => $relica,
                    'auteur' => $auteur,
                    'description' => $description,
                    'updated_at' => \Carbon\Carbon::now()
                );
                }else{
                    $data = array(
                    'fuel_pump_id' => $fuel_pump_id[$count],
                    'date' => $date,
                    'title' => $title,
                    'quantite' => $quantite[$count],
                    //'jauge_id' => $jauge_id[$count],
                    'prix_unitaire' => $prix_unitaire[$count],
                    'valeur_totale' => $valeur_totale,
                    'nouvelle_quantite' => $nouvelle_quantite[$count],
                    'nouveau_prix' => $nouveau_prix[$count],
                    'nouvelle_valeur_totale' => $nouvelle_valeur_totale,
                    'relica' => $relica,
                    'auteur' => $auteur,
                    'description' => $description,
                    'updated_at' => \Carbon\Carbon::now()
                );
                }
                
                $insert_data[] = $data;


                $valeurStockInitial = FuelPump::where('id', $fuel_pump_id[$count])->value('valeur_totale');

                $valeurAcquisition = $nouvelle_quantite[$count] * $nouveau_prix[$count];

                $valeurTotalUnite = $nouvelle_quantite[$count] + $quantite[$count];
                $cump = ($valeurStockInitial + $valeurAcquisition) / $valeurTotalUnite;

                $calcul_cump = array(
                        'prix_unitaire' => $cump,
                    );

                    $carburant = array(
                        'id' => $fuel_pump_id[$count],
                        'quantite' => $nouvelle_quantite[$count],
                        'valeur_totale' => $cump * $nouvelle_quantite[$count],
                        'prix_unitaire' => $nouveau_prix[$count],
                        'auteur' => $this->user->name,
                        'verified' => true,
                    );

                    FuelPump::where('id',$fuel_pump_id[$count])
                        ->update($carburant);
                    FuelInventoryDetail::where('inventory_no', $inventory_no)->where('fuel_pump_id',$fuel_pump_id[$count])
                        ->update($data);
                
            }
            $fuel_inventory = FuelInventory::where('inventory_no', $inventory_no)->first();
            $fuel_inventory->date = $date;
            $fuel_inventory->title = $title;
            $fuel_inventory->description = $description;
            $fuel_inventory->auteur = $auteur;
            $fuel_inventory->save();


        session()->flash('success', 'FuelInventory has been updated !!');
        return back();
    }

    public function bon_inventaire($inventory_no)
    {
        if (is_null($this->user) || !$this->user->can('fuel_inventory.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();
        $description = FuelInventory::where('inventory_no', $inventory_no)->value('description');
        $title = FuelInventory::where('inventory_no', $inventory_no)->value('title');
        $code = FuelInventory::where('inventory_no', $inventory_no)->value('inventory_no');
        $date = FuelInventory::where('inventory_no', $inventory_no)->value('date');
        $datas = FuelInventoryDetail::where('inventory_no', $inventory_no)->get();


        $totalValueActuelle = DB::table('fuel_inventory_details')
            ->where('inventory_no', '=', $inventory_no)
            ->sum('valeur_totale');
         $totalValueNew = DB::table('fuel_inventory_details')
            ->where('inventory_no', '=', $inventory_no)
            ->sum('nouvelle_valeur_totale');
        $gestionnaire = FuelInventory::where('inventory_no', $inventory_no)->value('auteur');
        $pdf = PDF::loadView('backend.pages.fuel.documents.bon_inventaire',compact('datas','code','totalValueActuelle','totalValueNew','gestionnaire','setting','title','description','date'))->setPaper('a4', 'landscape');

        Storage::put('public/carburant/bon_inventaire/'.$inventory_no.'.pdf', $pdf->output());

        // download pdf file
        return $pdf->download($inventory_no.'.pdf');
    }

    public function get_inventory_data()
    {
        return Excel::download(new InventoryExport, 'inventories.xlsx');
    }


    public function validateInventory($inventory_no)
    {
       if (is_null($this->user) || !$this->user->can('inventory.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any inventory !');
        }

        $datas = FuelInventoryDetail::where('inventory_no', $inventory_no)->get();

        foreach($datas as $data){
            $valeurStockInitial = FuelPump::where('id', $data->fuel_pump_id)->value('valeur_totale');

                $valeurAcquisition = $data->nouvelle_quantite * $data->nouveau_prix;

                $valeurTotalUnite = $data->nouvelle_quantite + $data->quantite;
                $cump = ($valeurStockInitial + $valeurAcquisition) / $valeurTotalUnite;

                $calcul_cump = array(
                        'prix_unitaire' => $cump,
                    );
                $fuel_calc = array(
                        'prix_unitaire' => $data->nouveau_prix
                    );

                $fuel_id = FuelPump::where('id', $data->fuel_pump_id)->value('fuel_id');
                Fuel::where('id',$fuel_id)
                        ->update($fuel_calc);

                    $carburant = array(
                        'id' => $data->fuel_pump_id,
                        'quantite' => $data->nouvelle_quantite,
                        'valeur_totale' => $data->nouveau_prix * $data->nouvelle_quantite,
                        'prix_unitaire' => $data->nouveau_prix,
                        'auteur' => $this->user->name,
                        'verified' => true,
                    );

                    FuelPump::where('id',$data->fuel_pump_id)
                        ->update($carburant);
        }

            FuelInventory::where('inventory_no', '=', $inventory_no)
                ->update(['etat' => 2]);

        session()->flash('success', 'inventory has been validated !!');
        return back();
    }

    public function rejectInventory($inventory_no)
    {
       if (is_null($this->user) || !$this->user->can('inventory.reject')) {
            abort(403, 'Sorry !! You are Unauthorized to reject any inventory !');
        }
            FuelInventory::where('inventory_no', '=', $inventory_no)
                ->update(['etat' => 1]);

        session()->flash('success', 'inventory has been rejected !!');
        return back();
    }

    public function resetInventory($inventory_no)
    {
       if (is_null($this->user) || !$this->user->can('inventory.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any inventory !');
        }
            FuelInventory::where('inventory_no', '=', $inventory_no)
                ->update(['etat' => 0]);

        session()->flash('success', 'inventory has been reseted !!');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($inventory_no)
    {
        if (is_null($this->user) || !$this->user->can('inventory.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any inventory !');
        }

        $inventory = FuelInventory::where('inventory_no', $inventory_no)->first();
        if (!is_null($inventory)) {
            $inventory->delete();
        }

        session()->flash('success', 'FuelInventory has been deleted !!');
        return back();
    }
}

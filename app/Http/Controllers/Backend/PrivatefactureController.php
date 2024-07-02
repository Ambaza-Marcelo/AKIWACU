<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use PDF;
use Excel;
use Mail;
use Validator;
use App\Models\PrivateStoreItem;
use App\Models\PrivateFacture;
use App\Models\PrivateFactureDetail;

class PrivatefactureController extends Controller
{
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
        if (is_null($this->user) || !$this->user->can('private_drink_stockout.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factures = PrivateFacture::orderBy('id','desc')->get();
        return view('backend.pages.private_invoice.index',compact('factures'));
    }

    public function create()
    {
        if (is_null($this->user) || !$this->user->can('private_drink_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $articles =  PrivateStoreItem::orderBy('name','asc')->get();

        return view('backend.pages.private_invoice.create',compact('articles','setting'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request  $request)
    {
        if (is_null($this->user) || !$this->user->can('private_drink_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice !');
        }

        $rules = array(
                'invoice_date' => 'required',
                'tp_type' => 'required',
                'tp_name' => 'required|max:100|min:3',
                'tp_TIN' => 'required|max:30|min:4',
                'tp_trade_number' => 'required|max:20|min:4',
                'tp_phone_number' => 'required|max:20|min:6',
                'tp_address_commune' => 'required|max:50|min:5',
                'tp_address_quartier' => 'required|max:50|min:5',
                'private_store_item_id.*'  => 'required',
                'item_quantity.*'  => 'required',
                //'item_price.*'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $private_store_item_id = $request->private_store_item_id;
            $item_quantity = $request->item_quantity;
            //$item_price = $request->item_price;
            $item_ct = 0;
            $item_tl =0; 

            $latest = PrivateFacture::orderBy('id','desc')->first();
            if ($latest) {
               $invoice_number = 'FN' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $invoice_number = 'FN' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }
            


            

            $invoice_signature = $request->tp_TIN."/ws400171161500565/".Carbon::parse($request->invoice_date)->format('YmdHis')."/".$invoice_number;

        for( $count = 0; $count < count($private_store_item_id); $count++ )
        {
            $taux_tva = PrivateStoreItem::where('id', $private_store_item_id[$count])->value('vat');

            $item_price = PrivateStoreItem::where('id', $private_store_item_id[$count])->value('selling_price');

            if ($item_price <= 0) {
                session()->flash('error', 'Le prix de vente est invalide');
                return back();
            }

            if($request->vat_taxpayer == 1){
                    $item_total_amount = ($item_price*$item_quantity[$count]);
                    
                    $item_price_nvat = $item_total_amount;
                    $vat = 0;
                    $item_price_wvat = ($item_price_nvat + $vat); 

            }else{
                $item_price_nvat = ($item_price*$item_quantity[$count]);
                $vat = 0;
                $item_price_wvat = ($item_price_nvat + $vat);
                $item_total_amount = $item_price_wvat ;
            }

          $data = array(
            'invoice_number'=>$invoice_number,
            'invoice_date'=> $request->invoice_date,
            'tp_type'=>$request->tp_type,
            'tp_name'=>$request->tp_name,
            'tp_TIN'=>$request->tp_TIN,
            'tp_trade_number'=>$request->tp_trade_number,
            'tp_phone_number'=>$request->tp_phone_number,
            'tp_address_province'=>$request->tp_address_province,
            'tp_address_commune'=>$request->tp_address_commune,
            'tp_address_quartier'=>$request->tp_address_quartier,
            'tp_address_avenue'=>$request->tp_address_avenue,
            'tp_address_rue'=>$request->tp_address_rue,
            'vat_taxpayer'=>$request->vat_taxpayer,
            'ct_taxpayer'=>$request->ct_taxpayer,
            'tl_taxpayer'=>$request->tl_taxpayer,
            'tp_fiscal_center'=>$request->tp_fiscal_center,
            'tp_activity_sector'=>$request->tp_activity_sector,
            'tp_legal_form'=>$request->tp_legal_form,
            'payment_type'=>$request->payment_type,
            'customer_name'=>$request->customer_name,
            'customer_TIN'=>$request->customer_TIN,
            'customer_address'=>$request->customer_address,
            'invoice_signature'=> $invoice_signature,
            'cancelled_invoice_ref'=>$request->cancelled_invoice_ref,
            'cancelled_invoice'=>$request->cancelled_invoice,
            'invoice_currency'=>$request->invoice_currency,
            'invoice_ref'=>$request->invoice_ref,
            'auteur' => $this->user->name,
            'invoice_signature_date'=> Carbon::now(),
            'private_store_item_id'=>$private_store_item_id[$count],
            'item_quantity'=>$item_quantity[$count],
            'item_price'=>$item_price,
            'item_price_nvat'=>$item_price_nvat,
            'vat'=>$vat,
            'item_price_wvat'=>$item_price_wvat,
            'item_total_amount'=>$item_total_amount,
        );
          $data1[] = $data;
      }


        PrivateFactureDetail::insert($data1);

            //create facture
            $facture = new PrivateFacture();
            $facture->invoice_date = $request->invoice_date;
            $facture->invoice_number = $invoice_number;
            $facture->invoice_date =  $request->invoice_date;
            $facture->tp_type = $request->tp_type;
            $facture->tp_name = $request->tp_name;
            $facture->tp_TIN = $request->tp_TIN;
            $facture->tp_trade_number = $request->tp_trade_number;
            $facture->tp_phone_number = $request->tp_phone_number;
            $facture->tp_address_province = $request->tp_address_province;
            $facture->tp_address_commune = $request->tp_address_commune;
            $facture->tp_address_quartier = $request->tp_address_quartier;
            $facture->vat_taxpayer = $request->vat_taxpayer;
            $facture->ct_taxpayer = $request->ct_taxpayer;
            $facture->tl_taxpayer = $request->tl_taxpayer;
            $facture->tp_fiscal_center = $request->tp_fiscal_center;
            $facture->tp_activity_sector = $request->tp_activity_sector;
            $facture->tp_legal_form = $request->tp_legal_form;
            $facture->invoice_currency = $request->invoice_currency;
            $facture->payment_type = $request->payment_type;
            $facture->customer_name = $request->customer_name;
            $facture->customer_TIN = $request->customer_TIN;
            $facture->customer_address = $request->customer_address;
            $facture->invoice_signature = $invoice_signature;
            $facture->cancelled_invoice_ref = $request->cancelled_invoice_ref;
            $facture->cancelled_invoice = $request->cancelled_invoice;
            $facture->invoice_ref = $request->invoice_ref;
            $facture->auteur = $this->user->name;
            $facture->invoice_signature_date = Carbon::now();
            $facture->save();

            session()->flash('success', 'Le vente est fait avec succés!!');
            return redirect()->route('admin.private-factures.index');
    }

    public function validerFacture($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('private_drink_stockout.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }
        
        $datas = PrivateFactureDetail::where('invoice_number', $invoice_number)->get();

        foreach($datas as $data){
            $valeurStockInitial = PrivateStoreItem::where('id', $data->private_store_item_id)->value('total_cump_value');
            $quantityStockInitial = PrivateStoreItem::where('id', $data->private_store_item_id)->value('quantity');

            $quantityRestant = $quantityStockInitial - $data->item_quantity;
                
                    $donnees = array(
                        'id' => $data->private_store_item_id,
                        'quantity' => $quantityRestant,
                        'total_cump_value' => $quantityRestant * $data->item_price,
                        'total_purchase_value' => $quantityRestant * $data->item_price,
                        'created_by' => $this->user->name,
                        'verified' => true
                    );
                    
                    if ($data->item_quantity <= $quantityStockInitial) {
                        
                        PrivateStoreItem::where('id',$data->private_store_item_id)
                        ->update($donnees);
                        $flag = 0;
                        
                    }else{

                        foreach ($datas as $data) {
                            $valeurStockInitial = PrivateStoreItem::where('id', $data->private_store_item_id)->value('total_cump_value');
                            $quantityStockInitial = PrivateStoreItem::where('id', $data->private_store_item_id)->where('verified',true)->value('quantity');

                            $quantityTotal = $quantityStockInitial + $data->item_quantity;
                      
                
                            $returnData = array(
                                'id' => $data->private_store_item_id,
                                'quantity' => $quantityTotal,
                                'total_cump_value' => $quantityTotal * $cump,
                                'total_purchase_value' => $quantityTotal * $cump,
                                'created_by' => $this->user->name,
                                'verified' => false
                            );

                            $status = PrivateStoreItem::where('id', $data->private_store_item_id)->value('verified');
                    
                        
                                PrivateStoreItem::where('id',$data->private_store_item_id)->where('verified',true)
                                ->update($returnData);
                                $flag = 1;
                        }

                        PrivateStoreItem::where('id','!=','')->update(['verified' => false]);

                        session()->flash('error', $this->user->name.' ,why do you want selling a quantity that you do not have!');
                        return redirect()->back();
                    }
        }
        
        PrivateStoreItem::where('id','!=','')->update(['verified' => false]);
        PrivateFacture::where('invoice_number', '=', $invoice_number)
            ->update(['etat' => 1,'statut_paied' => '0','validated_by' => $this->user->name]);
        PrivateFactureDetail::where('invoice_number', '=', $invoice_number)
            ->update(['etat' => 1,'statut_paied' => '0','validated_by' => $this->user->name]);

        session()->flash('success', 'La Facture  est validée avec succés');
        return back();
        

    }

    public function validerFactureCredit(Request  $request,$invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('private_drink_stockout.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }
        
        $request->validate([
            'customer_name' => 'required'
        ]);

        $customer_name = $request->customer_name;

        $datas = PrivateFactureDetail::where('invoice_number', $invoice_number)->get();

        foreach($datas as $data){
            $valeurStockInitial = PrivateStoreItem::where('id', $data->private_store_item_id)->value('total_cump_value');
            $quantityStockInitial = PrivateStoreItem::where('id', $data->private_store_item_id)->value('quantity');

            $quantityRestant = $quantityStockInitial - $data->item_quantity;
                     
                
                    $donnees = array(
                        'id' => $data->private_store_item_id,
                        'quantity' => $quantityRestant,
                        'total_cump_value' => $quantityRestant * $data->item_price,
                        'total_purchase_value' => $quantityRestant * $data->item_price,
                        'created_by' => $this->user->name,
                        'verified' => true
                    );
                    
                    if ($data->item_quantity <= $quantityStockInitial) {
                        
                        PrivateStoreItem::where('id',$data->private_store_item_id)
                        ->update($donnees);
                        $flag = 0;
                        
                    }else{

                        foreach ($datas as $data) {
                            $valeurStockInitial = PrivateStoreItem::where('id', $data->private_store_item_id)->value('total_cump_value');
                            $quantityStockInitial = PrivateStoreItem::where('id', $data->private_store_item_id)->where('verified',true)->value('quantity');

                            $quantityTotal = $quantityStockInitial + $data->item_quantity;
                      
                
                            $returnData = array(
                                'id' => $data->private_store_item_id,
                                'quantity' => $quantityTotal,
                                'total_cump_value' => $quantityTotal * $cump,
                                'total_purchase_value' => $quantityTotal * $cump,
                                'created_by' => $this->user->name,
                                'verified' => false
                            );

                            $status = PrivateStoreItem::where('id', $data->private_store_item_id)->value('verified');
                    

                        
                                PrivateStoreItem::where('id',$data->private_store_item_id)->where('verified',true)
                                ->update($returnData);
                                $flag = 1;
                            
                        }

                        PrivateStoreItem::where('id','!=','')->update(['verified' => false]);

                        session()->flash('error', $this->user->name.' ,why do you want selling a quantity that you do not have!');
                        return redirect()->back();
                    }
        }

        $item_total_amount = DB::table('private_facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');
        
        PrivateStoreItem::where('id','!=','')->update(['verified' => false]);

        PrivateFacture::where('invoice_number', '=', $invoice_number)
            ->update(['etat' => '01','etat_recouvrement' => '0','montant_total_credit' => $item_total_amount,'statut_paied' => '0','customer_name' => $customer_name,'validated_by' => $this->user->name]);
        PrivateFactureDetail::where('invoice_number', '=', $invoice_number)
            ->update(['etat' => '01','etat_recouvrement' => '0','montant_total_credit' => $item_total_amount,'statut_paied' => '0','customer_name' => $customer_name,'validated_by' => $this->user->name]);

        session()->flash('success', 'La Facture  est validée avec succés');
        return redirect()->route('admin.private-factures.index');
        
        
    }

    public function annulerFacture(Request $request,$invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('private_drink_stockout.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any invoice !');
        }


        $request->validate([
            'cn_motif' => 'required|min:10|max:500'
        ]);

        $cn_motif = $request->cn_motif;

        $invoice_signature = PrivateFacture::where('invoice_number', $invoice_number)->value('invoice_signature');

        $datas = PrivateFactureDetail::where('invoice_number', $invoice_number)->get();
        $facture = PrivateFactureDetail::where('invoice_number', $invoice_number)->first();

            PrivateFacture::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => -1,'statut' => -1,'cn_motif' => $cn_motif,'reseted_by' => $this->user->name]);
            PrivateFactureDetail::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => -1,'statut' => -1,'cn_motif' => $cn_motif,'reseted_by' => $this->user->name]);
             
            $email1 = 'ambazamarcellin2001@gmail.com';
            //$email2 = 'frangiye@gmail.com';
            //$email3 = 'khaembamartin@gmail.com';
            //$email4 = 'munyembari_mp@yahoo.fr';
            $auteur = $this->user->name;
            $mailData = [
                    'title' => 'Système de facturation électronique, Akiwacu',
                    'invoice_number' => $invoice_number,
                    'auteur' => $auteur,
                    'cn_motif' => $cn_motif,
                    ];
         
            Mail::to($email1)->send(new InvoiceResetedMail($mailData));
            //Mail::to($email2)->send(new InvoiceResetedMail($mailData));
            //Mail::to($email3)->send(new InvoiceResetedMail($mailData));
            //Mail::to($email4)->send(new InvoiceResetedMail($mailData));
            
            
            session()->flash('success', 'La Facture  est annulée avec succés');
            return back();

    }


    public function facture($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('private_drink_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized!');
        }


        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $datas = PrivateFactureDetail::where('invoice_number', $invoice_number)->get();
        $facture = PrivateFacture::where('invoice_number', $invoice_number)->first();
        $invoice_signature = PrivateFacture::where('invoice_number', $invoice_number)->value('invoice_signature');
        $data = PrivateFacture::where('invoice_number', $invoice_number)->first();
        $totalValue = DB::table('private_facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_price_nvat');
        $item_total_amount = DB::table('private_facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');
        $totalVat = DB::table('private_facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('vat');
        $client = PrivateFacture::where('invoice_number', $invoice_number)->value('customer_name');
        $date = PrivateFacture::where('invoice_number', $invoice_number)->value('invoice_date');
       
        $pdf = PDF::loadView('backend.pages.private_invoice.facture',compact('datas','invoice_number','totalValue','item_total_amount','client','setting','date','data','invoice_signature','facture','totalVat'))->setPaper('a6', 'portrait');

        Storage::put('public/privatefactures/'.$invoice_number.'.pdf', $pdf->output());


        $factures = PrivateFacture::where('invoice_number', $invoice_number)->get();

        $datas = PrivateFactureDetail::where('invoice_number', $invoice_number)->get();

        PrivateFacture::where('invoice_number', '=', $invoice_number)
                ->update(['statut' => 1]);
            PrivateFactureDetail::where('invoice_number', '=', $invoice_number)
                ->update(['statut' => 1]);

            // download pdf file
        return $pdf->download('FACTURE_'.$invoice_number.'.pdf');

        
    }

    public function voirFactureAnnuler($invoice_number){
        $facture = PrivateFactureDetail::where('invoice_number',$invoice_number)->first();
        $datas = PrivateFactureDetail::where('invoice_number', $invoice_number)->get();
        $clients =  Client::orderBy('customer_name','asc')->get();
        return view('backend.pages.private_invoice.reset', compact('facture','datas','clients'));
    }

    public function voirFactureCredit($invoice_number){
        $facture = PrivateFactureDetail::where('invoice_number',$invoice_number)->first();
        $datas = PrivateFactureDetail::where('invoice_number', $invoice_number)->get();
        return view('backend.pages.private_invoice.credit', compact('facture','datas'));
    }

    public function voirFactureRecouvrer($invoice_number){
        $facture = PrivateFactureDetail::where('invoice_number',$invoice_number)->first();
        $datas = PrivateFactureDetail::where('invoice_number', $invoice_number)->get();

        $total_amount = DB::table('private_facture_details')
            ->where('invoice_number',$invoice_number)->where('etat','01')->sum('item_total_amount');
        $r_credit = DB::table('private_factures')
            ->where('invoice_number',$invoice_number)->where('etat','01')->sum('reste_credit');
        if (!empty($r_credit)) {
            $reste_credit = $r_credit;
        }else{
            $reste_credit = $total_amount;
        }

        $montant_recouvre = DB::table('private_factures')
            ->where('invoice_number',$invoice_number)->where('etat','01')->sum('montant_recouvre');

        return view('backend.pages.private_invoice.recouvrement', compact('facture','datas','
            reste_credit','montant_recouvre'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $invoice_number
     * @return \Illuminate\Http\Response
     */
    public function show($invoice_number)
    {
        //
        if (is_null($this->user) || !$this->user->can('private_drink_stockout.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice !');
        }

        $factureDetails = PrivateFactureDetail::where('invoice_number',$invoice_number)->get();
        $facture = PrivateFacture::where('invoice_number',$invoice_number)->first();
        $total_amount = DB::table('private_facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');
        return view('backend.pages.private_invoice.show',compact('facture','factureDetails','total_amount'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $invoice_number
     * @return \Illuminate\Http\Response
     */

    public function edit($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('private_drink_stockout.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any invoice !');
        }

        $invoice_number = PrivateFacture::where('invoice_number',$invoice_number)->value('invoice_number');

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $articles =  PrivateStoreItem::orderBy('name','asc')->get();
        $datas =  PrivateFactureDetail::where('invoice_number',$invoice_number)->get();

        $data =  PrivateFacture::where('invoice_number',$invoice_number)->first();
        return view('backend.pages.private_invoice.edit',compact('articles','data','setting','datas','invoice_number'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $invoice_number
     * @return \Illuminate\Http\Response
     */
    public function update(Request  $request,$invoice_number)
    {

        if (is_null($this->user) || !$this->user->can('private_drink_stockout.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit any invoice !');
        }

        $rules = array(
                'invoice_date' => 'required',
                'tp_type' => 'required',
                'tp_name' => 'required|max:100|min:3',
                'tp_TIN' => 'required|max:30|min:4',
                'tp_trade_number' => 'required|max:20|min:4',
                'tp_phone_number' => 'required|max:20|min:6',
                'tp_address_commune' => 'required|max:50|min:5',
                'tp_address_quartier' => 'required|max:50|min:5',
                'private_store_item_id.*'  => 'required',
                'item_quantity.*'  => 'required',
                'item_ct.*'  => 'required',
                'item_tl.*'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $private_store_item_id = $request->private_store_item_id;
            $item_quantity = $request->item_quantity;
            $item_price = $request->item_price;
            $item_ct = $request->item_ct;
            $item_tl =$request->item_tl; 

            $employe_id = $request->employe_id;

        for( $count = 0; $count < count($private_store_item_id); $count++ )
        {
            $taux_tva = PrivateStoreItem::where('id', $private_store_item_id[$count])->value('vat');

            if($request->vat_taxpayer == 1){

                    $item_total_amount = ($item_price[$count]*$item_quantity[$count]);
                    
                    $item_price_nvat = $item_total_amount;
                    $vat = 0;
                    $item_price_wvat = ($item_price_nvat + $vat); 

            }else{
                $item_price_nvat = ($item_price[$count]*$item_quantity[$count])+$item_ct[$count];
                $vat = 0;
                $item_price_wvat = ($item_price_nvat + $vat);
                $item_total_amount = $item_price_wvat + $item_tl[$count];
            }

          $data = array(
            'invoice_date'=> $request->invoice_date,
            'invoice_number'=> $invoice_number,
            'tp_type'=>$request->tp_type,
            'tp_name'=>$request->tp_name,
            'tp_TIN'=>$request->tp_TIN,
            'tp_trade_number'=>$request->tp_trade_number,
            'tp_phone_number'=>$request->tp_phone_number,
            'tp_address_province'=>$request->tp_address_province,
            'tp_address_commune'=>$request->tp_address_commune,
            'tp_address_quartier'=>$request->tp_address_quartier,
            'tp_address_avenue'=>$request->tp_address_avenue,
            'tp_address_rue'=>$request->tp_address_rue,
            'vat_taxpayer'=>$request->vat_taxpayer,
            'ct_taxpayer'=>$request->ct_taxpayer,
            'tl_taxpayer'=>$request->tl_taxpayer,
            'tp_fiscal_center'=>$request->tp_fiscal_center,
            'tp_activity_sector'=>$request->tp_activity_sector,
            'tp_legal_form'=>$request->tp_legal_form,
            'payment_type'=>$request->payment_type,
            'customer_name'=>$request->customer_name,
            'customer_TIN'=>$request->customer_TIN,
            'customer_address'=>$request->customer_address,
            'cancelled_invoice_ref'=>$request->cancelled_invoice_ref,
            'cancelled_invoice'=>$request->cancelled_invoice,
            'invoice_currency'=>$request->invoice_currency,
            'invoice_ref'=>$request->invoice_ref,
            'invoice_signature_date'=> Carbon::now(),
            'private_store_item_id'=>$private_store_item_id[$count],
            'item_quantity'=>$item_quantity[$count],
            'item_price'=>$item_price[$count],
            'item_ct'=>$item_ct[$count],
            'item_tl'=>$item_tl[$count],
            'item_price_nvat'=>$item_price_nvat,
            'vat'=>$vat,
            'item_price_wvat'=>$item_price_wvat,
            'item_total_amount'=>$item_total_amount,
        );

          $data1[] = $data;

          PrivateFactureDetail::where('invoice_number',$invoice_number)->delete();
      }

      PrivateFactureDetail::insert($data1);

            //create facture
            $facture = PrivateFacture::where('invoice_number',$invoice_number)->first();
            $facture->invoice_date = $request->invoice_date;
            $facture->invoice_date =  $request->invoice_date;
            $facture->tp_type = $request->tp_type;
            $facture->tp_name = $request->tp_name;
            $facture->tp_TIN = $request->tp_TIN;
            $facture->tp_trade_number = $request->tp_trade_number;
            $facture->tp_phone_number = $request->tp_phone_number;
            $facture->tp_address_province = $request->tp_address_province;
            $facture->tp_address_commune = $request->tp_address_commune;
            $facture->tp_address_quartier = $request->tp_address_quartier;
            $facture->drink_order_no = $request->drink_order_no;
            $facture->vat_taxpayer = $request->vat_taxpayer;
            $facture->ct_taxpayer = $request->ct_taxpayer;
            $facture->tl_taxpayer = $request->tl_taxpayer;
            $facture->tp_fiscal_center = $request->tp_fiscal_center;
            $facture->tp_activity_sector = $request->tp_activity_sector;
            $facture->tp_legal_form = $request->tp_legal_form;
            $facture->invoice_currency = $request->invoice_currency;
            $facture->payment_type = $request->payment_type;
            $facture->customer_name = $request->customer_name;
            $facture->customer_TIN = $request->customer_TIN;
            $facture->customer_address = $request->customer_address;
            $facture->cancelled_invoice_ref = $request->cancelled_invoice_ref;
            $facture->cancelled_invoice = $request->cancelled_invoice;
            $facture->invoice_ref = $request->invoice_ref;
            $facture->save();

            session()->flash('success', 'Le facture est modifié avec succés!!');
            return redirect()->route('admin.private-factures.index');

    }

    public function recouvrement(Request  $request,$invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('private_drink_stockout.create')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice !');
        }

        $request->validate([
            'customer_name' => 'required',
            'statut_paied' => 'required',
            'etat_recouvrement' => 'required',
            'date_recouvrement' => 'required',
            'nom_recouvrement' => 'required',
            'note_recouvrement' => 'required',
            'montant_total_credit' => 'required',
            'montant_recouvre' => 'required',
        ]);


        $customer_name = $request->customer_name;
        $statut_paied = $request->statut_paied;
        $customer_address = $request->customer_address;
        $customer_TIN = $request->customer_TIN;
        $etat_recouvrement = $request->etat_recouvrement;
        $date_recouvrement = $request->date_recouvrement;
        $nom_recouvrement = $request->nom_recouvrement;
        $note_recouvrement = $request->note_recouvrement;
        $bank_name = $request->bank_name;
        $cheque_no = $request->cheque_no;
        $montant_total_credit = $request->montant_total_credit;
        $montant_recouvre_input = $request->montant_recouvre;

        $montant_recouvre = DB::table('factures')
            ->where('invoice_number',$invoice_number)->where('etat','01')->sum('montant_recouvre');

        if ($montant_total_credit >= $montant_recouvre_input) {

            $montant_total_recouvre = $montant_recouvre_input + $montant_recouvre;
            $reste_credit = $montant_total_credit - $montant_total_recouvre;

            if ($reste_credit == 0) {
                $etat_recouvrement = 2;
                Facture::where('invoice_number', '=', $invoice_number)
                    ->update([
                        'customer_name' => $customer_name,
                        'statut_paied' => $statut_paied,
                        'customer_address' => $customer_address,
                        'customer_TIN' => $customer_TIN,
                        'etat_recouvrement' => $etat_recouvrement,
                        'date_recouvrement' => $date_recouvrement,
                        'nom_recouvrement' => $nom_recouvrement,
                        'note_recouvrement' => $note_recouvrement,
                        'bank_name' => $bank_name,
                        'cheque_no' => $cheque_no,
                        'montant_total_credit' => $montant_total_credit,
                        'montant_recouvre' => $montant_total_recouvre,
                        'reste_credit' => $reste_credit,
                        'confirmed_by' => $this->user->name
                    ]);
                FactureDetail::where('invoice_number', '=', $invoice_number)
                    ->update([
                        'customer_name' => $customer_name,
                        'statut_paied' => $statut_paied,
                        'customer_address' => $customer_address,
                        'customer_TIN' => $customer_TIN,
                        'etat_recouvrement' => $etat_recouvrement,
                        'date_recouvrement' => $date_recouvrement,
                        'nom_recouvrement' => $nom_recouvrement,
                        'note_recouvrement' => $note_recouvrement,
                        'bank_name' => $bank_name,
                        'cheque_no' => $cheque_no,
                        'montant_total_credit' => $montant_total_credit,
                        'montant_recouvre' => $montant_total_recouvre,
                        'reste_credit' => $reste_credit,
                        'confirmed_by' => $this->user->name
                    ]);

                session()->flash('success', 'Le credit  est payé avec succés');
                return back();
            }elseif ($reste_credit < 0) {
                session()->flash('error', $this->user->name.' ,je vous prie de bien vouloir saisir les donnees exactes s\'il te plait! plus d\'info contacte IT Musumba Holding Marcellin ');
                return back();
            }
            else{
                $etat_recouvrement = 1;
                Facture::where('invoice_number', '=', $invoice_number)
                    ->update([
                        'customer_name' => $customer_name,
                        'statut_paied' => $statut_paied,
                        'customer_address' => $customer_address,
                        'customer_TIN' => $customer_TIN,
                        'etat_recouvrement' => $etat_recouvrement,
                        'date_recouvrement' => $date_recouvrement,
                        'nom_recouvrement' => $nom_recouvrement,
                        'note_recouvrement' => $note_recouvrement,
                        'bank_name' => $bank_name,
                        'cheque_no' => $cheque_no,
                        'montant_total_credit' => $montant_total_credit,
                        'montant_recouvre' => $montant_total_recouvre,
                        'reste_credit' => $reste_credit,
                        'confirmed_by' => $this->user->name
                    ]);
                FactureDetail::where('invoice_number', '=', $invoice_number)
                    ->update([
                        'customer_name' => $customer_name,
                        'statut_paied' => $statut_paied,
                        'customer_address' => $customer_address,
                        'customer_TIN' => $customer_TIN,
                        'etat_recouvrement' => $etat_recouvrement,
                        'date_recouvrement' => $date_recouvrement,
                        'nom_recouvrement' => $nom_recouvrement,
                        'note_recouvrement' => $note_recouvrement,
                        'bank_name' => $bank_name,
                        'cheque_no' => $cheque_no,
                        'montant_total_credit' => $montant_total_credit,
                        'montant_recouvre' => $montant_total_recouvre,
                        'reste_credit' => $reste_credit,
                        'confirmed_by' => $this->user->name
                    ]);

                session()->flash('success', 'Le credit  est payé avec succés');
                return redirect()->route('admin.credit-invoices.list');
            }
        }else{
            session()->flash('error', 'Le montant saisi doit etre inferieur ou egal au montant total de la facture');
            return redirect()->route('admin.private-factures.index');
        }

        
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $invoice_number
     * @return \Illuminate\Http\Response
     */
    public function destroy($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('private_drink_stockout.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any invoice !');
        }

        $facture = PrivateFacture::where('invoice_number',$invoice_number)->first();
        if (!is_null($facture)) {
            $facture->delete();
            PrivateFactureDetail::where('invoice_number',$invoice_number)->delete();

            $email = 'ambazamarcellin2001@gmail.com';
            $auteur = $this->user->name;
            $mailData = [
                    'title' => 'Suppression de facture',
                    'email' => $email,
                    'invoice_number' => $invoice_number,
                    'auteur' => $auteur,
                    ];
         
            Mail::to($email)->send(new DeleteFactureMail($mailData));
        }

        session()->flash('success', 'La facture est supprimée !!');
        return back();
    }

    
}


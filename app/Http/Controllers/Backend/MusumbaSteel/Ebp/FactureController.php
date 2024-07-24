<?php

namespace App\Http\Controllers\Backend\MusumbaSteel\Ebp;

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
use GuzzleHttp\Client;
use App\Models\MsEbpArticle;
use App\Models\MsEbpClient;
use App\Models\MsEbpFacture;
use App\Models\MsEbpFactureDetail;
use App\Models\Setting;
use App\Exports\MusumbaSteel\Ebp\FactureExport;


class FactureController extends Controller
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice ! more information you have to contact Marcellin');
        }

        $factures = MsEbpFacture::orderBy('invoice_date','desc')->take(500)->get();
        return view('backend.pages.musumba_steel.ebp.invoice.index',compact('factures'));
    }


    public function create()
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice ! more information you have to contact Marcellin');
        }

        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $articles =  MsEbpArticle::orderBy('name','asc')->get();
        $clients =  MsEbpClient::orderBy('customer_name','asc')->get();

        return view('backend.pages.musumba_steel.ebp.invoice.create',compact('articles','setting','clients'));
    }

    public function store(Request  $request)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create any invoice ! more information you have to contact Marcellin');
        }

        $rules = array(
                'invoice_date' => 'required',
                'invoice_number' => 'required|unique:ms_ebp_factures',
                'tp_type' => 'required',
                'tp_name' => 'required|max:100|min:3',
                'tp_TIN' => 'required|max:30|min:4',
                'tp_trade_number' => 'required|max:20|min:4',
                'tp_phone_number' => 'required|max:20|min:6',
                'tp_address_commune' => 'required|max:50|min:5',
                'tp_address_quartier' => 'required|max:50|min:5',
                'client_id' => 'required',
                //'customer_TIN' => 'required|max:30|min:4',
                //'customer_address' => 'required|max:100|min:5',
                //'invoice_signature' => 'required|max:90|min:10',
                //'invoice_signature_date' => 'required|max: |min:',
                'article_id.*'  => 'required',
                'item_quantity.*'  => 'required',
                'item_price.*'  => 'required',
                'item_ct.*'  => 'required',
                'item_tl.*'  => 'required'
            );

            $error = Validator::make($request->all(),$rules);

            if($error->fails()){
                return response()->json([
                    'error' => $error->errors()->all(),
                ]);
            }

            $article_id = $request->article_id;
            $item_quantity = $request->item_quantity;
            $item_price = $request->item_price;
            $item_ct = $request->item_ct;
            $item_tl =$request->item_tl;
            $item_tsce_tax = $request->item_tsce_tax;
            $item_ott_tax = $request->item_ott_tax;

            $client_id = $request->client_id;
            $invoice_number = $request->invoice_number;



            /*
            $latest = MsEbpFacture::latest()->first();
            if ($latest) {
               $invoice_number = 'FA' . (str_pad((int)$latest->id + 1, 4, '0', STR_PAD_LEFT)); 
            }else{
               $invoice_number = 'FA' . (str_pad((int)0 + 1, 4, '0', STR_PAD_LEFT));  
            }
            
            */
            

            $invoice_signature = $request->tp_TIN."/".config('app.obr_test_username')."/".Carbon::parse($request->invoice_date)->format('YmdHis')."/".$invoice_number;

        for( $count = 0; $count < count($article_id); $count++ )
        {
            $taux_tva = MsEbpArticle::where('id', $article_id[$count])->value('vat');

            if($request->vat_taxpayer == 1){
                
                $item_price_nvat = ($item_price[$count]*$item_quantity[$count]) + $item_ct[$count];
                $vat = 0;
                $item_price_wvat = ($item_price_nvat + $vat);
                $item_total_amount = $item_price_wvat + $item_tl[$count] + $item_tsce_tax[$count] + $item_ott_tax[$count];
            }else{
                $item_price_nvat = ($item_price[$count]*$item_quantity[$count]) + $item_ct[$count];
                $vat = 0;
                $item_price_wvat = ($item_price_nvat + $vat);
                $item_total_amount = $item_price_wvat + $item_tl[$count] + $item_tsce_tax[$count] + $item_ott_tax[$count];
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
            'invoice_signature_date'=> Carbon::now(),
            'article_id'=>$article_id[$count],
            'item_quantity'=>$item_quantity[$count],
            'item_price'=>$item_price[$count],
            'item_ct'=>$item_ct[$count],
            'item_tl'=>$item_tl[$count],
            'item_tsce_tax' => $item_tsce_tax[$count],
            'item_ott_tax' => $item_ott_tax[$count],
            'item_price_nvat'=>$item_price_nvat,
            'vat'=>$vat,
            'item_price_wvat'=>$item_price_wvat,
            'item_total_amount'=>$item_total_amount,
            'client_id'=> $client_id,
        );
          $data1[] = $data;
      }


        MsEbpFactureDetail::insert($data1);


            //create facture
            $facture = new MsEbpFacture();
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
            $facture->client_id = $client_id;
            $facture->invoice_signature_date = Carbon::now();
            $facture->save();

            session()->flash('success', 'Le vente est fait avec succés!!');
            return redirect()->route('admin.musumba-steel-facture.index');
    }

    public function validerFacture($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.validate')) {
            abort(403, 'Sorry !! You are Unauthorized to validate any invoice ! more information you have to contact Marcellin');
        }

        $datas = MsEbpFactureDetail::where('invoice_number', $invoice_number)->get();
        $factures = MsEbpFacture::where('invoice_number', $invoice_number)->get();
        
        $theUrl = config('app.guzzle_musumba_steel_url').'/ebms_api/login/';
        $response = Http::post($theUrl, [
            'username'=> config('app.obr_test_username'),
            'password'=> config('app.obr_test_pwd')

        ]);
        $data =  json_decode($response);
        $data2 = ($data->result);
        
        $token = $data2->token;

        foreach($datas as $data){
            if (!empty($data->article_id)) {
                $invoice_items = array(
                'item_designation'=>$data->article->name,
                'item_quantity'=>$data->item_quantity,
                'item_price'=>$data->item_price,
                'item_ct'=>$data->item_ct,
                'item_tl'=>$data->item_tl,
                'item_ott_tax'=>$data->item_ott_tax,
                'item_tsce_tax'=>$data->item_tsce_tax,
                'item_price_nvat'=>$data->item_price_nvat,
                'vat'=>$data->vat,
                'item_price_wvat'=>$data->item_price_wvat,
                'item_total_amount'=>$data->item_total_amount
                );

                $factureDetail[] = $invoice_items;
            }
        }

        foreach($factures as $facture){
        $theUrl = config('app.guzzle_musumba_steel_url').'/ebms_api/addInvoice_confirm';  
        $response = Http::withHeaders([
        'Authorization' => 'Bearer '.$token,
        'Accept' => 'application/json'])->post($theUrl, [
            'invoice_number'=>$facture->invoice_number,
            'invoice_date'=> $facture->invoice_date,
            'tp_type'=>$facture->tp_type,
            'tp_name'=>$facture->tp_name,
            'tp_TIN'=>$facture->tp_TIN,
            'tp_trade_number'=>$facture->tp_trade_number,
            'tp_phone_number'=>$facture->tp_phone_number,
            'tp_address_province'=>$facture->tp_address_province,
            'tp_address_commune'=>$facture->tp_address_commune,
            'tp_address_quartier'=>$facture->tp_address_quartier,
            'tp_address_avenue'=>$facture->tp_address_avenue,
            'tp_address_rue'=>$facture->tp_address_rue,
            'vat_taxpayer'=>$facture->vat_taxpayer,
            'ct_taxpayer'=>$facture->ct_taxpayer,
            'tl_taxpayer'=>$facture->tl_taxpayer,
            'tp_fiscal_center'=>$facture->tp_fiscal_center,
            'tp_activity_sector'=>$facture->tp_activity_sector,
            'tp_legal_form'=>$facture->tp_legal_form,
            'payment_type'=>$facture->payment_type,
            'customer_name'=>$facture->client->customer_name,
            'customer_TIN'=>$facture->client->customer_TIN,
            'customer_address'=>$facture->client->customer_address,
            'invoice_identifier'=> $facture->invoice_signature,
            'invoice_currency'=> $facture->invoice_currency,
            'cancelled_invoice_ref'=> $facture->cancelled_invoice_ref,
            'cancelled_invoice'=> $facture->cancelled_invoice,
            'invoice_ref'=> $facture->invoice_ref,
            //'invoice_signature_date'=> $facture->invoice_signature_date,
            'invoice_items' => $factureDetail,

        ]); 

        }

        $dataObr =  json_decode($response);
        $done = $dataObr->success;
        


        if ($done == true) {
            //$dataObr2 = $dataObr->result;
            //$invoice_registred_number = $dataObr2->invoice_registred_number;
            //$invoice_registred_date = $dataObr2->invoice_registred_date;
            /*
            MsEbpFacture::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => 2,'invoice_registred_number' => $invoice_registred_number,'invoice_registred_date' => $invoice_registred_date]);
            MsEbpFactureDetail::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => 2,'invoice_registred_number' => $invoice_registred_number,'invoice_registred_date' => $invoice_registred_date]);
            */
            MsEbpFacture::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => 2]);
            MsEbpFactureDetail::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => 2]);
            return $response->json();

        }else{

            return $response->json();

        }

    }

    public function annulerFacture(Request $request,$invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.reset')) {
            abort(403, 'Sorry !! You are Unauthorized to reset any invoice ! more information you have to contact Marcellin');
        }

        $datas = MsEbpFactureDetail::where('invoice_number', $invoice_number)->get();


        $request->validate([
            'cn_motif' => 'required|min:10|max:500'
        ]);

        $cn_motif = $request->cn_motif;

        $invoice_signature = MsEbpFacture::where('invoice_number', $invoice_number)->value('invoice_signature');
        
        $theUrl = config('app.guzzle_musumba_steel_url').'/ebms_api/login/';
        $response = Http::post($theUrl, [
            'username'=> config('app.obr_test_username'),
            'password'=> config('app.obr_test_pwd')

        ]);
        $data =  json_decode($response);
        $data2 = ($data->result);
        
    
        $token = $data2->token;

        $theUrl = config('app.guzzle_musumba_steel_url').'/ebms_api/cancelInvoice';      

        $response = Http::withHeaders([
        'Authorization' => 'Bearer '.$token,
        'Accept' => 'application/json'])->post($theUrl, [
            'invoice_signature'=>$invoice_signature,
            'cn_motif'=>$cn_motif 
        ]); 

        $data =  json_decode($response);
        $done = ($data->success);
        $msg = ($data->msg);


        if ($done == true) {

            MsEbpFacture::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => -1,'statut' => -1,'cn_motif' => $cn_motif,'reseted_by' => $this->user->name]);
            MsEbpFactureDetail::where('invoice_number', '=', $invoice_number)
                ->update(['etat' => -1,'statut' => -1,'cn_motif' => $cn_motif,'reseted_by' => $this->user->name]);
               
            $email1 = 'ambazamarcellin2001@gmail.com';
            $auteur = $this->user->name;
            $mailData = [
                    'title' => 'Système de facturation électronique, Musumba Steel',
                    'email1' => $email1,
                    'email2' => $email2,
                    'invoice_number' => $invoice_number,
                    'auteur' => $auteur,
                    'cn_motif' => $cn_motif,
                    ];
         
            Mail::to($email1)->send(new InvoiceResetedMail($mailData));
            
            
            session()->flash('success', 'La Facture  est annulée avec succés');
            return redirect()->route('admin.musumba-steel-facture.index');       
        }else{
            return $response->json();
        } 

    }

    public function voirFactureAnnuler($invoice_number){
        $facture = MsEbpFactureDetail::where('invoice_number',$invoice_number)->first();
        $datas = MsEbpFactureDetail::where('invoice_number', $invoice_number)->get();
        return view('backend.pages.musumba_steel.ebp.invoice.reset', compact('facture','datas'));
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
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice ! more information you have to contact Marcellin');
        }

        $factureDetails = MsEbpFactureDetail::where('invoice_number',$invoice_number)->get();
        $facture = MsEbpFacture::with('client')->where('invoice_number',$invoice_number)->first();
        $total_amount = DB::table('ms_ebp_facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');
        return view('backend.pages.musumba_steel.ebp.invoice.show',compact('facture','factureDetails','total_amount'));
    }


    public function invoiceSentObrToPdf(Request $request)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view any invoice sent ! more information you have to contact Marcellin');
        }

        $d1 = $request->query('start_date');
        $d2 = $request->query('end_date');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        $datas = MsEbpFactureDetail::select(
                        DB::raw('id,article_id,invoice_number,invoice_date,item_quantity,item_price,customer_name,client_id,customer_TIN,item_total_amount'))->where('etat',2)->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','article_id','invoice_date','invoice_number','item_quantity','item_price','customer_name','client_id','customer_TIN','item_total_amount')->orderBy('id','asc')->get();
        $total_amount_bif = DB::table('ms_ebp_facture_details')->where('etat',2)->where('invoice_currency','BIF')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');
        $total_amount_usd = DB::table('ms_ebp_facture_details')->where('etat',2)->where('invoice_currency','USD')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_total_amount');

        $total_vat = DB::table('ms_ebp_facture_details')->where('etat',2)->where('invoice_currency','BIF')->whereBetween('invoice_date',[$start_date,$end_date])->sum('vat');
        $total_item_price_nvat = DB::table('ms_ebp_facture_details')->where('etat',2)->where('invoice_currency','BIF')->whereBetween('invoice_date',[$start_date,$end_date])->sum('item_price_nvat');

        $currentTime = Carbon::now();

        $dateT =  $currentTime->toDateTimeString();

        $dateTime = str_replace([' ',':'], '_', $dateT);
        $pdf = PDF::loadView('backend.pages.musumba_steel.ebp.invoice.report',compact('datas','dateTime','end_date','start_date','total_amount_bif','total_amount_usd','total_vat','total_item_price_nvat'))->setPaper('a4', 'landscape');

        // download pdf file
        return $pdf->download('FACTURES_ENVOYES'.$dateTime.'.pdf');

        
    }

    public function exportToExcel()
    {
        return Excel::download(new FactureExport, 'musumba_steel_facture_envoye_a_obr.xlsx');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $invoice_number
     * @return \Illuminate\Http\Response
     */
    public function destroy($invoice_number)
    {
        if (is_null($this->user) || !$this->user->can('musumba_steel_facture.delete')) {
            abort(403, 'Sorry !! You are Unauthorized to delete any invoice ! more information you have to contact Marcellin');
        }

        $facture = MsEbpFacture::where('invoice_number',$invoice_number)->first();
        if (!is_null($facture)) {
            $facture->delete();
            MsEbpFactureDetail::where('invoice_number',$invoice_number)->delete();
            
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

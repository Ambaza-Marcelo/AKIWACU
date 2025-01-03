<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Helpers\Misc;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Validator;
use GuzzleHttp\Client;
use App\Models\Facture;
use App\Models\FactureDetail;
use App\Models\Setting;

class SendBillEbmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $invoice_number = "FN0050";
        $job = Misc::jobStart('SendBillEbmsJob');

            try {DB::beginTransaction();
        $setting = DB::table('settings')->orderBy('created_at','desc')->first();

        $datas = FactureDetail::where('invoice_number', $invoice_number)->get();
        $facture = Facture::where('invoice_number', $invoice_number)->first();
        $invoice_signature = Facture::where('invoice_number', $invoice_number)->value('invoice_signature');
        $data = Facture::where('invoice_number', $invoice_number)->first();
        $totalValue = DB::table('facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_price_nvat');
        $item_total_amount = DB::table('facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_total_amount');

        $totalVat = DB::table('facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('vat');

        $total_tsce_tax = DB::table('facture_details')
            ->where('invoice_number', '=', $invoice_number)
            ->sum('item_ct');

        $client = Facture::where('invoice_number', $invoice_number)->value('customer_name');
        $date = Facture::where('invoice_number', $invoice_number)->value('invoice_date');

        
        $factures = Facture::where('invoice_number', $invoice_number)->get();

        $datas = FactureDetail::where('invoice_number', $invoice_number)->get();
            

        $theUrl = config('app.guzzle_test_url').'/ebms_api/login/';
        $response = Http::post($theUrl, [
            'username'=> config('app.obr_test_username'),
            'password'=> config('app.obr_test_pwd')

        ]);
        $dataObr =  json_decode($response);
        $data2 = ($dataObr->result);
        
    
        $token = $data2->token;

        foreach($datas as $data){
            if (!empty($data->food_item_id)) {
                $invoice_items = array(
                'item_designation'=>$data->foodItem->name,
                'item_quantity'=>$data->item_quantity,
                'item_price'=>$data->item_price,
                'item_ct'=>$data->item_ct,
                'item_tl'=>$data->item_tl,
                'item_price_nvat'=>$data->item_price_nvat,
                'vat'=>$data->vat,
                'item_price_wvat'=>$data->item_price_wvat,
                'item_total_amount'=>$data->item_total_amount
                );

                $factureDetail[] = $invoice_items;
            }elseif(!empty($data->drink_id)){
                $invoice_items = array(
                'item_designation'=>$data->drink->name,
                'item_quantity'=>$data->item_quantity,
                'item_price'=>$data->item_price,
                'item_ct'=>$data->item_ct,
                'item_tl'=>$data->item_tl,
                'item_price_nvat'=>$data->item_price_nvat,
                'vat'=>$data->vat,
                'item_price_wvat'=>$data->item_price_wvat,
                'item_total_amount'=>$data->item_total_amount
                );

                $factureDetail[] = $invoice_items;
            }elseif(!empty($data->bartender_item_id)){
                $invoice_items = array(
                'item_designation'=>$data->bartenderItem->name,
                'item_quantity'=>$data->item_quantity,
                'item_price'=>$data->item_price,
                'item_ct'=>$data->item_ct,
                'item_tl'=>$data->item_tl,
                'item_price_nvat'=>$data->item_price_nvat,
                'vat'=>$data->vat,
                'item_price_wvat'=>$data->item_price_wvat,
                'item_total_amount'=>$data->item_total_amount
                );

                $factureDetail[] = $invoice_items;
            }elseif(!empty($data->barrist_item_id)){
                $invoice_items = array(
                'item_designation'=>$data->barristItem->name,
                'item_quantity'=>$data->item_quantity,
                'item_price'=>$data->item_price,
                'item_ct'=>$data->item_ct,
                'item_tl'=>$data->item_tl,
                'item_price_nvat'=>$data->item_price_nvat,
                'vat'=>$data->vat,
                'item_price_wvat'=>$data->item_price_wvat,
                'item_total_amount'=>$data->item_total_amount
                );

                $factureDetail[] = $invoice_items;
            }elseif(!empty($data->salle_id)){
                $invoice_items = array(
                'item_designation'=>$data->salle->name,
                'item_quantity'=>$data->item_quantity,
                'item_price'=>$data->item_price,
                'item_ct'=>$data->item_ct,
                'item_tl'=>$data->item_tl,
                'item_price_nvat'=>$data->item_price_nvat,
                'vat'=>$data->vat,
                'item_price_wvat'=>$data->item_price_wvat,
                'item_total_amount'=>$data->item_total_amount
                );

                $factureDetail[] = $invoice_items;
            }elseif(!empty($data->room_id)){
                $invoice_items = array(
                'item_designation'=>$data->room->name,
                'item_quantity'=>$data->item_quantity,
                'item_price'=>$data->item_price,
                'item_ct'=>$data->item_ct,
                'item_tl'=>$data->item_tl,
                'item_tsce_tax'=>0,
                'item_price_nvat'=>$data->item_price_nvat,
                'vat'=>$data->vat,
                'item_price_wvat'=>$data->item_price_wvat,
                'item_total_amount'=>$data->item_total_amount
                );

                $factureDetail[] = $invoice_items;
            }elseif(!empty($data->service_id)){
                $invoice_items = array(
                'item_designation'=>$data->service->name,
                'item_quantity'=>$data->item_quantity,
                'item_price'=>$data->item_price,
                'item_ct'=>$data->item_ct,
                'item_tl'=>$data->item_tl,
                'item_price_nvat'=>$data->item_price_nvat,
                'vat'=>$data->vat,
                'item_price_wvat'=>$data->item_price_wvat,
                'item_total_amount'=>$data->item_total_amount
                );

                $factureDetail[] = $invoice_items;
            }else{
                $invoice_items = array(
                'item_designation'=>$data->table->name,
                'item_quantity'=>$data->item_quantity,
                'item_price'=>$data->item_price,
                'item_ct'=>$data->item_ct,
                'item_tl'=>$data->item_tl,
                'item_price_nvat'=>$data->item_price_nvat,
                'vat'=>$data->vat,
                'item_price_wvat'=>$data->item_price_wvat,
                'item_total_amount'=>$data->item_total_amount
                );

                $factureDetail[] = $invoice_items;
            }

        }


        foreach($factures as $facture){
        $theUrl = config('app.guzzle_test_url').'/ebms_api/addInvoice_confirm';  
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
            'tp_address_avenue'=>$facture->tp_address_rue,
            'tp_address_rue'=>$facture->tp_address_rue,
            'vat_taxpayer'=>$facture->vat_taxpayer,
            'ct_taxpayer'=>$facture->ct_taxpayer,
            'tl_taxpayer'=>$facture->tl_taxpayer,
            'tp_fiscal_center'=>$setting->tp_fiscal_center,
            'tp_activity_sector'=>$facture->tp_activity_sector,
            'tp_legal_form'=>$facture->tp_legal_form,
            'payment_type'=>$facture->payment_type,
            'invoice_type'=>$facture->invoice_type,
            'customer_name'=>$facture->client->customer_name,
            'customer_TIN'=>$facture->client->customer_TIN,
            'vat_customer_payer'=>$facture->client->vat_customer_payer,
            'customer_address'=>$facture->client->customer_address,
            'invoice_signature'=> $facture->invoice_signature,
            'invoice_identifier'=> $facture->invoice_signature,
            'invoice_currency'=> $facture->invoice_currency,
            'cancelled_invoice_ref'=> $facture->cancelled_invoice_ref,
            'cancelled_invoice'=> $facture->cancelled_invoice,
            'invoice_ref'=> $facture->invoice_ref,
            'invoice_signature_date'=> $facture->invoice_signature_date,
            'invoice_items' => $factureDetail,

        ]); 

        }

        $dataObr =  json_decode($response);

        $done = $dataObr->success;
        $msg = $dataObr->msg;

        if ($done == true) {

            $electronic_signature = $dataObr->electronic_signature;

            Facture::where('invoice_number', '=', $invoice_number)
                ->update(['statut' => 1,'electronic_signature' => $electronic_signature]);
            FactureDetail::where('invoice_number', '=', $invoice_number)
                ->update(['statut' => 1,'electronic_signature' => $electronic_signature]);

            
            DB::commit();

        }else{
            return $response->json();
        }
        
        } catch (\Exception $e) {
            // An error occured; cancel the transaction...

            DB::rollback();

            // and throw the error again.

            throw $e;
        }
        Misc::jobEnd('SendBillEbmsJob');
    }
}

<?php

namespace App\Exports\MusumbaSteel\Ebp;

use Carbon\Carbon;
use App\Models\MsEbpFactureDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FactureExport implements FromCollection, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {

        $d1 = request()->input('start_date');
        $d2 = request()->input('end_date');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        return MsEbpFactureDetail::select(
                        DB::raw('id,article_id,invoice_currency,invoice_number,invoice_date,item_quantity,item_price,customer_name,client_id,customer_TIN,item_total_amount'))->where('etat',2)->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','article_id','invoice_currency','invoice_date','invoice_number','item_quantity','item_price','customer_name','customer_TIN','client_id','item_total_amount')->orderBy('id','asc')->get();
    }

    public function map($data) : array {

        if (!empty($data->client_id)) {
            $nom = $data->client->customer_name;
            $nif = $data->client->customer_TIN;
        }else{
          $nom = $data->customer_name;
          $nif = $data->customer_TIN;  
        }

        if ($data->invoice_currency == 'BIF') {
            $invoice_currency = 'BIF';
        }elseif ($data->invoice_currency == 'USD') {
            $invoice_currency = 'USD';
        }else{
            $invoice_currency = ' ';
        }

        return [
            $data->id,
            Carbon::parse($data->invoice_date)->format('d/m/Y'),
            $data->invoice_number,
            $nom,
            $nif,
            $data->article->name,
            $data->item_quantity,
            $data->item_price,
            $data->vat,
            $data->item_total_amount,
            $invoice_currency
        ] ;
 
 
    }

    public function headings() : array {
        return [
            '#',
            'Date',
            'No Facture',
            'Nom du Client',
            'NIF du Client',
            'Libellé',
            'Quantité',
            'PV HTVA',
            'TVA',
            'TTC',
            'MONNAIE'
        ] ;
    }
}

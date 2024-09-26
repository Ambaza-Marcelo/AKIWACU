<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\PrivateFactureDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PrivateStoreFactureExport implements FromCollection, WithMapping, WithHeadings
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

        return PrivateFactureDetail::select(
                        DB::raw('id,private_store_item_id,created_at,updated_at,invoice_number,invoice_date,item_quantity,customer_name,item_total_amount,vat,item_price_nvat,cump,etat,reseted_by,cn_motif,auteur,validated_by,etat'))->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','private_store_item_id','invoice_date','updated_at','created_at','invoice_number','item_quantity','etat','reseted_by','cn_motif','customer_name','item_total_amount','vat','item_price_nvat','cump','auteur','validated_by','etat')->orderBy('id','asc')->get();
    }

    public function map($data) : array {

        if ($data->etat == '0') {
            $etat = "ENCOURS....";
            $auteur = " ";
            $motif = " ";
        }elseif ($data->etat === '-1') {
            $etat = "ANNULE";
            $auteur = $data->reseted_by;
            $motif = $data->cn_motif;
        }elseif ($data->etat === '1') {
            $etat = "CASH";
            $auteur = " ";
            $motif = " ";
        }elseif ($data->etat === '01') {
            $etat = "CREDIT";
            $auteur = " ";
            $motif = " ";
        }

        return [
            $data->id,
            //Carbon::parse($data->created_at)->format('d/m/Y H:i:s'),
            Carbon::parse($data->updated_at)->format('d/m/Y H:i:s'),
            Carbon::parse($data->invoice_date)->format('d/m/Y'),
            $data->invoice_number,
            $data->customer_name,
            $data->privateStoreItem->name,
            $data->item_quantity,
            $data->item_total_amount,
            $etat,
            $data->auteur,
            $data->validated_by,
            $auteur,
            $motif
        ] ;
 
 
    }

    public function headings() : array {
        return [
            '#',
            //'Created at',
            'updated at ',
            'Date de facturation',
            'No Facture',
            'Nom du Client',
            'Libellé',
            'Quantité',
            'Montant Total',
            'ETAT',
            'Auteur',
            'Valide Par',
            'ANNULE PAR',
            'Motif'
        ] ;
    }
}

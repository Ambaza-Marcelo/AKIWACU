<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\FactureDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FactureEncoursExport implements FromCollection, WithMapping, WithHeadings
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

        return FactureDetail::select(
                        DB::raw('id,food_item_id,drink_id,barrist_item_id,bartender_item_id,salle_id,service_id,breakfast_id,swiming_pool_id,kidness_space_id,invoice_number,invoice_date,item_quantity,customer_name,client_id,drink_order_no,food_order_no,bartender_order_no,barrist_order_no,booking_no,item_total_amount,vat,item_price_nvat'))->where('etat','0')->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','drink_id','food_item_id','bartender_item_id','barrist_item_id','salle_id','service_id','breakfast_id','swiming_pool_id','kidness_space_id','invoice_date','invoice_number','item_quantity','drink_order_no','food_order_no','bartender_order_no','barrist_order_no','booking_no','customer_name','client_id','item_total_amount','vat','item_price_nvat')->orderBy('customer_name','asc')->get();
    }

    public function map($data) : array {

        if (!empty($data->drink_id)) {
            $libelle = $data->drink->name;
        }elseif (!empty($data->food_item_id)) {
            $libelle = $data->foodItem->name;
        }elseif (!empty($data->barrist_item_id)) {
            $libelle = $data->barristItem->name;
        }elseif (!empty($data->bartender_item_id)) {
            $libelle = $data->bartenderItem->name;
        }elseif (!empty($data->salle_id)) {
            $libelle = $data->salle->name;
        }elseif (!empty($data->service_id)) {
            $libelle = $data->service->name;
        }elseif (!empty($data->swiming_pool_id)) {
            $libelle = $data->swimingPool->name;
        }elseif (!empty($data->breakfast_id)) {
            $libelle = "BREAKFAST";
        }elseif (!empty($data->kidness_space_id)) {
            $libelle = $data->kidnessSpace->name;
        }

        if (!empty($data->client_id)) {
            $customer_name = $data->client->customer_name;
        }else{
            $customer_name = $data->customer_name;
        }

        if (!empty($data->drink_id)) {
            $order_no = $data->drink_order_no;
        }elseif (!empty($data->food_item_id)) {
            $order_no = $data->food_order_no;
        }elseif (!empty($data->barrist_item_id)) {
            $order_no = $data->barrist_order_no;
        }elseif (!empty($data->bartender_item_id)) {
            $order_no = $data->bartender_order_no;
        }elseif (!empty($data->salle_id)) {
            $order_no = $data->booking_no;
        }elseif (!empty($data->service_id)) {
            $order_no = $data->booking_no;
        }elseif (!empty($data->breakfast_id)) {
            $order_no = $data->booking_no;
        }elseif (!empty($data->swiming_pool_id)) {
            $order_no = $data->booking_no;
        }elseif (!empty($data->kidness_space_id)) {
            $order_no = $data->booking_no;
        }

        return [
            $data->id,
            Carbon::parse($data->invoice_date)->format('d/m/Y'),
            $data->invoice_number,
            $order_no,
            $customer_name,
            $libelle,
            $data->item_quantity,
            $data->item_price_nvat,
            $data->vat,
            $data->item_total_amount
        ] ;
 
 
    }

    public function headings() : array {
        return [
            '#',
            'Date',
            'No Facture',
            'No Commande',
            'Nom du Client',
            'Libellé',
            'Quantité',
            'PV HTVA',
            'TVA',
            'TTC'
        ] ;
    }
}

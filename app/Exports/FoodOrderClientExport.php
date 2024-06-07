<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\OrderKitchenDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FoodOrderClientExport implements FromCollection, WithMapping, WithHeadings
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

        return OrderKitchenDetail::select(
                        DB::raw('id,food_item_id,date,quantity,purchase_price,selling_price,total_amount_selling,employe_id,created_by,order_no,confirmed_by,status,rejected_by,rej_motif'))->whereBetween('date',[$start_date,$end_date])->groupBy('id','food_item_id','employe_id','date','quantity','status','purchase_price','selling_price','total_amount_selling','order_no','confirmed_by','rejected_by','created_by','rej_motif')->orderBy('id','asc')->get();
    }

    public function map($data) : array {

        if ($data->status == 0) {
            $status = "ENCOURS....";
            $rej_motif = "";
        }elseif ($data->status == -1) {
            $status = "REJETE";
            $rej_motif = $data->rej_motif;
        }elseif ($data->status == 1) {
            $status = "VALIDE";
            $rej_motif = "";
        }elseif ($data->status == 2) {
            $status = "FACTURE ENCOURS";
            $rej_motif = "";
        }elseif ($data->status == 3) {
            $status = "FACTURE VALIDE";
            $rej_motif = "";
        }else{
            $status = "";
            $rej_motif = "";
        }

        return [
            $data->id,
            Carbon::parse($data->date)->format('d/m/Y'),
            $data->order_no,
            $data->employe->name,
            $data->foodItem->name,
            $data->quantity,
            0,
            0,
            0,
            $data->selling_price,
            $data->total_amount_selling,
            $status,
            $data->created_by,
            $data->confirmed_by,
            $data->rejected_by,
            $rej_motif
        ] ;
 
 
    }

    public function headings() : array {
        return [
            '#',
            'Date',
            'No Commande',
            'Nom du Serveur',
            'Libellé',
            'Quantité',
            'C.U.M.P',
            'P.A',
            'TOTAL P.A',
            'PVU',
            'TOTAL PVU',
            'ETAT',
            'Auteur',
            'Accord de',
            'Rejete Par',
            'Motif Rejete'
        ] ;
    }
}

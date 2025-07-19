<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\BarristOrderDetail;
use App\Models\F\FBarristaOrderDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BarristOrderClientExport implements FromCollection, WithMapping, WithHeadings
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

        return FBarristaOrderDetail::select(
                        DB::raw('id,barrist_item_id,date,quantity,purchase_price,selling_price,total_amount_selling,employe_id,created_by,order_no,confirmed_by,status,table_id,updated_at'))->whereBetween('date',[$start_date,$end_date])->groupBy('id','barrist_item_id','employe_id','date','quantity','status','purchase_price','selling_price','total_amount_selling','order_no','confirmed_by','created_by','table_id','updated_at')->orderBy('id','asc')->get();
    }

    public function map($data) : array {

        if ($data->status == '0') {
            $status = "ENCOURS....";
        }elseif ($data->status === '-1') {
            $status = "";
        }elseif ($data->status === '1') {
            $status = "VALIDE";
        }elseif ($data->status === '2') {
            $status = "FACTURE ENCOURS";
        }elseif ($data->status == '3') {
        	$status = "FACTURE VALIDE";
        }else{
            $status = " ";
        }

        if (!empty($data->table_id)) {
            $table = $data->table->name;
        }else{
            $table = $data->table_no;
        }

        return [
            $data->id,
            $data->updated_at,
            Carbon::parse($data->date)->format('Y-m-d'),
			$data->order_no,
            $table,
            $data->employe->name,
            $data->barristItem->name,
            $data->quantity,
            0,
            0,
            0,
			$data->selling_price,
			$data->total_amount_selling,
            $status,
            $data->created_by,
            $data->confirmed_by,
            $data->rejected_by
        ] ;
 
 
    }

    public function headings() : array {
        return [
            '#',
            '',
            'Date operation',
            'No Commande',
            'Table',
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
            'Rejete Par'
        ] ;
    }
}


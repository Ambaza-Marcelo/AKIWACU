<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\OrderDrinkDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DrinkOrderClientExport implements FromCollection, WithMapping, WithHeadings
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

        return OrderDrinkDetail::select(
                        DB::raw('id,drink_id,date,quantity,purchase_price,selling_price,total_amount_selling,employe_id,created_by,order_no,confirmed_by,status'))->whereBetween('date',[$start_date,$end_date])->groupBy('id','drink_id','employe_id','date','quantity','status','purchase_price','selling_price','total_amount_selling','order_no','confirmed_by','created_by')->orderBy('id','asc')->get();
    }

    public function map($data) : array {

        if ($data->status == '0') {
            $status = "ENCOURS....";
        }elseif ($data->status === '-1') {
            $status = "REJETE";
        }elseif ($data->status === '1') {
            $status = "VALIDE";
        }elseif ($data->status === '2') {
            $status = "FACTURE ENCOURS";
        }elseif ($data->status == '3') {
        	$status = "FACTURE VALIDE";
        }else{
            $status = "";
        }

        return [
            $data->id,
            Carbon::parse($data->date)->format('Y-m-d'),
			$data->order_no,
            $data->employe->name,
            $data->drink->name,
            $data->quantity,
            $data->drink->cump,
            $data->drink->purchase_price,
            $data->drink->purchase_price * $data->quantity,
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
            'Rejete Par'
        ] ;
    }
}

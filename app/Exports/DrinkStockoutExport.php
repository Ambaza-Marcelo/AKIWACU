<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\DrinkStockoutDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DrinkStockoutExport implements FromCollection, WithMapping, WithHeadings
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

        return DrinkStockoutDetail::select(
                        DB::raw('id,drink_id,date,quantity,price,asker,created_by,stockout_no,validated_by,confirmed_by,approuved_by,rejected_by,status,unit,destination,origin_bg_store_id,origin_sm_store_id,origin_extra_store_id,item_movement_type,description'))->whereBetween('date',[$start_date,$end_date])->groupBy('id','drink_id','asker','date','quantity','status','price','stockout_no','confirmed_by','validated_by','approuved_by','created_by','rejected_by','unit','destination','origin_bg_store_id','origin_sm_store_id','origin_extra_store_id','item_movement_type','description')->orderBy('id','asc')->get();
    }

    public function map($data) : array {

        if ($data->status == '1') {
            $status = "ENCOURS....";
        }elseif ($data->status == '-1') {
            $status = "REJETE";
        }elseif ($data->status == '2') {
            $status = "VALIDE";
        }elseif ($data->status == '3') {
            $status = "CONFIRME";
        }elseif ($data->status == '4') {
        	$status = "APPROUVE";
        }else{
            $status = "";
        }

        if (!empty($data->origin_extra_store_id)) {
        	$origin = "GRAND STOCK";
        }elseif (!empty($data->origin_bg_store_id)) {
        	$origin = "STOCK INTERMEDIAIRE";
        }elseif (!empty($data->origin_sm_store_id)) {
        	$origin = "PETIT STOCK";
        }

        return [
            $data->id,
            Carbon::parse($data->date)->format('d/m/Y'),
			$data->stockout_no,
            $data->asker,
            $data->drink->name,
            $data->quantity,
            $data->price,
            $data->price * $data->quantity,
            $origin,
            $data->destination,
            $data->item_movement_type,
            $status,
            $data->created_by,
            $data->validated_by,
            $data->confirmed_by,
            $data->approuved_by,
            $data->rejected_by,
            $data->description
        ] ;
 
 
    }

    public function headings() : array {
        return [
            '#',
            'Date',
            'No Sortie',
            'Demandeur',
            'Libellé',
            'Quantité',
            'P.U',
            'V. Totale',
            'Origine',
            'destination',
            'Type Mouvement',
            'ETAT',
            'Auteur',
            'Valide Par',
            'Confirme par',
            'Approuve par',
            'Rejete Par',
            'description'
        ] ;
    }
}

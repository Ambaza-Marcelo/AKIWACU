<?php

namespace App\Exports\PrivateStore;

use Carbon\Carbon;
use App\Models\PrivateDrinkStockinDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PrivateDrinkStockinExport implements FromCollection, WithMapping, WithHeadings
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

        return PrivateDrinkStockinDetail::select(
                        DB::raw('id,private_store_item_id,date,quantity,purchase_price,handingover,created_by,stockin_no,validated_by,confirmed_by,approuved_by,rejected_by,status,receptionist,origin,item_movement_type,description'))->whereBetween('date',[$start_date,$end_date])->groupBy('id','private_store_item_id','handingover','date','quantity','status','purchase_price','stockin_no','confirmed_by','validated_by','approuved_by','created_by','rejected_by','receptionist','origin','item_movement_type','description')->orderBy('id','asc')->get();
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

        return [
            $data->id,
            Carbon::parse($data->date)->format('d/m/Y'),
			$data->stockin_no,
            $data->handingover,
            $data->receptionist,
            $data->privateStoreItem->name,
            $data->quantity,
            $data->purchase_price,
            $data->purchase_price * $data->quantity,
            $data->origin,
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
            'No Entree',
            'Remettant',
            'Receptionniste',
            'Libellé',
            'Quantité',
            'P.A',
            'TOTAL P.A',
            'Origine',
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
<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\FoodReceptionDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FoodReceptionExport implements FromCollection, WithMapping, WithHeadings
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

        return FoodReceptionDetail::select(
                        DB::raw('id,food_id,date,quantity_received,quantity_ordered,purchase_price,supplier_id,created_by,reception_no,validated_by,confirmed_by,approuved_by,rejected_by,status,invoice_no,order_no,purchase_no,receptionist,handingover,description,origin'))->whereBetween('date',[$start_date,$end_date])->groupBy('id','food_id','supplier_id','date','quantity_received','quantity_ordered','status','purchase_price','reception_no','confirmed_by','validated_by','approuved_by','created_by','rejected_by','invoice_no','order_no','purchase_no','receptionist','handingover','description','origin')->orderBy('id','asc')->get();
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
        }

        if (!empty($data->supplier_id)) {
            $supplier = $data->supplier->name;
        }else{
            $supplier = "";
        }

        return [
            $data->id,
            Carbon::parse($data->date)->format('Y-m-d'),
			$data->purchase_no,
			$data->order_no,
			$data->reception_no,
			$data->invoice_no,
            $supplier,
            $data->handingover,
            $data->receptionist,
            $data->food->name,
            $data->quantity_ordered,
            $data->quantity_received,
            $data->purchase_price,
            $data->purchase_price * $data->quantity_received,
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
            'BDA',
            'BC',
            'No Reception',
            'No Facture',
            'Nom du Fournisseur',
            'Remettant',
            'Receptionniste',
            'Libellé',
            'Quantité Commandé',
            'Quantité Recu',
            'P.A',
            'TOTAL P.A',
            'ETAT',
            'Auteur',
            'Valide Par',
            'Confirme par',
            'Approuve par',
            'Rejete Par',
            'Description'
        ] ;
    }
}
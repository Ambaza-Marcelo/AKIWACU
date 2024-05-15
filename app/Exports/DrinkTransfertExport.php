<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\DrinkTransferDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DrinkTransfertExport implements FromCollection, WithMapping, WithHeadings
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

        return DrinkTransferDetail::select(
                        DB::raw('id,drink_id,date,quantity_transfered,quantity_requisitioned,price,created_by,transfer_no,validated_by,confirmed_by,approuved_by,rejected_by,status,requisition_no,description'))->whereBetween('date',[$start_date,$end_date])->groupBy('id','drink_id','date','quantity_transfered','quantity_requisitioned','status','price','transfer_no','confirmed_by','validated_by','approuved_by','created_by','rejected_by','requisition_no','description')->orderBy('id','asc')->get();
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

        if (!empty($data->supplier_id)) {
            $supplier = $data->supplier->name;
        }else{
            $supplier = "";
        }

        return [
            $data->id,
            Carbon::parse($data->date)->format('d/m/Y'),
			$data->requisition_no,
			$data->transfer_no,
            $data->drink->name,
            $data->quantity_requisitioned,
            $data->quantity_transfered,
            $data->price,
            $data->price * $data->quantity_transfered,
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
            'Requisition No',
            'No Transfert',
            'Libellé',
            'Quantité Requisitionné',
            'Quantité Transfert',
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

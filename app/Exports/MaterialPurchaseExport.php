<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\MaterialPurchaseDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MaterialPurchaseExport implements FromCollection, WithMapping, WithHeadings
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

        return MaterialPurchaseDetail::select(
                        DB::raw('id,material_id,date,quantity,price,created_by,validated_by,confirmed_by,approuved_by,rejected_by,status,purchase_no,description'))->whereBetween('date',[$start_date,$end_date])->groupBy('id','material_id','date','quantity','status','price','confirmed_by','validated_by','approuved_by','created_by','rejected_by','purchase_no','description')->orderBy('id','asc')->get();
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
            Carbon::parse($data->date)->format('Y-m-d'),
			$data->purchase_no,
            $data->material->name,
            $data->quantity,
            $data->price,
            $data->price * $data->quantity,
            $status,
            $data->created_by,
            $data->description,
            $data->validated_by,
            $data->confirmed_by,
            $data->approuved_by,
            $data->rejected_by
        ] ;
 
 
    }

    public function headings() : array {
        return [
            '#',
            'Date',
            'BDA',
            'Libellé',
            'Quantité Demandé',
            'P.A Estimatif',
            'TOTAL P.A Estimatif',
            'ETAT',
            'Auteur',
            'Description',
            'Valide Par',
            'Confirme par',
            'Approuve par',
            'Rejete Par'
        ] ;
    }
}

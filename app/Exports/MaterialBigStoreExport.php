<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\MaterialBigStoreDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


class MaterialBigStoreExport implements FromCollection, WithMapping, WithHeadings
{
    protected $code;

    function __construct($code) {
        $this->code = $code;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return MaterialBigStoreDetail::select(
                        DB::raw('material_id,quantity,unit,purchase_price'))->where('material_id','!=','')->where('code',$this->code)->get();
    }

    public function map($data) : array {
        return [
            $data->id,
            $data->material->name,
            $data->material->code,
            //$data->material->materialCategory->name,
            $data->quantity,
            $data->material->materialMeasurement->purchase_unit,
            $data->purchase_price,
            $data->purchase_price * $data->quantity,
        ] ;
 
 
    }

    public function headings() : array {
        return [
            '#',
            'Article',
            'Code',
            //'Categorie',
            'Quantite',
            'Unité',
            'P.A',
            'Montant Total'
        ] ;
    }
}

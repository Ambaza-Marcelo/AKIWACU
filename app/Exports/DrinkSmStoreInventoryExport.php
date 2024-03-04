<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\DrinkSmallStoreInventoryDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DrinkSmStoreInventoryExport implements FromCollection, WithMapping, WithHeadings
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
        //$code_store = request()->input('code');
        return DrinkSmallStoreInventoryDetail::where('inventory_no',$this->code)->get();
    }

    public function map($data) : array {
        return [
            $data->id,
            $data->drink->name,
            $data->drink->specification,
            $data->drink->code,
            $data->quantity,
            $data->purchase_price,
            $data->total_purchase_value,
            $data->new_quantity,
            $data->new_purchase_price,
            $data->new_total_purchase_value,
        ] ;
 
 
    }

    public function headings() : array {
        return [
            '#',
            'Article',
            'Specification',
			'Code',
			'Qté Actuelle',
			'V.Unitaire Actuelle',
			'Valeur Stock Actuelle',
			'Nouvelle Qté',
			'Nouvelle V.U',
			'Nouvelle V du stock'
        ] ;
    }
}
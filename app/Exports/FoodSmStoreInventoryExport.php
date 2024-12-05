<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\FoodSmallStoreInventoryDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FoodSmStoreInventoryExport implements FromCollection, WithMapping, WithHeadings
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
        return FoodSmallStoreInventoryDetail::where('inventory_no',$this->code)->get();
    }

    public function map($data) : array {
        return [
            $data->id,
            $data->food->name,
            $data->food->code,
            $data->food->foodMeasurement->production_unit,
            $data->quantity,
            $data->purchase_price,
            $data->total_purchase_value,
            $data->new_quantity,
            $data->new_purchase_price,
            $data->new_total_purchase_value,
            $data->new_quantity - $data->quantity,
        ] ;
 
 
    }

    public function headings() : array {
        return [
            '#',
            'Article',
            'Code',
            'Unite de Mesure',
            'Qté Théorique',
            'PA Théorique',
            'Valeur Stock Actuelle',
            'Qté Physique',
            'Nouveau PA',
            'Nouvelle V du stock',
            'Relicat'
        ] ;
    }
}

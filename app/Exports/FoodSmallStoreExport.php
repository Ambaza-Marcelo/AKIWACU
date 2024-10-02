<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\FoodSmallStoreDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FoodSmallStoreExport implements FromCollection, WithMapping, WithHeadings
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
        return FoodSmallStoreDetail::select(
                        DB::raw('food_id,quantity_portion,unit,purchase_price'))->where('food_id','!=','')->where('code',$this->code)->get();
    }

    public function map($data) : array {
        return [
            $data->id,
            $data->food->name,
            $data->food->code,
            $data->quantity_portion,
            $data->food->unit,
            number_format($data->purchase_price,0,',',' '),
            number_format(($data->purchase_price * $data->quantity_portion),0,',',' '),
        ] ;
 
 
    }

    public function headings() : array {
        return [
            '#',
            'Article',
            'Code',
            'Quantite',
            'Unité',
            'P.A',
            'Total P.A',
        ] ;
    }
}

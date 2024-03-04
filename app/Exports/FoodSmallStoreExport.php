<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class FoodSmallStoreExport implements FromCollection
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
        return FoodBigStoreDetail::select(
                        DB::raw('food_id,quantity,unit,purchase_price'))->where('food_id','!=','')->where('code',$this->code)->get();
    }

    public function map($data) : array {
        return [
            $data->id,
            $data->food->name,
            $data->food->code,
            $data->quantity,
            $data->food->unit,
            number_format($data->purchase_price,0,',',' '),
            number_format(($data->purchase_price * $data->quantity),0,',',' '),
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

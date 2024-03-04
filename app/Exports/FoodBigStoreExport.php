<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\FoodBigStoreDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FoodBigStoreExport implements FromCollection, WithMapping, WithHeadings
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
                        DB::raw('id,food_id,quantity,unit,purchase_price,cump'))->where('food_id','!=','')->where('code',$this->code)->get();
    }

    public function map($data) : array {
        return [
            $data->id,
            $data->food->name,
            $data->food->code,
            $data->quantity,
            $data->food->unit,
            $data->cump,
            $data->purchase_price,
            ($data->purchase_price * $data->quantity),
        ] ;
 
 
    }

    public function headings() : array {
        return [
            '#',
            'Article',
            'Code',
            'Quantite',
            'Unité',
            'C.U.M.P',
            'P.A',
            'Total P.A',
        ] ;
    }
}

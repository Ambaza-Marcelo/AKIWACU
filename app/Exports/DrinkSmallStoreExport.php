<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\DrinkSmallStoreDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DrinkSmallStoreExport implements FromCollection, WithMapping, WithHeadings
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
        return DrinkSmallStoreDetail::select(
                        DB::raw('drink_id,quantity_bottle,unit,purchase_price,cump,selling_price'))->where('drink_id','!=','')->where('code',$this->code)->get();
    }

    public function map($data) : array {
        return [
            $data->id,
            $data->drink->name,
            $data->drink->code,
            $data->quantity_bottle,
            $data->drink->unit,
            $data->purchase_price,
            $data->drink->cump,
            $data->selling_price,
            ($data->purchase_price * $data->quantity_bottle),
            ($data->selling_price * $data->quantity_bottle),
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
            'C.U.M.P',
            'P.V',
            'Total P.A',
            'Total P.V'
        ] ;
    }
}

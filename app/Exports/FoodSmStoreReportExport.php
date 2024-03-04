<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\FactureDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


class FoodSmStoreReportExport implements FromCollection, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return DrinkBigStoreDetail::select(
                        DB::raw('drink_id,quantity_bottle,unit,purchase_price,selling_price'))->where('drink_id','!=','')->get();
    }

    public function map($data) : array {
        return [
            $data->id,
            $data->drink->name,
            $data->drink->code,
            $data->quantity_bottle,
            $data->drink->unit,
            number_format($data->purchase_price,0,',',' '),
            number_format($data->selling_price,0,',',' '),
            number_format(($data->purchase_price * $data->quantity_bottle),0,',',' '),
            number_format(($data->selling_price * $data->quantity_bottle),0,',',' '),
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
            'P.V',
            'Total P.A',
            'Total P.V'
        ] ;
    }
}

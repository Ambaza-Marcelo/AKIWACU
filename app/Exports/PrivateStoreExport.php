<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\PrivateStoreItem;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PrivateStoreExport implements FromCollection, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
     public function collection()
    {
        return PrivateStoreItem::orderBy('name')->get();
    }

    public function map($data) : array {
        return [
            $data->id,
            $data->name,
            $data->code,
            $data->quantity,
            $data->unit,
            $data->purchase_price
        ] ;
 
 
    }

    public function headings() : array {
        return [
            '#',
            'Article',
            'Code',
            'Quantite',
            'Unité',
            'P.A'
        ] ;
    }
}

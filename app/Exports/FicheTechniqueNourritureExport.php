<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\FoodItemDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FicheTechniqueNourritureExport implements FromCollection, WithMapping, WithHeadings
{

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return FoodItemDetail::all();
    }

    public function map($data) : array {
        return [
            $data->id,
            $data->name,
            $data->code,
            $data->vat,
            $data->selling_price,
            $data->food->name,
            $data->food->unit,
            $data->quantity,
            $data->food->purchase_price
        ] ;
 
 
    }

    public function headings() : array {
        return [
            '#',
            'Article',
            'Code',
            'Taux TVA',
			'Prix de vente',
			'Nom Ingredient',
			'Unité Ingredient',
			'Qté Ingredient',
			'P.A Ingredient'
        ] ;
    }
}

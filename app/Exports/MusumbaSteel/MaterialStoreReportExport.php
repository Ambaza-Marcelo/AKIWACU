<?php

namespace App\Exports\MusumbaSteel;

use Carbon\Carbon;
use App\Models\MsMaterialReport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MaterialStoreReportExport implements FromCollection, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $d1 = request()->input('start_date');
        $d2 = request()->input('end_date');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        return MsMaterialReport::select(
                        DB::raw('id,created_at,material_id,quantity_stock_initial,value_stock_initial,quantity_stockin,value_stockin,quantity_reception,value_reception,quantity_transfer,value_transfer,quantity_stockout,value_stockout,quantity_stock_final,value_stock_final,description'))->whereBetween('created_at',[$start_date,$end_date])/*->where('code_store',$code_store)*/->groupBy('id','created_at','material_id','quantity_stock_initial','value_stock_initial','quantity_stockin','value_stockin','quantity_reception','value_reception','quantity_transfer','value_transfer','quantity_stockout','value_stockout','quantity_stock_final','value_stock_final','description')->orderBy('id','asc')->get();
    }

    public function map($data) : array {
        return [
            $data->id,
            Carbon::parse($data->created_at)->format('d/m/Y'),
            $data->material->name,
            $data->material->code,
            $data->quantity_stock_initial,
            ($data->quantity_stock_initial * $data->material->cump),
            $data->quantity_stockin + $data->quantity_reception,
            ($data->value_stockin + $data->value_reception),
            $data->quantity_stockout + $data->quantity_transfer,
            (($data->quantity_stockout * $data->material->cump) + ($data->quantity_transfer * $data->material->cump)),
            ($data->quantity_stock_initial + $data->quantity_stockin + $data->quantity_reception) - ($data->quantity_stockout + $data->quantity_transfer),
            ((($data->quantity_stock_initial + $data->quantity_stockin + $data->quantity_reception) - ($data->quantity_stockout + $data->quantity_transfer)) * $data->material->cump),
            $data->description,
        ] ;
 
 
    }

    public function headings() : array {
        return [
            '#',
            'Date',
            'Article',
            'Code',
            'Quantite Stock Initial',
            'Valeur Stock Initial',
            'Quantite Entree',
            'Valeur Entree',
            'Quantite Sortie',
            'Valeur Sortie',
            'Quantite Stock Final',
            'Valeur Stock Final',
            'Description'
        ] ;
    }
}

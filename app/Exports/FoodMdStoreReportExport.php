<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\FoodBigStore;
use App\Models\FoodBigReport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


class FoodMdStoreReportExport implements FromCollection, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $d1 = request()->input('start_date');
        $d2 = request()->input('end_date');
        $code_store = request()->input('code_store');
        $store_signature = FoodBigStore::where('code',$code_store)->value('store_signature');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        return FoodBigReport::select(
                        DB::raw('id,created_at,created_by,food_id,date,document_no,type_transaction,quantity_stock_initial,cump,value_stock_initial,quantity_stockin,value_stockin,quantity_reception,value_reception,quantity_transfer,value_transfer,transfer_no,quantity_stockout,value_stockout,quantity_stock_final,value_stock_final'))->whereBetween('created_at',[$start_date,$end_date])/*->where('code_store',$code_store)*/->groupBy('id','created_at','created_by','document_no','date','type_transaction','food_id','quantity_stock_initial','cump','value_stock_initial','quantity_stockin','value_stockin','quantity_reception','value_reception','quantity_transfer','transfer_no','value_transfer','quantity_stockout','value_stockout','quantity_stock_final','value_stock_final')->orderBy('id','asc')->get();
    }

    public function map($data) : array {
        return [
            $data->id,
            Carbon::parse($data->created_at)->format('d/m/Y'),
            Carbon::parse($data->date)->format('d/m/Y'),
            $data->food->name,
            $data->food->code,
            $data->quantity_stock_initial,
            $data->cump,
            ($data->quantity_stock_initial * $data->cump),
            $data->quantity_stockin + $data->quantity_reception,
            ($data->quantity_stockin + $data->quantity_reception)*$data->cump,
            $data->quantity_stockout + $data->quantity_transfer,
            (($data->quantity_stockout * $data->cump) + ($data->quantity_transfer * $data->cump)),
            ($data->quantity_stock_initial + $data->quantity_stockin + $data->quantity_reception) - ($data->quantity_stockout + $data->quantity_transfer),
            ((($data->quantity_stock_initial + $data->quantity_stockin + $data->quantity_reception) - ($data->quantity_stockout + $data->quantity_transfer)) * $data->cump),
            $data->type_transaction,
            $data->document_no,
            $data->created_by,
            $data->description
        ] ;
 
 
    }

    public function headings() : array {
        return [
            '#',
            'Date de Saisie',
            'Date Operation',
            'Article',
            'Code',
            'Quantite Stock Initial',
            'C.U.M.P',
            'Valeur Stock Initial',
            'Quantite Entree',
            'Valeur Entree',
            'Quantite Sortie',
            'Valeur Sortie',
            'Quantite Stock Final',
            'Valeur Stock Final',
            'Type Transaction',
            'No Document',
            'Auteur',
            'Description'
        ] ;
    }
}

<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\DrinkSmallStore;
use App\Models\DrinkSmallReport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


class DrinkSmStoreReportExport implements FromCollection, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $d1 = request()->input('start_date');
        $d2 = request()->input('end_date');
        $code_store = request()->input('code_store');
        $store_signature = DrinkSmallStore::where('code',$code_store)->value('store_signature');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        return DrinkSmallReport::select(
                        DB::raw('id,date,created_at,created_by,cump,type_transaction,document_no,drink_id,quantity_stock_initial,value_stock_initial,quantity_stockin,value_stockin,quantity_transfer,value_reception,quantity_sold,value_transfer,quantity_stockout,value_stockout,quantity_stock_final,value_stock_final,description'))->whereBetween('date',[$start_date,$end_date])/*->where('code_store',$code_store)*/->groupBy('id','date','created_by','type_transaction','cump','document_no','created_at','drink_id','quantity_stock_initial','value_stock_initial','quantity_stockin','value_stockin','quantity_transfer','value_reception','quantity_sold','value_transfer','quantity_stockout','value_stockout','quantity_stock_final','value_stock_final','description')->orderBy('id','asc')->get();
    }

    public function map($data) : array {


        if($data->value_stockin){
            $value_stockin = $data->value_stockin;
        }elseif ($data->value_reception) {
            $value_stockin = $data->value_reception;
        }elseif ($data->value_transfer) {
            $value_stockin = $data->value_transfer;
        }elseif ($data->value_inventory) {
            $value_stockin = $data->value_inventory;
        }else{
            $value_stockin = 0;
        }

        if (!empty($data->quantity_stockout)) {
            $sortie = $data->quantity_stockout;
        }elseif (!empty($data->quantity_sold)) {
            $sortie = $data->quantity_sold;
        }else{
            $sortie = 0;
        }

        if (!empty($data->quantity_stockin)) {
            $entree = $data->quantity_stockin;
        }elseif (!empty($data->quantity_transfer)) {
            $entree = $data->quantity_transfer;
        }else{
            $entree = 0;
        }

        if(!empty($data->cump)){
            $cump = $data->cump;
        }else{
            $cump = $data->drink->cump;
        }

        return [
            $data->id,
            Carbon::parse($data->created_at)->format('d/m/Y'),
            Carbon::parse($data->date)->format('d/m/Y'),
            $data->drink->name,
            $data->drink->code,
            $data->quantity_stock_initial,
            $cump,
            ($data->quantity_stock_initial * $cump),
            $entree,
            $entree * $cump,
            $sortie,
            ($sortie * $cump),
            ($data->quantity_stock_initial + $entree) - $sortie,
            ((($data->quantity_stock_initial + $entree) - $sortie) * $cump),
            $data->created_by,
            $data->type_transaction,
            $data->document_no,
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
            'CUMP',
            'Valeur Stock Initial',
            'Quantite Entree',
            'Valeur Entree',
            'Quantite Sortie',
            'Valeur Sortie',
            'Quantite Stock Final',
            'Valeur Stock Final',
            'Auteur',
            'Type de Mouvement',
            'Document_no',
            'Description'
        ] ;
    }
}

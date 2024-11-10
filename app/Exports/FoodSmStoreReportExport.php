<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\FoodSmallReport;
use App\Models\FoodSmallStore;
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
        $d1 = request()->input('start_date');
        $d2 = request()->input('end_date');
        $code_store = request()->input('code_store');
        $store_signature = FoodSmallStore::where('code',$code_store)->value('store_signature');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        return FoodSmallReport::select(
                        DB::raw('id,created_at,food_id,quantity_stock_initial,quantity_stock_initial_portion,value_stock_initial,value_stock_initial_portion,quantity_stockin,value_stockin,quantity_portion,cump,quantity_inventory,value_inventory,quantity_inventory_portion,value_inventory_portion,quantity_reception,value_reception,quantity_transfer,value_transfer,quantity_stockout,value_stockout,quantity_stock_final,quantity_stock_final_portion,type_transaction,document_no,created_portion_by,created_by,description'))->whereBetween('created_at',[$start_date,$end_date])->groupBy('created_at','food_id','quantity_stock_initial','quantity_stock_initial_portion','value_stock_initial','value_stock_initial_portion','quantity_stockin','quantity_portion','cump','quantity_inventory','value_inventory','quantity_inventory_portion','value_inventory_portion','value_stockin','quantity_reception','value_reception','quantity_transfer','value_transfer','quantity_stockout','value_stockout','quantity_stock_final','quantity_stock_final_portion','type_transaction','document_no','created_portion_by','created_by','id','description')->orderBy('id')->get();
    }

    public function map($data) : array {
        /*
        if (!empty($data->quantity_stockin) || !empty($data->quantity_transfer) || !empty($data->quantity_portion)) {
            $entree_stock_transit = $data->quantity_stockin + $data->quantity_transfer + $data->quantity_portion;
            $entree_petit_stock = $data->quantity_stockin + $data->quantity_portion;
        }else{
            $entree_stock_transit = 0;
            $entree_petit_stock = 0;
        }

        if (!empty($data->quantity_stockin) || !empty($data->quantity_transfer) || !empty($data->quantity_stockout)) {

            $stock_transit_final = ($data->quantity_stock_initial + $data->quantity_stockin + $data->quantity_transfer) - $data->quantity_stockout;
            $petit_stock_final = 0;
        }elseif (!empty($data->quantity_portion)) {
            $stock_transit_final = 0;
            $petit_stock_final = ($data->quantity_stock_initial_portion + $data->quantity_stockin + $data->quantity_transfer) - $data->quantity_stockout;
        }

        */
        if (!empty($data->created_portion_by)) {
            $auteur = $data->created_portion_by;
        }else{
            $auteur = $data->created_by;
        }
        return [
            $data->id,
            Carbon::parse($data->created_at)->format('d/m/Y'),
            Carbon::parse($data->date)->format('d/m/Y'),
            $data->food->name,
            $data->food->code,
            $data->quantity_stock_initial,
            $data->food->foodMeasurement->purchase_unit,
            $data->quantity_stock_initial_portion,
            $data->food->foodMeasurement->production_unit,
            $data->cump,
            $data->quantity_transfer,
            $data->quantity_stockin,
            $data->quantity_portion,
            $data->quantity_stockout,
            $data->quantity_stock_final, 
            $data->quantity_stock_final_portion,                       
            $data->type_transaction,
            $data->document_no,                                    
            $auteur,
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
            'Stock Transit Initial',
            'Unité de mesure Stock Transit',
            'Petit Stock Initial',
            'Unité de mesure Petit Stock',
            'C.U.M.P',
            'Entrée Transfert',
            'Entrée Autre',
            'Portionnage',
            'Quantité Sortie',
            'Stock Transit Final',
            'Petit Stock Final',
            'Type Transaction',
            'No Document',
            'Auteur',
            'Description'
        ] ;
    }
}

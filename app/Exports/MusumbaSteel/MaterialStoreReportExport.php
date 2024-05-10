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
                        DB::raw('id,date,type_transaction,document_no,created_at,material_id,quantity_stock_initial,value_stock_initial,quantity_stockin,value_stockin,quantity_reception,value_reception,quantity_inventory,value_inventory,quantity_stockout,value_stockout,quantity_stock_final,value_stock_final,created_by,description'))->whereBetween('created_at',[$start_date,$end_date])/*->where('code_store',$code_store)*/->groupBy('id','date','type_transaction','document_no','created_at','material_id','quantity_stock_initial','value_stock_initial','quantity_stockin','value_stockin','quantity_reception','value_reception','quantity_inventory','value_inventory','quantity_stockout','value_stockout','quantity_stock_final','value_stock_final','description','created_by')->orderBy('id','desc')->get();
    }

    public function map($data) : array {

        if (!empty($data->quantity_inventory)) {
            $quantite = $data->quantity_inventory;
            $valeur = $data->value_inventory;
            $stock_total = $data->quantity_inventory;
            $stock_final = $data->quantity_inventory;
        }elseif (!empty($data->quantity_reception)) {
            $quantite = $data->quantity_reception;
            $valeur = $data->value_reception;
            $stock_total = $data->quantity_stock_initial + $quantite;
            $stock_final = ($data->quantity_stock_initial + $quantite) - ($data->quantity_stockout);
        }else{
            $quantite = $data->quantity_stockin;
            $valeur = $data->value_stockin;
            $stock_total = $data->quantity_stock_initial + $quantite;
            $stock_final = ($data->quantity_stock_initial + $quantite) - ($data->quantity_stockout);
        }

        return [
            $data->id,
            Carbon::parse($data->created_at)->format('d/m/Y'),
            $data->material->name,
            $data->material->code,
            $data->quantity_stock_initial,
            ($data->quantity_stock_initial * $data->material->cump),
            $quantite,
            $valeur,
            $data->quantity_stockout,
            ($data->quantity_stockout * $data->material->cump),
            $stock_final,
            ($stock_final * $data->material->cump),
            $data->type_transaction,
            $data->document_no,
            $data->created_by,
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
            'Type de Mouvement',
            'Document No',
            'Auteur',
            'Description'
        ] ;
    }
}

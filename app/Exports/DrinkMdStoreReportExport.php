<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\DrinkBigReport;
use App\Models\DrinkBigStore;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DrinkMdStoreReportExport implements FromCollection, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $d1 = request()->input('start_date');
        $d2 = request()->input('end_date');
        $code_store = request()->input('code_store');
        $store_signature = DrinkBigStore::where('code',$code_store)->value('store_signature');

        $startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';

        return DrinkBigReport::select(
                        DB::raw('id,date,type_transaction,cump,purchase_price,document_no,created_by,created_at,drink_id,quantity_stock_initial,value_stock_initial,quantity_stockin,value_stockin,quantity_reception,value_reception,quantity_transfer,value_transfer,quantity_stockout,value_stockout,quantity_stock_final,value_stock_final,description'))->whereBetween('date',[$start_date,$end_date])->where('code_store',$code_store)->groupBy('id','date','type_transaction','cump','purchase_price','document_no','created_by','description','created_at','drink_id','quantity_stock_initial','value_stock_initial','quantity_stockin','value_stockin','quantity_reception','value_reception','quantity_transfer','value_transfer','quantity_stockout','value_stockout','quantity_stock_final','value_stock_final')->orderBy('id','asc')->get();
    }

    public function map($data) : array {

        if(!empty($data->cump)){
            $cump = $data->cump;
        }else{
            $cump = $data->drink->cump;
        }

        if (!empty($data->quantity_stockin)) {
            $entree = $data->quantity_stockin;
            $purchase_price = $data->value_stockin/$data->quantity_stockin;
        }elseif (!empty($data->quantity_reception)) {
            $entree = $data->quantity_reception;
            $purchase_price = $data->value_reception/$data->quantity_reception;
        }else{
            $entree = 0;
            $purchase_price = 0;
        }

        if (!empty($data->quantity_stockout)) {
            $sortie = $data->quantity_stockout;
        }elseif (!empty($data->quantity_transfer)) {
            $sortie = $data->quantity_transfer;
        }else{
            $sortie = 0;
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
            $purchase_price,
            ($entree * $purchase_price),
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
            'P.A',
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

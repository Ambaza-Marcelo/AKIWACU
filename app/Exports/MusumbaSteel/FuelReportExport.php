<?php

namespace App\Exports\MusumbaSteel;

use Carbon\Carbon;
use App\Models\MsFuelReport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FuelReportExport implements FromCollection, WithMapping, WithHeadings
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

        return MsFuelReport::select(
                        DB::raw('id,created_at,date,pump_id,quantity_inventory,car_id,quantity_stock_initial,description,stock_total,cump,created_by,quantity_stockin,quantity_stockout,driver_id,start_index,end_index'))->whereBetween('created_at',[$start_date,$end_date])->groupBy('id','created_at','date','pump_id','car_id','quantity_stock_initial','description','cump','created_by','quantity_stockin','quantity_stockout','stock_total','driver_id','quantity_inventory','start_index','end_index')->orderBy('id','asc')->get();
    }

    public function map($data) : array {

        if (!empty($data->quantity_inventory)) {
            $quantite = $data->quantity_inventory;
            $stock_total = $data->quantity_inventory;
            $stock_final = $data->quantity_inventory;
        }else{
            $quantite = $data->quantity_stockin;
            $stock_total = $data->quantity_stock_initial + $data->quantity_stockin;
            $stock_final = ($data->quantity_stock_initial + $data->quantity_stockin) - ($data->quantity_stockout);
        }

        if (!empty($data->car_id)) {
            $plaque = $data->car->immatriculation;
            $firstname = $data->driver->firstname;
            $lastname = $data->driver->lastname;
        }else{
            $plaque = '';
            $firstname = '';
            $lastname = '';
        }

        return [
            $data->id,
            \Carbon\Carbon::parse($data->created_at)->format('d-m-Y'),
            \Carbon\Carbon::parse($data->date)->format('d-m-Y'),
            $data->pump->name,
            $data->pump->fuel->name,
            $data->quantity_stock_initial,
            $quantite,
            $stock_total,
            $data->quantity_stockout,
            $firstname.' '.$lastname.' '.$plaque,
            $stock_final,
            $data->start_index,
            $data->end_index,
            $data->created_by,
            $data->description
        ] ;
 
 
    }

    public function headings() : array {
        return [
            '#',
            'Date de Saisie',
            'Date Operation',
            'Cuve',
            'Carburant',
            'Q. Stock Initial',
            'Quantite Entree',
            'Stock Total',
            'Quantite Sortie',
            'Chauffeur/Plaque',
            'Stock Final',
            'Index Depart',
            'Index de fin',
            'Auteur',
            'Description'
        ] ;
    }
}

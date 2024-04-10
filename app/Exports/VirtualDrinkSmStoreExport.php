<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\VirtualDrinkSmStore;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class VirtualDrinkSmStoreExport implements FromCollection, WithMapping, WithHeadings
{

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $code_store = request()->input('code_store');

        $d1 = request()->input('start_date');
        $d2 = request()->input('end_date');
        
		$startDate = \Carbon\Carbon::parse($d1)->format('Y-m-d');
        $endDate = \Carbon\Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';
        
        return VirtualDrinkSmStore::select(
                        DB::raw('drink_id,date,quantity_bottle,unit,purchase_price,cump,selling_price'))->where('drink_id','!=','')->where('code',$code_store)->whereBetween('date',[$start_date,$end_date])->get();
    }

    public function map($data) : array {
        return [
            $data->id,
            Carbon::parse($data->date)->format('d/m/Y'),
            $data->drink->name,
            $data->drink->code,
            $data->quantity_bottle,
            $data->drink->unit,
            $data->purchase_price,
            $data->drink->cump,
            $data->selling_price,
            ($data->purchase_price * $data->quantity_bottle),
            ($data->selling_price * $data->quantity_bottle),
        ] ;
 
 
    }

    public function headings() : array {
        return [
            '#',
            'Date',
            'Article',
            'Code',
            'Quantite',
            'Unité',
            'P.A',
            'C.U.M.P',
            'P.V',
            'Total P.A',
            'Total P.V'
        ] ;
    }
}

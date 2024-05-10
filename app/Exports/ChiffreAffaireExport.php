<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\FactureDetail;
use App\Models\FoodItemDetail;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ChiffreAffaireExport implements FromCollection, WithMapping, WithHeadings
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

        return FactureDetail::select(
                        DB::raw('id,food_item_id,updated_at,drink_id,barrist_item_id,bartender_item_id,salle_id,service_id,breakfast_id,swiming_pool_id,kidness_space_id,invoice_number,invoice_date,item_quantity,customer_name,client_id,drink_order_no,food_order_no,bartender_order_no,barrist_order_no,booking_no,item_total_amount,vat,item_price_nvat,etat,reseted_by,cn_motif,auteur,validated_by,employe_id'))/*->where('etat','!=','0')->where('etat','!=','-1')*/->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','drink_id','food_item_id','bartender_item_id','barrist_item_id','salle_id','service_id','breakfast_id','swiming_pool_id','kidness_space_id','invoice_date','updated_at','invoice_number','item_quantity','etat','reseted_by','cn_motif','drink_order_no','food_order_no','bartender_order_no','barrist_order_no','booking_no','customer_name','client_id','item_total_amount','vat','item_price_nvat','auteur','validated_by','employe_id')->orderBy('customer_name','asc')->get();
    }

    public function map($data) : array {

    	if (!empty($data->drink_id)) {
    		$libelle = $data->drink->name;
            $type = "BOISSONS";
            $pa = $data->drink->purchase_price;
            $cump = $data->drink->cump;
    	}elseif (!empty($data->food_item_id)) {
    		$libelle = $data->foodItem->name;
            $type = "CUISINE";
            $cump = 0;
            $pa = FoodItemDetail::where('code',$data->code)->value('purchase_price');
    	}elseif (!empty($data->barrist_item_id)) {
    		$libelle = $data->barristItem->name;
            $type = "BARRIST(COFFEE BAR)";
            $cump = 0;
            $pa = 0;
    	}elseif (!empty($data->bartender_item_id)) {
    		$libelle = $data->bartenderItem->name;
            $type = "BARTENDER(GODET&VERRE)";
            $cump = 0;
            $pa = 0;
    	}elseif (!empty($data->salle_id)) {
    		$libelle = $data->salle->name;
            $type = "SALLE DE CONFERENCE";
            $cump = 0;
            $pa = 0;
    	}elseif (!empty($data->service_id)) {
    		$libelle = $data->service->name;
            $type = "PRESTATION DE SERVICE";
            $cump = 0;
            $pa = 0;
    	}elseif (!empty($data->swiming_pool_id)) {
            $libelle = $data->swimingPool->name;
            $type = "PISCINE";
            $cump = 0;
            $pa = 0;
        }elseif (!empty($data->breakfast_id)) {
            $libelle = "BREAKFAST";
            $type = "BREAKFAST";
            $cump = 0;
            $pa = 0;
        }elseif (!empty($data->kidness_space_id)) {
            $libelle = $data->kidnessSpace->name;
            $type = "JEUX ENFANT";
            $cump = 0;
            $pa = 0;
        }

    	if (!empty($data->client_id)) {
    		$customer_name = $data->client->customer_name;
    	}else{
    		$customer_name = $data->customer_name;
    	}

    	if (!empty($data->drink_id)) {
    		$order_no = $data->drink_order_no;
    	}elseif (!empty($data->food_item_id)) {
    		$order_no = $data->food_order_no;
    	}elseif (!empty($data->barrist_item_id)) {
    		$order_no = $data->barrist_order_no;
    	}elseif (!empty($data->bartender_item_id)) {
    		$order_no = $data->bartender_order_no;
    	}elseif (!empty($data->salle_id)) {
    		$order_no = $data->booking_no;
    	}elseif (!empty($data->service_id)) {
    		$order_no = $data->booking_no;
    	}elseif (!empty($data->breakfast_id)) {
            $order_no = $data->booking_no;
        }elseif (!empty($data->swiming_pool_id)) {
            $order_no = $data->booking_no;
        }elseif (!empty($data->kidness_space_id)) {
            $order_no = $data->booking_no;
        }

        if ($data->etat == '0') {
            $etat = "ENCOURS....";
            $auteur = " ";
            $motif = " ";
        }elseif ($data->etat === '-1') {
            $etat = "ANNULE";
            $auteur = $data->reseted_by;
            $motif = $data->cn_motif;
        }elseif ($data->etat === '1') {
            $etat = "CASH";
            $auteur = " ";
            $motif = " ";
        }elseif ($data->etat === '01') {
            $etat = "CREDIT";
            $auteur = " ";
            $motif = " ";
        }

        if (!empty($data->employe_id)) {
            $serveur = $data->employe->name;
        }else{
            $serveur = "";
        }

        return [
            $data->id,
            $data->updated_at,
            Carbon::parse($data->invoice_date)->format('d/m/Y'),
			$data->invoice_number,
			$order_no,
            $serveur,
            $customer_name,
            $libelle,
            $type,
            $data->item_quantity,
            $cump,
            $pa,
            $pa * $data->item_quantity,
			$data->item_price_nvat,
			$data->vat,
			$data->item_total_amount,
            $etat,
            $data->auteur,
            $data->validated_by,
            $auteur,
            $motif
        ] ;
 
 
    }

    public function headings() : array {
        return [
            '#',
            'updated at ',
            'Date de facturation',
            'No Facture',
            'No Commande',
            'Serveur',
            'Nom du Client',
            'Libellé',
            'Type',
            'Quantité',
            'C.U.M.P',
            'P.A',
            'TOTAL P.A',
            'PV HTVA',
            'TVA',
            'TTC',
            'ETAT',
            'Auteur',
            'Valide Par',
            'ANNULE PAR',
            'Motif'
        ] ;
    }
}

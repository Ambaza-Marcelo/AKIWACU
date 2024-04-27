<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\Facture;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class FactureArecouvreExport implements FromCollection, WithMapping, WithHeadings
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

        return Facture::select(
                        DB::raw('id,invoice_number,statut_paied,etat_recouvrement,montant_total_credit,montant_recouvre,reste_credit,bank_name,cheque_no,date_recouvrement,nom_recouvrement,note_recouvrement,invoice_date,customer_name,client_id,drink_order_no,food_order_no,bartender_order_no,barrist_order_no,booking_no,updated_at'))->where('etat','01')->whereBetween('invoice_date',[$start_date,$end_date])->groupBy('id','statut_paied','etat_recouvrement','montant_total_credit','montant_recouvre','reste_credit','bank_name','cheque_no','date_recouvrement','nom_recouvrement','note_recouvrement','invoice_date','invoice_number','drink_order_no','food_order_no','bartender_order_no','barrist_order_no','booking_no','customer_name','client_id','updated_at')->orderBy('id','asc')->get();
    }

    public function map($data) : array {

    	if (!empty($data->drink_id)) {
    		$libelle = $data->drink->name;
    	}elseif (!empty($data->food_item_id)) {
    		$libelle = $data->foodItem->name;
    	}elseif (!empty($data->barrist_item_id)) {
    		$libelle = $data->barristItem->name;
    	}elseif (!empty($data->bartender_item_id)) {
    		$libelle = $data->bartenderItem->name;
    	}elseif (!empty($data->salle_id)) {
    		$libelle = $data->salle->name;
    	}elseif (!empty($data->service_id)) {
    		$libelle = $data->service->name;
    	}

    	if (!empty($data->client_id)) {
    		$customer_name = $data->client->customer_name;
            $customer_telephone = $data->client->telephone;
    	}else{
    		$customer_name = $data->customer_name;
            $customer_telephone = "";
    	}

    	if (!empty($data->drink_order_no)) {
    		$order_no = $data->drink_order_no;
    	}elseif (!empty($data->food_order_no)) {
    		$order_no = $data->food_order_no;
    	}elseif (!empty($data->barrist_order_no)) {
    		$order_no = $data->barrist_order_no;
    	}elseif (!empty($data->bartender_order_no)) {
    		$order_no = $data->bartender_order_no;
    	}elseif (!empty($data->booking_no)) {
    		$order_no = $data->booking_no;
    	}elseif (!empty($data->booking_no)) {
    		$order_no = $data->booking_no;
    	}

        if ($data->statut_paied == '1') {
            $mode_paiement = "CASH";
        }elseif ($data->statut_paied == '2') {
            $mode_paiement = "BANQUE";
        }elseif ($data->statut_paied == '3') {
            $mode_paiement = "LUMICASH";
        }elseif ($data->statut_paied == '4') {
            $mode_paiement = "AUTRES";
        }else{
            $mode_paiement = "";
        }

        if ($data->etat_recouvrement == '1') {
            $type_paiement = "PAIEMENT PARTIEL";
            $date_recouvrement = Carbon::parse($data->date_recouvrement)->format('d/m/Y');
            $updated_at = Carbon::parse($data->updated_at)->format('d/m/Y');
        }elseif ($data->etat_recouvrement == '2') {
            $type_paiement = "PAIEMENT TOTAL";
            $date_recouvrement = Carbon::parse($data->date_recouvrement)->format('d/m/Y');
            $updated_at = Carbon::parse($data->updated_at)->format('d/m/Y');
        }else{
            $type_paiement = "ENCOURS";
            $date_recouvrement = "";
            $updated_at = "";
        }



        return [
            $data->id,
            Carbon::parse($data->invoice_date)->format('d/m/Y'),
			$data->invoice_number,
			$order_no,
            $customer_name,
            $customer_telephone,
            $data->montant_total_credit,
            $data->montant_recouvre,
            $data->reste_credit,
            $type_paiement,
            $mode_paiement,
            $data->bank_name,
            $data->cheque_no,
            $date_recouvrement,
            $updated_at,
            $data->nom_recouvrement,
            $data->note_recouvrement
        ] ;
 
 
    }

    public function headings() : array {
        return [
            '#',
            'Date de facturation',
            'No Facture',
            'No Commande',
            'Nom du Client',
            'Telephone du Client',
            'Montant Total Crédit',
            'Montant Total Recouvré',
            'Solde',
            'Type de Paiement',
            'Mode de paiement',
            'Banque',
            'Cheque No',
            'Date Recouvrement',
            'Date de saisie',
            'Nom chargé de Recouvrement',
            'Note de recouvrement'
        ] ;
    }
}

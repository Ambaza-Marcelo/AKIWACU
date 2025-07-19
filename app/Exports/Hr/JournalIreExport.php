<?php

namespace App\Exports\Hr;

use Carbon\Carbon;
use App\Models\HrPaiement;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class JournalIreExport implements FromCollection, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $d1 = request()->input('start_date');
        
        $d2 = request()->input('end_date');

        $startDate = Carbon::parse($d1)->format('Y-m-d');
        $endDate = Carbon::parse($d2)->format('Y-m-d');

        $start_date = $startDate.' 00:00:00';
        $end_date = $endDate.' 23:59:59';
        return HrPaiement::where('employe_id','!=','')->whereBetween('date_debut',[$start_date,$end_date])->get();
    }

    public function map($data) : array {

        $remuneration_brute = $data->somme_salaire_base + $data->allocation_familiale + $data->indemnite_logement + $data->indemnite_deplacement + $data->prime_fonction;

        if ($remuneration_brute < 450000 ) {
            $inss_pension = ($remuneration_brute * 6)/100;
            $inss_risque = 2400;
        }else{
            $inss_pension = 27000;
            $inss_risque = 2400;
        }


        if ($remuneration_brute < 450000) {
                $inss = ($remuneration_brute * 4)/100;
                $somme_cotisation_inss = ($remuneration_brute * 4)/100;
                $inss_employeur = ($remuneration_brute * 6)/100;
        }else{
                $inss = (450000 * 4)/100;
                $somme_cotisation_inss = (450000 * 4)/100;
                $inss_employeur = (450000 * 6)/100;
        }


        $total_deductions = $data->somme_cotisation_inss + $data->assurance_maladie_employe + $data->somme_impot + $data->retenue_pret + $data->soins_medicaux + $data->autre_retenue;
        $net_a_payer = $remuneration_brute - $total_deductions;


        $base_imposable = ($remuneration_brute - $data->indemnite_logement - $data->indemnite_deplacement - $inss - $data->assurance_maladie_employe);

        if ($base_imposable >= 0 && $base_imposable <= 150000) {
                $somme_impot = 0;
                $pourecentage = 0;
                $rabattement = $base_imposable;

                $fourchette20 = 0;
                $fourchette30 = 0;

                $ipr0 = 0;
                $ipr20 = 0;
                $ipr30 = 0;

                $ipr_a_payer = 0;

        }elseif ($base_imposable > 150000 && $base_imposable <= 300000) {
                $somme_impot = ((($base_imposable - 150000) * 20)/100);
                $pourecentage = 20;
                $rabattement = 150000;

                $fourchette20 = $base_imposable - $rabattement;
                $fourchette30 = 0;

                $ipr0 = 0;
                $ipr20 = ($fourchette20 * 20)/100;
                $ipr30 = 0;

                $ipr_a_payer = $ipr0 + $ipr20 + $ipr30;
        }elseif ($base_imposable > 300000) {
                $somme_impot = (30000 + (($base_imposable - 300000) * 30)/100);  
                $pourecentage = 30; 
                $rabattement = 150000; 

                $fourchette20 = 150000;
                $fourchette30 = $base_imposable - $rabattement - $fourchette20;

                $ipr0 = 0;
                $ipr20 = ($fourchette20 * 20)/100;
                $ipr30 = ($fourchette30 * 30)/100;

                $ipr_a_payer = $ipr0 + $ipr20 + $ipr30;
        }

        return [
            $data->id,
            Carbon::parse($data->date_debut)->format('m/Y'),
            $data->employe->matricule_no,
            $data->employe->firstname, 
            $data->employe->lastname,
            $data->somme_salaire_base,
            ($data->allocation_familiale),
            ($data->indemnite_logement),
            ($data->indemnite_deplacement),
            ($data->prime_fonction),
            ($remuneration_brute),
            ($data->somme_cotisation_inss),
            ($data->assurance_maladie_employe),
            $base_imposable,
            ($rabattement),
            ($fourchette20),
            ($fourchette30),
            ($ipr0),
            ($ipr20),
            ($ipr30),
            ($ipr_a_payer),
        ] ;
 
 
    }

    public function headings() : array {
        return [
            '#',
            'Mois/Année',
            'Matricule',
            'Nom',
            'Prenom',
            'S. de base',
            'All. Fam.',
            'Ind. de log.',
            'Ind. de dépl.',
            'Prime/Autr. Ind.',
            'Rém. brute',
            'INSS Employé',
            'A. Mal. Employé',
            'Base IPR',
            'Rabatmt 0%',
            'Fourchette 20%',
            'Fourchette 30%',
            'IPR 0%',
            'IPR 20%',
            'IPR 30%',
            'IRE a payer',
        ] ;
    }
}

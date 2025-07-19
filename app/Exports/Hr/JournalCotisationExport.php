<?php

namespace App\Exports\Hr;

use Carbon\Carbon;
use App\Models\HrPaiement;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class JournalCotisationExport implements FromCollection, WithMapping, WithHeadings
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
        $total_deductions = $data->somme_cotisation_inss + $data->assurance_maladie_employe + $data->somme_impot + $data->retenue_pret + $data->soins_medicaux + $data->autre_retenue;
        $net_a_payer = $remuneration_brute - $total_deductions;
        if ($remuneration_brute < 450000 ) {
            $inss_pension = ($remuneration_brute * 6)/100;
            $inss_risque = 2400;
            $base_pension = $remuneration_brute;
        }else{
            $inss_pension = 27000;
            $inss_risque = 2400;
            $base_pension = 450000;
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
            ($base_pension),
            ($data->somme_cotisation_inss),
            ($data->inss_employeur),
            (($data->inss_employeur + $data->somme_cotisation_inss)),
            ($base_pension),
            ($inss_pension),
            $inss_risque,
            ($inss_pension + $inss_risque),
            ($data->inss_employeur + $data->somme_cotisation_inss),
        ] ;
 
 
    }

    public function headings() : array {
        return [
            '#',
            'Mois/Année',
            'Matricule',
            'Nom',
            'Prenom',
            'Salaire de base',
            'Allocation Familiale',
            'Indemnité de logement',
            'Indemnité de déplacement',
            'Prime de fonction',
            'Rémuneration brute',
            'Base Pension',
            'Pension INSS Employé',
            'Pension INSS Employeur',
            'Total INSS',
            'Base Risque',
            'INSS Pension',
            'INSS Risque',
            'Total INSS Risque',
            'INSS a Payer'
        ] ;
    }
}

<?php

namespace App\Exports\Hr;

use Carbon\Carbon;
use App\Models\HrPaiement;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class JournalPaieExport implements FromCollection, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $code = request()->input('code');

        return HrPaiement::where('code',$code)->where('employe_id','!=','')->get();
    }

    public function map($data) : array {

        $remuneration_brute = $data->somme_salaire_base + $data->allocation_familiale + $data->indemnite_logement + $data->indemnite_deplacement + $data->prime_fonction;
        $total_deductions = floor($data->somme_cotisation_inss) + floor($data->assurance_maladie_employe) + floor($data->somme_impot) + floor($data->retenue_pret) + floor($data->soins_medicaux) + floor($data->autre_retenue);
        $net_a_payer = floor($remuneration_brute) - floor($total_deductions);
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
            Carbon::parse($data->created_at)->format('m/Y'),
            $data->employe->matricule_no,
            $data->employe->firstname, 
            $data->employe->lastname,
            $data->employe->numero_compte,
            $data->somme_salaire_base,
            floor($data->allocation_familiale),
            floor($data->indemnite_logement),
            floor($data->indemnite_deplacement),
            floor($data->prime_fonction),
            floor($remuneration_brute),
            floor($inss_pension),
            floor($inss_risque),
            floor($data->somme_cotisation_inss),
            floor($data->inss_employeur),
            floor($data->assurance_maladie_employe),
            floor($data->assurance_maladie_employeur),
            round($data->somme_impot),
            floor($data->retenue_pret),
            floor($data->soins_medicaux),
            floor($data->autre_retenue),
            floor($total_deductions),
            floor($net_a_payer),
            
        ] ;
 
 
    }

    public function headings() : array {
        return [
            '#',
            'Mois/Année',
            'Matricule',
            'Nom',
            'Prenom',
            'No Compte',
            'Salaire de base',
            'Allocation Familiale',
            'Indemnité de logement',
            'Indemnité de déplacement',
            'Prime de fonction',
            'Rémuneration brute',
            'INSS Pension',
            'INSS Risque',
            'INSS Employé',
            'INSS Employeur',
            'Assurance Maladie Employé',
            'Assurance Maladie Employeur',
            'IRE Retenu',
            'Retenu Pret',
            'Soins medicaux',
            'Autres retenues',
            'Total Retenue',
            'Salaire Net'
        ] ;
    }
}

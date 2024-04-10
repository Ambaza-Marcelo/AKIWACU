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
        
        if ($remuneration_brute < 450000 ) {
            $inss_pension = ($remuneration_brute * 6)/100;
            $inss_risque = 2400;
            $base_pension = $remuneration_brute;
        }else{
            $inss_pension = 27000;
            $inss_risque = 2400;
            $base_pension = 450000;
        }

        $net = $remuneration_brute - $data->somme_cotisation_inss - $data->somme_impot;

            if ($net < 250000) {
                $assurance_maladie_employe = 0;
                $assurance_maladie_employeur = 15000;
            }else{
                $assurance_maladie_employe = 6000;
                $assurance_maladie_employeur = 9000;
            }
        $total_deductions = $data->somme_cotisation_inss + $assurance_maladie_employe + $data->somme_impot + $data->retenue_pret + $data->soins_medicaux + $data->autre_retenue;
        $net_a_payer = $remuneration_brute - $total_deductions;
        return [
            $data->id,
            Carbon::parse($data->created_at)->format('m/Y'),
            $data->employe->matricule_no,
            $data->employe->firstname, 
            $data->employe->lastname,
            $data->employe->numero_compte,
            $data->somme_salaire_base,
            ($data->allocation_familiale),
            ($data->indemnite_logement),
            ($data->indemnite_deplacement),
            ($data->prime_fonction),
            ($remuneration_brute),
            ($inss_pension),
            ($inss_risque),
            ($data->somme_cotisation_inss),
            ($data->inss_employeur),
            ($assurance_maladie_employe),
            ($assurance_maladie_employeur),
            ($data->somme_impot),
            ($data->retenue_pret),
            ($data->soins_medicaux),
            ($data->autre_retenue),
            ($total_deductions),
            ($net_a_payer),
            
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

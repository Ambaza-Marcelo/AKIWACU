<?php

namespace App\Exports\Hr;

use Carbon\Carbon;
use App\Models\HrEmploye;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmployeExport implements FromCollection, WithMapping, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $company_id = request()->input('company_id');

        return HrEmploye::orderBy('firstname')->get();
    }

    public function map($data) : array {

    	if($data->gender == 1){
    		$gender = "MALE";
    	}else{
    		$gender = "FEMELE";
    	}

    	if ($data->statut_matrimonial == 1) {
    		$statut_matrimonial = "Marié(e)";
    	}elseif ($data->statut_matrimonial == 2) {
    		$statut_matrimonial = "Divorcé(e)";
    	}elseif ($data->statut_matrimonial == 3) {
    		$statut_matrimonial = "Veuf(ve)";
    	}elseif ($data->statut_matrimonial == 4) {
    		$statut_matrimonial = "Célibtaire";
    	}

        return [
            $data->id,
            $data->matricule_no,
            $data->firstname, 
            $data->lastname,
            $data->numero_compte,
            $gender,
            $statut_matrimonial,
            $data->birthdate,
            $data->cni,
            $data->date_debut,
            $data->departement->name,
			$data->service->name,
			$data->fonction->name,
            $data->somme_salaire_base,
            $data->allocation_familiale,
            $data->indemnite_logement,
            $data->indemnite_deplacement,
            $data->prime_fonction,
            $data->somme_salaire_base + $data->indemnite_logement + $data->indemnite_deplacement + $data->prime_fonction,
            
        ] ;
 
 
    }

    public function headings() : array {
        return [
            '#',
            'Matricule',
            'Nom',
            'Prenom',
            'No Compte',
            'Genre',
            'Statut Matrimonial',
            'Date Naissance',
            'CNI',
            'Date Embauche',
            'Departement',
            'Service',
            'Fonction',
            'Salaire de base',
            'Allocation Familiale',
            'Indemnité de logement',
            'Indemnité de déplacement',
            'Prime de fonction',
            'Rémuneration brute',
        ] ;
    }
}

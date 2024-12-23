<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrEmployesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_employes', function (Blueprint $table) {
            $table->id();
            $table->string('firstname')->nullable(true);
            $table->string('lastname')->nullable(true);
            $table->string('phone_no')->nullable(true);
            $table->string('image')->nullable(true);
            $table->string('mail')->nullable(true);
            $table->string('matricule_no')->nullable(true);
            $table->string('type_contrat')->nullable(true);
            $table->unique('matricule_no');
            $table->string('fathername')->nullable(true);
            $table->string('mothername')->nullable(true);
            $table->string('cni')->nullable(true);
            $table->date('birthdate')->nullable(true);
            $table->string('bloodgroup')->nullable(true);
            $table->string('pays')->nullable(true);
            $table->string('province')->nullable(true);
            $table->string('commune')->nullable(true);
            $table->string('zone')->nullable(true);
            $table->string('quartier')->nullable(true);
            $table->string('gender')->nullable(true);
            $table->string('statut_matrimonial')->nullable(true);
            $table->string('children_number')->nullable(true);
            $table->string('province_residence_actuel')->nullable(true);
            $table->string('commune_residence_actuel')->nullable(true);
            $table->string('zone_residence_actuel')->nullable(true);
            $table->string('quartier_residence_actuel')->nullable(true);
            $table->string('avenue_residence_actuel')->nullable(true);
            $table->string('wife_or_husband')->nullable(true);
            $table->string('numero')->nullable(true);
            $table->string('code_departement')->nullable(true);
            $table->string('code_service')->nullable(true);
            $table->string('document')->nullable(true);
            $table->dateTime('date_debut')->nullable(true);
            $table->dateTime('date_fin')->nullable(true);
            $table->string('etat')->default('0');
            $table->string('somme_salaire_base')->nullable(true);
            $table->string('somme_salaire_net')->nullable(true);
            $table->string('nbre_jours_ouvrables')->nullable(true);
            $table->string('nbre_jours_conges')->nullable(true);
            $table->string('nbre_jours_conges_consomes')->nullable(true);
            $table->string('code_banque')->nullable(true);
            $table->string('numero_compte')->nullable(true);
            $table->string('indemnite_deplacement')->nullable(true);
            $table->string('indemnite_logement')->nullable(true);
            $table->string('prime_fonction')->nullable(true);
            $table->string('taux_assurance_maladie')->nullable(true);
            $table->string('taux_retraite')->nullable(true);
            $table->string('interet_credit_logement')->nullable(true);
            $table->string('retenue_pret')->nullable(true);
            $table->string('autre_retenue')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->bigInteger('departement_id')->unsigned()->nullable(true);
            $table->bigInteger('service_id')->unsigned()->nullable(true);
            $table->bigInteger('fonction_id')->unsigned()->nullable(true);
            $table->bigInteger('grade_id')->unsigned()->nullable(true);
            $table->bigInteger('banque_id')->unsigned()->nullable(true);
            $table->bigInteger('company_id')->unsigned()->nullable(true);
            $table->foreign('departement_id')
                    ->references('id')
                    ->on('hr_departements')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('service_id')
                    ->references('id')
                    ->on('hr_services')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('fonction_id')
                    ->references('id')
                    ->on('hr_fonctions')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('grade_id')
                    ->references('id')
                    ->on('hr_grades')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('banque_id')
                    ->references('id')
                    ->on('hr_banques')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('company_id')
                    ->references('id')
                    ->on('hr_companies')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hr_employes');
    }
}

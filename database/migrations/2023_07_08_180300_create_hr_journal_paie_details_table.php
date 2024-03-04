<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrJournalPaieDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_journal_paie_details', function (Blueprint $table) {
            $table->id();
            $table->string('mois')->nullable(true);
            $table->string('date_debut')->nullable(true);
            $table->string('date_fin')->nullable(true);
            $table->string('etat')->nullable(true);
            $table->string('code')->nullable(true);
            $table->string('statut_matrimonial')->nullable(true);
            $table->string('children_number')->nullable(true);
            $table->string('allocation_familiale')->nullable(true);
            $table->string('somme_indemnite_logement')->nullable(true);
            $table->string('somme_indemnite_deplacement')->nullable(true);
            $table->string('retenue_pret')->nullable(true);
            $table->string('somme_prime_fonction')->nullable(true);
            $table->string('somme_autre_indemnite')->nullable(true);
            $table->string('impot_sur_salaire')->nullable(true);
            $table->string('somme_salaire_base')->nullable(true);
            $table->string('somme_salaire_brut_imposable')->nullable(true);
            $table->string('somme_salaire_brut_non_imposable')->nullable(true);
            $table->string('somme_salaire_net_imposable')->nullable(true);
            $table->string('somme_salaire_net_non_imposable')->nullable(true);
            $table->string('somme_cotisation')->nullable(true);
            $table->string('somme_cotisation_employeur')->nullable(true);
            $table->string('somme_impot')->nullable(true);
            $table->string('code_banque')->nullable(true);
            $table->string('code_departement')->nullable(true);
            $table->string('code_service')->nullable(true);
            $table->string('numero_compte')->nullable(true);
            $table->string('indemnite_deplacement')->nullable(true);
            $table->string('indemnite_logement')->nullable(true);
            $table->string('prime_fonction')->nullable(true);
            $table->string('somme_cotisation_inss')->nullable(true);
            $table->string('inss_employeur')->nullable(true);
            $table->string('soins_medicaux')->nullable(true);
            $table->string('assurance_maladie_employe')->nullable(true);
            $table->string('assurance_maladie_employeur')->nullable(true);
            $table->string('somme_autre_cotisation')->nullable(true);
            $table->string('nbre_jours_conge_pris')->nullable(true);
            $table->string('nbre_personnes_a_charge')->nullable(true);
            $table->string('nbre_jours_ouvrables')->nullable(true);
            $table->string('avance_sur_salaire')->nullable(true);
            $table->string('autre_retenue')->nullable(true);
            $table->string('total_retenue')->nullable(true);
            $table->string('nbre_jours_prestes')->nullable(true);
            $table->string('cloture_par')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->bigInteger('employe_id')->unsigned()->nullable(true);
            $table->bigInteger('take_conge_id')->unsigned()->nullable(true);
            $table->foreign('employe_id')
                    ->references('id')
                    ->on('hr_employes')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('take_conge_id')
                    ->references('id')
                    ->on('hr_take_conges')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('company_id')->unsigned()->nullable(true);
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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('hr_journal_paie_details');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrReglagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_reglages', function (Blueprint $table) {
            $table->id();
            $table->string('nbre_jours_ouvrables')->nullable(true);
            $table->string('nbre_jours_feries')->nullable(true);
            $table->string('jour_anticipation_conge')->nullable(true);
            $table->string('jour_conge_par_mois')->nullable(true);
            $table->string('min_jour_conge_paye')->nullable(true);
            $table->string('max_jour_conge_paye')->nullable(true);
            $table->string('taux_assurance_maladie')->nullable(true);
            $table->string('taux_retraite')->nullable(true);
            $table->string('interet_credit_logement')->nullable(true);
            $table->string('retenue_pret')->nullable(true);
            $table->string('prafond_impot')->nullable(true);
            $table->string('prafond_cotisation')->nullable(true);
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
        Schema::dropIfExists('hr_reglages');
    }
}

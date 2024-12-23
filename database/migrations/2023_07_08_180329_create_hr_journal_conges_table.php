<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrJournalCongesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_journal_conges', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_heure_debut')->nullable(true);
            $table->dateTime('date_heure_fin')->nullable(true);
            $table->string('nbre_jours_conge_pris')->nullable(true);
            $table->string('nbre_heures_conge_pris')->nullable(true);
            $table->string('etat')->nullable(true);
            $table->string('code')->nullable(true);
            $table->string('valide_par')->nullable(true);
            $table->string('confirme_par')->nullable(true);
            $table->string('approuve_par')->nullable(true);
            $table->string('auteur')->nullable(true);
            $table->bigInteger('employe_id')->unsigned()->nullable(true);
            $table->bigInteger('type_conge_id')->unsigned()->nullable(true);
            $table->foreign('employe_id')
                    ->references('id')
                    ->on('hr_employes')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('type_conge_id')
                    ->references('id')
                    ->on('hr_type_conges')
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
        Schema::dropIfExists('hr_journal_conges');
    }
}

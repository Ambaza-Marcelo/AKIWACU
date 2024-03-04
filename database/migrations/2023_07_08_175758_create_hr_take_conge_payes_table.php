<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrTakeCongePayesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_take_conge_payes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->nullable(true);
            $table->string('date_heure_debut')->nullable(true);
            $table->string('date_heure_fin')->nullable(true);
            $table->string('nbre_jours_conge_paye')->nullable(true);
            $table->string('nbre_jours_conge_sollicite')->nullable(true);
            $table->string('nbre_jours_conge_pris')->nullable(true);
            $table->string('nbre_jours_conge_restant')->nullable(true);
            $table->string('valide_par')->nullable(true);
            $table->string('confirme_par')->nullable(true);
            $table->string('approuve_par')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('etat')->nullable(true);
            $table->bigInteger('employe_id')->unsigned()->nullable(true);
            $table->bigInteger('conge_paye_id')->unsigned()->nullable(true);
            $table->foreign('employe_id')
                    ->references('id')
                    ->on('hr_employes')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('conge_paye_id')
                    ->references('id')
                    ->on('hr_conge_payes')
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
        Schema::dropIfExists('hr_take_conge_payes');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}

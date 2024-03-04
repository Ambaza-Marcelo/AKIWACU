<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrStagiairesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_stagiaires', function (Blueprint $table) {
            $table->id();
            $table->string('firstname')->nullable(true);
            $table->string('lastname')->nullable(true);
            $table->string('phone_no')->nullable(true);
            $table->string('mail')->nullable(true);
            $table->string('fathername')->nullable(true);
            $table->string('mothername')->nullable(true);
            $table->string('cni')->nullable(true);
            $table->string('birthdate')->nullable(true);
            $table->string('bloodgroup')->nullable(true);
            $table->string('province')->nullable(true);
            $table->string('commune')->nullable(true);
            $table->string('zone')->nullable(true);
            $table->string('quartier')->nullable(true);
            $table->string('gender')->nullable(true);
            $table->string('etat')->nullable(true);
            $table->string('academique_ou_professionnel')->nullable(true);
            $table->string('province_residence_actuel')->nullable(true);
            $table->string('commune_residence_actuel')->nullable(true);
            $table->string('zone_residence_actuel')->nullable(true);
            $table->string('quartier_residence_actuel')->nullable(true);
            $table->string('avenue_residence_actuel')->nullable(true);
            $table->string('numero')->nullable(true);
            $table->string('date_debut')->nullable(true);
            $table->string('date_fin')->nullable(true);
            $table->string('somme_prime')->nullable(true);
            $table->string('auteur')->nullable(true);
            $table->bigInteger('departement_id')->unsigned()->nullable(true);
            $table->bigInteger('service_id')->unsigned()->nullable(true);
            $table->bigInteger('fonction_id')->unsigned()->nullable(true);
            $table->bigInteger('grade_id')->unsigned()->nullable(true);
            $table->bigInteger('ecole_id')->unsigned()->nullable(true);
            $table->bigInteger('filiere_id')->unsigned()->nullable(true);
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
            $table->foreign('ecole_id')
                    ->references('id')
                    ->on('hr_ecoles')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('filiere_id')
                    ->references('id')
                    ->on('hr_filieres')
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
        Schema::dropIfExists('hr_stagiaires');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}

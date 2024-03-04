<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSotbCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sotb_cars', function (Blueprint $table) {
            $table->id();
            $table->string('marque')->nullable(true);
            $table->string('couleur')->nullable(true);
            $table->string('immatriculation');
            $table->string('chassis_no')->nullable(true);
            $table->string('type')->nullable(true);
            $table->string('etat')->nullable(true);
            $table->string('auteur')->nullable(true);
            $table->bigInteger('fuel_id')->unsigned()->nullable(true);
            $table->foreign('fuel_id')
                    ->references('id')
                    ->on('sotb_fuels')
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
        Schema::dropIfExists('sotb_cars');
    }
}

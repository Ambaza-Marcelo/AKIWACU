<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsFuelPumpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_fuel_pumps', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->string('emplacement')->nullable();
            $table->string('capacity');
            $table->bigInteger('fuel_id')->unsigned();
            $table->string('quantity');
            $table->string('purchase_price')->nullable();
            $table->string('cost_price')->nullable();
            $table->string('cump')->nullable();
            $table->string('total_purchase_value')->nullable();
            $table->string('total_cost_value')->nullable();
            $table->string('quantite_seuil')->nullable();
            $table->string('etat')->nullable(true);
            $table->string('auteur')->nullable();
            $table->boolean('verified')->nullable(true);
            $table->foreign('fuel_id')
                    ->references('id')
                    ->on('ms_fuels')
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
        Schema::dropIfExists('ms_fuel_pumps');
    }
}

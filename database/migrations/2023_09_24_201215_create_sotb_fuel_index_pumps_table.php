<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSotbFuelIndexPumpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sotb_fuel_index_pumps', function (Blueprint $table) {
            $table->id();
            $table->string('start_index');
            $table->string('end_index');
            $table->string('date')->nullable(true);
            $table->string('final_index')->nullable(true);
            $table->string('auteur')->nullable();
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
        Schema::dropIfExists('sotb_fuel_index_pumps');
    }
}

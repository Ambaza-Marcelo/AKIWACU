<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialMeasurementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_measurements', function (Blueprint $table) {
            $table->id();
            $table->string('purchase_unit')->nullable(true);
            $table->unique('purchase_unit');
            $table->string('stockout_unit')->nullable(true);
            $table->unique('stockout_unit');
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
        Schema::dropIfExists('material_measurements');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsFuelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_fuels', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(true);
            $table->string('quantity')->nullable(true);
            $table->string('purchase_price')->nullable(true);
            $table->string('cump')->nullable(true);
            $table->string('cost_price')->nullable(true);
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
        Schema::dropIfExists('ms_fuels');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSotbDriverCarsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sotb_driver_cars', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('car_id')->unsigned();
            $table->string('auteur')->nullable();
            $table->foreign('car_id')
                    ->references('id')
                    ->on('sotb_cars')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('driver_id')->unsigned();
            $table->foreign('driver_id')
                    ->references('id')
                    ->on('sotb_drivers')
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
        Schema::dropIfExists('sotb_driver_cars');
    }
}

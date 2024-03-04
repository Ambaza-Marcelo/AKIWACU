<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSotbFuelRequisitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sotb_fuel_requisitions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('requisition_no');
            $table->string('requisition_signature')->nullable(true);
            $table->text('description')->nullable(true);
            $table->text('rejected_motif')->nullable(true);
            $table->string('type_pump')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('validated_by')->nullable(true);
            $table->string('confirmed_by')->nullable(true);
            $table->string('approuved_by')->nullable(true);
            $table->string('rejected_by')->nullable(true);
            $table->string('reseted_by')->nullable(true);
            $table->string('status')->default('0');
            $table->bigInteger('car_id')->unsigned()->nullable(true);
            $table->foreign('car_id')
                    ->references('id')
                    ->on('sotb_cars')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('fuel_id')->unsigned()->nullable(true);
            $table->foreign('fuel_id')
                    ->references('id')
                    ->on('sotb_fuels')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('driver_id')->unsigned()->nullable(true);
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
        Schema::dropIfExists('sotb_fuel_requisitions');
    }
}

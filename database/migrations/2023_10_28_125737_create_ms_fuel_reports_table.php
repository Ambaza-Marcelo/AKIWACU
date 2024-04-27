<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsFuelReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_fuel_reports', function (Blueprint $table) {
            $table->id();
            $table->string('date')->nullable();
            $table->bigInteger('fuel_id')->unsigned()->nullable(true);    
            $table->string('quantity_stock_initial')->nullable();
            $table->string('value_stock_initial')->nullable();
            $table->string('quantity_stockin')->nullable();
            $table->string('value_stockin')->nullable();
            $table->string('quantity_transfer')->nullable();
            $table->string('value_transfer')->nullable();
            $table->string('quantity_return')->nullable();
            $table->string('value_return')->nullable();
            $table->string('stock_total')->nullable();
            $table->string('quantity_stockout')->nullable();
            $table->string('value_stockout')->nullable();
            $table->string('quantity_reception')->nullable();
            $table->string('value_reception')->nullable();
            $table->string('quantity_inventory')->nullable();
            $table->string('value_inventory')->nullable();
            $table->string('quantity_stock_final')->nullable();
            $table->string('value_stock_final')->nullable();
            $table->string('stockin_no')->nullable();
            $table->string('reception_no')->nullable();
            $table->string('stockout_no')->nullable();
            $table->string('transfer_no')->nullable();
            $table->string('return_no')->nullable();
            $table->string('inventory_no')->nullable();
            $table->string('destination')->nullable();
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('validated_by')->nullable(true);
            $table->string('confirmed_by')->nullable(true);
            $table->string('approuved_by')->nullable(true);
            $table->string('rejected_by')->nullable(true);
            $table->string('description')->nullable(true);
            $table->string('type_transaction')->nullable(true);
            $table->string('document_no')->nullable(true);
            $table->string('cump')->nullable();
            $table->string('purchase_price')->nullable();
            $table->string('transaction')->nullable();
            $table->string('start_index')->nullable(true);
            $table->string('end_index')->nullable(true);
            $table->string('final_index')->nullable(true);
            $table->bigInteger('pump_id')->unsigned()->nullable(true);
            $table->bigInteger('car_id')->unsigned()->nullable(true);
            $table->bigInteger('driver_id')->unsigned()->nullable(true);
            $table->foreign('pump_id')
                    ->references('id')
                    ->on('ms_fuel_pumps')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('fuel_id')
                    ->references('id')
                    ->on('ms_fuels')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('car_id')
                    ->references('id')
                    ->on('ms_cars')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('driver_id')
                    ->references('id')
                    ->on('ms_drivers')
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
        Schema::dropIfExists('ms_fuel_reports');
    }
}

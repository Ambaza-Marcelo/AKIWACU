<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSotbFuelStockoutDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sotb_fuel_stockout_details', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('quantity')->nullable(true);
            $table->string('unit')->nullable(true);
            $table->string('price')->nullable(true);
            $table->string('stockout_no');
            $table->string('stockout_signature')->nullable(true);
            $table->string('requisition_no')->nullable(true);
            $table->string('asker')->nullable(true);
            $table->string('destination')->nullable(true);
            $table->string('store_type')->nullable(true);
            $table->string('item_purchase_or_sale_currency')->nullable(true);
            $table->string('item_movement_type')->nullable(true);
            $table->string('item_movement_invoice_ref')->nullable(true);
            $table->string('item_movement_description')->nullable(true);
            $table->text('description')->nullable(true);
            $table->text('rejected_motif')->nullable(true);
            $table->string('total_value')->nullable(true);
            $table->string('purchase_price')->nullable(true);
            $table->string('selling_price')->nullable(true);
            $table->string('total_purchase_value')->nullable(true);
            $table->string('total_selling_value')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('validated_by')->nullable(true);
            $table->string('confirmed_by')->nullable(true);
            $table->string('approuved_by')->nullable(true);
            $table->string('rejected_by')->nullable(true);
            $table->string('reseted_by')->nullable(true);
            $table->string('status')->default('0');
            $table->bigInteger('fuel_id')->unsigned();
            $table->foreign('fuel_id')
                    ->references('id')
                    ->on('sotb_fuels')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('pump_id')->unsigned()->nullable(true);
            $table->foreign('pump_id')
                    ->references('id')
                    ->on('sotb_fuel_pumps')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('car_id')->unsigned()->nullable(true);
            $table->foreign('car_id')
                    ->references('id')
                    ->on('sotb_cars')
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
        Schema::dropIfExists('sotb_fuel_stockout_details');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodStockoutDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_stockout_details', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('quantity')->nullable(true);
            $table->string('quantity_portion')->nullable(true);
            $table->string('unit_portion')->nullable(true);
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
            $table->string('status')->default('1');
            $table->bigInteger('origin_sm_store_id')->unsigned()->nullable(true);
            $table->bigInteger('origin_bg_store_id')->unsigned()->nullable(true);
            $table->bigInteger('origin_extra_store_id')->unsigned()->nullable(true);
            $table->foreign('origin_sm_store_id')
                    ->references('id')
                    ->on('food_small_stores')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('origin_bg_store_id')
                    ->references('id')
                    ->on('food_big_stores')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('origin_extra_store_id')
                    ->references('id')
                    ->on('food_extra_big_stores')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('food_id')->unsigned();
            $table->foreign('food_id')
                    ->references('id')
                    ->on('foods')
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
        Schema::dropIfExists('food_stockout_details');
    }
}

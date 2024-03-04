<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarristProductionStores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barrist_production_stores', function (Blueprint $table) {
            $table->id();
            $table->string('quantity')->nullable(true);
            $table->string('food_transfer_no')->nullable(true);
            $table->string('drink_transfer_no')->nullable(true);
            $table->string('quantity_food')->nullable(true);
            $table->string('quantity_drink')->nullable(true);
            $table->string('name')->nullable(true);
            $table->string('code')->nullable(true);
            $table->string('emplacement')->nullable(true);
            $table->string('manager')->nullable(true);
            $table->string('vat')->nullable(true);
            $table->string('item_ct')->nullable(true);
            $table->string('item_tl')->nullable(true);
            $table->string('unit')->nullable(true);
            $table->string('unit_food')->nullable(true);
            $table->string('unit_drink')->nullable(true);
            $table->string('purchase_price')->nullable(true);
            $table->string('selling_price')->nullable(true);
            $table->string('cost_price')->nullable(true);
            $table->string('cump')->nullable(true);
            $table->string('total_value')->nullable(true);
            $table->string('total_purchase_value')->nullable(true);
            $table->string('total_selling_value')->nullable(true);
            $table->string('total_cost_value')->nullable(true);
            $table->string('total_cump_value')->nullable(true);
            $table->string('threshold_quantity')->nullable(true);
            $table->boolean('verified')->default(false);
            $table->bigInteger('barrist_item_id')->unsigned()->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->text('description')->nullable(true);
            $table->foreign('barrist_item_id')
                    ->references('id')
                    ->on('barrist_items')
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
        Schema::dropIfExists('barrist_production_stores');
    }
}

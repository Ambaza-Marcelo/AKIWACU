<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrinkSmallStoreInventoryDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drink_small_store_inventory_details', function (Blueprint $table) {
            $table->id();
            $table->string('inventory_no')->nullable(true);
            $table->string('code_store')->nullable();
            $table->string('inventory_signature')->nullable(true);
            $table->string('date')->nullable(true);
            $table->string('title')->nullable(true);
            $table->string('quantity')->nullable(true);
            $table->string('quantity_ml')->nullable(true);
            $table->string('unit')->nullable(true);
            $table->string('cump')->nullable(true);
            $table->string('purchase_price')->nullable(true);
            $table->string('selling_price')->nullable(true);
            $table->string('selling_price_ml')->nullable(true);
            $table->string('total_purchase_value')->nullable(true);
            $table->string('total_selling_value')->nullable(true);
            $table->string('total_cump_value')->nullable(true);
            $table->string('new_quantity')->nullable(true);
            $table->string('new_quantity_ml')->nullable(true);
            $table->string('new_selling_price_ml')->nullable(true);
            $table->string('new_unit')->nullable(true);
            $table->string('new_cump')->nullable(true);
            $table->string('new_purchase_price')->nullable(true);
            $table->string('new_selling_price')->nullable(true);
            $table->string('new_total_purchase_value')->nullable(true);
            $table->string('new_total_selling_value')->nullable(true);
            $table->string('new_total_cump_value')->nullable(true);
            $table->string('new_total_selling_value_ml')->nullable(true);
            $table->string('relicat')->nullable(true);
            $table->string('relicat_ml')->nullable(true);
            $table->text('description');
            $table->string('status')->nullable(true);
            $table->text('rejected_motif')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('validated_by')->nullable(true);
            $table->string('confirmed_by')->nullable(true);
            $table->string('approuved_by')->nullable(true);
            $table->string('rejected_by')->nullable(true);
            $table->string('reseted_by')->nullable(true);
            $table->bigInteger('drink_id')->unsigned();
            $table->bigInteger('store_id')->unsigned()->nullable(true);
            $table->foreign('drink_id')
                    ->references('id')
                    ->on('drinks')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('store_id')
                    ->references('id')
                    ->on('drink_small_stores')
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
        Schema::dropIfExists('drink_small_store_inventory_details');
    }
}

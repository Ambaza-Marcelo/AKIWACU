<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarristItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barrist_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->string('specification')->nullable(true);
            $table->string('vat')->nullable(true);
            $table->string('item_ct')->nullable(true);
            $table->string('item_tl')->nullable(true);
            $table->string('code_store')->nullable(true);
            $table->string('quantity')->nullable(true);
            $table->string('unit')->nullable(true);
            $table->string('purchase_price')->nullable(true);
            $table->string('cost_price')->nullable(true);
            $table->string('taux_marge')->nullable(true);
            $table->string('selling_price')->nullable(true);
            $table->string('cump')->nullable(true);
            $table->string('taux_reduction')->nullable(true);
            $table->string('taux_majoration')->nullable(true);
            $table->string('taux_staff')->nullable(true);
            $table->string('threshold_quantity')->nullable(true);
            $table->string('expiration_date')->nullable(true);
            $table->string('status')->nullable(true);
            $table->string('store_type')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('image')->nullable(true);
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
        Schema::dropIfExists('barrist_items');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drinks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->unique('name');
            $table->unique('code');
            $table->string('specification')->nullable(true);
            $table->string('vat')->nullable(true);
            $table->string('item_ct')->nullable(true);
            $table->string('item_tl')->nullable(true);
            $table->string('brarudi_price')->nullable(true);
            $table->string('code_store')->nullable(true);
            $table->string('quantity_bottle')->nullable(true);
            $table->string('quantity_ml')->nullable(true);
            $table->string('unit')->nullable(true);
            $table->string('purchase_price')->nullable(true);
            $table->string('cost_price')->nullable(true);
            $table->string('selling_price')->nullable(true);
            $table->string('cump')->nullable(true);
            $table->string('taux_reduction')->nullable(true);
            $table->string('taux_majoration')->nullable(true);
            $table->string('taux_staff')->nullable(true);
            $table->string('taux_marge')->nullable(true);
            $table->string('threshold_quantity')->nullable(true);
            $table->string('expiration_date')->nullable(true);
            $table->string('status')->nullable(true);
            $table->string('store_type')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('image')->nullable(true);
            $table->bigInteger('dcategory_id')->unsigned()->nullable(true);
            $table->foreign('dcategory_id')
                    ->references('id')
                    ->on('drink_categories')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('drink_measurement_id')->unsigned()->nullable(true);
            $table->foreign('drink_measurement_id')
                    ->references('id')
                    ->on('drink_measurements')
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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('drinks');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}

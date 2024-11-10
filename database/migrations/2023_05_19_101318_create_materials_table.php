<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->unique('name');
            $table->unique('code');
            $table->string('specification')->nullable(true);
            $table->string('code_store')->nullable(true);
            $table->string('quantity')->nullable(true);
            $table->string('unit')->nullable(true);
            $table->string('purchase_price')->nullable(true);
            $table->string('cost_price')->nullable(true);
            $table->string('selling_price')->nullable(true);
            $table->string('cump')->nullable(true);
            $table->string('threshold_quantity')->nullable(true);
            $table->string('expiration_date')->nullable(true);
            $table->string('status')->nullable(true);
            $table->string('store_type')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->bigInteger('mcategory_id')->unsigned()->nullable(true);
            $table->foreign('mcategory_id')
                    ->references('id')
                    ->on('material_categories')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('material_measurement_id')->unsigned()->nullable(true);
            $table->foreign('material_measurement_id')
                    ->references('id')
                    ->on('material_measurements')
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
        Schema::dropIfExists('materials');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}

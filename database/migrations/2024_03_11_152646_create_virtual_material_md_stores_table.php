<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVirtualMaterialMdStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('virtual_material_md_stores', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date')->nullable(true);
            $table->string('quantity')->nullable(true);
            $table->string('name')->nullable(true);
            $table->string('code')->nullable(true);
            $table->string('specification')->nullable(true);
            $table->string('store_signature')->nullable(true);
            $table->string('emplacement')->nullable(true);
            $table->string('manager')->nullable(true);
            $table->string('unit')->nullable(true);
            $table->string('purchase_price')->nullable(true);
            $table->string('selling_price')->nullable(true);
            $table->string('cost_price')->nullable(true);
            $table->string('cump')->nullable(true);
            $table->string('total_value_bottle')->nullable(true);
            $table->string('total_purchase_value')->nullable(true);
            $table->string('total_selling_value')->nullable(true);
            $table->string('total_cost_value')->nullable(true);
            $table->string('total_cump_value')->nullable(true);
            $table->string('threshold_quantity')->nullable(true);
            $table->boolean('verified')->default(false);
            $table->bigInteger('material_id')->unsigned()->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->text('description')->nullable(true);
            $table->foreign('material_id')
                    ->references('id')
                    ->on('materials')
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
        Schema::dropIfExists('virtual_material_md_stores');
    }
}

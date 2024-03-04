<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSotbMaterialReturnDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sotb_material_return_details', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('transfer_no')->nullable(true);
            $table->string('return_no');
            $table->string('return_signature')->nullable(true);
            $table->string('quantity_returned')->nullable(true);
            $table->string('quantity_transfered')->nullable(true);
            $table->string('unit')->nullable(true);
            $table->string('price')->nullable(true);
            $table->string('cump')->nullable(true);
            $table->string('total_value_returned')->nullable(true);
            $table->string('total_value_transfered')->nullable(true);
            $table->text('description')->nullable(true);
            $table->text('rejected_motif')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('validated_by')->nullable(true);
            $table->string('confirmed_by')->nullable(true);
            $table->string('approuved_by')->nullable(true);
            $table->string('rejected_by')->nullable(true);
            $table->string('reseted_by')->nullable(true);
            $table->string('status')->default('1');
            $table->bigInteger('material_id')->unsigned();
            $table->bigInteger('origin_sm_store_id')->unsigned()->nullable(true);
            $table->bigInteger('origin_md_store_id')->unsigned()->nullable(true);
            $table->bigInteger('destination_md_store_id')->unsigned()->nullable(true);
            $table->bigInteger('destination_bg_store_id')->unsigned()->nullable(true);
            $table->foreign('material_id')
                    ->references('id')
                    ->on('sotb_materials')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('origin_sm_store_id')
                    ->references('id')
                    ->on('sotb_material_sm_stores')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('destination_md_store_id')
                    ->references('id')
                    ->on('sotb_material_md_stores')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('origin_md_store_id')
                    ->references('id')
                    ->on('sotb_material_md_stores')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('destination_bg_store_id')
                    ->references('id')
                    ->on('sotb_material_bg_stores')
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
        Schema::dropIfExists('sotb_material_return_details');
    }
}

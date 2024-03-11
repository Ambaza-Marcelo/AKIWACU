<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialTransfers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_transfers', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('transfer_no');
            $table->string('requisition_no');
            $table->unique('transfer_no');
            $table->string('transfer_signature')->nullable(true);
            $table->text('description')->nullable(true);
            $table->text('rejected_motif')->nullable(true);
            $table->string('type_store')->nullable(true);
            $table->string('code_store')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('validated_by')->nullable(true);
            $table->string('confirmed_by')->nullable(true);
            $table->string('approuved_by')->nullable(true);
            $table->string('rejected_by')->nullable(true);
            $table->string('reseted_by')->nullable(true);
            $table->string('status')->default('1');
            $table->bigInteger('origin_store_id')->unsigned()->nullable(true);
            $table->bigInteger('origin_extra_store_id')->unsigned()->nullable(true);
            $table->bigInteger('destination_store_id')->unsigned()->nullable(true);
            $table->bigInteger('destination_bg_store_id')->unsigned()->nullable(true);
            $table->foreign('origin_store_id')
                    ->references('id')
                    ->on('material_big_stores')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('destination_store_id')
                    ->references('id')
                    ->on('material_small_stores')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('origin_extra_store_id')
                    ->references('id')
                    ->on('material_extra_big_stores')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('destination_bg_store_id')
                    ->references('id')
                    ->on('material_big_stores')
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
        Schema::dropIfExists('material_transfers');
    }
}

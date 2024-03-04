<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodStockinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_stockins', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('stockin_no');
            $table->string('stockin_signature')->nullable(true);
            $table->string('store_type')->nullable(true);
            $table->string('receptionist')->nullable(true);
            $table->string('handingover')->nullable(true);
            $table->string('origin')->nullable(true);
            $table->string('item_purchase_or_sale_currency')->nullable(true);
            $table->string('item_movement_type')->nullable(true);
            $table->string('item_movement_invoice_ref')->nullable(true);
            $table->string('item_movement_description')->nullable(true);
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
            $table->bigInteger('destination_bg_store_id')->unsigned()->nullable(true);
            $table->bigInteger('destination_sm_store_id')->unsigned()->nullable(true);
            $table->bigInteger('destination_extra_store_id')->unsigned()->nullable(true);
            $table->foreign('destination_bg_store_id')
                    ->references('id')
                    ->on('food_big_stores')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('destination_sm_store_id')
                    ->references('id')
                    ->on('food_small_stores')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('destination_extra_store_id')
                    ->references('id')
                    ->on('food_extra_big_stores')
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
        Schema::dropIfExists('food_stockins');
    }
}

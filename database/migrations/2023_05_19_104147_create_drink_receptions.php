<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrinkReceptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drink_receptions', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->string('reception_no');
            $table->unique('reception_no');
            $table->string('reception_signature')->nullable(true);
            $table->string('invoice_no')->nullable(true);
            $table->string('order_no')->nullable(true);
            $table->string('purchase_no')->nullable(true);
            $table->string('origin')->nullable(true);
            $table->string('receptionist')->nullable(true);
            $table->string('handingover')->nullable(true);
            $table->string('vat_taxpayer')->nullable(true);
            $table->string('vat_supplier_payer')->nullable(true);
            $table->string('invoice_currency')->nullable(true);
            $table->string('waybill')->nullable(true);
            $table->string('type_store')->nullable(true);
            $table->string('code_store')->nullable(true);
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
            $table->string('type_reception')->default('1');
            $table->double('vat_rate')->default('0');
            $table->bigInteger('supplier_id')->unsigned()->nullable(true);
            $table->foreign('supplier_id')
                    ->references('id')
                    ->on('suppliers')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('destination_store_id')->unsigned()->nullable(true);
            $table->bigInteger('destination_extra_store_id')->unsigned()->nullable(true);
            $table->foreign('destination_store_id')
                    ->references('id')
                    ->on('drink_big_stores')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('destination_extra_store_id')
                    ->references('id')
                    ->on('drink_extra_big_stores')
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
        Schema::dropIfExists('drink_receptions');
    }
}

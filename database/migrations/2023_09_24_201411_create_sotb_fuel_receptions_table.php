<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSotbFuelReceptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sotb_fuel_receptions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('reception_no');
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
            $table->text('description')->nullable(true);
            $table->text('rejected_motif')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('validated_by')->nullable(true);
            $table->string('confirmed_by')->nullable(true);
            $table->string('approuved_by')->nullable(true);
            $table->string('rejected_by')->nullable(true);
            $table->string('reseted_by')->nullable(true);
            $table->string('status')->default('0');
            $table->bigInteger('supplier_id')->unsigned()->nullable(true);
            $table->foreign('supplier_id')
                    ->references('id')
                    ->on('sotb_suppliers')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('pump_id')->unsigned()->nullable(true);
            $table->foreign('pump_id')
                    ->references('id')
                    ->on('sotb_fuel_pumps')
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
        Schema::dropIfExists('sotb_fuel_receptions');
    }
}

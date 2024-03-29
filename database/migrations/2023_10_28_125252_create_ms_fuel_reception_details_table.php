<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsFuelReceptionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_fuel_reception_details', function (Blueprint $table) {
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
            $table->string('quantity_ordered')->nullable(true);
            $table->string('quantity_received')->nullable(true);
            $table->string('quantity_remaining')->nullable(true);
            $table->string('unit')->nullable('true');
            $table->string('price_nvat')->nullable(true);
            $table->string('vat')->nullable(true);
            $table->string('price_wvat')->nullable(true);
            $table->string('total_amount_ordered')->nullable(true);
            $table->string('total_amount_received')->nullable(true);
            $table->string('total_amount_remaining')->nullable(true);
            $table->string('purchase_price')->nullable(true);
            $table->string('selling_price')->nullable(true);
            $table->string('total_amount_purchase')->nullable(true);
            $table->string('total_amount_selling')->nullable(true);
            $table->bigInteger('fuel_id')->unsigned()->nullable(true);
            $table->foreign('fuel_id')
                    ->references('id')
                    ->on('ms_fuels')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('supplier_id')->unsigned()->nullable(true);
            $table->foreign('supplier_id')
                    ->references('id')
                    ->on('ms_fuel_suppliers')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('pump_id')->unsigned()->nullable(true);
            $table->foreign('pump_id')
                    ->references('id')
                    ->on('ms_fuel_pumps')
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
        Schema::dropIfExists('ms_fuel_reception_details');
    }
}

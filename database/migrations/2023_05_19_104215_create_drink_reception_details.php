<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrinkReceptionDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drink_reception_details', function (Blueprint $table) {
            $table->id();
            $table->date('date');
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
            $table->string('reception_no');
            $table->string('type_store')->nullable(true);
            $table->string('code_store')->nullable(true);
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
            $table->string('reception_signature')->nullable(true);
            $table->text('description')->nullable(true);
            $table->text('rejected_motif')->nullable(true);
            $table->string('total_value')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('validated_by')->nullable(true);
            $table->string('confirmed_by')->nullable(true);
            $table->string('approuved_by')->nullable(true);
            $table->string('rejected_by')->nullable(true);
            $table->string('reseted_by')->nullable(true);
            $table->string('status')->default('1');
            $table->bigInteger('supplier_id')->unsigned()->nullable(true);
            $table->foreign('supplier_id')
                    ->references('id')
                    ->on('suppliers')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('drink_id')->unsigned();
            $table->foreign('drink_id')
                    ->references('id')
                    ->on('drinks')
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
        Schema::dropIfExists('drink_reception_details');
    }
}

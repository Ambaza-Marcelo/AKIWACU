<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrinkPurchaseDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drink_purchase_details', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->double('quantity')->nullable(true);
            $table->string('unit')->nullable(true);
            $table->double('price')->nullable(true);
            $table->double('purchase_price')->nullable(true);
            $table->string('purchase_no');
            $table->string('type_store')->nullable(true);
            $table->string('code_store')->nullable(true);
            $table->string('purchase_signature')->nullable(true);
            $table->text('description')->nullable(true);
            $table->text('rejected_motif')->nullable(true);
            $table->double('total_value')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('validated_by')->nullable(true);
            $table->string('confirmed_by')->nullable(true);
            $table->string('approuved_by')->nullable(true);
            $table->string('rejected_by')->nullable(true);
            $table->string('reseted_by')->nullable(true);
            $table->string('status')->default('1');
            $table->string('vat_taxpayer')->nullable(true);
            $table->string('vat_supplier_payer')->nullable(true);
            $table->double('vat_rate')->default('0');
            $table->string('invoice_currency')->nullable(true);
            $table->double('price_nvat')->nullable(true);
            $table->double('vat')->nullable(true);
            $table->double('price_wvat')->nullable(true);
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
        Schema::dropIfExists('drink_purchase_details');
    }
}

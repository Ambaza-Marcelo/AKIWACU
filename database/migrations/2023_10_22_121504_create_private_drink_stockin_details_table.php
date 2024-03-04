<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrivateDrinkStockinDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('private_drink_stockin_details', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('quantity')->nullable(true);
            $table->string('unit')->nullable(true);
            $table->string('price_nvat')->nullable(true);
            $table->string('vat')->nullable(true);
            $table->string('price_wvat')->nullable(true);
            $table->string('total_amount_purchase')->nullable(true);
            $table->string('total_amount_selling')->nullable(true);
            $table->string('purchase_price')->nullable(true);
            $table->string('selling_price')->nullable(true);
            $table->string('stockin_no');
            $table->string('receptionist')->nullable(true);
            $table->string('handingover')->nullable(true);
            $table->string('origin')->nullable(true);
            $table->string('item_purchase_or_sale_currency')->nullable(true);
            $table->string('item_movement_type')->nullable(true);
            $table->string('item_movement_invoice_ref')->nullable(true);
            $table->string('item_movement_description')->nullable(true);
            $table->string('stockin_signature')->nullable(true);
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
            $table->bigInteger('private_store_item_id')->unsigned();
            $table->foreign('private_store_item_id')
                    ->references('id')
                    ->on('private_store_items')
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
        Schema::dropIfExists('private_drink_stockin_details');
    }
}

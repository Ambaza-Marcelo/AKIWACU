<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrivateStoreReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('private_store_reports', function (Blueprint $table) {
            $table->id();
            $table->string('quantity_stock_initial')->nullable();
            $table->string('value_stock_initial')->nullable();
            $table->string('quantity_stockin')->nullable();
            $table->string('value_stockin')->nullable();
            $table->string('stock_total')->nullable();
            $table->string('quantity_stockout')->nullable();
            $table->string('value_stockout')->nullable();
            $table->string('quantity_sold')->nullable();
            $table->string('value_sold')->nullable();
            $table->string('quantity_inventory')->nullable();
            $table->string('value_inventory')->nullable();
            $table->string('quantity_stock_final')->nullable();
            $table->string('value_stock_final')->nullable();
            $table->string('stockin_no')->nullable();
            $table->string('reception_no')->nullable();
            $table->string('stockout_no')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('inventory_no')->nullable();
            $table->string('destination')->nullable();
            $table->string('asker')->nullable();
            $table->string('unit')->nullable(true);
            $table->string('date')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('validated_by')->nullable(true);
            $table->string('confirmed_by')->nullable(true);
            $table->string('approuved_by')->nullable(true);
            $table->string('rejected_by')->nullable(true);
            $table->text('description')->nullable(true);
            $table->string('type_transaction')->nullable(true);
            $table->string('document_no')->nullable(true);
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
        Schema::dropIfExists('private_store_reports');
    }
}

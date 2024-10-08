<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodSmallReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_small_reports', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('food_id')->unsigned();
            $table->string('quantity_stock_initial')->nullable();
            $table->string('code_store')->nullable();
            $table->string('code_store_origin')->nullable();
            $table->string('code_store_destination')->nullable();
            $table->string('value_stock_initial')->nullable();
            $table->string('quantity_stockin')->nullable();
            $table->string('value_stockin')->nullable();
            $table->string('quantity_transfer')->nullable();
            $table->string('value_transfer')->nullable();
            $table->string('stock_total')->nullable();
            $table->string('quantity_stockout')->nullable();
            $table->string('value_stockout')->nullable();
            $table->string('quantity_reception')->nullable();
            $table->string('value_reception')->nullable();
            $table->string('quantity_inventory')->nullable();
            $table->string('value_inventory')->nullable();
            $table->string('quantity_inventory_portion')->nullable();
            $table->string('value_inventory_portion')->nullable();
            $table->string('quantity_portion')->nullable();
            $table->string('unit_portion')->nullable();
            $table->string('value_portion')->nullable();
            $table->string('quantity_stock_final')->nullable();
            $table->string('value_stock_final')->nullable();
            $table->string('stockin_no')->nullable();
            $table->string('reception_no')->nullable();
            $table->string('stockout_no')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('transfer_no')->nullable();
            $table->string('return_no')->nullable();
            $table->string('inventory_no')->nullable();
            $table->string('destination')->nullable();
            $table->string('asker')->nullable();
            $table->string('unit')->nullable(true);
            $table->string('cump')->nullable(true);
            $table->date('date')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('validated_by')->nullable(true);
            $table->string('confirmed_by')->nullable(true);
            $table->string('approuved_by')->nullable(true);
            $table->string('rejected_by')->nullable(true);
            $table->text('description')->nullable(true);
            $table->string('type_transaction')->nullable(true);
            $table->string('document_no')->nullable(true);
            $table->string('quantity_stock_initial_portion')->nullable(true);
            $table->string('value_stock_initial_portion')->nullable(true);
            $table->string('quantity_stock_final_portion')->nullable(true);
            $table->string('value_stock_final_portion')->nullable(true);
            $table->string('created_portion_by')->nullable(true);
            $table->string('description_portion')->nullable(true);
            $table->bigInteger('employe_id')->unsigned()->nullable(true);
            $table->foreign('food_id')
                    ->references('id')
                    ->on('foods')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('employe_id')
                    ->references('id')
                    ->on('employes')
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
        Schema::dropIfExists('food_small_reports');
    }
}

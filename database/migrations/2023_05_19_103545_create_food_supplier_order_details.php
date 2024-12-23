<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodSupplierOrderDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_supplier_order_details', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->string('quantity');
            $table->string('unit')->nullable('true');
            $table->string('purchase_price')->nullable(true);
            $table->string('order_no');
            $table->string('order_signature')->nullable(true);
            $table->text('description')->nullable(true);
            $table->text('rejected_motif')->nullable(true);
            $table->string('total_value');
            $table->string('type_store')->nullable(true);
            $table->string('code_store')->nullable(true);
            $table->string('purchase_no')->nullable(true);
            $table->string('end_date')->nullable(true);
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
            $table->bigInteger('food_id')->unsigned();
            $table->foreign('food_id')
                    ->references('id')
                    ->on('foods')
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
        Schema::dropIfExists('food_supplier_order_details');
    }
}

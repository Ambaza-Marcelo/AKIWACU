<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarristOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barrist_order_details', function (Blueprint $table) {
            $table->id();
            $table->string('date');
            $table->string('order_no');
            $table->string('order_signature')->nullable(true);
            $table->string('quantity')->nullable(true);
            $table->string('total_amount_purchase')->nullable(true);
            $table->string('total_amount_selling')->nullable(true);
            $table->string('purchase_price')->nullable(true);
            $table->string('selling_price')->nullable(true);
            $table->bigInteger('employe_id')->unsigned()->nullable(true);
            $table->string('status')->default('0');
            $table->boolean('flag')->default(0);
            $table->string('table_no')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('validated_by')->nullable(true);
            $table->string('confirmed_by')->nullable(true);
            $table->string('approuved_by')->nullable(true);
            $table->string('reseted_by')->nullable(true);
            $table->string('rejected_by')->nullable(true);
            $table->string('cancelled_by')->nullable(true);
            $table->text('description')->nullable(true);
            $table->text('rej_motif')->nullable(true);
            $table->text('cn_motif')->nullable(true);
            $table->string('type_space')->nullable(true);
            $table->string('taux_majoration')->nullable(true);
            $table->string('montant_majoration')->nullable(true);
            $table->string('montant_total_majoration')->nullable(true);
            $table->foreign('employe_id')
                    ->references('id')
                    ->on('employes')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('barrist_item_id')->unsigned()->nullable(true);
            $table->foreign('barrist_item_id')
                    ->references('id')
                    ->on('barrist_items')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('ingredient_id')->unsigned()->nullable(true);
            $table->foreign('ingredient_id')
                    ->references('id')
                    ->on('ingredients')
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
        Schema::dropIfExists('barrist_order_details');
    }
}

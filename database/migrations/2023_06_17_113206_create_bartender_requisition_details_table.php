<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBartenderRequisitionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bartender_requisition_details', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('quantity_requisitioned')->nullable(true);
            $table->string('quantity_received')->nullable(true);
            $table->string('unit')->nullable('true');
            $table->string('price')->nullable(true);
            $table->string('cump')->nullable(true);
            $table->string('requisition_no');
            $table->string('requisition_signature')->nullable(true);
            $table->text('description')->nullable(true);
            $table->text('rej_motif')->nullable(true);
            $table->string('total_value_requisitioned')->nullable(true);
            $table->string('total_value_received')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('validated_by')->nullable(true);
            $table->string('confirmed_by')->nullable(true);
            $table->string('approuved_by')->nullable(true);
            $table->string('rejected_by')->nullable(true);
            $table->string('reseted_by')->nullable(true);
            $table->string('store_type')->nullable(true);
            $table->string('status')->default('1');
            $table->bigInteger('drink_id')->unsigned()->nullable(true);
            $table->bigInteger('food_id')->unsigned()->nullable(true);
            $table->foreign('drink_id')
                    ->references('id')
                    ->on('drinks')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
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
        Schema::dropIfExists('bartender_requisition_details');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBartenderTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bartender_transfers', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('transfer_no');
            $table->string('requisition_no');
            $table->string('transfer_signature')->nullable(true);
            $table->text('description')->nullable(true);
            $table->string('type_transaction')->nullable(true);
            $table->string('type_movement')->nullable(true);
            $table->text('rejected_motif')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('validated_by')->nullable(true);
            $table->string('confirmed_by')->nullable(true);
            $table->string('approuved_by')->nullable(true);
            $table->string('rejected_by')->nullable(true);
            $table->string('reseted_by')->nullable(true);
            $table->string('status_portion')->nullable(true);
            $table->string('store_type')->nullable(true);
            $table->string('status')->default('1');
            $table->bigInteger('origin_fstore_id')->unsigned()->nullable(true);
            $table->bigInteger('origin_dstore_id')->unsigned()->nullable(true);
            $table->bigInteger('bartender_store_id')->unsigned()->nullable(true);
            $table->foreign('origin_fstore_id')
                    ->references('id')
                    ->on('food_big_stores')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('origin_dstore_id')
                    ->references('id')
                    ->on('drink_big_stores')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('bartender_store_id')
                    ->references('id')
                    ->on('bartender_stores')
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
        Schema::dropIfExists('bartender_transfers');
    }
}

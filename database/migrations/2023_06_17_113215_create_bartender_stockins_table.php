<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBartenderStockinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bartender_stockins', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('stockin_no');
            $table->unique('stockin_no');
            $table->string('stockin_signature')->nullable(true);
            $table->string('receptionist')->nullable(true);
            $table->string('handingover')->nullable(true);
            $table->string('origin')->nullable(true);
            $table->string('type_transaction')->nullable(true);
            $table->string('type_movement')->nullable(true);
            $table->text('description')->nullable(true);
            $table->text('rejected_motif')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('validated_by')->nullable(true);
            $table->string('confirmed_by')->nullable(true);
            $table->string('approuved_by')->nullable(true);
            $table->string('rejected_by')->nullable(true);
            $table->string('reseted_by')->nullable(true);
            $table->string('status')->default('1');
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
        Schema::dropIfExists('bartender_stockins');
    }
}

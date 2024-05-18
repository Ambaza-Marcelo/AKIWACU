<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBartenderOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bartender_orders', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('order_no');
            $table->unique('order_no');
            $table->string('order_signature')->nullable(true);
            $table->text('description')->nullable(true);
            $table->text('rejected_motif')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('table_no')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('validated_by')->nullable(true);
            $table->string('confirmed_by')->nullable(true);
            $table->string('approuved_by')->nullable(true);
            $table->string('rejected_by')->nullable(true);
            $table->string('reseted_by')->nullable(true);
            $table->string('status')->default('0');
            $table->boolean('flag')->default(0);
            $table->text('rej_motif')->nullable(true);
            $table->text('cn_motif')->nullable(true);
            $table->string('type_space')->nullable(true);
            $table->bigInteger('employe_id')->unsigned()->nullable(true);
            $table->foreign('employe_id')
                    ->references('id')
                    ->on('employes')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('table_id')->unsigned()->nullable(true);
            $table->foreign('table_id')
                    ->references('id')
                    ->on('tables')
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
        Schema::dropIfExists('bartender_orders');
    }
}

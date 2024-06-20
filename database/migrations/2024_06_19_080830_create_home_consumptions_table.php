<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHomeConsumptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('home_consumptions', function (Blueprint $table) {
            $table->id();
            $table->string('date');
            $table->string('consumption_no');
            $table->string('consumption_signature')->nullable(true);
            $table->string('quantity')->nullable(true);
            $table->bigInteger('employe_id')->unsigned()->nullable(true);
            $table->bigInteger('staff_member_id')->unsigned()->nullable(true);
            $table->string('status')->default('0');
            $table->boolean('flag')->default(0);
            $table->string('table_no')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('confirmed_by')->nullable(true);
            $table->string('approuved_by')->nullable(true);
            $table->string('rejected_by')->nullable(true);
            $table->string('reseted_by')->nullable(true);
            $table->text('description')->nullable(true);
            $table->text('rej_motif')->nullable(true);
            $table->text('cn_motif')->nullable(true);
            $table->foreign('employe_id')
                    ->references('id')
                    ->on('employes')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->foreign('staff_member_id')
                    ->references('id')
                    ->on('staff_members')
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
        Schema::dropIfExists('home_consumptions');
    }
}

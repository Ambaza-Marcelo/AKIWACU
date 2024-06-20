<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_members', function (Blueprint $table) {
            $table->id();
            $table->timestamp('start_date')->nullable(true);
            $table->timestamp('end_date')->nullable(true);
            $table->string('name');
            $table->unique('name');
            $table->string('etat')->default('0');
            $table->string('flag')->default('0');
            $table->string('total_amount_authorized')->nullable(true);
            $table->string('total_amount_consumed')->nullable(true);
            $table->string('total_amount_remaining')->nullable(true);
            $table->bigInteger('position_id')->unsigned()->nullable(true);
            $table->foreign('position_id')
                    ->references('id')
                    ->on('positions')
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
        Schema::dropIfExists('staff_members');
    }
}

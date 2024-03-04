<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTechniqueDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('technique_details', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(true);
            $table->string('amount')->nullable(true);
            $table->string('booking_no')->nullable(true);
            $table->string('booking_signature')->nullable(true);
            $table->string('auteur')->nullable(true);
            $table->bigInteger('technique_id')->unsigned()->nullable(true);
            $table->foreign('technique_id')
                    ->references('id')
                    ->on('techniques')
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
        Schema::dropIfExists('technique_details');
    }
}

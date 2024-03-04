<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable(true);
            $table->string('telephone')->nullable(true);
            $table->string('status')->nullable(true);
            $table->string('image')->nullable(true);
            $table->string('created_by');

            $table->bigInteger('position_id')->unsigned()->nullable(true);
            $table->bigInteger('address_id')->unsigned()->nullable(true);
            $table->foreign('address_id')
                    ->references('id')
                    ->on('addresses')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
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
        Schema::dropIfExists('employes');
    }
}

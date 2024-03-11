<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unique('name');
            $table->string('mail')->nullable(true);
            $table->string('phone_no')->nullable(true);
            $table->string('tin_number')->nullable(true);
            $table->string('vat_taxpayer')->nullable(true);
            $table->string('type')->nullable(true);
            $table->string('category')->nullable(true);
            $table->string('created_by');
            $table->bigInteger('address_id')->unsigned()->nullable(true);
            $table->foreign('address_id')
                    ->references('id')
                    ->on('addresses')
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
        Schema::dropIfExists('suppliers');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialBigStores extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_big_stores', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(true);
            $table->string('code')->nullable(true);
            $table->string('store_signature')->nullable(true);
            $table->string('emplacement')->nullable(true);
            $table->string('manager')->nullable(true);
            $table->boolean('verified')->default(false);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->text('description')->nullable(true);
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
        Schema::dropIfExists('material_big_stores');
    }
}

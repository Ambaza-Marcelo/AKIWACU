<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMsMaterialSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_material_suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('mail')->nullable(true);
            $table->string('phone_no')->nullable(true);
            $table->string('tin_number')->nullable(true);
            $table->string('vat_taxpayer')->nullable(true);
            $table->string('type')->nullable(true);
            $table->string('category')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('address')->nullable(true);
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
        Schema::dropIfExists('ms_material_suppliers');
    }
}

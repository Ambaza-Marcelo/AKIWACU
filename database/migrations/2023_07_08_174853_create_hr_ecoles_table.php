<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrEcolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_ecoles', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->nullable(true);
            $table->string('etat')->nullable(true);
            $table->text('description')->nullable(true);
            $table->string('adresse')->nullable(true);
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
        Schema::dropIfExists('hr_ecoles');
    }
}

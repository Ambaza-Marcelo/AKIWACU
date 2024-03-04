<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nif');
            $table->string('rc');
            $table->string('commune');
            $table->string('zone');
            $table->string('quartier');
            $table->string('rue');
            $table->string('telephone1')->nullable(true);
            $table->string('telephone2')->nullable(true);
            $table->string('email');
            $table->string('logo');
            $table->string('developpeur')->nullable(true);
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
        Schema::dropIfExists('settings');
    }
}

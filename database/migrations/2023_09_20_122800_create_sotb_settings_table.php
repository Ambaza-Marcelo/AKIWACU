<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSotbSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sotb_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nif')->nullable(true);
            $table->string('rc')->nullable(true);
            $table->string('province')->nullable(true);
            $table->string('commune')->nullable(true);
            $table->string('zone')->nullable(true);
            $table->string('quartier')->nullable(true);
            $table->string('avenue')->nullable(true);
            $table->string('rue')->nullable(true);
            $table->string('telephone1')->nullable(true);
            $table->string('telephone2')->nullable(true);
            $table->string('email')->nullable(true);
            $table->string('logo')->nullable(true);
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
        Schema::dropIfExists('sotb_settings');
    }
}

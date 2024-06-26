<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrBanquesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_banques', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(true);
            $table->string('code')->nullable(true);
            $table->string('currency')->nullable(true);
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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('hr_banques');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}

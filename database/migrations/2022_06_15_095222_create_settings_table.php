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
            $table->string('tp_type');
            $table->unique('name');
            $table->string('nif');
            $table->unique('nif');
            $table->string('rc');
            $table->unique('rc');
            $table->boolean('vat_taxpayer')->default(0);
            $table->boolean('ct_taxpayer')->default(0);
            $table->boolean('tl_taxpayer')->default(0);
            $table->boolean('item_tsce_tax')->default(0);
            $table->boolean('item_ott_tax')->default(0);
            $table->string('tp_fiscal_center');
            $table->string('tp_activity_sector');
            $table->string('tp_legal_form')->nullable(true);
            $table->string('postal_number')->nullable(true);
            $table->string('province');
            $table->string('commune');
            $table->string('zone');
            $table->string('quartier');
            $table->string('evenue')->nullable(true);
            $table->string('rue')->nullable(true);
            $table->string('telephone1')->nullable(true);
            $table->string('telephone2')->nullable(true);
            $table->string('email')->nullable(true);
            $table->unique('email');
            $table->double('max_line')->default(500);
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
        Schema::dropIfExists('settings');
    }
}

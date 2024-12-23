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
            $table->string('vat_taxpayer');
            $table->string('ct_taxpayer');
            $table->string('tl_taxpayer');
            $table->string('item_tsce_tax');
            $table->string('item_ott_tax');
            $table->string('tp_fiscal_center');
            $table->string('tp_activity_sector');
            $table->string('tp_legal_form')->nullable(true);
            $table->string('postal_number')->nullable(true);
            $table->string('province');
            $table->string('commune');
            $table->string('zone');
            $table->string('quartier');
            $table->string('rue')->nullable(true);
            $table->string('telephone1')->nullable(true);
            $table->string('telephone2')->nullable(true);
            $table->string('email')->nullable(true);
            $table->unique('email');
            $table->string('max_line');
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

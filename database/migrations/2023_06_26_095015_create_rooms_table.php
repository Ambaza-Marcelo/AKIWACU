<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(true);
            $table->unique('name');
            $table->string('code')->nullable(true);
            $table->string('specification')->nullable(true);
            $table->double('vat')->nullable(true);
            $table->double('item_ct')->nullable(true);
            $table->double('item_tl')->nullable(true);
            $table->double('item_tsce_tax')->nullable(true);
            $table->double('item_ott_tax')->nullable(true);
            $table->string('cump')->nullable(true);
            $table->double('taux_marge')->nullable(true);
            $table->double('selling_price')->nullable(true);
            $table->double('quantity')->nullable(true);
            $table->double('threshold_quantity')->nullable(true);
            $table->string('status')->nullable(true);
            $table->string('etat')->nullable(true);
            $table->string('auteur')->nullable(true);
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
        Schema::dropIfExists('rooms');
    }
}

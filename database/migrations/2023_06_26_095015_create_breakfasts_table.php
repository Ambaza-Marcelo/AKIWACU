<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBreakfastsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('breakfasts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(true);
            $table->string('code')->nullable(true);
            $table->string('specification')->nullable(true);
            $table->string('vat')->nullable(true);
            $table->string('item_ct')->nullable(true);
            $table->string('item_tl')->nullable(true);
            $table->string('cump')->nullable(true);
            $table->string('taux_marge')->nullable(true);
            $table->string('selling_price')->nullable(true);
            $table->string('quantity')->nullable(true);
            $table->string('threshold_quantity')->nullable(true);
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
        Schema::dropIfExists('breakfasts');
    }
}

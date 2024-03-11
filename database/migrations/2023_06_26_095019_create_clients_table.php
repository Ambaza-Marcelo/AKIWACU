<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name')->nullable(true);
            $table->unique('customer_name');
            $table->string('telephone')->nullable(true);
            $table->unique('telephone');
            $table->string('mail')->nullable(true);
            $table->unique('mail');
            $table->string('customer_TIN')->nullable(true);
            $table->unique('customer_TIN');
            $table->string('customer_address')->nullable(true);
            $table->string('vat_customer_payer')->nullable(true);
            $table->string('company')->nullable(true);
            $table->string('etat')->nullable(true);
            $table->string('autre')->nullable(true);
            $table->string('total_amount_paied')->nullable(true);
            $table->string('total_amount_credit')->nullable(true);
            $table->string('avalise_par')->nullable(true);
            $table->string('date')->nullable(true);
            $table->string('points')->nullable(true);
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
        Schema::dropIfExists('clients');
    }
}

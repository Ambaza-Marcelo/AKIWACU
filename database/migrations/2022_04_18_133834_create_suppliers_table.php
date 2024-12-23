<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('supplier_name')->nullable(true);
            $table->unique('supplier_name');
            $table->string('telephone')->nullable(true);
            $table->unique('telephone');
            $table->string('mail')->nullable(true);
            $table->unique('mail');
            $table->string('supplier_TIN')->nullable(true);
            $table->unique('supplier_TIN');
            $table->string('tp_type')->nullable(true);
            $table->string('supplier_address')->nullable(true);
            $table->string('vat_supplier_payer')->nullable(true);
            $table->string('company')->nullable(true);
            $table->string('etat')->nullable(true);
            $table->string('autre')->nullable(true);
            $table->string('bank_account_number')->nullable(true);
            $table->string('bank_branch_name')->nullable(true);
            $table->string('currency')->nullable(true);
            $table->string('date')->nullable(true);
            $table->string('points')->nullable(true);
            $table->bigInteger('banque_id')->unsigned()->nullable(true);
            $table->foreign('banque_id')
                    ->references('id')
                    ->on('hr_banques')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
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
        Schema::dropIfExists('suppliers');
    }
}

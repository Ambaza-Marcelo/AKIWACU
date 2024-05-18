<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date')->nullable(true);
            $table->timestamp('opening_date')->nullable(true);
            $table->timestamp('closing_date')->nullable(true);
            $table->string('name');
            $table->unique('name');
            $table->string('order_no')->nullable(true);
            $table->string('type')->nullable(true);
            $table->string('waiter_name')->nullable(true);
            $table->string('etat')->default('0');
            $table->string('flag')->default('0');
            $table->string('opened_by')->nullable(true);
            $table->string('closed_by')->nullable(true);
            $table->string('total_amount_paying')->nullable(true);
            $table->string('total_amount_paid')->nullable(true);
            $table->string('total_amount_remaining')->nullable(true);
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
        Schema::dropIfExists('tables');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}

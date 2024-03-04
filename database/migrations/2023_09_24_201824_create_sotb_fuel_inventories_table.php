<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSotbFuelInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sotb_fuel_inventories', function (Blueprint $table) {
            $table->id();
            $table->string('inventory_no')->nullable(true);
            $table->string('code_pump')->nullable();
            $table->string('inventory_signature')->nullable(true);
            $table->string('date');
            $table->string('title')->nullable(true);
            $table->text('description');
            $table->string('status')->nullable(true);
            $table->text('rejected_motif')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('validated_by')->nullable(true);
            $table->string('confirmed_by')->nullable(true);
            $table->string('approuved_by')->nullable(true);
            $table->string('rejected_by')->nullable(true);
            $table->string('reseted_by')->nullable(true);
            $table->bigInteger('pump_id')->unsigned()->nullable(true);
            $table->foreign('pump_id')
                    ->references('id')
                    ->on('sotb_fuel_pumps')
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
        Schema::dropIfExists('sotb_fuel_inventories');
    }
}

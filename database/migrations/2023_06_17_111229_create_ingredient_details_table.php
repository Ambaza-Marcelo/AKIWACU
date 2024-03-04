<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIngredientDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingredient_details', function (Blueprint $table) {
            $table->id();
            $table->string('order_no');
            $table->string('order_signature')->nullable(true);
            $table->bigInteger('employe_id')->unsigned()->nullable(true);
            $table->foreign('employe_id')
                    ->references('id')
                    ->on('employes')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('barrist_item_id')->unsigned()->nullable(true);
            $table->foreign('barrist_item_id')
                    ->references('id')
                    ->on('barrist_items')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('ingredient_id')->unsigned()->nullable(true);
            $table->foreign('ingredient_id')
                    ->references('id')
                    ->on('ingredients')
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
        Schema::dropIfExists('ingredient_details');
    }
}

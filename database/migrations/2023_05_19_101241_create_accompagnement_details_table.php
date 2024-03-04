<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccompagnementDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accompagnement_details', function (Blueprint $table) {
            $table->id();
            $table->string('order_no');
            $table->string('order_signature')->nullable(true);
            $table->bigInteger('employe_id')->unsigned()->nullable(true);
            $table->foreign('employe_id')
                    ->references('id')
                    ->on('employes')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('food_item_id')->unsigned()->nullable(true);
            $table->foreign('food_item_id')
                    ->references('id')
                    ->on('food_items')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('accompagnement_id')->unsigned()->nullable(true);
            $table->foreign('accompagnement_id')
                    ->references('id')
                    ->on('accompagnements')
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
        Schema::dropIfExists('accompagnement_details');
    }
}

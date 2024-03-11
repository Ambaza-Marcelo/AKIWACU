<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFoodBigStoreInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_big_store_inventories', function (Blueprint $table) {
            $table->id();
            $table->string('inventory_no')->nullable(true);
            $table->unique('inventory_no');
            $table->string('code_store')->nullable();
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
            $table->bigInteger('store_id')->unsigned()->nullable(true);
            $table->foreign('store_id')
                    ->references('id')
                    ->on('food_big_stores')
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
        Schema::dropIfExists('food_big_store_inventories');
    }
}

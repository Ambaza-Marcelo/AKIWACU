<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrNoteInternesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_note_internes', function (Blueprint $table) {
            $table->id();
            $table->string('date')->nullable(true);
            $table->string('title')->nullable(true);
            $table->text('message')->nullable(true);
            $table->string('signe_par')->nullable(true);
            $table->string('confirme_par')->nullable(true);
            $table->bigInteger('departement_id')->unsigned()->nullable(true);
            $table->foreign('departement_id')
                    ->references('id')
                    ->on('hr_departements')
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
        Schema::dropIfExists('hr_note_internes');
    }
}

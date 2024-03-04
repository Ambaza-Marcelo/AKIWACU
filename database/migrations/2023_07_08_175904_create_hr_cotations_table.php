<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHrCotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hr_cotations', function (Blueprint $table) {
            $table->id();
            $table->string('somme_note_obtenu')->nullable(true);
            $table->string('somme_note_total')->nullable(true);
            $table->string('pourcentage')->nullable(true);
            $table->string('mention')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->bigInteger('employe_id')->unsigned()->nullable(true);
            $table->foreign('employe_id')
                    ->references('id')
                    ->on('hr_employes')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('company_id')->unsigned()->nullable(true);
            $table->foreign('company_id')
                    ->references('id')
                    ->on('hr_companies')
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
        Schema::dropIfExists('hr_cotations');
    }
}

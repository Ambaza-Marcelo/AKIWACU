<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('booking_no');
            $table->string('booking_signature')->nullable(true);
            $table->text('description')->nullable(true);
            $table->text('rejected_motif')->nullable(true);
            $table->string('created_by')->nullable(true);
            $table->string('statut_demandeur')->nullable(true);
            $table->string('nom_demandeur')->nullable(true);
            $table->string('adresse_demandeur')->nullable(true);
            $table->string('telephone_demandeur')->nullable(true);
            $table->string('nom_referent')->nullable(true);
            $table->string('telephone_referent')->nullable(true);
            $table->string('courriel_referent')->nullable(true);
            $table->string('type_evenement')->nullable(true);
            $table->string('nombre_personnes')->nullable(true);
            $table->string('date_debut')->nullable(true);
            $table->string('date_fin')->nullable(true);
            $table->string('updated_by')->nullable(true);
            $table->string('validated_by')->nullable(true);
            $table->string('confirmed_by')->nullable(true);
            $table->string('approuved_by')->nullable(true);
            $table->string('rejected_by')->nullable(true);
            $table->string('reseted_by')->nullable(true);
            $table->string('status')->default('0');
            $table->boolean('flag')->default(0);
            $table->bigInteger('booking_client_id')->unsigned()->nullable(true);
            $table->foreign('booking_client_id')
                    ->references('id')
                    ->on('booking_clients')
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
        Schema::dropIfExists('bookings');
    }
}

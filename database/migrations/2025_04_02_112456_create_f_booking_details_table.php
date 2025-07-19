<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFBookingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('f_booking_details', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->string('booking_no');
            $table->string('booking_signature')->nullable(true);
            $table->string('quantity')->nullable(true);
            $table->string('total_amount_selling')->nullable(true);
            $table->string('selling_price')->nullable(true);
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
            $table->boolean('flag')->default(0);
            $table->string('status')->default('0');
            $table->text('rej_motif')->nullable(true);
            $table->text('cn_motif')->nullable(true);
            $table->string('type_space')->nullable(true);
            $table->string('taux_majoration')->nullable(true);
            $table->string('montant_majoration')->nullable(true);
            $table->string('montant_total_majoration')->nullable(true);
            $table->string('taux_reduction')->nullable(true);
            $table->string('montant_reduction')->nullable(true);
            $table->string('montant_total_reduction')->nullable(true);
            $table->text('note_reduction')->nullable(true);
            $table->text('reglement')->nullable(true);
            $table->bigInteger('technique_id')->unsigned()->nullable(true);
            $table->foreign('technique_id')
                    ->references('id')
                    ->on('techniques')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('salle_id')->unsigned()->nullable(true);
            $table->foreign('salle_id')
                    ->references('id')
                    ->on('salles')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('service_id')->unsigned()->nullable(true);
            $table->foreign('service_id')
                    ->references('id')
                    ->on('services')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('kidness_space_id')->unsigned()->nullable(true);
            $table->foreign('kidness_space_id')
                    ->references('id')
                    ->on('kidness_spaces')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('swiming_pool_id')->unsigned()->nullable(true);
            $table->foreign('swiming_pool_id')
                    ->references('id')
                    ->on('swiming_pools')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('breakfast_id')->unsigned()->nullable(true);
            $table->foreign('breakfast_id')
                    ->references('id')
                    ->on('breakfasts')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('client_id')->unsigned()->nullable(true);
            $table->foreign('client_id')
                    ->references('id')
                    ->on('clients')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('room_id')->unsigned()->nullable(true);
            $table->foreign('room_id')
                    ->references('id')
                    ->on('rooms')
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
        Schema::dropIfExists('f_booking_details');
    }
}

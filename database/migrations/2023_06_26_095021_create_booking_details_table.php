<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(Schema::hasTable('booking_details')) return;  
        Schema::create('booking_details', function (Blueprint $table) {
            $table->id();
            $table->date('date');
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
            $table->bigInteger('booking_client_id')->unsigned()->nullable(true);
            $table->foreign('booking_client_id')
                    ->references('id')
                    ->on('booking_clients')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('client_id')->unsigned()->nullable(true);
            $table->foreign('client_id')
                    ->references('id')
                    ->on('clients')
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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('booking_details');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}

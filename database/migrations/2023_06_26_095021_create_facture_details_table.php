<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFactureDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facture_details', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->nullable(true);
            $table->timestamp('invoice_date')->nullable(true);
            $table->string('tp_type')->nullable(true);
            $table->string('tp_name')->nullable(true);
            $table->string('tp_TIN')->nullable(true);
            $table->string('tp_trade_number')->nullable(true);
            $table->string('tp_phone_number')->nullable(true);
            $table->string('tp_address_province')->nullable(true);
            $table->string('tp_address_commune')->nullable(true);
            $table->string('tp_address_quartier')->nullable(true);
            $table->string('tp_address_avenue')->nullable(true);
            $table->string('tp_address_rue')->nullable(true);
            $table->string('tp_address_number')->nullable(true);
            $table->string('vat_taxpayer')->nullable(true);
            $table->string('ct_taxpayer')->nullable(true);
            $table->string('tl_taxpayer')->nullable(true);
            $table->string('tp_fiscal_center')->nullable(true);
            $table->string('tp_activity_sector')->nullable(true);
            $table->string('tp_legal_form')->nullable(true);
            $table->string('payment_type')->nullable(true);
            $table->string('customer_name')->nullable(true);
            $table->string('customer_TIN')->nullable(true);
            $table->string('customer_address')->nullable(true);
            $table->string('vat_customer_payer')->nullable(true);
            $table->string('cancelled_invoice_ref')->nullable(true);
            $table->string('cancelled_invoice')->nullable(true);
            $table->string('invoice_ref')->nullable(true);
            $table->text('cn_motif')->nullable(true);
            $table->string('code_store')->nullable(true);
            $table->string('food_order_no')->nullable(true);
            $table->string('drink_order_no')->nullable(true);
            $table->string('barrist_order_no')->nullable(true);
            $table->string('bartender_order_no')->nullable(true);
            $table->string('booking_no')->nullable(true);
            $table->string('invoice_currency')->nullable(true);
            $table->string('electronic_signature')->nullable(true);
            $table->string('invoice_registered_number')->nullable(true);
            $table->string('invoice_registered_date')->nullable(true);
            $table->timestamp('invoice_signature_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('invoice_signature')->nullable(true);
            $table->string('item_designation')->nullable(true);
            $table->string('item_quantity')->nullable(true);
            $table->string('item_price')->nullable(true);
            $table->string('item_ct')->nullable(true);
            $table->string('item_tl')->nullable(true);
            $table->string('item_price_nvat')->nullable(true);
            $table->string('brarudi_purchase_price')->nullable(true);
            $table->string('vat')->nullable(true);
            $table->string('item_price_wvat')->nullable(true);
            $table->string('item_total_amount')->nullable(true);
            $table->string('auteur')->nullable(true);
            $table->string('validated_by')->nullable(true);
            $table->string('confirmed_by')->nullable(true);
            $table->string('approuved_by')->nullable(true);
            $table->string('reseted_by')->nullable(true);
            $table->string('rejected_by')->nullable(true);
            $table->string('cancelled_by')->nullable(true);
            $table->string('etat')->default('0');
            $table->string('paid_either')->nullable(true);
            $table->string('statut')->nullable(true);
            $table->string('statut_paied')->nullable(true);
            $table->string('montant_total_credit')->nullable(true);
            $table->string('montant_recouvre')->nullable(true);
            $table->string('reste_credit')->nullable(true);
            $table->string('etat_recouvrement')->nullable(true);
            $table->string('bank_name')->nullable(true);
            $table->string('cheque_no')->nullable(true);
            $table->string('bordereau_no')->nullable(true);
            $table->string('date_recouvrement')->nullable(true);
            $table->string('nom_recouvrement')->nullable(true);
            $table->string('taux_reduction')->nullable(true);
            $table->string('montant_reduction')->nullable(true);
            $table->string('montant_total_reduction')->nullable(true);
            $table->text('note_reduction')->nullable(true);
            $table->text('note_credit')->nullable(true);
            $table->text('note_recouvrement')->nullable(true);
            $table->text('description')->nullable(true);
            $table->string('cump')->nullable(true);
            $table->string('total_cump_value')->nullable(true);
            $table->text('invoice_identifier')->nullable(true);
            $table->string('type_space')->nullable(true);
            $table->string('taux_majoration')->nullable(true);
            $table->string('montant_majoration')->nullable(true);
            $table->string('montant_total_majoration')->nullable(true);
            $table->bigInteger('employe_id')->unsigned()->nullable(true);
            $table->foreign('employe_id')
                    ->references('id')
                    ->on('employes')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('drink_id')->unsigned()->nullable(true);
            $table->foreign('drink_id')
                    ->references('id')
                    ->on('drinks')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('food_item_id')->unsigned()->nullable(true);
            $table->foreign('food_item_id')
                    ->references('id')
                    ->on('food_items')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('barrist_item_id')->unsigned()->nullable(true);
            $table->foreign('barrist_item_id')
                    ->references('id')
                    ->on('barrist_items')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('bartender_item_id')->unsigned()->nullable(true);
            $table->foreign('bartender_item_id')
                    ->references('id')
                    ->on('bartender_items')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('client_id')->unsigned()->nullable(true);
            $table->foreign('client_id')
                    ->references('id')
                    ->on('clients')
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
            $table->bigInteger('booking_client_id')->unsigned()->nullable(true);
            $table->foreign('booking_client_id')
                    ->references('id')
                    ->on('booking_clients')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('table_id')->unsigned()->nullable(true);
            $table->foreign('table_id')
                    ->references('id')
                    ->on('tables')
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
        Schema::dropIfExists('facture_details');
    }
}

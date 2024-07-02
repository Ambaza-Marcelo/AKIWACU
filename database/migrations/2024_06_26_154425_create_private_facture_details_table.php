<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrivateFactureDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('private_facture_details', function (Blueprint $table) {
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
            $table->string('cn_motif')->nullable(true);
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
            $table->bigInteger('private_store_item_id')->unsigned()->nullable(true);
            $table->foreign('private_store_item_id')
                    ->references('id')
                    ->on('private_store_items')
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
        Schema::dropIfExists('private_facture_details');
    }
}

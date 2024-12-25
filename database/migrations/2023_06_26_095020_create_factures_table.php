<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFacturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->nullable(true);
            $table->unique('invoice_number');
            $table->timestamp('invoice_date')->nullable(true);
            $table->string('tp_type')->nullable(true);
            $table->string('tp_name')->nullable(true);
            $table->string('tp_TIN')->nullable(true);
            $table->string('invoice_type')->default('FN');
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
            $table->string('invoice_currency')->nullable(true);
            $table->string('code_store')->nullable(true);
            $table->string('food_order_no')->nullable(true);
            $table->unique('food_order_no');
            $table->string('drink_order_no')->nullable(true);
            $table->unique('drink_order_no');
            $table->string('barrist_order_no')->nullable(true);
            $table->unique('barrist_order_no');
            $table->string('bartender_order_no')->nullable(true);
            $table->unique('bartender_order_no');
            $table->string('booking_no')->nullable(true);
            $table->unique('booking_no');
            $table->string('auteur')->nullable(true);
            $table->string('validated_by')->nullable(true);
            $table->string('confirmed_by')->nullable(true);
            $table->string('approuved_by')->nullable(true);
            $table->string('reseted_by')->nullable(true);
            $table->string('rejected_by')->nullable(true);
            $table->string('cancelled_by')->nullable(true);
            $table->string('electronic_signature')->nullable(true);
            $table->string('invoice_registered_number')->nullable(true);
            $table->string('invoice_registered_date')->nullable(true);
            $table->timestamp('invoice_signature_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->string('invoice_signature')->nullable(true);
            $table->string('invoice_identifier')->nullable(true);
            $table->double('montant_total_credit')->nullable(true);
            $table->double('montant_recouvre')->nullable(true);
            $table->double('reste_credit')->nullable(true);
            $table->string('bank_name')->nullable(true);
            $table->string('cheque_no')->nullable(true);
            $table->string('bordereau_no')->nullable(true);
            $table->string('date_recouvrement')->nullable(true);
            $table->string('nom_recouvrement')->nullable(true);
            $table->text('note_reduction')->nullable(true);
            $table->text('note_credit')->nullable(true);
            $table->text('note_recouvrement')->nullable(true);
            $table->string('type_space')->nullable(true);
            $table->text('description')->nullable(true);
            $table->string('etat')->default('0');
            $table->string('paid_either')->nullable(true);
            $table->string('statut')->nullable(true);
            $table->string('statut_paied')->nullable(true);
            $table->string('etat_recouvrement')->nullable(true);
            $table->bigInteger('employe_id')->unsigned()->nullable(true);
            $table->foreign('employe_id')
                    ->references('id')
                    ->on('employes')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('client_id')->unsigned()->nullable(true);
            $table->foreign('client_id')
                    ->references('id')
                    ->on('clients')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('table_id')->unsigned()->nullable(true);
            $table->foreign('table_id')
                    ->references('id')
                    ->on('tables')
                    ->onUpdate('cascade')
                    ->onDelete('cascade');
            $table->bigInteger('banque_id')->unsigned()->nullable(true);
            $table->foreign('banque_id')
                    ->references('id')
                    ->on('hr_banques')
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
        Schema::dropIfExists('factures');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}

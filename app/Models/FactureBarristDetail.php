<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactureBarristDetail extends Model
{
    //
    protected $table = 'barrist_invoice_details';
    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'tp_type',
        'tp_name',
        'tp_TIN',
        'tp_trade_number',
        'tp_phone_number',
        'tp_address_commune',
        'tp_address_quartier',
        'vat_taxpayer',
        'ct_taxpayer',
        'tl_taxpayer',
        'tp_fiscal_center',
        'tp_activity_sector',
        'tp_legal_form',
        'payment_type',
        'customer_name',
        'invoice_signature',
        'invoice_signature_date',
        'invoice_signature',
        'invoice_signature_date',
        'item_designation',
        'item_quantity',
        'item_price',
        'item_ct',
        'item_tl',
        'item_price_nvat',
        'vat',
        'item_price_wvat',
        'item_total_amount',
        'barrist_item_id'
    ];

    public function barristItem(){
        return $this->belongsTo('App\Models\BarristItem');
    }

    public function employe(){
        return $this->belongsTo('App\Models\Employe');
    }
}

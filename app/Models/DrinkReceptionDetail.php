<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrinkReceptionDetail extends Model
{
    //
    protected $fillable = [
        'date',
        'quantity_ordered',
        'quantity_received',
        'quantity_remaining',
        'unit',
        'price_nvat',
        'vat',
        'price_wvat',
        'total_amount_ordered',
        'total_amount_received',
        'total_amount_remaining',
        'purchase_price',
        'reception_no',
        'invoice_no',
        'order_no',
        'receptionist',
        'handingover',
        'vat_taxpayer',
        'vat_supplier_payer',
        'invoice_currency',
        'waybill',
        'reception_signature',
        'description',
        'rejected_motif',
        'total_value',
        'start_date',
        'end_date',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
        'status',
        'supplier_id',
        'drink_id',
        'destination_store_id',
        'destination_extra_store_id',
    ];

    public function supplier(){
        return $this->belongsTo('App\Models\Supplier');
    }

    public function drink(){
        return $this->belongsTo('App\Models\Drink');
    }

    public function destinationStore(){
        return $this->belongsTo('App\Models\DrinkBigStore');
    }

    public function destinationExtraStore(){
        return $this->belongsTo('App\Models\DrinkExtraBigStore');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbFuelReception extends Model
{
    //
    protected $table = 'sotb_fuel_receptions';
    protected $fillable = [
        'date',
        'reception_no',
        'reception_signature',
        'invoice_no',
        'order_no',
        'receptionist',
        'handingover',
        'vat_taxpayer',
        'vat_supplier_payer',
        'invoice_currency',
        'waybill',
        'description',
        'rejected_motif',
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
        'pump_id',
    ];

    public function supplier(){
        return $this->belongsTo('App\Models\SotbSupplier');
    }

    public function pump(){
        return $this->belongsTo('App\Models\SotbFuelPump');
    }
}

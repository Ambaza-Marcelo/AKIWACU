<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NoteCredit extends Model
{
    //
    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'sale_date',
        'type_facture',
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
        'employe_id',
        'client_id',
        'bookingClient',
        'table_id'
    ];

    public function employe(){
        return $this->belongsTo('\App\Models\Employe');
    }

    public function client(){
        return $this->belongsTo('\App\Models\EGRClient');
    }

    public function bookingClient(){
        return $this->belongsTo('\App\Models\BookingClient');
    }

    public function table(){
        return $this->belongsTo('App\Models\Table');
    }
}

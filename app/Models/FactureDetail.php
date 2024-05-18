<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactureDetail extends Model
{
    //
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
        'drink_id',
        'food_order_no',
        'drink_order_no',
        'barrist_order_no',
        'food_item_id',
        'barrist_item_id',
        'table_id'
    ];

    public function drink(){
        return $this->belongsTo('App\Models\Drink');
    }

    public function employe(){
        return $this->belongsTo('App\Models\Employe');
    }

    public function client(){
        return $this->belongsTo('\App\Models\Client');
    }

    public function bookingClient(){
        return $this->belongsTo('\App\Models\BookingClient');
    }

    public function barristItem(){
        return $this->belongsTo('App\Models\BarristItem');
    }

    public function bartenderItem(){
        return $this->belongsTo('App\Models\BartenderItem');
    }

    public function foodItem(){
        return $this->belongsTo('App\Models\FoodItem');
    }

    public function salle(){
        return $this->belongsTo('App\Models\BookingSalle');
    }

    public function service(){
        return $this->belongsTo('App\Models\BookingService');
    }

    public function table(){
        return $this->belongsTo('App\Models\BookingTable');
    }

    public function swimingPool(){
        return $this->belongsTo('App\Models\SwimingPool');
    }

    public function breakFast(){
        return $this->belongsTo('App\Models\BreakFast');
    }

    public function kidnessSpace(){
        return $this->belongsTo('App\Models\KidnessSpace');
    }

    public function table(){
        return $this->belongsTo('App\Models\Table');
    }
}

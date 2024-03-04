<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodStockinDetail extends Model
{
    //
    protected $fillable = [
        'date',
        'quantity',
        'unit',
        'price_nvat',
        'vat',
        'price_wvat',
        'total_amount_purchase',
        'total_amount_selling',
        'purchase_price',
        'selling_price',
        'stockin_no',
        'receptionist',
        'handingover',
        'stockin_signature',
        'description',
        'rejected_motif',
        'total_value',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
        'status',
        'food_id',
        'destination_sm_store_id',
        'destination_bg_store_id',
        'destination_extra_store_id'
    ];

    public function drinkSmallStore(){
        return $this->belongsTo('App\Models\DrinkSmallStore');
    }

    public function drinkbigStore(){
        return $this->belongsTo('App\Models\DrinkBigStore');
    }

    public function drinkExtraStore(){
        return $this->belongsTo('App\Models\DrinkExtraStore');
    }

    public function food(){
        return $this->belongsTo('App\Models\Food');
    }
}

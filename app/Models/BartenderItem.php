<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BartenderItem extends Model
{
    //
    protected $fillable = [
        'name',
        'code',
        'specification',
        'code_store',
        'quantity',
        'unit',
        'purchase_price',
        'cost_price',
        'selling_price',
        'cump',
        'threshold_quantity',
        'expiration_date',
        'status',
        'store_type',
        'updated_by',
        'created_by',
    ];

    public function bartenderProductionStore(){
        return $this->hasMany('App\Models\BartenderProductionStore','bartender_item_id');
    }

    public function bartenderOrderDetail(){
        return $this->hasMany('App\Models\BartenderOrderDetail','bartender_item_id');
    }

    public function factureDetail(){
        return $this->hasMany('App\Models\FactureDetail','bartender_item_id');
    }
}

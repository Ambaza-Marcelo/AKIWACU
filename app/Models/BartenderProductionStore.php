<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BartenderProductionStore extends Model
{
    //
    protected $fillable = [
        'quantity',
        'name',
        'code',
        'emplacement',
        'manager',
        'unit',
        'purchase_price',
        'selling_price',
        'cost_price',
        'cump',
        'total_value',
        'total_purchase_value',
        'total_selling_value',
        'total_cost_value',
        'total_cump_value',
        'threshold_quantity',
        'verified',
        'bartender_item_id',
        'created_by',
        'updated_by',
        'description',
    ];

    public function bartenderItem(){
        return $this->belongsTo('App\Models\BartenderItem');
    }
}

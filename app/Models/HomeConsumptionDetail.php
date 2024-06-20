<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeConsumptionDetail extends Model
{
    //
    protected $fillable = [
        'date',
        'consumption_no',
        'consumption_signature',
        'quantity',
        'amount_consumed',
        'total_amount_consumed',
        'employe_id',
        'status',
        'table_no',
        'created_by',
        'description',
        'food_item_id',
        'barrist_item_id'

    ];

    public function staffMember(){
        return $this->belongsTo('App\Models\StaffMember');
    }

    public function foodItem(){
        return $this->belongsTo('App\Models\FoodItem');
    }

    public function barristItem(){
        return $this->belongsTo('App\Models\BarristItem');
    }
}

<?php

namespace App\Models\F;

use Illuminate\Database\Eloquent\Model;

class FDrinkOrderDetail extends Model
{
    //
    protected $table = 'f_drink_order_details';
    protected $fillable = [
        'date',
        'order_no',
        'order_signature',
        'quantity',
        'total_amount_purchase',
        'total_amount_selling',
        'purchase_price',
        'selling_price',
        'employe_id',
        'status',
        'table_no',
        'created_by',
        'description',
        'employe_id',
        'drink_id',
        'table_id'

    ];

    public function employe(){
        return $this->belongsTo('App\Models\Employe');
    }

    public function drink(){
        return $this->belongsTo('App\Models\Drink');
    }

    public function table(){
        return $this->belongsTo('App\Models\F\FTable');
    }
}

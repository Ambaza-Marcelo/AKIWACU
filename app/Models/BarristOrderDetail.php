<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarristOrderDetail extends Model
{
    //
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
        'barrist_item_id'

    ];

    public function employe(){
        return $this->belongsTo('App\Models\Employe');
    }

    public function barristItem(){
        return $this->belongsTo('App\Models\BarristItem');
    }
}

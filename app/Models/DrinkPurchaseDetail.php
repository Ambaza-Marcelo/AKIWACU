<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrinkPurchaseDetail extends Model
{
    //
    protected $fillable = [
        'date',
        'quantity',
        'unit',
        'price',
        'purchase_no',
        'purchase_signature',
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
        'drink_id',
    ];

    public function drink(){
        return $this->belongsTo('App\Models\Drink');
    }
}

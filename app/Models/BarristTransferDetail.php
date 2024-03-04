<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarristTransferDetail extends Model
{
    //
    protected $fillable = [
        'date',
        'quantity_requisitioned',
        'quantity_transfered',
        'unit',
        'price',
        'transfer_no',
        'requisition_no',
        'transfer_signature',
        'description',
        'rejected_motif',
        'total_value_requisitioned',
        'total_value_transfered',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
        'status',
        'drink_id',
        'food_id',
        'origin_fstore_id',
        'origin_dstore_id',
        'barrist_store_id',
    ];

    public function origindStore(){
        return $this->belongsTo('App\Models\DrinkBigStore');
    }

    public function originfStore(){
        return $this->belongsTo('App\Models\FoodBigStore');
    }

    public function barristStore(){
        return $this->belongsTo('App\Models\BarristStore');
    }

    public function drink(){
        return $this->belongsTo('App\Models\Drink');
    }

    public function food(){
        return $this->belongsTo('App\Models\Food');
    }
}

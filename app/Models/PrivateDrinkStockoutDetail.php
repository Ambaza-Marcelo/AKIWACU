<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateDrinkStockoutDetail extends Model
{
    //
    protected $fillable = [
        'date',
        'quantity',
        'unit',
        'price',
        'stockout_no',
        'stockout_signature',
        'requisition_no',
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
        'asker',
        'destination',
        'private_store_item_id',
        'item_movement_type'
    ];

    public function privateStoreItem(){
        return $this->belongsTo('App\Models\PrivateStoreItem');
    }
}

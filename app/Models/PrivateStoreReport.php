<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateStoreReport extends Model
{
    //
    protected $fillable = [
        'quantity_stock_initial',
        'value_stock_initial',
        'quantity_stockin',
        'value_stockin',
        'stock_total',
        'quantity_stockout',
        'value_stockout',
        'quantity_sold',
        'value_sold',
        'quantity_inventory',
        'value_inventory',
        'quantity_stock_final',
        'value_stock_final',
        'destination',
        'asker',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'type_transaction',
        'document_no',
        'private_store_item_id'
    ];

    public function privateStoreItem(){
        return $this->belongsTo('App\Models\PrivateStoreItem');
    }
}

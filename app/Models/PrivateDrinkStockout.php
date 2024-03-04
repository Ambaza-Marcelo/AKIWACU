<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateDrinkStockout extends Model
{
    //
    protected $fillable = [
        'date',
        'stockout_no',
        'stockout_signature',
        'asker',
        'description',
        'rejected_motif',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
        'asker',
        'destination',
        'status',
        'item_movement_type'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsFuelPurchase extends Model
{
    //
    protected $table = 'ms_fuel_purchases';
    protected $fillable = [
        'date',
        'purchase_no',
        'purchase_signature',
        'description',
        'rejected_motif',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
        'status',
    ];
}

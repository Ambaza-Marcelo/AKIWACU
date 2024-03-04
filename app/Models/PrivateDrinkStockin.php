<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrivateDrinkStockin extends Model
{
    //
    protected $fillable = [
        'date',
        'stockin_no',
        'stockin_signature',
        'receptionist',
        'handingover',
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
        'item_movement_type'
    ];
}

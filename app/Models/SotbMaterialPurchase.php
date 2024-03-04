<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbMaterialPurchase extends Model
{
    //
    protected $table = 'sotb_material_purchases';
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

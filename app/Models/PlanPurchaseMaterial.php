<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanPurchaseMaterial extends Model
{
    //
    protected $table = 'plan_purchase_materials';
    protected $fillable = [
        'start_date',
        'end_date',
        'plan_no',
        'plan_signature',
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

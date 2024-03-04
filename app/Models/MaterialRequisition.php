<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialRequisition extends Model
{
    //
    protected $fillable = [
        'date',
        'requisition_no',
        'requisition_signature',
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

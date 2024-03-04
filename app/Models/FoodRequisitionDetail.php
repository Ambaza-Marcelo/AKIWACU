<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodRequisitionDetail extends Model
{
    //
    protected $fillable = [
        'date',
        'quantity_requisitioned',
        'quantity_received',
        'unit',
        'price',
        'requisition_no',
        'requisition_signature',
        'description',
        'rejected_motif',
        'total_value_requisitioned',
        'total_value_received',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
        'status',
        'food_id',
    ];

    public function food(){
        return $this->belongsTo('App\Models\Food');
    }
}

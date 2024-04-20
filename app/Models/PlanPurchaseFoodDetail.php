<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanPurchaseFoodDetail extends Model
{
    //
    protected $table = 'plan_purchase_food_details';
    protected $fillable = [
        'start_date',
        'end_date',
        'quantity',
        'unit',
        'pucrhase_price',
        'plan_no',
        'plan_signature',
        'description',
        'rejected_motif',
        'total_pucrhase_amount',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
        'status',
        'drink_id',
    ];

    public function food(){
        return $this->belongsTo('App\Models\Food');
    }
}

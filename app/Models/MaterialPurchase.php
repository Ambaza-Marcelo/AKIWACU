<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialPurchase extends Model
{
    //
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
        'supplier_id'
    ];

    public function supplier(){
        return $this->belongsTo('App\Models\Supplier');
    }
}

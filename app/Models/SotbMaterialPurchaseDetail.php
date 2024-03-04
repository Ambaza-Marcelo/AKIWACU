<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbMaterialPurchaseDetail extends Model
{
    //
    protected $table = 'sotb_material_purchase_details';
    protected $fillable = [
        'date',
        'quantity',
        'unit',
        'price',
        'purchase_no',
        'purchase_signature',
        'description',
        'rejected_motif',
        'total_value',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
        'status',
        'material_id',
    ];

    public function material(){
        return $this->belongsTo('App\Models\SotbMaterial');
    }
}

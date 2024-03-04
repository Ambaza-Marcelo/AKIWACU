<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbMaterialBgStoreInventoryDetail extends Model
{
    //
    protected $table = 'sotb_material_bg_store_inventory_details';
    protected $fillable = [
        'inventory_no',
        'inventory_signature',
        'date',
        'title',
        'quantity',
        'unit',
        'cump',
        'purchase_price',
        'selling_price',
        'total_purchase_value',
        'total_selling_value',
        'total_cump_value',
        'new_quantity',
        'new_unit',
        'new_cump',
        'new_purchase_price',
        'new_selling_price',
        'new_total_purchase_value',
        'new_total_selling_value',
        'new_total_cump_value',
        'relicat',
        'description',
        'rejected_motif',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
        'material_id',
        'store_id',
    ];

    public function material(){
        return $this->belongsTo('App\Models\SotbMaterial');
    }

    public function store(){
        return $this->belongsTo('App\Models\SotbMaterialBgStore');
    }
}

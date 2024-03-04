<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbFuelInventoryDetail extends Model
{
    //
    protected $table = 'sotb_material_md_store_inventory_details';
    protected $fillable = [
        'inventory_no',
        'inventory_signature',
        'date',
        'title',
        'quantity',
        'unit',
        'cump',
        'purchase_price',
        'cost_price',
        'total_purchase_value',
        'total_cost_value',
        'total_cump_value',
        'new_quantity',
        'new_unit',
        'new_cump',
        'new_purchase_price',
        'new_cost_price',
        'new_total_purchase_value',
        'new_total_cost_value',
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
        'fuel_id',
        'pump_id',
    ];

    public function fuel(){
        return $this->belongsTo('App\Models\SotbFuel');
    }

    public function pump(){
        return $this->belongsTo('App\Models\SotbFuelPump');
    }
}

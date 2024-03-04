<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsMaterialStockoutDetail extends Model
{
    //
    protected $table = 'ms_material_stockout_details';
    protected $fillable = [
        'material_id',
        'date',
        'quantity',
        'unit',
        'price',
        'stockout_no',
        'stockout_signature',
        'requisition_no',
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
        'asker',
        'destination',
        'status',
        'store_type',
        'origin_store_id',
    ];

    public function originStore(){
        return $this->belongsTo('App\Models\MsMaterialStore');
    }

    public function material(){
        return $this->belongsTo('App\Models\MsMaterial');
    }
}

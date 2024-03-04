<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsMaterialStockout extends Model
{
    //
    protected $table = 'ms_material_stockouts';
    protected $fillable = [
        'date',
        'stockout_no',
        'stockout_signature',
        'requisition_no',
        'asker',
        'description',
        'rejected_motif',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
        'asker',
        'destination',
        'status',
        'store_type',
        'origin_store_id',
    ];

    public function originStore(){
        return $this->belongsTo('App\Models\MsMaterialStore');
    }

}

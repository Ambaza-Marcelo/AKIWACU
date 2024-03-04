<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialExtraBigStoreInventory extends Model
{
    //
    protected $fillable = [
        'inventory_no',
        'inventory_signature',
        'date',
        'title',
        'description',
        'rejected_motif',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
        'store_id',
    ];

    public function store(){
        return $this->belongsTo('App\Models\MaterialExtraBigStore');
    }
}

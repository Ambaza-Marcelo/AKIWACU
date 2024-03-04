<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarristTransfer extends Model
{
    //
    protected $fillable = [
        'date',
        'transfer_no',
        'requisition_no',
        'transfer_signature',
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
        'origin_fstore_id',
        'origin_dstore_id',
        'barrist_store_id',
    ];

    public function origindStore(){
        return $this->belongsTo('App\Models\DrinkBigStore');
    }

    public function originfStore(){
        return $this->belongsTo('App\Models\FoodBigStore');
    }

    public function barristStore(){
        return $this->belongsTo('App\Models\BarristStore');
    }
}

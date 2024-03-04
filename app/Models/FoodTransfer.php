<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoodTransfer extends Model
{
    //
    protected $table = 'food_transfers';


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
        'origin_store_id',
        'destination_store_id',
    ];

    public function originStore(){
        return $this->belongsTo('App\Models\FoodBigStore');
    }
    public function destinationStore(){
        return $this->belongsTo('App\Models\FoodSmallStore');
    }
}

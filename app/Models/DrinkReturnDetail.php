<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrinkReturnDetail extends Model
{
    //
    protected $fillable = [
        'date',
        'quantity_returned',
        'quantity_transfered',
        'unit',
        'price',
        'transfer_no',
        'requisition_no',
        'transfer_signature',
        'description',
        'rejected_motif',
        'total_value_returned',
        'total_value_transfered',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
        'status',
        'drink_id',
        'origin_store_id',
        'destination_store_id',
        'destination_extra_store_id',
    ];

    public function drink(){
        return $this->belongsTo('App\Models\Drink');
    }

    public function origineStore(){
        return $this->belongsTo('App\Models\DrinkSmallStore');
    }

    public function destinationStore(){
        return $this->belongsTo('App\Models\DrinkBigStore');
    }

    public function destinationExtraStore(){
        return $this->belongsTo('App\Models\DrinkExtraBigStore');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrinkReturn extends Model
{
    //
    protected $fillable = [
        'date',
        'transfer_no',
        'return_no',
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
        'destination_extra_store_id',
    ];

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

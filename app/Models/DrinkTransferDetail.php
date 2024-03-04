<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrinkTransferDetail extends Model
{
    //
    protected $table = 'drink_transfer_details';

    protected $fillable = [
        'date',
        'quantity_requisitioned',
        'quantity_transfered',
        'unit',
        'price',
        'transfer_no',
        'requisition_no',
        'transfer_signature',
        'description',
        'rejected_motif',
        'total_value_requisitioned',
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
        'origin_extra_store_id',
        'destination_bg_store_id',
    ];

    public function origineStore(){
        return $this->belongsTo('App\Models\DrinkBigStore');
    }

    public function destinationStore(){
        return $this->belongsTo('App\Models\DrinkSmallStore');
    }

    public function originExtraStore(){
        return $this->belongsTo('App\Models\DrinkExtraBigStore');
    }
    
    public function destinationBigStore(){
        return $this->belongsTo('App\Models\DrinkBigStore');
    }

    public function drink(){
        return $this->belongsTo('App\Models\Drink');
    }
}

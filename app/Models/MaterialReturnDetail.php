<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialReturnDetail extends Model
{
    //
    protected $table = 'material_return_details';

    protected $fillable = [
        'date',
        'quantity_returned',
        'quantity_transfered',
        'unit',
        'price',
        'transfer_no',
        'return_signature',
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
        'material_id',
        'origin_store_id',
        'destination_store_id',
    ];

    public function material(){
        return $this->belongsTo('App\Models\Material');
    }

    public function origineStore(){
        return $this->belongsTo('App\Models\MaterialSmallStore');
    }

    public function destinationStore(){
        return $this->belongsTo('App\Models\MaterialBigStore');
    }
}

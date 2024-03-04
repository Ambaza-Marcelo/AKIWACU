<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsMaterialStockin extends Model
{
    //
    protected $table = 'ms_material_stockins';
    protected $fillable = [
        'date',
        'stockin_no',
        'stockin_signature',
        'receptionist',
        'handingover',
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
        'destination_store_id',
    ];

    public function destinationStore(){
        return $this->belongsTo('App\Models\MsMaterialStore');
    }

}

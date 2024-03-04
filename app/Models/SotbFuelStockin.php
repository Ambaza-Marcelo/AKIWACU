<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbFuelStockin extends Model
{
    //
    protected $table = 'sotb_fuel_stockins';
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
        'pump_id',
    ];

    public function pump(){
        return $this->belongsTo('App\Models\SotbFuelPump');
    }

}

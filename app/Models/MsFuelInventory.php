<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsFuelInventory extends Model
{
    //
    protected $table = 'ms_fuel_inventories';
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
        'pump_id',
    ];

    public function pump(){
        return $this->belongsTo('App\Models\MsFuelPump');
    }
}

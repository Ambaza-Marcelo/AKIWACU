<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderKitchen extends Model
{
    //
    protected $fillable = [
        'date',
        'order_no',
        'order_signature',
        'description',
        'rejected_motif',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
        'table_no',
        'status',
        'employe_id',
        'table_id'
    ];

    public function employe(){
        return $this->belongsTo('App\Models\Employe');
    }

    public function table(){
        return $this->belongsTo('App\Models\Table');
    }
}

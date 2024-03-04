<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDrink extends Model
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
        'employe_id'
    ];

    public function employe(){
        return $this->belongsTo('App\Models\Employe');
    }
}

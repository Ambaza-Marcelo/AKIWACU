<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsFuelPump extends Model
{
    //
    protected $table = 'ms_fuel_pumps';
    protected $fillable = [
        'name',
        'code',
        'emplacement',
        'capacity',
        'fuel_id',
        'quantity',
        'purchase_price',
        'cost_price',
        'cump',
        'total_purchase_value',
        'total_cost_value',
        'quantite_seuil',
        'etat',
        'auteur',
        'verified',
    ];

    public function fuel(){
        return $this->belongsTo('App\Models\MsFuel');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbFuelPump extends Model
{
    //
    protected $table = 'sotb_fuel_pumps';
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
        return $this->belongsTo('App\Models\SotbFuel');
    }
}

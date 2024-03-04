<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employe extends Model
{
    //
    protected $fillable = [
        'image',
        'name',
        'position_id',
        'telephone',
        'status',
        'created_by',
        'address_id'
    ];

    public function position(){
        return $this->belongsTo('App\Models\Position');
    }

    public function address(){
    	return $this->belongsTo('\App\Models\Address');
    }

    public function facture(){
        return $this->hasMany('App\Models\Facture','employe_id');
    }

    public function factureDetail(){
        return $this->hasMany('App\Models\FactureDetail','employe_id');
    }

    public function factureRestaurant(){
        return $this->hasMany('App\Models\FactureRestaurant','employe_id');
    }

    public function factureRestaurantDetail(){
        return $this->hasMany('App\Models\FactureRestaurantDetail','employe_id');
    }


}

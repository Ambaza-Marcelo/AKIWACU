<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SwimingPool extends Model
{
    //
    protected $table = 'swiming_pools';
    protected $fillable = [
        'name',
        'code',
        'specification',
        'vat',
        'item_ct',
        'item_tl',
        'selling_price',
        'status',
        'etat',
        'auteur',
    ];

    public function booking(){
        return $this->hasMany('App\Models\BookingBooking','swiming_pool_id');
    }

    public function factureDetail(){
        return $this->hasMany('App\Models\factureDetail','swiming_pool_id');
    }
}

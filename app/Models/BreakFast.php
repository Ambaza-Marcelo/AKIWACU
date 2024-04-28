<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BreakFast extends Model
{
    //
    protected $table = 'breakfasts';
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

    public function bookingDetail(){
        return $this->hasMany('App\Models\BookingBookingDetail','breakfast_id');
    }

    public function factureDetail(){
        return $this->hasMany('App\Models\factureDetail','breakfast_id');
    }
}

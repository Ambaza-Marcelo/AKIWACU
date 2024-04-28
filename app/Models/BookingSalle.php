<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingSalle extends Model
{
    //
    protected $table = 'salles';
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
        return $this->hasMany('App\Models\BookingBookingDetail','salle_id');
    }

    public function factureDetail(){
        return $this->hasMany('App\Models\factureDetail','salle_id');
    }
}

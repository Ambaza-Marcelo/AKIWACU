<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingRoom extends Model
{
    //
    protected $table = 'rooms';
    protected $fillable = [
        'name',
        'code',
        'specification',
        'vat',
        'item_ct',
        'item_tl',
        'item_tsce_tax',
        'item_ott_tax',
        'selling_price',
        'status',
        'etat',
        'auteur',

    ];

    public function booking(){
        return $this->hasMany('App\Models\BookingBookingDetail','room_id');
    }

    public function factureDetail(){
        return $this->hasMany('App\Models\factureDetail','room_id');
    }
}

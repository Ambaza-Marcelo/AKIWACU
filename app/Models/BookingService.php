<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingService extends Model
{
    //
    protected $table = 'services';
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
        return $this->hasMany('App\Models\BookingBooking','service_id');
    }

    public function factureDetail(){
        return $this->hasMany('App\Models\factureDetail','service_id');
    }
}

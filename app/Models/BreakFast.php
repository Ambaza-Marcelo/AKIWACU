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

    public function booking(){
        return $this->hasMany('App\Models\BookingBooking','break_fast_id');
    }

    public function factureDetail(){
        return $this->hasMany('App\Models\factureDetail','break_fast_id');
    }
}

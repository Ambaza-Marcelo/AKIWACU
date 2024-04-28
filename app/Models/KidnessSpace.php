<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KidnessSpace extends Model
{
    //
    protected $table = 'kidness_spaces';
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
        return $this->hasMany('App\Models\BookingBookingDetail','kidness_space_id');
    }

    public function factureDetail(){
        return $this->hasMany('App\Models\factureDetail','kidness_space_id');
    }
}

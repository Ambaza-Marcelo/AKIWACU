<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingTechnique extends Model
{
    //
    protected $table = 'techniques';
    protected $fillable = [
        'name',
        'amount',
    ];

    public function booking(){
        return $this->hasMany('App\Models\BookingBooking','technique_id');
    }
}

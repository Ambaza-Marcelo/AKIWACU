<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingTechniqueDetail extends Model
{
    //
    protected $table = 'technique_details';
    protected $fillable = [
        'name',
        'amount',
        'booking_no',
        'booking_signature',
        'technique_id',
    ];

    public function technique(){
        return $this->belongsTo('App\Models\BookingTechnique');
    }
}

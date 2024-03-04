<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingClient extends Model
{
    //
    protected $table = 'booking_clients';
    protected $fillable = [
        'customer_name',
        'telephone',
        'mail',
        'customer_TIN',
        'customer_address',
        'vat_customer_payer',
        'company',
        'etat',
        'autre',
        'total_amount_paied',
        'total_amount_credit',
        'avalise_par',
        'date',
    ];

    public function factureDetail(){
        return $this->hasMany('App\Models\FactureDetail','booking_client_id');
    }

    public function facture(){
        return $this->hasMany('App\Models\Facture','booking_client_id');
    }

    public function bookingBooking(){
        return $this->hasMany('App\Models\BookingBooking','booking_client_id');
    }

    public function bookingBookingDetail(){
        return $this->hasMany('App\Models\BookingBookingDetail','booking_client_id');
    }
}

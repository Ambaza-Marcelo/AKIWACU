<?php

namespace App\Models\F;

use Illuminate\Database\Eloquent\Model;

class FBooking extends Model
{
    //
    protected $table = 'f_bookings';
    protected $fillable = [
        'date',
        'booking_no',
        'booking_signature',
        'description',
        'rejected_motif',
        'created_by',
        'statut_demandeur',
        'nom_demandeur',
        'adresse_demandeur',
        'telephone_demandeur',
        'nom_referent',
        'telephone_referent',
        'courriel_referent',
        'type_evenement',
        'nombre_personnes',
        'date_debut',
        'date_fin',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
        'status',
        'booking_client_id'
    ];

    public function client(){
        return $this->belongsTo('App\Models\BookingClient');
    }
}

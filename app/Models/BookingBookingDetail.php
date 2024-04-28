<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingBookingDetail extends Model
{
    //
    protected $table = 'booking_details';
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
        'technique_id',
        'salle_id',
        'service_id',
        'swiming_pool_id',
        'breakfast_id',
        'kidness_space_id',
        'booking_client_id'
    ];

    public function technique(){
        return $this->belongsTo('App\Models\BookingTechnique');
    }

    public function service(){
        return $this->belongsTo('App\Models\BookingService');
    }

    public function salle(){
        return $this->belongsTo('App\Models\BookingSalle');
    }

    public function swimingPool(){
        return $this->belongsTo('App\Models\SwimingPool');
    }

    public function breakFast(){
        return $this->belongsTo('App\Models\BreakFast');
    }

    public function kidnessSpace(){
        return $this->belongsTo('App\Models\KidnessSpace');
    }

    public function client(){
        return $this->belongsTo('App\Models\BookingClient');
    }
}

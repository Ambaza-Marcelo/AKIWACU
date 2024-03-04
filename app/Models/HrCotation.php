<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrCotation extends Model
{
    //
    protected $fillable = [
        'somme_note_obtenu',
        'somme_note_total',
        'pourcentage',
        'mention',
        'employe_id'
    ];

    public function employe(){
        return $this->belongsTo('App\Models\HrEmploye');
    }
}

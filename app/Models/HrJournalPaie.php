<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrJournalPaie extends Model
{
    //
    protected $table = 'hr_journal_paies';
    protected $fillable = [
        'date_debut',
        'date_fin',
        'etat',
        'code'
    ];
}

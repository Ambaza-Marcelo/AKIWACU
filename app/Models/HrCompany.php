<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrCompany extends Model
{
    //
    protected $table = 'hr_companies';
    protected $fillable = [
        'name',
        'nif',
        'rc',
        'commune',
        'zone',
        'quartier',
        'rue',
        'telephone1',
        'telephone2',
        'email',
        'logo',
        'developpeur'
    ];
}

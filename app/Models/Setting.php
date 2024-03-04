<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    //
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

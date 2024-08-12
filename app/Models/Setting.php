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
        'province',
    	'zone',
    	'quartier',
    	'rue',
        'tp_type',
        'vat_taxpayer',
        'ct_taxpayer',
        'tl_taxpayer',
        'tp_fiscal_center',
        'tp_activity_sector',
        'tp_legal_form',
        'postal_number',
    	'telephone1',
    	'telephone2',
    	'email',
    	'logo',
    	'developpeur'
    ];
}

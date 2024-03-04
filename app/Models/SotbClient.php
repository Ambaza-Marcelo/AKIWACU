<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbClient extends Model
{
    //
    protected $table = 'sotb_clients';
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
}

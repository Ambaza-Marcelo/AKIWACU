<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    //
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
    	return $this->hasMany('App\Models\FactureDetail','client_id');
    }

    public function facture(){
    	return $this->hasMany('App\Models\Facture','client_id');
    }
}

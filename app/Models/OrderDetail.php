<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    //
    protected $fillable = [
    	'date',
    	'article_id',
    	'quantity',
    	'unit',
        'commande_no',
        'description',
        'total_value',
        'supplier_id',
        'created_by',
        'status'

    ];

    public function article(){
    	return $this->belongsTo('App\Models\Article');
    }
}

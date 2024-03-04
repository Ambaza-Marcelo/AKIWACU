<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryDetail extends Model
{
    //
     protected $fillable = [
     	'inventory_no',
     	'date',
        'title',
        'quantity',
        'description',
        'unit',
     	'unit',
        'unit_price',
     	'total_value',
     	'new_quantity',
     	'new_price',
     	'new_total_value',
        'relica',
        'description',
        'created_by'
     ];

   public function article(){
    	return $this->belongsTo('App\Models\Article');
    }
}

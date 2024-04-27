<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{

     protected $fillable = [
        'inventory_no',
     	'date',
     	'title',
        'description',
        'created_by'
     ];
}

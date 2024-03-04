<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    //
    protected $fillable=[
    	'code',
    	'name'
    ];


    public function employe(){
    	return $this->hasMany('App\Models\Employe','service_id');
    }
}

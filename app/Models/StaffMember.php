<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffMember extends Model
{
    //
    protected $fillable = [
    	'start_date',
    	'end_date',
		'name',
		'etat',
		'flag',
		'total_amount_authorized',
		'total_amount_consumed',
		'total_amount_remaining',
		'position_id'
    ];

    public function position(){
    	return $this->belongsTo('App\Models\Position');
    }

    public function homeConsumption(){
    	return $this->hasMany('App\Models\HomeConsumption','staff_member_id');
    }

    public function homeConsumptionDetail(){
    	return $this->hasMany('App\Models\HomeConsumptionDetail','staff_member_id');
    }
}

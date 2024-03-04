<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsDriver extends Model
{
    //
    protected $table = 'ms_drivers';
    protected $fillable = [
        'firstname',
        'lastname',
        'telephone',
        'email',
        'gender',
    ];

    public function requisition(){
        return $this->hasMany('App\Models\MsFuelRequisition','driver_id');
    }

    public function requisitionDetail(){
        return $this->hasMany('App\Models\MsFuelRequisitionDetail','driver_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeConsumption extends Model
{
    //
    protected $fillable = [
        'date',
        'consumption_no',
        'consumption_signature',
        'status',
        'table_no',
        'created_by',
        'description',
        'staff_member_id'

    ];

    public function staffMember(){
        return $this->belongsTo('App\Models\StaffMember');
    }

}

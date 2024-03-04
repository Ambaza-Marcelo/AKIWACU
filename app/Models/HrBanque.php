<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HrBanque extends Model
{
    //
    protected $fillable = [
        'name',
        'code',
        'currency'
    ];

    public function employe(){
        return $this->hasMany('App\Models\HrEmploye','banque_id');
    }
}

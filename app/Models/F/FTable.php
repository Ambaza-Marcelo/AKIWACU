<?php

namespace App\Models\F;

use Illuminate\Database\Eloquent\Model;

class FTable extends Model
{
    //
    protected $table = 'f_tables';
    protected $fillable = [
        'date',
        'opening_date',
        'closing_date',
        'name',
        'order_no',
        'type',
        'waiter_name',
        'etat',
        'flag',
        'opened_by',
        'closed_by',
        'total_amount_paying',
        'total_amount_paid',
        'total_amount_remaining',
    ];

    public function fFoodOrder(){
        return $this->hasMany('App\Models\F\FFoodOrder','table_id');
    }

    public function fFoodOrderDetail(){
        return $this->hasMany('App\Models\F\FFoodOrderDetail','table_id');
    }

    public function fDrinkOrder(){
        return $this->hasMany('App\Models\F\FDrinkOrder','table_id');
    }

    public function fDrinkOrderDetail(){
        return $this->hasMany('App\Models\F\FDrinkOrderDetail','table_id');
    }

    public function fBarristaOrder(){
        return $this->hasMany('App\Models\F\FBarristaOrder','table_id');
    }

    public function fBarristaOrderDetail(){
        return $this->hasMany('App\Models\F\FBarristaOrderDetail','table_id');
    }

    public function fBartenderOrder(){
        return $this->hasMany('App\Models\F\FBartenderOrder','table_id');
    }

    public function fBartenderOrderDetail(){
        return $this->hasMany('App\Models\F\FBartenderOrderDetail','table_id');
    }
}

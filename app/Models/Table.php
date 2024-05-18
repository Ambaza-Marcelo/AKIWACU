<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    //
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

    public function orderKitchen(){
        return $this->hasMany('App\Models\OrderKitchen','table_id');
    }

    public function orderKitchenDetail(){
        return $this->hasMany('App\Models\OrderKitchenDetail','table_id');
    }

    public function orderDrink(){
        return $this->hasMany('App\Models\OrderDrink','table_id');
    }

    public function orderDrinkDetail(){
        return $this->hasMany('App\Models\OrderDrinkDetail','table_id');
    }

    public function barristOrder(){
        return $this->hasMany('App\Models\BarristOrder','table_id');
    }

    public function barristOrderDetail(){
        return $this->hasMany('App\Models\BarristOrderDetail','table_id');
    }

    public function bartenderOrder(){
        return $this->hasMany('App\Models\BartenderOrder','table_id');
    }

    public function bartenderOrderDetail(){
        return $this->hasMany('App\Models\BartenderOrderDetail','table_id');
    }
}

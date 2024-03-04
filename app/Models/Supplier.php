<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    //
    protected $fillable=[
    	'name',
    	'mail',
    	'phone_no',
    	'address_id',
        'tin_number',
        'vat_taxpayer',
        'category',
    ];

    public function address(){
    	return $this->belongsTo('App\Models\Address');
    }

    public function drinkSupplierOrder(){
    	return $this->hasMany('App\Models\DrinkSupplierOrder','supplier_id');
    }

    public function drinkSupplierOrderDetail(){
        return $this->hasOne('App\Models\DrinkSupplierOrderDetail','supplier_id');
    }

    public function foodSupplierOrder(){
        return $this->hasOne('App\Models\FoodSupplierOrder','supplier_id');
    }

    public function foodSupplierOrderDetail(){
        return $this->hasOne('App\Models\FoodSupplierOrderDetail','supplier_id');
    }

    public function materialSupplierOrder(){
        return $this->hasOne('App\Models\materialSupplierOrder','supplier_id');
    }
    public function materialSupplierOrderDetail(){
        return $this->hasOne('App\Models\materialSupplierOrderDetail','supplier_id');
    }

    public function drinkReception(){
        return $this->hasMany('App\Models\DrinkReception','supplier_id');
    }

    public function drinkReceptionDetail(){
        return $this->hasOne('App\Models\DrinkReceptionDetail','supplier_id');
    }

    public function foodReception(){
        return $this->hasOne('App\Models\FoodReception','supplier_id');
    }

    public function foodReceptionDetail(){
        return $this->hasOne('App\Models\FoodReceptionDetail','supplier_id');
    }

    public function materialReception(){
        return $this->hasOne('App\Models\materialReception','supplier_id');
    }
    public function materialReceptionDetail(){
        return $this->hasOne('App\Models\materialReceptionDetail','supplier_id');
    }
}

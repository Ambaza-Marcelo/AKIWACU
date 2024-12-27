<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    //
    protected $fillable=[
    	'supplier_name',
        'telephone',
        'mail',
        'supplier_TIN',
        'supplier_address',
        'vat_supplier_payer',
        'company',
        'etat',
        'autre',
        'total_amount_paied',
        'total_amount_credit',
        'avalise_par',
        'date',
    ];

    public function address(){
    	return $this->belongsTo('App\Models\Address');
    }

    public function drinkSupplierOrder(){
    	return $this->hasMany('App\Models\DrinkSupplierOrder','supplier_id');
    }

    public function drinkSupplierOrderDetail(){
        return $this->hasMany('App\Models\DrinkSupplierOrderDetail','supplier_id');
    }

    public function foodSupplierOrder(){
        return $this->hasMany('App\Models\FoodSupplierOrder','supplier_id');
    }

    public function foodSupplierOrderDetail(){
        return $this->hasMany('App\Models\FoodSupplierOrderDetail','supplier_id');
    }

    public function materialSupplierOrder(){
        return $this->hasMany('App\Models\materialSupplierOrder','supplier_id');
    }
    public function materialSupplierOrderDetail(){
        return $this->hasMany('App\Models\materialSupplierOrderDetail','supplier_id');
    }

    public function drinkReception(){
        return $this->hasMany('App\Models\DrinkReception','supplier_id');
    }

    public function drinkReceptionDetail(){
        return $this->hasMany('App\Models\DrinkReceptionDetail','supplier_id');
    }

    public function foodReception(){
        return $this->hasMany('App\Models\FoodReception','supplier_id');
    }

    public function foodReceptionDetail(){
        return $this->hasMany('App\Models\FoodReceptionDetail','supplier_id');
    }

    public function materialReception(){
        return $this->hasMany('App\Models\materialReception','supplier_id');
    }
    public function materialReceptionDetail(){
        return $this->hasMany('App\Models\materialReceptionDetail','supplier_id');
    }
}

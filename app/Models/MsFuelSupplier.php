<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsFuelSupplier extends Model
{
    //
    protected $table = 'ms_fuel_suppliers';
    protected $fillable=[
        'name',
        'mail',
        'phone_no',
        'address',
        'tin_number',
        'vat_taxpayer',
        'category',
    ];

    public function fuelSupplierOrder(){
        return $this->hasOne('App\Models\MsFuelSupplierOrder','supplier_id');
    }
    public function fuelSupplierOrderDetail(){
        return $this->hasOne('App\Models\MsFuelSupplierOrderDetail','supplier_id');
    }

    public function fuelReception(){
        return $this->hasOne('App\Models\MsFuelReception','supplier_id');
    }
    public function fuelReceptionDetail(){
        return $this->hasOne('App\Models\MsFuelReceptionDetail','supplier_id');
    }
}

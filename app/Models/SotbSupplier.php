<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbSupplier extends Model
{
    //
    protected $table = 'sotb_suppliers';
    protected $fillable=[
        'name',
        'mail',
        'phone_no',
        'address',
        'tin_number',
        'vat_taxpayer',
        'category',
    ];

    public function materialSupplierOrder(){
        return $this->hasOne('App\Models\SotbMaterialSupplierOrder','supplier_id');
    }
    public function materialSupplierOrderDetail(){
        return $this->hasOne('App\Models\SotbMaterialSupplierOrderDetail','supplier_id');
    }

    public function materialReception(){
        return $this->hasOne('App\Models\SotbMaterialReception','supplier_id');
    }
    public function materialReceptionDetail(){
        return $this->hasOne('App\Models\SotbMaterialReceptionDetail','supplier_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsMaterialSupplier extends Model
{
    //
    protected $table = 'ms_material_suppliers';
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
        return $this->hasOne('App\Models\MsMaterialSupplierOrder','supplier_id');
    }
    public function materialSupplierOrderDetail(){
        return $this->hasOne('App\Models\MsMaterialSupplierOrderDetail','supplier_id');
    }

    public function materialReception(){
        return $this->hasOne('App\Models\MsMaterialReception','supplier_id');
    }
    public function materialReceptionDetail(){
        return $this->hasOne('App\Models\MsMaterialReceptionDetail','supplier_id');
    }
}

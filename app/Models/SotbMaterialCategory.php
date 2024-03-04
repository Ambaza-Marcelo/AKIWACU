<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SotbMaterialCategory extends Model
{
    //
    protected $table = 'sotb_material_categories';
    protected $fillable = ['name'];


    public function material(){
        return $this->hasMany('App\Models\SotbMaterial','mcategory_id');
    }
}

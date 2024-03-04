<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsMaterialCategory extends Model
{
    //
    protected $table = 'ms_material_categories';
    protected $fillable = ['name'];


    public function material(){
        return $this->hasMany('App\Models\MsMaterial','mcategory_id');
    }
}

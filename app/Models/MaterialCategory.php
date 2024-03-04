<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaterialCategory extends Model
{
    //
    protected $fillable = ['name'];


    public function materialCategory(){
        return $this->hasMany('App\Models\MaterialCategory','mcategory_id');
    }
}

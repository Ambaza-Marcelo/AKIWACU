<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsEbpCategory extends Model
{
    //
    protected $table = 'ms_ebp_categories';
    protected $fillable = ['name'];


    public function article(){
        return $this->hasMany('App\Models\MsEbpArticle','category_id');
    }
}

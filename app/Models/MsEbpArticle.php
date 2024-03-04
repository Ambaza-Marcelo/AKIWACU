<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsEbpArticle extends Model
{
    //
    protected $table = 'ms_ebp_articles';

    protected $fillable = [
        'name',
        'code',
        'specification',
        'quantity',
        'unit',
        'purchase_price',
        'cost_price',
        'selling_price',
        'cump',
        'threshold_quantity',
        'expiration_date',
        'status',
        'updated_by',
        'created_by',
        'category_id',
    ];

    public function category(){
        return $this->belongsTo('App\Models\MsEbpCategory');
    }


    public function factureDetail(){
        return $this->hasMany('App\Models\MsEbpFactureDetail','article_id');
    }

}

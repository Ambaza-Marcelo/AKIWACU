<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsMaterialStockinDetail extends Model
{
    //
    protected $table = 'ms_material_stockin_details';
    protected $fillable = [
        'date',
        'quantity',
        'unit',
        'price_nvat',
        'vat',
        'price_wvat',
        'total_amount_purchase',
        'total_amount_selling',
        'purchase_price',
        'selling_price',
        'stockin_no',
        'receptionist',
        'handingover',
        'stockin_signature',
        'description',
        'rejected_motif',
        'total_value',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
        'reseted_by',
        'status',
        'material_id',
        'destination_store_id',
    ];

    public function destinationStore(){
        return $this->belongsTo('App\Models\MsMaterialStore');
    }

}

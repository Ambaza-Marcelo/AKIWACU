<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsMaterialStoreReport extends Model
{
    //
    protected $table = 'ms_material_store_reports';
    protected $fillable = [
        'material_id',
        'quantity_stock_initial',
        'code_store',
        'value_stock_initial',
        'quantity_stockin',
        'value_stockin',
        'quantity_transfer',
        'value_transfer',
        'stock_total',
        'quantity_stockout',
        'value_stockout',
        'quantity_return',
        'value_return',
        'quantity_reception',
        'value_reception',
        'quantity_inventory',
        'value_inventory',
        'quantity_stock_final',
        'value_stock_final',
        'cump',
        'purchase_price',
        'stockin_no',
        'reception_no',
        'stockout_no',
        'invoice_no',
        'transfer_no',
        'return_no',
        'inventory_no',
        'destination',
        'asker',
        'created_by',
        'updated_by',
        'validated_by',
        'confirmed_by',
        'approuved_by',
        'rejected_by',
    ];


    public function material(){
        return $this->belongsTo('App\Models\MsMaterial');
    }
}

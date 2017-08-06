<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmartOrder extends Model
{
    protected $table = 'smartorders';
    protected $fillable = ['customer', 'company', 'address_1', 'address_2', 'suburb',
        'state','postcode', 'country', 'customer_email', 'customer_phone', 'sc_sku', 'international_sku',
        'qty', 'item_des', 'order_instructions', 'shipping_method', 'order_id', 'order_date', 'source_name',
        'source_order_id', 'isDespatched', 'check_flag', 'order_filename', 'file_date', 'comments',
        'product_id', 'color', 'size_value'];
    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }
}

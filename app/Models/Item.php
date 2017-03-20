<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'sku_id',
        'product_id',
        'color_id',
        'size_value'
    ];

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }
    public function color()
    {
        return $this->belongsTo('App\Models\Color');
    }
}

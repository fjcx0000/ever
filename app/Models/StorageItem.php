<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmartOrder extends Model
{
    protected $table = 'smartorders';
    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }
    public function color()
    {
        return $this->belongsTo('App\Models\Color', 'color_id');
    }
}

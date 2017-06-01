<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageItem extends Model
{
    protected $table = 'storage_items';
    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'product_id');
    }
    public function color()
    {
        return $this->belongsTo('App\Models\Color', 'color_id');
    }
    public function locations()
    {
        return $this->belongsToMany('App\Models\StorageLocation', 'items_locations', 'item_id', 'location_id');
    }
}

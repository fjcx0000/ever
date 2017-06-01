<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageLocation extends Model
{
    protected $table = 'storage_locations';

    public function items()
    {
        return $this->belongsToMany('App\Models\StorageItem', 'items_locations', 'location_id', 'item_id');
    }

}

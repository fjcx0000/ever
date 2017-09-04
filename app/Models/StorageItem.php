<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageItem extends Model
{
    protected $table = 'storage_items';
    public function location()
    {
        return $this->belongsTo('App\Models\StorageLocation', 'location_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageLocation extends Model
{
    protected $table = 'storage_locations';

    public function items()
    {
        return $this->hasMany('App\Models\StorageItem', 'location_id');
    }

}

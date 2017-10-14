<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageLocation extends Model
{
    protected $table = 'storage_locations';
    protected $fillable = [
        'area',
        'line',
        'unit',
        'level',
        'storage_guid',
        'storageno',
        'status',
    ];

    public function items()
    {
        return $this->hasMany('App\Models\StorageItem', 'location_id');
    }

}

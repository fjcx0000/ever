<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\StorageLocation;
use App\Models\StorageItem;

class StorageLocunit extends Model
{
    //
    protected $table = 'storage_loclist';
    protected $fillable = [
        'area',
        'line',
        'unit',
        'levels',
        'locname',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageItem extends Model
{
    protected $table = 'storage_items';
    protected $fillable = [
        'location_id',
        'storage_guid',
        'goodsno',
        'goodsname',
        'goods_guid',
        'colorcode',
        'colordesc',
        'color_guid',
        'comments',
    ];
    public function location()
    {
        return $this->belongsTo('App\Models\StorageLocation', 'location_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StoragegoodsErp extends Model
{
    protected $table = 'storagegoods_erp';
    protected $fillable = [
        'storage_guid',
        'storageno',
        'goodsno',
        'goods_guid',
        'status',
    ];
}

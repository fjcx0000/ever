<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sku extends Model
{
    protected $fillable = [
        'sku_id',
        'isUsed' /* false: not used; true: used */
    ];
    public $incrementing = false;
    public $primaryKey = 'sku_id';
}

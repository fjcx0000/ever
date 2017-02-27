<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_id',
        'ename',
        'cname',
        'image',
        'brand_id',
        'supplier_id',
        'description'
    ];
    public $incrementing = false;
    public $primaryKey = 'product_id';

    public function brand()
    {
        return $this->belongsTo('App\Models\Brand');
    }
    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier');
    }
    public function items()
    {
        return $this->hasMany('App\Models\Item','product_id');
    }
}

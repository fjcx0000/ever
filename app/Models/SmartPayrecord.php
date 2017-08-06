<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmartPayrecord extends Model
{
    protected $table = 'smart_payrecords';
    protected $fillable = ['sc_sku','international_sku', 'source_order_id', 'date', 'qty', 'price', 'amount', 'check_flag', 'file_id'];
    public function file()
    {
        return $this->belongsTo('App\Models\SmartPayfile', 'file_id');
    }
}

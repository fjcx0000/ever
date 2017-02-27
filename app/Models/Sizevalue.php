<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sizevalue extends Model
{
    protected $fillable = [
        'sizetype_id',
        'size_value',
        'description'
    ];

    public function sizetype()
    {
        return $this->belongsTo('App\Models\Sizetype');
    }

}

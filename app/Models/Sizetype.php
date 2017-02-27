<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sizetype extends Model
{
    protected $fillable = [
        'sizetype_id',
        'cname',
        'ename'
    ];
    public $incrementing = false;
    public $primaryKey = 'sizetype_id';

    public function sizevalues()
    {
        return $this->hasMany('App\Models\Sizevalue');
    }
}

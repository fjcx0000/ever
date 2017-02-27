<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
     protected $fillable = [
         'color_id',
         'cname',
         'ename'
    ];
    public $incrementing = false;
    public $primaryKey = 'color_id';
}

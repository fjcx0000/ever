<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmartPayfile extends Model
{
    protected $table = 'smart_payfiles';
    protected $fillable = ['id', 'filename','start_date','end_date','rec_number', 'check_flag', 'file_id'];
    public function records()
    {
        return $this->hasMany(SmartPayrecord::class, 'file_id', 'id');
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: Allen
 * Date: 8/08/2017
 * Time: 11:37 PM
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;


class Oracle extends Facade
{
    protected static function getFacadeAccessor() { return 'oracle'; }
}
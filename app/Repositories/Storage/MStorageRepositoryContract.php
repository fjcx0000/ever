<?php
namespace App\Repositories\Storage;
/**
 * Created by PhpStorm.
 * User: think
 * Date: 20/04/2017
 * Time: 10:47 PM
 */
interface MStorageRepositoryContract
{
    public function getfirstLocData();
    public function getNextLocData($loc);
    public function getLocData($loc);
    public function getArealist();
    public function getLinelist($area);
    public function getUnitlist($area, $line);
    public function delLocitem($id);
}
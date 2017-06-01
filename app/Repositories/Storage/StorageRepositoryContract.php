<?php
namespace App\Repositories\Storage;
/**
 * Created by PhpStorm.
 * User: think
 * Date: 20/04/2017
 * Time: 10:47 PM
 */
interface StorageRepositoryContract
{
    public function getLocations($reqData);
    public function delLocation($reqData);
    public function importLocationFile($reqData);
    public function getItems($reqData);
    public function delItem($reqData);
    public function importProductData();
    public function getRelations($reqData);
    public function delRelation($reqData);
    public function importRelationFile($reqData);

}
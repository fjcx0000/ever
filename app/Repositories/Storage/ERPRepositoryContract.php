<?php
/**
 * Created by PhpStorm.
 * User: Allen
 * Date: 30/11/2017
 * Time: 10:56 PM
 */

namespace App\Repositories\Storage;
use Illuminate\Support\Collection;


interface ERPRepositoryContract
{
   public function convertSendNoticeMissingItemsList(Collection $missingItemList);
   public function getRetailInventory(Collection $sendNoticeList);
}
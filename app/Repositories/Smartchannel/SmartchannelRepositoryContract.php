<?php
/**
 * Created by PhpStorm.
 * User: think
 * Date: 6/06/2017
 * Time: 11:33 PM
 */

namespace App\Repositories\Smartchannel;


interface SmartchannelRepositoryContract
{
    public function getOrders($reqData);
    public function importOrderFile($reqData);
    public function importPaymentFile($reqData);
    public function getPayfiles($reqData);
    public function checkPayrecords($reqData);
}
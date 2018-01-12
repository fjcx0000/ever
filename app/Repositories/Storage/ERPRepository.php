<?php
/**
 * Created by PhpStorm.
 * User: Allen
 * Date: 1/12/2017
 * Time: 10:25 PM
 */

namespace App\Repositories\Storage;
use Illuminate\Support\Collection;
use DB;
use Oracle;
use Jacky;

class ERPRepository implements ERPRepositoryContract
{
    public function convertSendNoticeMissingItemsList(Collection $missingItemList)
    {
        //过滤掉已配已出记录
        $filtered = $missingItemList->filter(function($item, $key){
            return $item['配单数量'] - $item['发货数量'];
        });


        //过滤重复记录
        $uniqued = $filtered->unique('条形码');
        //加上存储位置和barcode，并按照存储位置排序
        $converted = $uniqued->map(function($item, $key){
            $location = DB::table('storage_items')
                ->join('storage_locations', 'storage_items.location_id', '=', 'storage_locations.id')
                ->select('storage_locations.storageno')
                ->where([
                    ['goodsno', '=', $item['货品编号']],
                    ['colordesc',  '=', $item['颜色名称']],
                ])->orderBy('storage_locations.storageno')
                ->first();
            if (empty($location)) {
                $location = DB::table('storage_items')
                    ->join('storage_locations', 'storage_items.location_id', '=', 'storage_locations.id')
                    ->select('storage_locations.storageno')
                    ->where( 'goodsno', '=', $item['货品编号'])
                    ->orderBy('storage_locations.storageno')
                    ->first();
            }
            if (!empty($location))
                $item['存储位置'] = $location->storageno;
            else
                $item['存储位置'] = "NULL";
            $item['barcode'] = "*".$item['条形码']."*";
            $item->forget('发货单据日期');
            return $item;
        })->sortBy('存储位置');

        return $converted;
    }
    public function getRetailInventory(Collection $sendNoticeList)
    {
        // empty inventory_erp and sendnotice_erp
        DB::table('inventory_erp')
            ->truncate();
        DB::table('sendnotice_erp')
            ->truncate();

        //Import inventory from erp api
        /*
        $inventory = Oracle::table('r440102')
            ->select('goodsno','goodsname','colorcode','colordesc','barcode','sizedesc','qty','lockqty')
            ->where( 'channelcode','=','100003' )
            ->orderBy('goodsno')
            ->orderBy('colorcode')
            ->orderBy('sizedesc')
            ->get();
    echo "inventory records number = ".$inventory->count()."<br/>";
        */
        $resp = Jacky::get('EverErp','/Api/Token',['appkey' => 'EverUgg', 'appsecret' => '123456']);
        if ($resp->Status)
        {
            throw new \Exception($resp->ErrorMsg);
        }
        $token = $resp->Data->get('Token');
        $resp = Jacky::get('EverErp','/Api/Stock',[
            'token' => $token,
            'channelCode' => '100003'
        ]);
        if ($resp->Status)
        {
            throw new \Exception($resp->ErrorMsg);
        }
        $inventory = $resp->Data;

        $inventory->each(function($item,$key){
            DB::table('inventory_erp')
                ->insert([
                    'goodsno' => $item->ProductCode,
                    'goodsname' => $item->ProductName,
                    'colorcode' => $item->ColorCode,
                    'colordesc' => $item->ColorName,
                    'sizedesc' => $item->Size,
                    'barcode' => $item->Barcode,
                    'qty' => $item->StockQty,
                    'lockqty' => $item->AvaiStockQty,
                ]);
        });

        //import sendnotice rom erp api
        $sendNoticeList->each(function($sheetid) use ($token) {
            $resp = Jacky::get('EverErp','/Api/SendNotice',[
                'token' => $token,
                'sheetId' => $sheetid,
                'manualId' => ''
            ]);
            if ($resp->Status)
            {
                throw new \Exception($resp->ErrorMsg);
            }
            $sendnotice = collect($resp->Data->get("Details"));
            $sendnotice->each(function($item,$key) use ($sheetid){
               DB::table('sendnotice_erp')
                ->insert([
                    'SheetId' => $sheetid,
                    'ProductCode' => $item->ProductCode,
                    'ProductName' => $item->ProductName,
                    'ColorCode' => $item->ColorCode,
                    'ColorName' => $item->ColorName,
                    'Barcode' => $item->Barcode,
                    'Size' => $item->Size,
                    'Quantity' => $item->Quantity,
                    'Price' => $item->Price,
                ]);
            });

            //addup inventory with sendnotices
            DB::select("update inventory_erp inner join sendnotice_erp on inventory_erp.barcode=sendnotice_erp.Barcode
                set inventory_erp.lockqty=inventory_erp.lockqty+sendnotice_erp.Quantity 
                where sendnotice_erp.SheetId = '$sheetid'");
        });

        //Output inventory collection
        $inventory = DB::table('inventory_erp')->get();
        return $inventory;
    }
}
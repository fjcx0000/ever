<?php
/**
 * Created by PhpStorm.
 * User: think
 * Date: 28/08/2017
 * Time: 11:25 PM
 */

namespace App\Repositories\Storage;


use App\Facades\Oracle;
use App\Models\StoragegoodsErp;
use App\Models\StorageLocation;
use App\Models\StorageItem;
use App\Models\StorageLocunit;
use DB;
use Dompdf\Exception;

class MStorageRepository implements MStorageRepositoryContract
{
    public function getfirstLocData()
    {
        $locunit = StorageLocunit::orderByRaw("area, line, unit")->first();
        if (empty($locunit)) return null;
        return $this->convert($locunit);
    }

    public function getNextLocData($loc)
    {
        $locname = $loc->area.$loc->line."-".$loc->unit;
        $locunit = StorageLocunit::where('locname','>',$locname)
            ->orderBy('locname')
            ->first();
        if (empty($locunit)) return null;
        return $this->convert($locunit);
    }

    public function getPrevLocData($loc)
    {
        $locname = $loc->area.$loc->line."-".$loc->unit;
        $locunit = StorageLocunit::where('locname','<',$locname)
            ->orderBy('locname', 'desc')
            ->first();
        if (empty($locunit)) return null;
        return $this->convert($locunit);
    }

    public function getLocData($loc)
    {
        $locunit = StorageLocunit::where([
            ['area','=',$loc->area],
            ['line','=',$loc->line],
            ['unit','=',$loc->unit],
        ])->first();
        if (empty($locunit)) return null;
        return $this->convert($locunit);
    }

    public function getArealist()
    {
        $areas = DB::table('storage_loclist')
            ->select('area')
            ->distinct()
            ->get();
        return $areas->all();
    }

    public function getLinelist($area)
    {
        $lines = DB::table('storage_loclist')
            ->select('line')
            ->distinct()
            ->where('area', '=', $area)
            ->get();
        return $lines->all();
    }

    public function getUnitlist($area, $line)
    {
        $units = DB::table('storage_loclist')
            ->select('unit')
            ->where([
                ['area', '=', $area],
                ['line', '=', $line],
            ])
            ->distinct()
            ->get();
        return $units->all();
    }

    public function delLocitem($id)
    {
        return StorageItem::destroy($id);
    }

    public function addStorageItem($request)
    {
        $area = $request->area;
        $line = $request->line;
        $unit = $request->unit;
        $level = $request->level;
        $goodsno = $request->goodsno;
        $colorcode = $request->colorcode;

        $location = StorageLocation::where([
            ['area', '=', $area],
            ['line', '=', $line],
            ['unit', '=', $unit],
            ['level', '=', $level],
        ])->first();
        if ($location == null){
            return -1;
        }

        $goods = Oracle::table('goods')
            ->select('goodsno', 'goodsname', 'goodsid')
            ->where('goodsno','=', $goodsno)
            ->first();
        if ($goods == null) return -2;

        $color = Oracle::table('color')
            ->select('colorcode', 'colordesc', 'id')
            ->where('colorcode','=', $colorcode)
            ->first();
        if ($color == null) return -3;

        $item =  StorageItem::firstOrCreate(array(
            'location_id' => $location->id,
            'storage_guid' => $location->storage_guid,
            'goodsno' => $goods->goodsno,
            'goodsname' => $goods->goodsname,
            'goods_guid' => $goods->goodsid,
            'colorcode' => $color->colorcode,
            'colordesc' => $color->colordesc,
            'color_guid' => $color->id,
        ));
        $item->comments = $request->comments;
        $item->save();
        return 1;
    }

    public function getItemLocations($request)
    {
        if(empty($goodsno = $request->goodsno))
            return [];
        $colorcode = empty($request->colorcode)? '%' : $request->colorcode;

        $locs = DB::table('storage_items')
            ->join('storage_locations', 'storage_items.location_id', '=', 'storage_locations.id')
            ->select('storageno', 'goodsno', 'goodsname', 'colordesc', 'comments')
            ->where([
                ['goodsno','=',$goodsno],
                ['colorcode','like',$colorcode],
            ])
            ->orderBy('storageno')
            ->get();
        return $locs;


    }

    public function loadLocationsFromErp()
    {
        // update status of locations
        DB::table('storage_locations')
            ->update(['status'=>'invalid']);


        $locations = Oracle::table('erp_storage')
            ->select('guid','storageno','storagename')
            ->get();
        $newLocNum = 0;
        $locations->each(function($location, $key) use (&$newLocNum){
            $locarr = explode('-', $location->storageno);
            $area = substr($locarr[0], 0, 1);
            $line = substr($locarr[0], 1);
            $unit = $locarr[1];
            $level = count($locarr) > 2? $locarr[2] : "1";
            $storageLoc = StorageLocation::where('storage_guid', '=', $location->guid)->first();
            if($storageLoc == null) {
                $storageLoc = new StorageLocation;
                $storageLoc->area = $area;
                $storageLoc->line = $line;
                $storageLoc->unit = $unit;
                $storageLoc->level = $level;
                $storageLoc->storage_guid = $location->guid;
                $storageLoc->storageno = $location->storageno;
                $storageLoc->status = 'sync';
                $storageLoc->save();
                $newLocNum++;
            } else {
                $storageLoc->area = $area;
                $storageLoc->line = $line;
                $storageLoc->unit = $unit;
                $storageLoc->level = $level;
                $storageLoc->storage_guid = $location->guid;
                $storageLoc->storageno = $location->storageno;
                $storageLoc->status = 'sync';
                $storageLoc->save();
            }
        });
        // Clean invalid locations
        DB::table('storage_items')
            ->join('storage_locations', 'storage_items.location_id', '=', 'storage_locations.id')
            ->where('storage_locations.status', '=', 'invalid')
            ->delete();
        DB::table('storage_locations')
            ->where('status', '=', 'invalid')
            ->delete();
        DB::table('storage_loclist')
            ->truncate();
        DB::insert("insert into storage_loclist (area,line,unit,levels) 
                      select area,line,unit, count(*) as levels from storage_locations
                      group by area,line,unit");
        DB::update("update storage_loclist set locname=concat(area,line,'-',unit)");

        return ['new' => $newLocNum, 'total' => StorageLocation::count()];
    }
    public function updateStorageItemsToErp()
    {
        //  load erp storage items data to temp table
        DB::table('storagegoods_erp')
            ->truncate();

        $erpItems = Oracle::table('storagegoods')
            ->join('goods', 'storagegoods.goodsid','=','goods.goodsid')
            ->join('erp_storage', 'storagegoods.storageid','=','erp_storage.guid')
            ->select('storagegoods.storageid','erp_storage.storageno','storagegoods.goodsid','goods.goodsno')
            ->get();

        $erpItems->each(function($erpItem,$key){
            try {
                $item = StoragegoodsErp::firstOrCreate(array(
                    'storage_guid' => $erpItem->storageid,
                    'goods_guid' => $erpItem->goodsid,
                ));
                $item->storage_guid = $erpItem->storageid;
                $item->goods_guid = $erpItem->goodsid;
                $item->goodsno = $erpItem->goodsno;
                $item->storageno = $erpItem->storageno;
                $item->status = 'invalid';
                $item->save();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        });

        // update items to temp table
        $itemCur = collect();
        $tmpStorageItems = DB::table('storage_items')
            ->join('storage_locations', 'storage_items.location_id', '=', 'storage_locations.id')
            ->select('storage_items.storage_guid', 'storage_items.goodsno', 'storage_items.goods_guid',
                'storage_locations.area', 'storage_locations.line', 'storage_locations.unit', 'storage_locations.level')
            ->groupBy('storage_locations.area','storage_locations.line','storage_locations.unit','storage_locations.level','storage_items.goodsno')
            ->get();
        $tmpArray = $tmpStorageItems->toArray();
        $locCur = "";
        foreach($tmpArray as $tempLocItem) {
            switch ($tempLocItem->level) {
                case '1' :
                    $location = $tempLocItem->area.$tempLocItem->line.'-'.$tempLocItem->unit;
                    break;
                case '2' :
                case '3' :
                    $location = $tempLocItem->area.$tempLocItem->line.'-'.$tempLocItem->unit.'-'.$tempLocItem->level;
            }
            if ($itemCur->contains($tempLocItem->goodsno) || $locCur == $location)
                continue;
            $itemCur->push($tempLocItem->goodsno);
            $locCur = $location;
            $storagegoodsErp = StoragegoodsErp::where([
                ['storage_guid', '=', $tempLocItem->storage_guid],
                ['goods_guid', '=', $tempLocItem->goods_guid],
            ])->first();
            if (empty($storagegoodsErp)) {
                $storagegoodsErp = new StoragegoodsErp;
                $storagegoodsErp->storage_guid = $tempLocItem->storage_guid;
                $storagegoodsErp->goods_guid = $tempLocItem->goods_guid;
                $storagegoodsErp->storageno = $location;
                $storagegoodsErp->goodsno = $tempLocItem->goodsno;
                $storagegoodsErp->status = 'new';
                $storagegoodsErp->save();
            } else {
                $storagegoodsErp->status = 'sync';
                $storagegoodsErp->save();
            }
        }
        /*
        $tmpStorageItems = DB::table('storage_items')
            ->join('storage_locations', 'storage_items.location_id', '=', 'storage_locations.id')
            ->select('storage_items.storage_guid', 'storage_items.goodsno', 'storage_items.goods_guid',
                'storage_locations.area', 'storage_locations.line', 'storage_locations.unit')
            ->where('storage_locations.level', '=', '2')
            ->groupBy('storage_locations.area','storage_locations.line','storage_locations.unit','storage_items.goodsno')
            ->get();
        $tmpArray = $tmpStorageItems->toArray();
        $locCur = "";
        foreach($tmpArray as $tempLocItem) {
            $location = $tempLocItem->area.$tempLocItem->line.'-'.$tempLocItem->unit.'-2';
            if ($itemCur->contains($tempLocItem->goodsno) || $locCur == $location)
                continue;
            $itemCur->push($tempLocItem->goodsno);
            $locCur = $location;
            $storagegoodsErp = StoragegoodsErp::where([
                ['storage_guid', '=', $tempLocItem->storage_guid],
                ['goods_guid', '=', $tempLocItem->goods_guid],
            ])->first();
            if (empty($storagegoodsErp)) {
                $storagegoodsErp = new StoragegoodsErp;
                $storagegoodsErp->storage_guid = $tempLocItem->storage_guid;
                $storagegoodsErp->goods_guid = $tempLocItem->goods_guid;
                $storagegoodsErp->storageno = $location;
                $storagegoodsErp->goodsno = $tempLocItem->goodsno;
                $storagegoodsErp->status = 'new';
                $storagegoodsErp->save();
            } else {
                $storagegoodsErp->status = 'sync';
                $storagegoodsErp->save();
            }
        }
        */

        // upload to ERP storagegoods table
        $storagegoodsErpList = StoragegoodsErp::orderBy('status','desc')->get();
        $storagegoodsErpList->each(function($storagegoodsErp, $key){
            switch($storagegoodsErp->status) {
                case 'sync':
                    break;
                case 'invalid':
                    Oracle::table('storagegoods')
                        ->where([
                            ['storageid', '=', $storagegoodsErp->storage_guid],
                            ['goodsid', '=', $storagegoodsErp->goods_guid],
                        ])->delete();
                    break;
                case 'new':
                    Oracle::table('storagegoods')
                        ->insert([
                            'storageid' => $storagegoodsErp->storage_guid,
                            'goodsid' => $storagegoodsErp->goods_guid,
                            'sdate' => DB::raw('SYSDATE'),
                        ]);
                    break;
                default:
                    break;
            }
        });
    }

    public function checkErpStorageItems()
    {
        $tmpStorageItems = DB::table('storage_items')
            ->join('storage_locations', 'storage_items.location_id', '=', 'storage_locations.id')
            ->select('storage_items.goodsno', 'storage_items.goodsname',
                'storage_locations.area', 'storage_locations.line', 'storage_locations.unit', 'storage_locations.level')
            ->groupBy('storage_locations.area','storage_locations.line','storage_locations.unit','storage_locations.level','storage_items.goodsno')
            ->get();
        $erpItems = Oracle::table('storagegoods')
            ->join('goods', 'storagegoods.goodsid','=','goods.goodsid')
            ->select('goods.goodsno')
            ->distinct()
            ->get();
        //$filtered = $tmpStorageItems->whereNotIn('goodsno', $erpItems->pluck('goodsno')->all());
        $goodsArr = $erpItems->pluck('goodsno')->all();
        $filtered = $tmpStorageItems->filter(function($value, $key) use ($goodsArr) {
            if (in_array($value->goodsno, $goodsArr))
                return false;
            else
                return true;
        });
        return $filtered->toArray();
    }

    private function convert(StorageLocunit $locunit)
    {
        $locdata['area'] = $locunit->area;
        $locdata['line'] = $locunit->line;
        $locdata['unit'] = $locunit->unit;
        $locdata['levels'] = array();
        $locs = StorageLocation::where([
            ['area','=', $locunit->area],
            ['line','=', $locunit->line],
            ['unit','=', $locunit->unit]
        ])->get();
        foreach ($locs->all() as $location) {
            $level = array();
            $level['level'] = empty($location->level) ? '1' : $location->level;
            $level['items'] = array();
            foreach ($location->items->all() as $item) {
                $locitem = array();
                $locitem['id'] = $item->id;
                $locitem['goodsno'] = $item->goodsno;
                $locitem['goodsname'] = $item->goodsname;
                $locitem['colordesc'] = $item->colordesc;
                $locitem['comments'] = $item->comments;
                array_push($level['items'], $locitem);
            }
            array_push($locdata['levels'], $level);
        }
        return $locdata;
    }

}
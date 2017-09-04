<?php
/**
 * Created by PhpStorm.
 * User: think
 * Date: 28/08/2017
 * Time: 11:25 PM
 */

namespace App\Repositories\Storage;


use DB;
use App\Models\StorageLocation;
use App\Models\StorageItem;
use App\Models\StorageLocunit;

class MStorageRepository implements MStorageRepositoryContract
{
    public function getfirstLocData()
    {
        $locunit = StorageLocunit::orderByRaw("area, line, unit")->first();
        return $this->convert($locunit);
    }

    public function getNextLocData($loc)
    {
        $locname = $loc->area.$loc->line."-".$loc->unit;
        $locunit = StorageLocunit::where('locname','>',$locname)->first();
        return $this->convert($locunit);
    }

    public function getLocData($loc)
    {
        $locunit = StorageLocunit::where([
            ['area','=',$loc->area],
            ['line','=',$loc->line],
            ['unit','=',$loc->unit],
        ])->first();
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
<?php

namespace App\Repositories\Storage;
use DB;
use App\Models\StorageLocation;
use App\Models\StorageItem;
use App\Exceptions\StorageException;
use Excel;
use Carbon\Carbon;

/**
 * Created by PhpStorm.
 * User: think
 * Date: 20/04/2017
 * Time: 10:48 PM
 */
class StorageRepository implements StorageRepositoryContract
{
    public function getLocations($reqData)
    {
        $area = empty($reqData->area)? '%' : $reqData->area;
        $line = empty($reqData->line)? '%' : $reqData->line;
        $unit = empty($reqData->unit)? '%' : $reqData->unit;
        $level = empty($reqData->level)? '%' : $reqData->level;

        return DB::table('storage_locations')
            ->select('id', 'area', 'line', 'unit', 'level')
            ->where([
                ['area', 'like', $area],
                ['line', 'like', $line],
                ['unit', 'like', $unit],
                ['level', 'like', $level],
            ])
            ->orderBy('area','asc')
            ->orderBy('line','asc')
            ->orderBy('unit','asc')
            ->get();
    }

    public function delLocation($reqData)
    {
        $id = $reqData->id;
        $location = StorageLocation::find($id);
        if ($location->items()->count() > 0) {
            throw new StorageException("Location has storage item record and can't delete!");
        }
        StorageLocation::destroy($id);
    }

    public function importLocationFile($reqData)
    {
        $clientFilename = $reqData->uploadfile->getClientOriginalName();
        if (!$reqData->hasFile('uploadfile')) {
            throw new StorageException("uploadfile doesn't exist.");
        }
        $path = $reqData->uploadfile->getRealPath();
        $data = Excel::selectSheetsByIndex(0)->load($path, function ($reader) {
        })->get();
        if (empty($data) || $data->count() == 0) {
            throw new StorageException("uploadfile is empty.");
        }
        $timestamp = Carbon::now();
        foreach ($data->toArray() as $key => $value) {
            if (!empty($value)) {
                if (empty($value['level'])) {
                    $value['level'] = "";
                }
                DB::insert('INSERT INTO storage_locations (area,line,unit,level,created_at,updated_at) VALUES(?,?,?,?,?,?)
                    ON DUPLICATE KEY UPDATE updated_at=?',
                    [$value['area'], $value['line'],$value['unit'],$value['level'],$timestamp,$timestamp,$timestamp]);
            }
        }
        return $data->count();
    }

    public function getItems($reqData)
    {

        if (empty($reqData->products)) {
            return DB::table('storage_items')
                ->join('products', 'storage_items.product_id', '=', 'products.product_id')
                ->join('colors', 'storage_items.color_id', '=', 'colors.color_id')
                ->select('id', 'storage_items.product_id as product_id', 'products.ename as product_ename',
                    'storage_items.color_id as color_id', 'colors.ename as color_ename', 'size_value')
                ->where( 'storage_items.product_id', 'like', '%' )
                ->orderBy('product_id', 'asc')
                ->orderBy('color_id', 'asc')
                ->orderBy('size_value', 'asc')
                ->get();
        } else {
            $products = explode(",", $reqData->products);
            return DB::table('storage_items')
                ->join('products', 'storage_items.product_id', '=', 'products.product_id')
                ->join('colors', 'storage_items.color_id', '=', 'colors.color_id')
                ->select('id', 'storage_items.product_id as product_id', 'products.ename as product_ename',
                    'storage_items.color_id as color_id', 'colors.ename as color_ename', 'size_value')
                ->wherein('products.product_id', $products)
                ->orderBy('product_id', 'asc')
                ->orderBy('color_id', 'asc')
                ->orderBy('size_value', 'asc')
                ->get();

        }
    }

    public function delItem($reqData)
    {
        $id = $reqData->id;
        $item = StorageItem::find($id);
        if ($item->locations()->count() > 0) {
            throw new StorageException("Item has storage location record and can't delete!");
        }
        StorageItem::destroy($id);
    }

    public function importProductData()
    {
        $items = DB::table('items')
            ->select('product_id', 'color_id')
            ->distinct()
            ->orderBy('product_id','asc')
            ->orderBy('color_id','asc')
            ->get();

        $timestamp = Carbon::now();
        foreach ($items->toArray() as $key => $value) {
            if (!empty($value)) {
                DB::insert('INSERT INTO storage_items (product_id, color_id,size_value,created_at,updated_at) 
                    VALUES(?,?,"",?,?) ON DUPLICATE KEY UPDATE updated_at=?',
                    [$value->product_id, $value->color_id, $timestamp, $timestamp,$timestamp]);
            }
        }
        return $items->count();
    }

    public function getRelations($reqData)
    {
        $area = empty($reqData->area)? '%' : $reqData->area;
        $line = empty($reqData->line)? '%' : $reqData->line;
        $unit = empty($reqData->unit)? '%' : $reqData->unit;
        $level = empty($reqData->level)? '%' : $reqData->level;

        return DB::table('items_locations')
            ->join('storage_locations', 'storage_locations.id', '=', 'items_locations.location_id')
            ->join('storage_items', 'storage_items.id', '=', 'items_locations.item_id')
            ->join('products', 'storage_items.product_id', '=', 'products.product_id')
            ->join('colors', 'storage_items.color_id', '=', 'colors.color_id')
            ->select('items_locations.id as id','area', 'line', 'unit', 'level' ,
                'products.product_id as product_id', 'products.ename as product_name',
                'colors.ename as color', 'size_value', 'comment')
            ->where([
                ['storage_locations.area', 'like', $area],
                ['storage_locations.line', 'like', $line],
                ['storage_locations.unit', 'like', $unit],
                ['storage_locations.level', 'like', $level],
            ])
            ->orderBy('area','asc')
            ->orderBy('line','asc')
            ->orderBy('unit','asc')
            ->get();
    }

    public function delRelation($reqData)
    {
        //TODO: check item is used
        $id = $reqData->id;
        DB::table('items_locations')
            ->where('id', '=', $id)
            ->delete();
    }

    public function importRelationFile($reqData)
    {
        if (!$reqData->hasFile('uploadfile')) {
            throw new StorageException("uploadfile doesn't exist.");
        }
        $path = $reqData->uploadfile->getRealPath();
        $data = Excel::selectSheetsByIndex(0)->load($path, function ($reader) {
        })->get();
        if (empty($data) || $data->count() == 0) {
            throw new StorageException("uploadfile is empty.");
        }
        $timestamp = Carbon::now();
        foreach ($data->toArray() as $key => $value) {
            if (!empty($value)) {
                if (empty($value['level'])) {
                    $value['level'] = "";
                }
                if (empty($value['color_id'])) {
                    $value['color_id'] = "";
                }
                if (empty($value['size_value'])) {
                    $value['size_value'] = "";
                }
                $location = StorageLocation::where([
                    ['area', '=', $value['area']],
                    ['line', '=', $value['line']],
                    ['unit', '=', $value['unit']],
                    ['level', '=', $value['level']],
                ])->firstOrFail();
                $item = StorageItem::where([
                    ['product_id', '=', $value['product_id']],
                    ['color_id', '=', $value['color_id']],
                    ['size_value', '=', $value['size_value']],
                ])->firstOrFail();

                DB::insert('INSERT INTO items_locations (location_id,item_id,comment,created_at,updated_at) VALUES(?,?,?,?,?)
                    ON DUPLICATE KEY UPDATE updated_at=?',
                    [$location->id, $item->id,$value['comment'],$timestamp,$timestamp,$timestamp]);
            }
        }
        return $data->count();
    }
}
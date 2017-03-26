<?php
namespace App\Repositories\Product;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Supplier;
use DB;
use Excel;
use Carbon\Carbon;

/**
 * Class ClientRepository
 * @package App\Repositories\Client
 */
class ProductRepository implements ProductRepositoryContract
{
    /**
     *
     */
    const CREATED = 'created';
    /**
     *
     */
    const UPDATED_ASSIGN = 'updated_assign';

    /**
     * @param $id
     * @return mixed
     */
    public function find($product_id)
    {
        return Product::findOrFail($product_id);
    }

    /**
     * @return mixed
     */
    public function enquiryProducts($requestData)
    {
        if (empty($requestData->products)) {
            return DB::table('items')
                ->join('products', 'items.product_id', '=', 'products.product_id')
                ->join('colors', 'items.color_id', '=', 'colors.color_id')
                ->select('products.product_id as product_id',
                    'products.ename as prd_ename',
                    'products.cname as prd_cname',
                    'colors.color_id as color_id',
                    'colors.ename as color_ename',
                    'colors.cname as color_cname',
                    'size_value', 'sku_id')
                ->where('products.product_id', 'like', '%')
                ->orderBy('product_id','asc')
                ->orderBy('color_id','asc')
                ->orderBy('size_value','asc')
                ->get();
        } else {
            $products = explode(",", $requestData->products);
            return DB::table('items')
                ->join('products', 'items.product_id', '=', 'products.product_id')
                ->join('colors', 'items.color_id', '=', 'colors.color_id')
                ->select('products.product_id as product_id',
                    'products.ename as prd_ename',
                    'products.cname as prd_cname',
                    'colors.color_id as color_id',
                    'colors.ename as color_ename',
                    'colors.cname as color_cname',
                    'size_value', 'sku_id')
                ->wherein('products.product_id', $products)
                ->orderBy('product_id','asc')
                ->orderBy('color_id','asc')
                ->orderBy('size_value','asc')
                ->get();
        }
    }

    /**
     * @param $requestData
     */
    public function create($requestData)
    {
        $product = Product::create($requestData);
        Session()->flash('flash_message', 'Product successfully added');
    }

    /**
     * @param $id
     * @param $requestData
     */
    public function update($id, $requestData)
    {
        $product = Product::findOrFail($id);
        $product->fill($requestData->all())->save();
    }

    /**
     * @param $id
     */
    public function destroy($product_id)
    {
        try {
            $product = Product::findorFail($product_id);
            $product->delete();
            Session()->flash('flash_message', 'Product successfully deleted');
        } catch (\Illuminate\Database\QueryException $e) {
            Session()->flash('flash_message_warning', 'Product can NOT have items assigned when deleted');
        }
    }

    /**
     * @return mixed
     */
    public function listAllSuppliers()
    {
        return Supplier::pluck('supplier_name', 'supplier_id');
    }

    /**
     * @return mixed
     */
    public function listAllBrands()
    {
        return Brand::pluck('description', 'brand_id');
    }

    /**
     * @param $requestData
     */
    public function importSKU($requestData)
    {
        $clientFilename = $requestData->excel->getClientOriginalName();
        if (!$requestData->hasFile('excel')) {
            Session()->flash('flash_message_warning', 'Excel File Not Found! ' . $clientFilename);
            return;
        }
        $path = $requestData->excel->getRealPath();
        $data = Excel::selectSheetsByIndex(0)->load($path, function ($reader) {
        })->get();
        if (empty($data) || $data->count() == 0) {
            Session()->flash('flash_message_warning', 'Excel File is empty! ' . $clientFilename);
            return;
        }
        $timestamp = Carbon::now();
        foreach ($data->toArray() as $key => $value) {
            if (!empty($value)) {
                DB::insert('INSERT INTO skus (sku_id, isUsed,created_at,updated_at) VALUES(?,?,?,?)
                    ON DUPLICATE KEY UPDATE isUsed=?, updated_at=?',
                    [$value['sku_id'], $value['isused'], $timestamp, $timestamp, $value['isused'], $timestamp]);
            }
        }
        Session()->flash('flash_message', 'Excel file imported successfully, ' . $data->count() . ' records inserted.');
    }

    /**
     * @param $requestData
     */
    public function importProduct($requestData)
    {
        $clientFilename = $requestData->excel->getClientOriginalName();
        if (!$requestData->hasFile('excel')) {
            Session()->flash('flash_message_warning', 'Excel File Not Found! ' . $clientFilename);
            return;
        }
        $path = $requestData->excel->getRealPath();
        $data = Excel::selectSheetsByIndex(0)->load($path, function ($reader) {
        })->get();
        if (empty($data) || $data->count() == 0) {
            Session()->flash('flash_message_warning', 'Excel File is empty! ' . $clientFilename);
            return;
        }
        $timestamp = Carbon::now();
        // insert into items
        $itemNumber = $data->count();

        foreach ($data->toArray() as $key => $value) {
            if (!empty($value)) {
                DB::insert('INSERT INTO items (sku_id, product_id, color_id, size_value, created_at, updated_at)
                    VALUES(?,?,?,?,?,?) ON DUPLICATE KEY UPDATE sku_id=?, updated_at=?',
                    [$value['sku_id'], $value['product_id'], $value['color_id'], $value['size_value'],
                        $timestamp, $timestamp, $value['sku_id'], $timestamp]);
            }
        }
        // insert into products
        $productArray = $data->keyBy('product_id');
        $productNumber = $productArray->count();
        foreach ($productArray as $key => $value) {
            if (!empty($value)) {
                DB::insert('INSERT INTO products (product_id, ename, cname, supplier_id, created_at, updated_at)
                    VALUES(?,?,?,?,?,?) ON DUPLICATE KEY UPDATE ename=?, cname=?, supplier_id=?, updated_at=?',
                    [$value['product_id'], $value['product_ename'], $value['product_cname'], $value['supplier_id'],
                        $timestamp, $timestamp,
                        $value['product_ename'], $value['product_cname'], $value['supplier_id'], $timestamp]);
            }
        }

        // insert into colors
        $colorArray = $data->keyBy('color_id');
        $colorNumber = $colorArray->count();
        foreach ($colorArray as $key => $value) {
            if (!empty($value)) {
                DB::insert('INSERT INTO colors (color_id, ename, cname, created_at, updated_at)
                    VALUES(?,?,?,?,?) ON DUPLICATE KEY UPDATE ename=?, cname=?, updated_at=?',
                    [$value['color_id'], $value['color_ename'], $value['color_cname'],
                        $timestamp, $timestamp, $value['color_ename'], $value['color_cname'], $timestamp]);
            }
        }
        Session()->flash('flash_message', 'Excel file imported successfully, inserted [' . $productNumber . '] products,
                            [' . $itemNumber . '] items, [' . $colorNumber . '] colors.');
    }

    /**
     * @return mixed
     */
    public function selectProducts($enqStr)
    {
        return DB::table('products')
            ->select('product_id', 'ename')
            ->where('product_id', 'like', "%$enqStr%")
            ->orwhere('cname', 'like', "%$enqStr%")
            ->orwhere('ename', 'like', "%$enqStr%")
            ->orderBy('product_id', 'asc')
            ->get();
    }

    /**
     * Allocate sku to items, and update sku record as used
     * @param Array $products
     * @return allocated sku number
     */
    public function allocateSKU($products)
    {
        //count sku number needed
        $itemNum = 0;
        DB::transaction(function() use (&$itemNum,$products) {
            // get  updating number
            $items = DB::table('items')
                ->select('id')
                ->wherein('product_id', $products)
                ->wherenull('sku_id')
                ->get();
            $itemNum = $items->count();
            // get skus
            $skus = DB::table('skus')
                ->select('sku_id')
                ->where('isUsed','=','0')
                ->limit($itemNum)
                ->get();
            $skuArray = $skus->pluck('sku_id')->all();
            $tmpCursor = 0;
            // update items with sku
            foreach($items as $item) {
                DB::table('items')
                    ->where('id','=',$item->id)
                    ->update(['sku_id'=>$skuArray[$tmpCursor]]);
                $tmpCursor++;
            }
            // update skus isused field
            DB::table('skus')
                ->wherein('sku_id',$skuArray)
                ->update(['isUsed'=>'1']);

        });

        return $itemNum;
    }
}

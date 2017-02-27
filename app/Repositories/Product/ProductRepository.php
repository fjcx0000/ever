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
        $product_id = isset($requestData->product_id)?$requestData->product_id : null;
        $cname = isset($requestData->cname)?$requestData->cname : null;
        $ename = isset($requestData->ename)?$requestData->ename : null;
        if (!$product_id && !$cname && !$ename) { //all data enquiry
            $limitNum = env('RECORDS_DISPLAY_NUM');
            return DB::table('products')
            ->join('brands', 'brands.brand_id', '=', 'products.brand_id')
            ->join('suppliers', 'suppliers.supplier_id', '=', 'products.supplier_id')
            ->select('product_id', 'cname', 'ename', 'brands.description as brand', 'suppliers.supplier_name', 'products.updated_at')
            ->orderBy('products.updated_at', 'desc')
            ->limit($limitNum)
            ->get();

        } else {
            return DB::table('products')
                ->join('brands', 'brands.brand_id', '=', 'products.brand_id')
                ->join('suppliers', 'suppliers.supplier_id', '=', 'products.supplier_id')
                ->select('product_id', 'cname', 'ename', 'brands.description as brand', 'suppliers.supplier_name', 'products.updated_at')
                ->where([
                    ['product_id', 'like', "$product_id%"],
                    ['ename', 'like', "$ename%"],
                    ['cname', 'like', "$cname%"]
                ])
                ->orderBy('products.updated_at', 'desc')
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
            Session()->flash('flash_message_warning', 'Excel File Not Found! '.$clientFilename);
            return;
        }
        $path = $requestData->excel->getRealPath();
        $data = Excel::selectSheetsByIndex(0)->load($path, function($reader){})->get();
        if (empty($data) || $data->count()==0) {
            Session()->flash('flash_message_warning', 'Excel File is empty! '.$clientFilename);
            return;
        }
        $timestamp = Carbon::now();
        foreach ($data->toArray() as $key => $value) {
            if (!empty($value)) {
                DB::insert('INSERT INTO skus (sku_id, isUsed,created_at,updated_at) VALUES(?,?,?,?)
                    ON DUPLICATE KEY UPDATE isUsed=?, updated_at=?',
                    [$value['sku_id'],$value['isused'], $timestamp, $timestamp, $value['isused'], $timestamp]);
            }
        }
        Session()->flash('flash_message', 'Excel file imported successfully, '.$data->count().' records inserted.');
    }

    /**
     * @param $requestData
     */
    public function importProduct($requestData)
    {
        $clientFilename = $requestData->excel->getClientOriginalName();
        if (!$requestData->hasFile('excel')) {
            Session()->flash('flash_message_warning', 'Excel File Not Found! '.$clientFilename);
            return;
        }
        $path = $requestData->excel->getRealPath();
        $data = Excel::selectSheetsByIndex(0)->load($path, function($reader){})->get();
        if (empty($data) || $data->count()==0) {
            Session()->flash('flash_message_warning', 'Excel File is empty! '.$clientFilename);
            return;
        }
        $timestamp = Carbon::now();
        // insert into items
        $itemNumber = $data->count();

        foreach ($data->toArray() as $key => $value) {
            if (!empty($value)) {
                DB::insert('INSERT INTO items (sku_id, product_id, color_id, size_value, created_at, updated_at)
                    VALUES(?,?,?,?,?,?) ON DUPLICATE KEY UPDATE sku_id=?, updated_at=?',
                    [$value['sku_id'],$value['product_id'], $value['color_id'], $value['size_value'],
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
                    [$value['product_id'],$value['product_ename'], $value['product_cname'], $value['supplier_id'],
                        $timestamp, $timestamp,
                        $value['product_ename'], $value['product_cname'], $value['supplier_id'], $timestamp]);
            }
        }

        // insert into colors
        Session()->flash('flash_message', 'Excel file imported successfully, inserted ['.$productNumber.'] products,
                            ['.$itemNumber.'] items.');
    }
}

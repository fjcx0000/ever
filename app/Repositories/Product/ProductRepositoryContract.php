<?php
namespace App\Repositories\Product;

interface ProductRepositoryContract
{
    public function find($product_id);

    public function enquiryProducts($requstData);

    public function create($requestData);

    public function update($product_id, $requestData);

    public function destroy($product_id);

    public function listAllSuppliers();

    public function listAllBrands();

    public function importSKU($requestData);

    public function importProduct($requestData);

    public function selectProducts($enqStr);

    public function allocateSKU($products);

    public function getProductDetail($product_id);

}

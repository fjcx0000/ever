<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Excel\ProductListImport;
use Datatables;
use Storage;
use File;
use Excel;
use Session;
use Illuminate\Http\Request;
use App\Http\Requests\Product\StoreProductRequest;
use App\Repositories\Product\ProductRepositoryContract;

class ProductsController extends Controller
{
    protected $products;

    public function __construct(
        ProductRepositoryContract $products
    )
    {
        $this->products = $products;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('products.index');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return mixed
     */
    public function create()
    {
        return view('products.create')
            ->withBrands($this->products->listAllBrands())
            ->withSuppliers($this->products->listAllSuppliers());
    }

    /**
     * @param StoreProductRequest $request
     * @return mixed
     */
    public function store(StoreProductRequest $request)
    {
        $this->products->create($request->all());
        return redirect()->route('products.index');
    }

    /**
     * @param Request $request
     * @return null-not exist; others - exist
     */
    public function productExists(Request $request)
    {
        if ($request->ajax()) {
            try {
                $chkResult = $this->products->find($request->product_id);
                if ($chkResult)
                     return "EXIST";
            } catch (\Exception $e) {
                return "NOT FOUND";
            }
        }
    }

    /**
     * Make json respnse for datatables
     * @return mixed
     */
    public function anyData(Request $request)
    {
        //$results = $this->products->listLatestProducts(10);
        $results = $this->products->enquiryProducts($request);
        return Datatables::of($results)
           // ->where('product_id', '=', $request->product_id)
            ->add_column('edit', '
                <a href="{{ route(\'products.edit\', $product_id) }}" class="btn btn-success" >Edit</a>')
            ->add_column('delete', '
                <form action="{{ route(\'products.destroy\', $product_id) }}" method="POST">
            <input type="hidden" name="_method" value="DELETE">
            <input type="submit" name="submit" value="Delete" class="btn btn-danger" onClick="return confirm(\'Are you sure?\')"">

            {{csrf_field()}}
            </form>')
            ->make(true);
    }

    /**
     * File select
     */
    public function fileselect(Request $request)
    {
        return view('products.fileupload');
    }
    /**
     * File upload
     */
    public function fileupload(Request $request)
    {
        $this->validate($request,[
            'filetype'=>'required',
            'excel'=>'required',
        ]);
        switch ($request->filetype) {
            case "sku":
                $this->products->importSKU($request);
                break;
            case "product":
                $this->products->importProduct($request);
                break;
            default:
                Session()->flash('flash_message_warning', 'Wrong file type!');
                break;
        }
        return redirect()->route('products.fileselect');
    }
    /**
     * import excel file
     */
    public function importProductList(ProductListImport $import)
    {
        $result = $import->get();
    }
}
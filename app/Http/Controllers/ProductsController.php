<?php

namespace App\Http\Controllers;

use App\Http\Helpers\Excel\ProductListImport;
use Datatables;
use Illuminate\Http\JsonResponse;
use Storage;
use File;
use Excel;
use Session;
use Carbon;
use Response;
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
    public function itemData(Request $request)
    {
        //$results = $this->products->listLatestProducts(10);
        $results = $this->products->enquiryProducts($request);
        return Datatables::of($results)
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
    /**
     * select products
     * return select option list
     */
    public function selectProducts(Request $request)
    {
        if ($request->ajax()) {
            $products = $this->products->selectProducts($request->enqstr);
            return view('products.selectoptions')->with('products',$products);
        }
    }
    /**
     * show nosku product list
     */
    public function showNoskuList(Request $request)
    {
        return view('products.skuallocation');
    }
    /**
     *  get NoSku Product List
     */
    public function getNoskuList(Request $request)
    {

    }
    /**
     * allocate skus to items
     */
    public function allocateSku(Request $request)
    {
        $this->validate($request,[
            'products'=>'required',
        ]);
        $products = explode(",", $request->products);
        $number = $this->products->allocateSKU($products);
        return $number." items were allocated with skus.";
    }
    /**
     * export and download sku file
     */
    public function exportSkuFile(Request $request)
    {
        $skuData = $this->products->enquiryProducts($request);
        $dt = Carbon::now();
        $excelFile = 'sku_'.$dt->year.$dt->month.$dt->day.$dt->hour.$dt->minute.$dt->second;
        Excel::create($excelFile,function($excel) use ($skuData) {
            $excel->sheet('sheet1', function($sheet) use ($skuData) {
                $skusArray[] = ['货号','颜色','尺寸','12位国际编码','条形码'];
                foreach ($skuData as $sku) {
                    $skusArray[] = [
                        $sku->product_id,
                        $sku->color_id,
                        $sku->size_value,
                        '0',
                        $sku->sku_id
                    ];
                }
                $sheet->fromArray($skusArray,null,'A1', false, false);
            });
        })->store('xlsx', storage_path('app/excel'))
        ->download('xlsx');
        //return $excelFile." has been created, ".$skuData->count()." records exported.";
    }
    /**
     * enquiry products details, response JSON
     */
    public function getProductDetails(Request $request)
    {
        $this->validate($request,[
            'product_id'=>'required',
        ]);
        $details = $this->products->getProductDetail($request->product_id);
        if (empty($details)) {
            return new JsonResponse("Record not found",404);
        } else {
            return new JsonResponse($details);
        }
    }
}
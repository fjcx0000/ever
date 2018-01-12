<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Excel;
use DNS1D;
use App\Repositories\Storage\ERPRepositoryContract;
use GuzzleHttp\Client;
use Jacky;


class ExcelController extends Controller
{
    protected $erpRepository;
    public function __construct(
        ERPRepositoryContract $erpRepository
    )
    {
        $this->erpRepository = $erpRepository;
    }
    public function index()
    {
        return view('excel.index');
    }
    public function inventoryIndex()
    {
        return view('excel.inventory');
    }
    public function getInventoryExcel(Request $request)
    {
        if(!empty($request->sendnotices))
            $sendnoticeList = collect(explode(',',$request->sendnotices));
        else
            $sendnoticeList = collect([]);
        $inventory = $this->erpRepository->getRetailInventory($sendnoticeList);
        Excel::create('retailInventory', function($excel) use($inventory) {
            $excel->sheet('inventory', function ($sheet) use ($inventory) {
                $exportArray = $inventory->map(function($item){
                    //var_dump($item);
                    return (array)$item;
                })->toArray();
                $sheet->fromArray($exportArray);
            });
        })->export('xls');
        /*
        $client = new Client();
        $resp = $client->request('GET','http://203.219.167.218/EverService/Api/Token',[
            'query' =>
                [ 'appkey' => 'EverUgg', 'appsecret' => '123456']
        ]);
        echo $resp->getBody();
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
        dd($resp->Data);
        */
    }
    public function processFile(Request $request)
    {
        $this->validate($request,[
            'filetype'=>'required',
            'uploadfile'=>'required',
        ]);
        if (!$request->hasFile('uploadfile')) {
            throw new StorageException("uploadfile doesn't exist.");
        }
        $path = $request->uploadfile->getRealPath();
        $data = Excel::selectSheetsByIndex(0)->load($path,function($reader){
        })->get();
        if (empty($data) || $data->count() == 0) {
            throw new StorageException("uploadfile is empty.");
        }
        try {
            switch ($request->filetype) {
            case "EXCEL01": // 已配未出EXCEL整理
                $exportData = $this->erpRepository->convertSendNoticeMissingItemsList($data);
                //dd($exportData);
                break;
            case "EXCEL02":
                break;
            default:
                throw new StorageException("Undefined File Type");
            }

            Excel::create('missingItemList', function($excel) use($exportData) {
                $excel->sheet('convert result', function ($sheet) use ($exportData) {
                    /*
                    $exportArray[] = ['颜色', 'barcode', '货号'];
                    foreach ($exportData as $row) {
                        var_dump($row->toArray());

                        $exportArray[] = [
                            $row->get('颜色'),
                            "*" . $row->get('颜色') . "*",
                            $row->get('货号'),
                        ];
                    }
                    var_dump($exportArray);

                    $exportData->map(function ($row) {
                        $row['barcode'] = DNS1D::getBarcodePNG($row['sku'], "C39+");
                        return $row;
                    });
                    */
                    $exportArray = $exportData->toArray();
                    $sheet->fromArray($exportArray);
                });
            })->export('xls');

        } catch (Exception $e) {
            return [
                'result' => false,
                'message' => $e->getMessage(),
            ];
        }

    }
}

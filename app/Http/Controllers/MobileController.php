<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;
use Yajra\Oci8\Connectors\OracleConnector;
use Yajra\Oci8\Oci8Connection;
use Oracle;
use Excel;
use Carbon\Carbon;


class MobileController extends Controller
{

    private $sizeOrderList = [
        'L4','L5','L6','L7','L8','L9','L10','L11','L12','L13','L14','L15','L16',
        '34','35','36','37','38','39','40','41','42','43','44','45','46',
        'XS','S','M','L','XL',
        '4\5','6\7','8\9','10\11','12\13','1\2'];

    public function index()
    {
        /*
        $agent = new Agent();
        if ($agent->isDesktop()) {
            return redirect('/');
        }
        */
        return view('mobile.index');
    }
    public function getProducts(Request $request)
    {
        if (empty($request->term))
            return [];

        $qryStr = "%".$request->term."%";

        $goods = Oracle::table('goods')
            ->select('goodsno', 'goodsname')
            ->where([
                ['status', '=', 1],
                ['goodsno','like', $qryStr],
            ])
            ->orWhere([
                ['status', '=', 1],
                ['goodsname','like', $qryStr],
            ])
            ->get();
        $goodslist = $goods->map(function($item, $key) {
            return $item->goodsno.'-'.$item->goodsname;
        });
        return $goodslist->all();
    }
    public function getColors(Request $request)
    {
        if(empty($goodsno = $request->goodsno))
            return [];
        $colors = Oracle::table('goodscolor a')
            ->join('goods b','b.goodsid','=','a.goodsid')
            ->join('color c','c.id','=','a.colorid')
            ->select('c.colorcode','c.colordesc')
            ->where('b.goodsno','=',$goodsno)
            ->orderBy('c.colorcode')
            ->get();
        return $colors->all();
    }
    public function getSizes(Request $request)
    {

        if(empty($goodsno = $request->goodsno))
            return [];
        if(empty($colorcode = $request->colorcode))
            return [];
        $sizes = Oracle::select("
          SELECT a.barcode,d.sizedesc  
          FROM GOODSBARCODE a, goods b,color c, sizecategory d 
          where a.goodsid=b.goodsid and a.colorid = c.id and d.id = a.sizeid and 
            b.goodsno='".$goodsno."' and c.colorcode = '".$colorcode."'");

        usort($sizes, function($a,$b) {
            $posi_a = array_search($a->sizedesc, $this->sizeOrderList);
            $posi_b = array_search($b->sizedesc, $this->sizeOrderList);
            return $posi_a - $posi_b;
        });
        return $sizes;
    }
    public function getStock(Request $request)
    {
        if(empty($goodsno = $request->goodsno))
            return [];
        $colorcode = empty($request->colorcode)? '%' : $request->colorcode;
        $barcode = empty($request->barcode)? '%' : $request->barcode;

        $stocks = Oracle::table('r440102')
            ->select('colordesc','barcode','sizedesc','qty','lockqty')
            ->where([
                ['channelcode','=','100003'],
                ['goodsno','=',$goodsno],
                ['colorcode','like',$colorcode],
                ['barcode','like',$barcode],
            ])
            ->orderBy('colordesc')
            ->get();
        $groupStocks = $stocks->groupBy('colordesc')
            ->map(function($item, $key){
                return $item->sortBy(function($subItem,$key) {
                    return array_search($subItem->sizedesc, $this->sizeOrderList);
                })->values();
            });
        return $groupStocks->all();
    }
    public function study(Request $request)
    {
        return view('mobile.study');
    }
    public function uploadStorageFile(Request $request)
    {
        return view('mobile.storagefile');
    }
    public function convertErpStorageFile(Request $request)
    {
        $this->validate($request,[
            'excel'=>'required',
        ]);

        if (!$request->hasFile('excel')) {
            return "Excel not exist";
        }
        $path = $request->excel->getRealPath();
        $data = Excel::selectSheetsByIndex(0)->load($path, function ($reader) {
        })->get();
        if (empty($data) || $data->count() == 0) {
            return "Excel file is empty";
        }
        $erpStorages = array(['storageno']);
        foreach ($data->toArray() as $key => $value) {
            if (!empty($value)) {
                $area = $value['area'];
                $startLine = $value['startline'];
                $endLine = $value['endline'];
                $units = $value['units'];
                for($i=$startLine; $i<=$endLine; $i++) {
                    for ($j=1;$j<= $units; $j++) {
                        $storageno1 = $area.str_pad($i,2,'0',STR_PAD_LEFT).'-'.$j;
                        $storageno2 = $area.str_pad($i,2,'0',STR_PAD_LEFT).'-'.$j.'-2';
                        $storageno3 = $area.str_pad($i,2,'0',STR_PAD_LEFT).'-'.$j.'-3';
                        $erpStorages[] = [$storageno1];
                        $erpStorages[] = [$storageno2];
                        $erpStorages[] = [$storageno3];
                    }
                }
            }
        }

        $excelFile = 'erp_storageno_list';
        Excel::create($excelFile,function($excel) use ($erpStorages) {
            $excel->sheet('sheet1', function($sheet) use ($erpStorages) {
                $sheet->fromArray($erpStorages,null,'A1', false, false);
            });
        })->store('xlsx', storage_path('app/excel'))
            ->download('xlsx');

    }
}

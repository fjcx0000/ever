<?php
/**
 * Created by PhpStorm.
 * User: think
 * Date: 6/06/2017
 * Time: 11:34 PM
 */

namespace App\Repositories\Smartchannel;

use DB;
use Excel;
use App\Models\SmartOrder;
use App\Models\SmartPayrecord;
use App\Models\SmartPayfile;
use Carbon\Carbon;


class SmartchannelRepository implements SmartchannelRepositoryContract
{
    public function getOrders($reqData)
    {
        $file_startdate = empty($reqData->file_startdate)? Carbon::createFromFormat('d-m-Y','01-01-2016')
                            : Carbon::createFromFormat('d-m-Y',$reqData->file_startdate);
        $file_enddate = empty($reqData->file_enddate)? Carbon::now()->format('Y-m-d')
                            : Carbon::createFromFormat('d-m-Y',$reqData->file_enddate);
        $order_id = empty($reqData->order_id)? '%' : $reqData->order_id;
        $products = empty($reqData->product_id)? '' : explode(",", $reqData->product_id);
        $despatchVal = [
            'TRUE' => 1,
            'FALSE' => 0,
        ];
        $checkVal = [
            'N' => 0,
            '1' => 1,
            '2' => 2,
            '3' => 3,
        ];
        $isDespatched = empty($reqData->isDespatched)? '%' : $despatchVal[$reqData->isDespatched];
        $check_flag = empty($reqData->check_flag)? '%' : $checkVal[$reqData->check_flag];


        if(empty($products)) {
            return DB::table('smartorders')
                ->select('id', DB::raw("DATE_FORMAT(file_date,'%d-%m-%Y') as file_date"), 'order_id', 'product_id', 'color', 'size_value',
                    'qty', 'isDespatched', 'check_flag')
                ->whereBetween('file_date',[$file_startdate,$file_enddate])
                ->where([
                    ['order_id', 'like', $order_id],
                    ['isDespatched', 'like', $isDespatched],
                    ['check_flag', 'like', $check_flag],
                ])
                ->get();

        } else {
            return DB::table('smartorders')
                ->select('id', DB::raw("DATE_FORMAT(file_date,'%d-%m-%Y') as file_date"), 'order_id', 'product_id', 'color', 'size_value',
                    'qty', 'isDespatched', 'check_flag')
                ->whereBetween('file_date',[$file_startdate,$file_enddate])
                ->whereIn('product_id',$products)
                ->where([
                    ['order_id', 'like', $order_id],
                    ['isDespatched', 'like', $isDespatched],
                    ['check_flag', 'like', $check_flag],
                ])
                ->get();

        }
    }
    public function importOrderFile($reqData)
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
        $order_filename = $reqData->uploadfile->getClientOriginalName();
        $file_date = Carbon::createFromFormat('Y-m-d', substr($order_filename,4,10));
        foreach ($data->toArray() as $key => $value) {
            if (!empty($value)) {
                if (array_key_exists('unit_price', $value)) unset($value['unit_price']);
                if (array_key_exists('group', $value)) unset($value['group']);
                $arrs = explode('-',$value['sc_sku']);
                $value['order_date'] = Carbon::createFromFormat('m/d/Y', $value['order_date']);
                $value['product_id'] = $arrs[0];
                $value['color'] = $arrs[1];
                $value['size_value'] = $arrs[2];
                $value['isDespatched'] = TRUE;
                $value['check_flag'] = 0;

                $value['order_filename'] = $order_filename;
                $value['file_date'] = $file_date;

                SmartOrder::create($value);
            }
        }
        return $data->count();
    }
    public function importPaymentFile($reqData)
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
        $payment_filename = $reqData->uploadfile->getClientOriginalName();
        $start_date = Carbon::createFromFormat('Ymd', substr($payment_filename,3,8));
        $end_date = Carbon::createFromFormat('Ymd', substr($payment_filename,12,8));
        $smartPayfile = new SmartPayfile;
        $smartPayfile->filename = $payment_filename;
        $smartPayfile->start_date = $start_date;
        $smartPayfile->end_date = $end_date;
        $smartPayfile->rec_number = $data->count();
        $smartPayfile->check_flag = 0;
        $smartPayfile->save();
        foreach ($data->toArray() as $key => $value) {
            if (!empty($value)) {
                $value['check_flag'] = 0;
                $value['file_id'] = $smartPayfile->id;
                SmartPayrecord::create($value);
            }
        }
        return $data->count();
    }
    public function getPayfiles($reqData)
    {
        $start_date = empty($reqData->start_date) ? '2016-01-01' : $reqData->start_date;
        $end_date = empty($reqData->end_date) ? Carbon::now()->format('Y-m-d') : $reqData->end_date;

        return DB::table('smart_payfiles')
            ->select('id', 'filename', 'start_date', 'end_date', 'rec_number', 'check_flag')
            ->where([
                ['start_date', '>=', $start_date],
                ['end_date', '<=', $end_date],
            ])
            ->get();
    }
    public function checkPayrecords($reqData)
    {
        $payfile = SmartPayfile::find($reqData->file_id);
        $start_date = $payfile->start_date;
        $end_date = $payfile->end_date;
        //check matched records
        DB::table('smart_payrecords')
            ->join('smartorders', function($join){
                $join->on("smartorders.source_order_id", "=", "smart_payrecords.source_order_id")
                    ->on("smartorders.sc_sku", "=", "smart_payrecords.sc_sku")
                    ->on("smartorders.qty", "=", "smart_payrecords.qty");
            })
            ->where("file_id", "=", $reqData->file_id)
            ->whereBetween("smartorders.file_date", [$start_date, $end_date])
            ->where("smartorders.isDespatched", "=", TRUE)
            ->update(['smart_payrecords.check_flag'=>1,
                        'smartorders.check_flag'=>1]);
        //Check order_id&item matched but qty wrong records
        DB::table('smart_payrecords')
            ->join('smartorders', function($join){
                $join->on("smartorders.source_order_id", "=", "smart_payrecords.source_order_id")
                    ->on("smartorders.sc_sku", "=", "smart_payrecords.sc_sku")
                    ->on("smartorders.qty", "<>", "smart_payrecords.qty");
            })
            ->where("file_id", "=", $reqData->file_id)
            ->whereBetween("smartorders.file_date", [$start_date, $end_date])
            ->where("smartorders.isDespatched", "=", TRUE)
            ->update(['smart_payrecords.check_flag'=>3,
                'smartorders.check_flag'=>3]);

        //check unmatched records in smartorders
        DB::table('smartorders')
            ->whereBetween("file_date", [$start_date, $end_date])
            ->where("isDespatched", "=", TRUE)
            ->where("check_flag", "=", 0)
            ->update(['check_flag'=>2]);

        //check unmatched records in smart_payrecords
        DB::table('smart_payrecords')
            ->where("file_id", "=", $reqData->file_id)
            ->where("check_flag", "=", 0)
            ->update(['check_flag'=>2]);

        $check_results=array();
        $query = DB::table('smart_payrecords')
            ->select(DB::raw('COUNT(check_flag) as correct_num'))
            ->where('file_id', '=', $reqData->file_id)
            ->where('check_flag', '=', 1)
            ->first();
        $check_results['correct_num'] = $query->correct_num;
        $query = DB::table('smart_payrecords')
            ->select(DB::raw('COUNT(check_flag) as qtyerr_num'))
            ->where('file_id', '=', $reqData->file_id)
            ->where('check_flag', '=', 3)
            ->first();
        $check_results['qtyerr_num'] = $query->qtyerr_num;
        $query = DB::table('smart_payrecords')
            ->select(DB::raw('COUNT(check_flag) as payextra_num'))
            ->where('file_id', '=', $reqData->file_id)
            ->where('check_flag', '=', 2)
            ->first();
        $check_results['payextra_num'] = $query->payextra_num;
        $query = DB::table('smartorders')
            ->select(DB::raw('COUNT(check_flag) as orderextra_num'))
            ->whereBetween("file_date", [$start_date, $end_date])
            ->where("isDespatched", "=", TRUE)
            ->where('check_flag', '=', 2)
            ->first();
        $check_results['orderextra_num'] = $query->orderextra_num;
        return $check_results;
    }
}
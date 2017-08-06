<?php

namespace App\Http\Controllers;

use App\Models\SmartPayfile;
use App\Models\SmartPayrecord;
use Illuminate\Http\Request;
use App\Repositories\Smartchannel\SmartchannelRepositoryContract;
use Exception;
use Datatables;
use App\Models\SmartOrder;
use Illuminate\Pagination\Paginator;

class SmartChannelController extends Controller
{
    protected $smartChannel;
    public function __construct(
        SmartchannelRepositoryContract $smartChannel
    )
    {
        $this->smartChannel = $smartChannel;
    }

    /**
     * Display order management page
     */
    public function orderIndex(Request $request)
    {
        return view('smartchannel.orders');
    }
    /**
     * Enquiry order data
     */
    public function getOrders(Request $request)
    {
        $this->validate($request,[
            'limit'=>'required',
            'offset'=>'required',
        ]);
        $request->page = $request->offset / $request->limit + 1;
        $orders = $this->smartChannel->getOrders($request);
        $orderPage = $orders->forPage($request->page, $request->limit)->values();
        //var_dump($orderPage);
        return [
            'total' => $orders->count(),
            'rows' => $orderPage->toArray(),
        ];
    }
    public function importFile(Request $request)
    {

        $this->validate($request,[
            'filetype'=>'required',
            'uploadfile'=>'required',
        ]);
        try { switch ($request->filetype) {
                case "order":
                    $impNumber = $this->smartChannel->importOrderFile($request);
                    break;
                case "payment":
                    $impNumber = $this->smartChannel->importPaymentFile($request);
                    break;
                default:
                    return [
                        'result' => false,
                        'message' => "Wrong filetype!",
                    ];
            }
            return [
                'result' => true,
                'message' => "File processed successfully, ".$impNumber." records imported."
            ];
        } catch (Exception $e) {
            return [
                'result' => false,
                'message' => $e->getMessage(),
            ];
        }

    }
    public function getOrderDetails(Request $request)
    {
        $this->validate($request,[
            'id'=>'required',
        ]);
        try {
            $order = SmartOrder::findOrFail($request->id);
            return [
                'result' => true,
                'data' => $order->toArray(),
            ];
        } catch (Exception $e) {
            return [
                'result' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    public function updateOrderField(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'fieldname' => 'required',
            'fieldvalue' => 'required',
        ]);
        try {
            $fieldname = $request->fieldname;
            $order = SmartOrder::findOrFail($request->id);
            $order->$fieldname = $request->fieldvalue;
            $order->save();
            return [
                'result' => true,
                'data' => $order->toArray(),
            ];
        } catch (Exception $e) {
            return [
                'result' => false,
                'message' => $e->getMessage(),
            ];
        }

    }
    public function removeOrders(Request $request)
    {
        $this->validate($request, [
            'idlist' => 'required',
        ]);
        try {
            $ids = explode(',', $request->idlist);
            SmartOrder::whereIn('id', $ids)->delete();
            return [
                'result' => true,
                'message' => count($ids)." records deleted.",
            ];
        } catch (Exception $e) {
            return [
                'result' => false,
                'message' => $e->getMessage(),
            ];
        }

    }

    public function paymentIndex(Request $request)
    {
        return view('smartchannel.payments');
    }

    public function getPayfiles(Request $request)
    {
        $this->validate($request,[
            'limit'=>'required',
            'offset'=>'required',
        ]);
        $request->page = $request->offset / $request->limit + 1;
        $payfiles = $this->smartChannel->getPayfiles($request);
        $payfiles_1 = $payfiles->map(function($payfile) {
            $payfile->paylist = $payfile->id;
            return $payfile;
        });
        $payfilePage = $payfiles_1->forPage($request->page, $request->limit)->values();
        //var_dump($orderPage);
        return [
            'total' => $payfiles_1->count(),
            'rows' => $payfilePage->toArray(),
        ];

    }
    public function updatePayfileField(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            'fieldname' => 'required',
            'fieldvalue' => 'required',
        ]);
        try {
            $fieldname = $request->fieldname;
            $payfile = SmartPayfile::findOrFail($request->id);
            $payfile->$fieldname = $request->fieldvalue;
            $payfile->save();
            return [
                'result' => true,
                'data' => $payfile->toArray(),
            ];
        } catch (Exception $e) {
            return [
                'result' => false,
                'message' => $e->getMessage(),
            ];
        }

    }
    public function getPaylist(Request $request)
    {
        $this->validate($request, [
            'file_id' => 'required',
        ]);
        try {
            $payfile = SmartPayfile::findOrFail($request->file_id);
            $paylist = $payfile->records();
            return Datatables::of($paylist)
                ->make(true);
        } catch (Exception $e) {
            return [
                'result' => false,
                'message' => $e->getMessage(),
            ];
        }

    }
    public function removePayfiles(Request $request)
    {
        $this->validate($request, [
            'idlist' => 'required',
        ]);
        try {
            $ids = explode(',', $request->idlist);
            SmartPayrecord::whereIn('file_id', $ids)->delete();
            SmartPayfile::whereIn('id', $ids)->delete();
            return [
                'result' => true,
                'message' => count($ids)." records deleted.",
            ];
        } catch (Exception $e) {
            return [
                'result' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    public function checkPayrecords(Request $request)
    {
        $this->validate($request,[
            'file_id'=>'required',
        ]);
        try {
            $checkResult = $this->smartChannel->checkPayrecords($request);
            return [
                'result' => true,
                'data' => $checkResult,
            ];
        } catch (Exception $e) {
            return [
                'result' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

}

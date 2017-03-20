<?php
/**
 * Created by PhpStorm.
 * User: think
 * Date: 13/03/2017
 * Time: 11:13 PM
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use \DTS\eBaySDK\Constants;
use \DTS\eBaySDK\Trading\Services;
use \DTS\eBaySDK\Trading\Types;
use \DTS\eBaySDK\Trading\Enums;

use App\Models\Item;
use Datatables;
use Carbon;
use \Illuminate\Database\Eloquent\Collection;

class EbayController extends Controller
{
    private $ebayService;
    public function __construct()
    {
        $this->ebayService = new Services\TradingService([
            'authToken' => 'AgAAAA**AQAAAA**aAAAAA**7uXHWA**nY+sHZ2PrBmdj6wVnY+sEZ2PrA2dj6AGk4KpC5mHoQ2dj6x9nY+seQ**IqcDAA**AAMAAA**aMnihHYReIkdxMb2x8yK9Z7Dr+To19oQUN40N/ODdZIRYT7uavObOtxSQbq7oFsLzarqvIYDVt3mSF+1P2u7CSygRZlJGGpQamh/5t6dfWTD1B2yQx+WCJC0xUIgwLdg5f0+RWneguFfLwfPJ+HyPkFFCCl2rK6azw1YcoCFGoHI4fAEdAAHl1gsHnfd0Ge5Vt4FZo53uUEUOaIfwv7e0ruaLJ9CZI/xnMfCLopF3mboUscPdUKTFxvKzrU+uama/EgVVppDs8B+KH/2kv79xPJmJ5sLsmtyo0T+lGiZlyI7EVzYBdjoOzcHioyezd2/fdjSfAIKwHMA2DFCBkHQ2aoOoMRtJStrBR9I9rkZrQbiOJF3bFDKMz4BbV2KSJAI5P+ZMvCL2h583+9QoZxolFOdA0GTSy05JZw8xTqQx1aCSDz3AJn60W9oPtG8Dapsh3o5bXIXptAed8anLELZQE56gxwVm8LDcHJ9mHRuGHNYuCaSjSFQqMf4c9ac3Yl2hLRPKZnTjQbyI0KAjofL61Gj5/EWgiWJRmcTpQXcfKbWRXsWPjEWK0XKZPVndSHJo34ZJOGCBGbwqhJ3PPPLw3WH+MGSu6XLOC2bzMtnrEj5Ce5seUUrinspHG0ba0w+iaT87RimrRIl4aiYC6k397MTNkqjEhAhRTzUuvJA77ewMIOirmCV7f5BiIW/+m6tNvJ1qrvq7uPgNTkbPoEjW7LPRdFLudHfxJPwbYgKk1fQOzvA6ayVP3UhW2O6VFLe',
            'credentials' => [
                'appId' => 'UggExpre-uggexpre-PRD-52461718a-b5e923ed',
                'certId' => 'PRD-2461718aa091-6a50-42eb-90cd-a49b',
                'devId' => 'b8fbce70-d5b0-4c45-b0d0-04aa95ec3825'
            ],
            'siteId' => Constants\SiteIds::GB
        ]);
    }

    public function index(Request $request)
    {
        return view('ebay.index');
    }
    public function checkSKU(Request $request)
    {
        $ebayItemID = $request->itemID;
        if (!empty($ebayItemID)) {
            return(Datatables::of($this->getEbayItemData($ebayItemID))->make(true));
        } else {
            return(Datatables::of($this->getEbayActiveList())->make(true));
        }
    }
    private function getEbayItemData($itemID)
    {
        $ebayRequest = new Types\GetItemRequestType();
        $ebayRequest->ItemID = $itemID;
        $ebayResponse = $this->ebayService->getItem($ebayRequest);
        if (isset($ebayResponse->Errors)) {
            $respErrors=null;
            foreach ($ebayResponse->Errors as $error) {

                sprintf($respErrors, "%s, %s: %s %s<br>",
                        $respErrors,
                        $error->SeverityCode === Enums\SeverityCodeType::C_ERROR ? "Error" : "Warning",
                        $error->ShortMessage
                    );
                }
            return response()->json(['error',$respErrors], 500);
        }
        $ebayCollection = new Collection;

        if ($ebayResponse->Ack != 'Failure') {
            $item = $ebayResponse->Item;
            foreach ($item->Variations->Variation as $variation) {
                $ebaySKU = $variation->SKU;
                foreach ($variation->VariationSpecifics as $vSpec) {
                    foreach ($vSpec->NameValueList as $value) {
                        if ($value->Name == "Color" or $value->Name == "color" or $value->Name == "Colour")
                            $ebayColor = $value->Value[0];
                        else
                        if ($value->Name == "Size" or $value->Name == "size" or $value->Name == "SIZE")
                            $ebaySize = $value->Value[0];
                    }
                }
                $checkResult = '<span class="bg-success glyphicon glyphicon-ok">正确</span>';
                $erpItem = Item::where('sku_id', $ebaySKU)->first();
                if (is_null($erpItem)) {
                    $checkResult = '<span class="bg-danger glyphicon glyphicon-remove">SKU错误</span>';
                } elseif (stripos($item->SKU,$erpItem->product->product_id) === false) {
                    $checkResult = '<span class="bg-danger glyphicon glyphicon-remove">货号错误</span>';
                } elseif (stripos($ebayColor, $erpItem->color->ename) === false) {
                    $checkResult = '<span class="bg-danger glyphicon glyphicon-remove">颜色错误</span>';
                } elseif (stripos($ebaySize, $erpItem->size_value) === false ) {
                    $checkResult = '<span class="bg-danger glyphicon glyphicon-remove">尺码错误</span>';
                }

                $ebayCollection->push([
                    'ebay_itemid' => $item->ItemID,
                    'ebay_title' => $item->Title,
                    'ebay_color' => isset($ebayColor) ? $ebayColor : NULL,
                    'ebay_size' => isset($ebaySize) ? $ebaySize : NULL,
                    'ebay_sku' => $ebaySKU,
                    'product_id' => $item->SKU,
                    'color_ename' => isset($erpItem) ? NULL : $erpItem->color->ename,
                    'size_value' => isset($erpItem) ? NULL : $erpItem->size_value,
                    'check_result' => $checkResult
                ]);
            }

            return $ebayCollection;
        }
    }

    private function getEbayActiveList()
    {
        $ebayRequest = new Types\GetMyeBaySellingRequestType();
        $ebayRequest->ActiveList = new Types\ItemListCustomizationType();
        $ebayRequest->ActiveList->Include = true;
        $ebayRequest->ActiveList->Pagination = new Types\PaginationType();
        $ebayRequest->ActiveList->Pagination->EntriesPerPage = 100;
        $pageNum = 1;
        $ebayCollection = new Collection;

        do {

            $ebayResponse = $this->ebayService->getMyeBaySelling($ebayRequest);
            if (isset($ebayResponse->Errors)) {
                $respErrors;
                foreach ($ebayResponse->Errors as $error) {

                    sprintf($respErrors, "%s, %s: %s %s<br>",
                        $respErrors,
                        $error->SeverityCode === Enums\SeverityCodeType::C_ERROR ? "Error" : "Warning",
                        $error->ShortMessage
                    );
                }
                return response()->json(['error',$respErrors], 500);
            }


            if ($ebayResponse->Ack !== 'Failure' && isset($ebayResponse->ActiveList)) {
                foreach ($ebayResponse->ActiveList->ItemArray->Item as $item) {
                    foreach ($item->Variations->Variation as $variation) {
                        $ebaySKU = $variation->SKU;
                        foreach ($variation->VariationSpecifics as $vSpec) {
                            foreach ($vSpec->NameValueList as $value) {
                                if ($value->Name == "Color" or $value->Name == "color" or $value->Name == "Colour")
                                    $ebayColor = $value->Value[0];
                                else
                                    if ($value->Name == "Size" or $value->Name == "size" or $value->Name == "SIZE")
                                        $ebaySize = $value->Value[0];
                            }
                        }
                        $checkResult = '<span class="bg-success glyphicon glyphicon-ok">正确</span>';
                        $erpItem = Item::where('sku_id', $ebaySKU)->first();
                        if (is_null($erpItem)) {
                            $checkResult = '<span class="bg-danger glyphicon glyphicon-remove">SKU错误</span>';
                        } elseif (stripos($item->SKU,$erpItem->product->product_id) === false) {
                            $checkResult = '<span class="bg-danger glyphicon glyphicon-remove">货号错误</span>';
                        } elseif (stripos($ebayColor, $erpItem->color->ename) === false) {
                            $checkResult = '<span class="bg-danger glyphicon glyphicon-remove">颜色错误</span>';
                        } elseif (stripos($ebaySize, $erpItem->size_value) === false ) {
                            $checkResult = '<span class="bg-danger glyphicon glyphicon-remove">尺码错误</span>';
                        }

                        $ebayCollection->push([
                            'ebay_itemid' => $item->ItemID,
                            'ebay_title' => $item->Title,
                            'ebay_color' => isset($ebayColor) ? $ebayColor : NULL,
                            'ebay_size' => isset($ebaySize) ? $ebaySize : NULL,
                            'ebay_sku' => $ebaySKU,
                            'product_id' => $item->SKU,
                            'color_ename' => is_null($erpItem) ? NULL : $erpItem->color->ename,
                            'size_value' => is_null($erpItem) ? NULL : $erpItem->size_value,
                            'check_result' => $checkResult
                        ]);
                    }
                }
            }
            $pageNum ++;
        } while(isset($ebayResponse->ActiveList) && $pageNum <= $ebayResponse->ActiveList->PaginationResult->TotalNumberOfPages);
        return $ebayCollection;
    }
}
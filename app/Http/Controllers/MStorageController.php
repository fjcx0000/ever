<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Storage\MStorageRepositoryContract;

class MStorageController extends Controller
{
    protected $mstorage;

    public function __construct(
        MStorageRepositoryContract $mstorage
    )
    {
        $this->mstorage = $mstorage;
    }
    public function index(Request $request)
    {
        return view('mobile.mstorage');
    }
    public function getFirstLocdata(Request $request)
    {
        return $this->mstorage->getfirstLocData();
    }
    public function getNextLocdata(Request $request)
    {
        $this->validate($request, [
            'area' => 'required',
            'line' => 'required',
            'unit' => 'required',
        ]);
        return $this->mstorage->getNextLocData($request);
    }
    public function getPrevLocdata(Request $request)
    {
        $this->validate($request, [
            'area' => 'required',
            'line' => 'required',
            'unit' => 'required',
        ]);
        return $this->mstorage->getPrevLocData($request);
    }
    public function getLocdata(Request $request)
    {
        $this->validate($request, [
            'area' => 'required',
            'line' => 'required',
            'unit' => 'required',
        ]);
        return $this->mstorage->getLocData($request);
    }

    public function getArealist(Request $request)
    {
        return $this->mstorage->getArealist();
    }
    public function getLinelist(Request $request)
    {
        $this->validate($request, [
            'area' => 'required',
        ]);
        return $this->mstorage->getLinelist($request->area);
    }
    public function getUnitlist(Request $request)
    {
        $this->validate($request, [
            'area' => 'required',
            'line' => 'required',
        ]);
        return $this->mstorage->getUnitlist($request->area, $request->line);
    }
    public function deleteItem(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
        ]);
        return $this->mstorage->delLocitem($request->id);
    }
    public function addItem(Request $request)
    {
        $this->validate($request, [
            'area' => 'required',
            'line' => 'required',
            'unit' => 'required',
            'level' => 'required',
            'goodsno' => 'required',
            'colorcode' => 'required',
        ]);
        $result = $this->mstorage->addStorageItem($request);
        switch ($result) {
            case '-1':
                $resp = "location not found";
                break;
            case '-2':
                $resp = "goodsno not found";
                break;
            case '-3':
                $resp = "colorcode not found";
                break;
            case 1:
                $resp = "增加成功";
                break;
            default:
                $resp = "Unknown Error";
                break;
        }
        return $resp;
    }
    public function erpOptionIndex(Request $request)
    {
        return view('mobile.erpoptions');
    }
    public function erpLoadLocs(Request $request)
    {
        return $this->mstorage->loadLocationsFromErp();
    }
    public function erpUpdateItems(Request $request)
    {
        return $this->mstorage->updateStorageItemsToErp();
    }
    public function erpCheckItems(Request $request)
    {
        return $this->mstorage->checkErpStorageItems();
    }
    public function getItemLocations(Request $request)
    {
        $this->validate($request, [
            'goodsno' => 'required',
        ]);
        $locs = $this->mstorage->getItemLocations($request);
        if (empty($locs))
            return null;
        else
            return $locs->toArray();
    }
}

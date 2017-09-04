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
}

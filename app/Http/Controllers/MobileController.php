<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class MobileController extends Controller
{
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
        return [
            '11622-Summer Moccasins',
            '11613-Moccasins',
        ];
    }
    public function getStock(Request $request)
    {
        return [
            "prd_no" => "11613",
            "prd_name" => "moccasin",
            "stocks" => [
                ["color" => "black", "size" =>"35", "stock"=>10, "avail" => 5],
                ["color" => "black", "size" =>"36", "stock"=>8, "avail" => 4],
            ],
        ];
    }
}

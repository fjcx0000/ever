<?php
namespace App\Http\Helpers\Excel;

use Input;
/**
 * Created by PhpStorm.
 * User: think
 * Date: 20/02/2017
 * Time: 11:27 PM
 */
class ProductListImport extends \Maatwebsite\Excel\Files\ExcelFile
{
    public function getFile()
    {
        $file = Input::file('productlist');
        $filename = $this->doSomethingLikeUpload($file);
        return $filename;
    }
    public function getFilters()
    {
        return [
            'chunk'
        ];
    }
}
<?php
namespace App\Http\Controllers;

use DB;
use App\Repositories\User\UserRepositoryContract;

class PagesController extends Controller
{

    protected $users;

    public function __construct(
        UserRepositoryContract $users
    ) {
        $this->users = $users;
    }

    public function dashboard()
    {

      /**
         * Other Statistics
         *
         */
        $companyname = "Ever Australia";
        $users = $this->users->getAllUsers();



       
        return view('pages.dashboard', compact(
            'users',
            'companyname'
        ));
    }
}

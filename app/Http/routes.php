<?php

use App\Task;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
//Route::post('login', 'Auth\AuthController@login');
//Route::get('login', 'Auth\AuthController@showLoginForm');
//Route::get('/', 'Auth\AuthController@showLoginForm');
//Route::group(['middleware' => 'auth'], function() {
  //  Route::get('/', function () {
  //      return redirect('/tasks');
  //  });
    Route::get('/', 'TaskController@index');
    Route::get('/tasks', 'TaskController@index');
    Route::post('/task', 'TaskController@store');
    Route::delete('/task/{task}', 'TaskController@destroy');
//});

/**
 * Add a new task
 */

/*
Route::post('/task', function (Request $request) {
        $validator = Validator::make($request->all(), [
                'name' => 'required|max:255',
        ]);
        if ($validator->fails()) {
                return redirect('/')
                        ->withInput()
                        ->withErrors($validator);
        }

        // Create the task...
        $task = new Task;
        $task->name = $request->name;
        $task->save();
        return redirect('/');
});
 */

/**
 *  Delete an existing task
 */
/*
Route::delete('/task/{id}', function ($id) {
        Task::findOrFail($id)->delete();
        return redirect('/');
});
 */

Route::get('mytest', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});

Route::group(['middleware' => 'web'], function () {
    Route::auth();

    Route::get('/home', 'HomeController@index');
});

<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/
Route::auth();
Route::get('/logout', 'Auth\LoginController@logout');
Route::group(['middleware' => ['auth']], function () {
 	/**
     * MAIN
     */
        Route::get('/', 'PagesController@dashboard');
        Route::get('dashboard', 'PagesController@dashboard')->name('dashboard');
    /**
     * USERS
     */
        Route::get('users/data', 'UsersController@anyData')->name('users.data');
        Route::get('users/taskdata/{id}', 'UsersController@taskData')->name('users.taskdata');
        Route::get('users/closedtaskdata/{id}', 'UsersController@closedTaskData')->name('users.closedtaskdata');
        Route::get('users/clientdata/{id}', 'UsersController@clientData')->name('users.clientdata');
        Route::resource('users', 'UsersController');
        /* ROLES */
        Route::resource('roles', 'RolesController');
    
    /**
     * DEPARTMENTS
     */
        Route::resource('departments', 'DepartmentsController');
        
              
    /**
     * NOTIFICATIONS
     */
        Route::get('notifications/getall', 'NotificationsController@getAll')->name('notifications.get');
        Route::post('notifications/markread', 'NotificationsController@markRead');
        Route::get('notifications/markall', 'NotificationsController@markAll');
        Route::get('notification/{id}', 'NotificationsController@markRead');

    /**
     * IMPORT AND EXPORT
     */
        Route::get('documents/import', 'DocumentsController@import');

    });
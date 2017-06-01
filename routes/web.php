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
     * Main
     */
        Route::get('/', 'PagesController@dashboard');
        Route::get('dashboard', 'PagesController@dashboard')->name('dashboard');

    /**
     * Products
     */
    Route::group(['prefix' => 'products'], function () {
        Route::get('/enquiry', 'ProductsController@enquiryData')->name('products.enquiry');
        Route::get('/data', 'ProductsController@itemData')->name('products.data');
        Route::get('/productdetails', 'ProductsController@getProductDetails')->name('products.productdetails');
        Route::post('/product_exists', 'ProductsController@productExists');
        Route::get('/fileselect','ProductsController@fileselect')->name('products.fileselect');
        Route::post('/fileupload','ProductsController@fileupload')->name('products.fileupload');
        Route::post('/selectproducts', 'ProductsController@selectProducts');
        Route::get('/show_nosku', 'productsController@showNoskuList')->name('products.shownosku');
        Route::post('/get_nosku', 'productsController@getNoskuList')->name('products.getnosku');
        Route::post('/allocatesku', 'productsController@allocateSku')->name('products.allocatesku');
        Route::post('/exportskufile', 'productsController@exportSkuFile')->name('products.exportskufile');
    });
    Route::resource('products', 'ProductsController');

    /**
     * Storage
     */
    Route::group(['prefix' => 'storages'], function() {
        Route::get('/locindex', 'StorageController@locIndex')->name('storages.locindex');
        Route::post('/addlocation', 'StorageController@addLocation')->name('storages.addlocation');
        Route::get('/getlocations', 'StorageController@getLocations')->name('storages.getlocations');
        Route::post('/dellocation', 'StorageController@delLocation')->name('storages.dellocation');
        Route::post('/uploadlocfile', 'StorageController@uploadLocationFile')->name('storages.uploadlocfile');

        Route::get('/itemindex', 'StorageController@itemIndex')->name('storages.itemindex');
        Route::post('/additem', 'StorageController@addItem')->name('storages.additem');
        Route::get('/getitems', 'StorageController@getItems')->name('storages.getitems');
        Route::post('/delitem', 'StorageController@delItem')->name('storages.delitem');
        Route::get('/importproductdata', 'StorageController@importProductData')->name('storages.importproductdata');


        Route::get('/locitemindex', 'StorageController@locitemIndex')->name('storages.locitemindex');
        Route::get('/searchlocations', 'StorageController@searchLocations')->name('storages.searchlocations');
        Route::get('/searchitems', 'StorageController@searchItems')->name('storages.searchitems');
        Route::post('/addrelation', 'StorageController@addRelation')->name('storages.addrelation');
        Route::get('/getrelations', 'StorageController@getRelations')->name('storages.getrelations');
        Route::post('/delrelation', 'StorageController@delRelation')->name('storages.delrelation');
    });

    /**
     * Ebay Operations
     */
    Route::group(['prefix' => 'ebay'],function() {
        Route::get('/','EbayController@index')->name('ebay.index');
        Route::get('/checksku','EbayController@checkSKU')->name('ebay.checksku');
    });

    /**
     * Users
     */
    Route::group(['prefix' => 'users'], function () {
        Route::get('/data', 'UsersController@anyData')->name('users.data');
        Route::get('/taskdata/{id}', 'UsersController@taskData')->name('users.taskdata');
        Route::get('/leaddata/{id}', 'UsersController@leadData')->name('users.leaddata');
        Route::get('/clientdata/{id}', 'UsersController@clientData')->name('users.clientdata');
    });
        Route::resource('users', 'UsersController');

    /**
     * Roles
     */
        Route::resource('roles', 'RolesController');

    /**
     * Clients
     */
    Route::group(['prefix' => 'clients'], function () {
        Route::get('/data', 'ClientsController@anyData')->name('clients.data');
        Route::post('/create/cvrapi', 'ClientsController@cvrapiStart');
        Route::post('/upload/{id}', 'DocumentsController@upload');
    });
        Route::resource('clients', 'ClientsController');

    /**
     * Tasks
     */
    Route::group(['prefix' => 'tasks'], function () {
        Route::get('/data', 'TasksController@anyData')->name('tasks.data');
        Route::patch('/updatestatus/{id}', 'TasksController@updateStatus');
        Route::patch('/updateassign/{id}', 'TasksController@updateAssign');
        Route::post('/updatetime/{id}', 'TasksController@updateTime');
        Route::post('/invoice/{id}', 'TasksController@invoice');
        Route::post('/comments/{id}', 'CommentController@store');
    });
        Route::resource('tasks', 'TasksController');

    /**
     * Leads
     */
    Route::group(['prefix' => 'leads'], function () {
        Route::get('/data', 'LeadsController@anyData')->name('leads.data');
        Route::patch('/updateassign/{id}', 'LeadsController@updateAssign');
        Route::post('/notes/{id}', 'NotesController@store');
        Route::patch('/updatestatus/{id}', 'LeadsController@updateStatus');
        Route::patch('/updatefollowup/{id}', 'LeadsController@updateFollowup')->name('leads.followup');
    });
        Route::resource('leads', 'LeadsController');

    /**
     * Settings
     */
    Route::group(['prefix' => 'settings'], function () {
        Route::get('/', 'SettingsController@index')->name('settings.index');
        Route::patch('/permissionsUpdate', 'SettingsController@permissionsUpdate');
        Route::patch('/overall', 'SettingsController@updateOverall');
    });

    /**
     * Departments
     */
        Route::resource('departments', 'DepartmentsController'); 

    /**
     * Integrations
     */
    Route::group(['prefix' => 'integrations'], function () {
        Route::get('Integration/slack', 'IntegrationsController@slack');
    });
        Route::resource('integrations', 'IntegrationsController');

    /**
     * Notifications
     */
    Route::group(['prefix' => 'notifications'], function () {
        Route::get('/getall', 'NotificationsController@getAll')->name('notifications.get');
        Route::post('/markread', 'NotificationsController@markRead');
        Route::get('/markall', 'NotificationsController@markAll');
        Route::get('/{id}', 'NotificationsController@markRead');
    });

    /**
     * Invoices
     */
    Route::group(['prefix' => 'invoices'], function () {
        Route::post('/updatepayment/{id}', 'InvoicesController@updatePayment')->name('invoice.payment.date');
        Route::post('/reopenpayment/{id}', 'InvoicesController@reopenPayment')->name('invoice.payment.reopen');
        Route::post('/sentinvoice/{id}', 'InvoicesController@updateSentStatus')->name('invoice.sent');
        Route::post('/reopensentinvoice/{id}', 'InvoicesController@updateSentReopen')->name('invoice.sent.reopen');
        Route::post('/newitem/{id}', 'InvoicesController@newItem')->name('invoice.new.item');
    });
        Route::resource('invoices', 'InvoicesController');
});

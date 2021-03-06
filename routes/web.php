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
     * Smart Channel Service
     */
    Route::group(['prefix' => 'smartchannel'],function(){
        Route::get('/orderindex', 'SmartChannelController@orderIndex')->name('smartchannel.orderindex');
        Route::get('/getorders', 'SmartChannelController@getOrders')->name('smartchannel.getorders');
        Route::get('/getorderdetails', 'SmartChannelController@getOrderDetails')->name('smartchannel.getorderdetails');
        Route::post('/importfile', 'SmartChannelController@importFile')->name('smartchannel.importfile');
        Route::post('/updateorderfield', 'SmartChannelController@updateOrderField')->name('smartchannel.updateorderfield');
        Route::post('/removeorders', 'SmartChannelController@removeOrders')->name('smartchannel.removeorders');
        Route::get('/paymentindex', 'SmartChannelController@paymentIndex')->name('smartchannel.paymentindex');
        Route::get('/getpayfiles', 'SmartChannelController@getPayfiles')->name('smartchannel.getpayfiles');
        Route::post('/updatepayfilefield', 'SmartChannelController@updatePayfileField')->name('smartchannel.updatepayfilefield');
        Route::get('/getpaylist', 'SmartChannelController@getPaylist')->name('smartchannel.getpaylist');
        Route::post('/removepayfiles', 'SmartChannelController@removePayfiles')->name('smartchannel.removepayfiles');
        Route::post('/checkpayrecords', 'SmartChannelController@checkPayrecords')->name('smartchannel.checkpayrecords');
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

/**
 * Mobile
 */
Route::group(['prefix' => 'mobile'],function() {
    Route::get('/','MobileController@index')->name('mobile.index');
    Route::get('/getproducts','MobileController@getProducts')->name('mobile.getproducts');
    Route::get('/getstock','MobileController@getStock')->name('mobile.getstock');
    Route::get('/getcolors','MobileController@getColors')->name('mobile.getcolors');
    Route::get('/getsizes','MobileController@getSizes')->name('mobile.getsizes');
    Route::get('/study','MobileController@study')->name('mobile.study');

    Route::get('/uploadfile', 'MobileController@uploadStorageFile')->name('mobile.uploadfile');
    Route::post('/convertfile', 'MobileController@convertErpStorageFile')->name('mobile.convertfile');
});
/**
 * Mobile Storage
 */
Route::group(['prefix' => 'mstorage'], function() {
    Route::get('/', 'MStorageController@index')->name('mstorage.index');
    Route::get('/getfirstloc', 'MStorageController@getFirstLocdata')->name('mstorage.firstloc');
    Route::get('/getnextloc', 'MStorageController@getNextLocdata')->name('mstorage.nextloc');
    Route::get('/getprevloc', 'MStorageController@getPrevLocdata')->name('mstorage.prevloc');
    Route::get('/getarealist', 'MStorageController@getArealist')->name('mstorage.arealist');
    Route::get('/getlinelist', 'MStorageController@getLinelist')->name('mstorage.linelist');
    Route::get('/getunitlist', 'MStorageController@getUnitlist')->name('mstorage.unitlist');
    Route::get('/getlocdata', 'MStorageController@getLocdata')->name('mstorage.locdata');
    Route::get('/getitemlocs', 'MStorageController@getItemLocations')->name('mstorage.itemlocs');
    Route::post('/deleteitem', 'MStorageController@deleteItem')->name('mstorage.deleteitem');
    Route::post('/additem', 'MStorageController@addItem')->name('mstorage.additem');


    Route::get('/erpoptions', 'MStorageController@erpOptionIndex')->name('mstorage.erpoptions');
    Route::post('/erploadlocs', 'MStorageController@erpLoadLocs')->name('mstorage.erploadlocs');
    Route::post('/erpupdateitems', 'MStorageController@erpUpdateItems')->name('mstorage.erpupdateitems');
    Route::get('/erpcheckitems', 'MStorageController@erpCheckItems')->name('mstorage.erpcheckitems');

});

/**
 * Excel Process
 */
Route::group(['prefix' => 'excel'], function() {
    Route::get('/', 'ExcelController@index')->name('excel.index');
    Route::post('/processfile', 'ExcelController@processFile')->name('excel.processfile');
    Route::get('/inventoryindex', 'ExcelController@inventoryIndex')->name('excel.inventoryindex');
    Route::post('/getinventory', 'ExcelController@getInventoryExcel')->name('excel.getinventory');
});

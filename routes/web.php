<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

######## User Routes ########
Route::get('/home', 'UserController@index')->name('users.dashboard');
Route::get('/user-store', 'UserController@store')->name('users.store');

######## Auth Routes ########
Auth::routes();


######## Merchant Routes ########
Route::group(['middleware' => ['role:merchant'], 'prefix' => 'merchant'], function () {
    Route::get('/dashboard', 'MerchantController@dashboard')->name('merchant.dashboard');
    Route::get('/customer', 'MerchantController@customer')->name('merchant.customer.index');

    Route::get('/product', 'MerchantController@product')->name('merchant.product.index');
    Route::post('/save-product', 'MerchantController@saveProduct')->name('merchant.product.save');
    Route::get('/get-product', 'MerchantController@getProduct')->name('merchant.product.get');
    Route::post('/update-product', 'MerchantController@updateProduct')->name('merchant.product.update');
    Route::post('/delete-product', 'MerchantController@deleteProduct')->name('merchant.product.delete');

    Route::get('/store', 'MerchantController@store')->name('merchant.store.index');
    Route::post('/save-store', 'MerchantController@saveStore')->name('merchant.store.save');    
    Route::get('/settings', 'MerchantController@settings')->name('settings.index');

    Route::get('/customer-data', 'MerchantController@customerData')->name('customer-data.index');
    Route::get('/edit-profile/{id}', 'MerchantController@editProfile')->name('edit-profile');
    Route::get('/view-profile/{id}', 'MerchantController@viewProfile')->name('view-profile');
});

Route::group(['middleware' => ['role:admin'], 'prefix' => 'admin'], function () {
    Route::get('/dashboard', 'AdminController@index')->name('admin.dashboard');
    Route::get('/create-merchants', 'AdminController@createMerchant')->name('admin.create_merchants');
    Route::post('/store-merchants', 'AdminController@storeMerchant')->name('admin.store_merchants');
});









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
use Intervention\Image\Facades\Image;

Route::get('/', function () {
    return redirect('login');
});

Route::auth();

Route::group(['middleware' => 'auth'], function () {
    

    Route::get('/', function () {
        $total_products = DB::table('products')->count();
        $total_ads = DB::table('ads')->count();
        $total_cars = DB::table('cars')->count();

        return view('dashboard', compact('total_products','total_ads', 'total_cars'));
    });

    Route::get('products', ['as' => 'products', 'uses' => 'ProductController@manage']);
    Route::get('/get-ajax-products', ['as' => 'get-ajax-products', 'uses' => 'ProductController@getAjaxProducts']);
    Route::get('add-product', ['as' => 'add-product', 'uses' => 'ProductController@add']);
    Route::post('post-add-product', 'ProductController@postAdd');
    Route::get('edit-product/{id}', ['as' => 'edit-product', 'uses' => 'ProductController@edit']);
    Route::post('post-edit-product', 'ProductController@postEdit');
    Route::get('delete-product/{id}', ['as' => 'delete-product', 'uses' => 'ProductController@delete']);

    Route::get('ads', ['as' => 'ads', 'uses' => 'AdsController@manage']);
    Route::get('/get-ajax-ads', ['as' => 'get-ajax-ads', 'uses' => 'AdsController@getAjaxAds']);
    Route::get('add-ad', ['as' => 'add-ad', 'uses' => 'AdsController@add']);
    Route::post('post-add-ad', 'AdsController@postAdd');
    Route::get('edit-ad/{id}', ['as' => 'edit-ad', 'uses' => 'AdsController@edit']);
    Route::post('post-edit-ad', 'AdsController@postEdit');
    Route::get('delete-ad/{id}', ['as' => 'delete-ad', 'uses' => 'AdsController@delete']);

    Route::get('cars', ['as' => 'cars', 'uses' => 'CarController@manage']);
    Route::get('/get-ajax-cars', ['as' => 'get-ajax-cars', 'uses' => 'CarController@getAjaxCars']);
    Route::get('add-car', ['as' => 'add-car', 'uses' => 'CarController@add']);
    Route::post('post-add-car', 'CarController@postAdd');
    Route::get('edit-car/{id}', ['as' => 'edit-car', 'uses' => 'CarController@edit']);
    Route::post('post-edit-car', 'CarController@postEdit');
    Route::get('delete-car/{id}', ['as' => 'delete-car', 'uses' => 'CarController@delete']);
    
});

Route::get('images/{filename}', function ($filename) {
    return Image::make(storage_path() . '/' . $filename)->response();
});


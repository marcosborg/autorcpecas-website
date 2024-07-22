<?php

Route::post('login', 'Api\\AuthController@login');

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('send-photo', 'Api\\AuthController@sendPhoto');
    Route::get('get-categories/{category_id}', 'Api\\AuthController@getCategories');
    Route::get('get-category/{category_id}', 'Api\\AuthController@getCategory');
    Route::get('get-manufacturers', 'Api\\AuthController@getManufacturers');
    Route::post('create-product', 'Api\\AuthController@createProduct');
    Route::get('get-manufacturer/{manufacturer_id}', 'Api\\AuthController@getManufacturer');

});

<?php

Route::post('login', 'Api\\AuthController@login');

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::post('send-photo', 'Api\\AuthController@sendPhoto');
    Route::get('get-categories', 'Api\\AuthController@getCategories');
    Route::get('get-manufacturers', 'Api\\AuthController@getManufacturers');

});

<?php

Route::post('login', 'Api\\AuthController@login');

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::get('get-categories/{category_id}', 'Api\\AuthController@getCategories');
    Route::get('get-category/{category_id}', 'Api\\AuthController@getCategory');
    Route::get('get-manufacturers', 'Api\\AuthController@getManufacturers');
    Route::post('create-product', 'Api\\AuthController@createProduct');
    Route::get('get-manufacturer/{manufacturer_id}', 'Api\\AuthController@getManufacturer');

});

Route::post('send-photo', 'Api\\AuthController@sendPhoto');
Route::post('upload-image', 'Api\\AuthController@uploadImage');

Route::prefix('chat')->group(function(){
    Route::post('start-conversation', 'ChatController@startConversation');
    Route::post('send-message', 'ChatController@sendMessage');
});
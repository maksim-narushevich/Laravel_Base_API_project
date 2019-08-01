<?php


Route::prefix('v1')->group(function(){
    Route::post('login', 'Api\AuthController@login');
    Route::post('register', 'Api\AuthController@register');
    Route::post('confirm', 'Api\AuthController@confirm');


    Route::group(['middleware' => 'auth:api'], function(){
        Route::get('auth-user', 'Api\UserController@getUser');
        Route::get('users', 'Api\UserController@getUserList');
        Route::get('users/{user}', 'Api\UserController@getUserByID')->name('user');
        Route::put('users/{user}', 'Api\UserController@updateUserByID');
        Route::delete('auth-user/delete', 'Api\UserController@deleteAuthUser');
        Route::delete('users/{user}', 'Api\UserController@deleteUser');


        Route::post('services/sms', 'Api\ServicesController@sendSMS');
        Route::post('services/image/upload', 'Api\ServicesController@imageUpload');
        Route::post('services/image/upload-with-resizing', 'Api\ServicesController@imageUploadWithResizing');
        Route::get('services/image/get', 'Api\ServicesController@getImage');
        Route::post('services/logging', 'Api\ServicesController@sendLog');

        Route::apiResource('/products',"Api\ProductController");
        Route::group(['prefix'=>'products'],function (){
            Route::apiResource('{product}/reviews',"Api\ReviewController");
        });
    });


});

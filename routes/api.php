<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function(){
    Route::post('login', 'Api\AuthController@login');
    Route::post('register', 'Api\AuthController@register');
    Route::post('confirm', 'Api\AuthController@confirm');

    Route::group(['middleware' => 'auth:api'], function(){
        Route::get('auth-user', 'Api\UserController@getUser');
        Route::get('users', 'Api\UserController@getUserList');
        Route::delete('auth-user/delete', 'Api\UserController@deleteAuthUser');
        Route::delete('user/delete/{user}', 'Api\UserController@deleteUser');


        Route::post('services/sms', 'Api\ServicesController@sendSMS');
        Route::post('services/image/upload', 'Api\ServicesController@imageUpload');
    });

    Route::apiResource('/products',"Api\ProductController");
    Route::group(['prefix'=>'products'],function (){
        Route::apiResource('{product}/reviews',"Api\ReviewController");
    });
});

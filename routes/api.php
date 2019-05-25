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

    Route::group(['middleware' => 'auth:api'], function(){
        Route::get('auth-user', 'Api\AuthController@getUser');
    });

    Route::apiResource('/products',"Api\ProductController");
    Route::group(['prefix'=>'products'],function (){
        Route::apiResource('{product}/reviews',"Api\ReviewController");
    });
});

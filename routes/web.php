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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//AWS services routes
Route::get('/sendSMS/{phone_number}', 'AWSController@sendSMS');
Route::get('/aws/s3', 'AWSController@bucket');


//Mail services routes
Route::get('/email/send', 'MailController@send');

//RabbitMQ
Route::get('/queue/dispatch', 'RabbitMQController@dispatchJob');
Route::post('/test-service', 'RabbitMQController@testService')->name('test-microservice');




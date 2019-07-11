<?php

namespace App\Providers;

use App\Services\Logging\LoggerService;
use App\Services\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //PAGINATOR SERVICE INITIALIZATION
        $this->app->bind(Paginator::class, function ($app) {
            return new Paginator($app->config['paginator.max_per_page'],$app->config['paginator.use_paginator']);
        });


        //LOGGER MICROSERVICE ADAPTER INITIALIZATION
        $this->app->bind(LoggerService::class, function ($app) {

            $arrLogger=[
                "user"=>$app->config['logger.user']??"",
                "password"=>$app->config['logger.password']??"",
                "url"=>$app->config['logger.url']??"",
                "type"=>$app->config['logger.type']??"",
            ];
            return new LoggerService($arrLogger);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

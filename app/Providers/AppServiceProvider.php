<?php

namespace App\Providers;

use App\Services\Logging\LoggerService;
use App\Services\Pagination\Paginator;
use App\Services\SMS\SMSService;
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

            $arrLogger=$this->getAMQPConnection($app);
            $arrLogger["type"]=$app->config['logging.type']??"";
            return new LoggerService($arrLogger);
        });


        //SMS MICROSERVICE ADAPTER INITIALIZATION
        $this->app->bind(SMSService::class, function ($app) {

            $arrSmsCredentials=$this->getAMQPConnection($app);
            $arrSmsCredentials["provider"]=$app->config['sms.provider']??"";
            return new SMSService($arrSmsCredentials);
        });

        if ($this->app->isLocal()) {
            $this->app->register(TelescopeServiceProvider::class);
        }
    }



    private function getAMQPConnection($app):array {
        return [
            "host"=>$app->config['queue.connections.rabbitmq.host']??"",
            "port"=>$app->config['queue.connections.rabbitmq.port']??"",
            "login"=>$app->config['queue.connections.rabbitmq.login']??"",
            "password"=>$app->config['queue.connections.rabbitmq.password']??"",
        ];
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

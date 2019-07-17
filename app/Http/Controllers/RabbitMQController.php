<?php

namespace App\Http\Controllers;


use App\Jobs\TestJob;
use App\Models\Product;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


class RabbitMQController extends Controller
{


    protected function dispatchJob()
    {
        $product=Product::findOrFail(10);
        //dispatch test job
        TestJob::dispatch($product);
    }


    protected function testService(Request $request)
    {
        throw new Exception('My first Sentry error!');
        dd("Run some test here");
    }

}

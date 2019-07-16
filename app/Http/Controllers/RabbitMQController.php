<?php

namespace App\Http\Controllers;


use App\Jobs\TestJob;
use App\Models\Product;
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
        $connection = new AMQPStreamConnection('rabbitmq_api', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $exchangeName='api_services';
        $channel->exchange_declare($exchangeName, 'topic', false, true, false);

        $routing_key =  'service.logging.log';

        //Data to be sent
        $data = [];
        $log_time=date("Y-m-d h:i:s");
        $service="base_api";
        if (empty($data)) {
            $data['service'] = $service;
            $data['log_data'] = [
                "service_name"=>$service,
                "code"=>501,
                "message"=>"Internal server error",
            ];
            $data['log_time'] = $log_time;
        }

        $msg = new AMQPMessage(json_encode($data));

        $channel->basic_publish($msg, $exchangeName, $routing_key);


        $channel->close();
        $connection->close();

        dd(' [x] Sent routing key:', $routing_key, ':', $data, "\n");
    }

}

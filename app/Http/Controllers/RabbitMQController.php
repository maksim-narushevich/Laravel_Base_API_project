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

        $exchangeName='user_updates';
        $channel->exchange_declare($exchangeName, 'topic', false, true, false);

        $routing_key =  'user.profile.update';

        //Data to be sent
        $data = [];
        if (empty($data)) {
            $data['id'] = 5;
            $data['full_name'] = "maksim";
            $data['service'] = ['main_service'=>"PHP Service"];
        }

        $msg = new AMQPMessage(json_encode($data));

        $channel->basic_publish($msg, $exchangeName, $routing_key);


        $channel->close();
        $connection->close();

        dd(' [x] Sent routing key:', $routing_key, ':', $data, "\n");
    }

}

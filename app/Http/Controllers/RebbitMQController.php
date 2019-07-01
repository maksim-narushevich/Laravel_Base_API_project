<?php

namespace App\Http\Controllers;


use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


class RebbitMQController extends Controller
{


    protected function send()
    {
        $connection = new AMQPStreamConnection('rabbitmq_api', 5672, 'guest', 'guest');
        $channel = $connection->channel();
        $channel->queue_declare('hello', false, false, false, false);

        $msg = new AMQPMessage('Hello World!');
        $channel->basic_publish($msg, '', 'hello');

        echo " [x] Sent 'Hello World!'\n";
        $channel->close();
        $connection->close();
    }

    protected function receive()
    {
        $connection = new AMQPStreamConnection('rabbitmq_api', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $channel->queue_declare('hello', false, false, false, false);

        echo " [*] Waiting for messages. To exit press CTRL+C\n";
        $callback = function ($msg) {
            echo ' [x] Received ', $msg->body, "\n";
        };

        $channel->basic_consume('hello', '', false, true, false, false, $callback);

        while (count($channel->callbacks)) {
            $channel->wait();
        }

    }
}

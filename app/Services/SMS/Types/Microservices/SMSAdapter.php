<?php

namespace App\Services\SMS\Types\Microservices;

use App\Services\SMS\SMSInterface;
use App\Services\RabbitMQService;
use App\Services\SMS\SMSException;


class SMSAdapter implements SMSInterface
{

    /** @var array */
    private $params;

    public function __construct($params)
    {
        RabbitMQService::validateLoggerCredentials($params);
        $this->params = $params;
    }


    /**
     * @param array $smsData
     * @return array
     * @throws SMSException
     */
    public function sendSMS(array $smsData)
    {
        if (!empty($smsData) && is_array($smsData)) {
            $connection = RabbitMQService::getAMQPStreamConnection($this->params);
            $channel = $connection->channel();
            $exchangeName = 'api_services';
            $channel->exchange_declare($exchangeName, 'topic', false, true, false);
            $routing_key = 'service.sms.send';

            //Data to be sent
            $data = [];
            $time = date("Y-m-d h:i:s");
            $service = config('app.service_name');

            $data['service'] = $service;
            $data['data'] = [
                "service_name" => $service,
                "send_time" => $time,
            ];
            $data['send_time'] = $time;
            //ADD APP LOGGING DATA BEFORE SEND TO SERVICE
            $data['data'] = array_merge($data['data'], $smsData);

            $msg = RabbitMQService::getAMQPMessage($data);
            $channel->basic_publish($msg, $exchangeName, $routing_key);
            $channel->close();
            $connection->close();
            return ["success"=>"SMS successfully sent!"];
        } else {
            throw new SMSException("sms_service_data_must_be_non_empty_array");
        }
    }
}

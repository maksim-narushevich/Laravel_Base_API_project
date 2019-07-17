<?php

namespace App\Services\Logging\Types;

use App\Services\Logging\LoggerException;
use App\Services\Logging\LoggerInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;


class FluentdLogger implements LoggerInterface
{


    /** @var array
     *  Required parameters that must be provided to initialize logging service
     */
    private $arrRequiredCredentials = ['host', 'port', 'login', 'password'];

    /** @var array */
    private $params;

    public function __construct($params)
    {
        $this->validateLoggerCredentials($params);
    }


    /**
     * @param array $logData
     * @throws LoggerException
     */
    public function sendLog(array $logData)
    {
        if (!empty($logData) && is_array($logData)) {
            $connection = new AMQPStreamConnection($this->params['host'], $this->params['port'], $this->params['login'], $this->params['password']);
            $channel = $connection->channel();
            $exchangeName = 'api_services';
            $channel->exchange_declare($exchangeName, 'topic', false, true, false);
            $routing_key = 'service.logging.log';

            //Data to be sent
            $data = [];
            $log_time = date("Y-m-d h:i:s");
            $service = config('app.service_name');

            $data['service'] = $service;
            $data['log_data'] = [
                "service_name" => $service,
                "log_time" => $log_time,
            ];
            $data['log_time'] = $log_time;
            //ADD APP LOGGING DATA BEFORE SEND TO SERVICE
            $data['log_data'] = array_merge($data['log_data'], $logData);

            $msg = new AMQPMessage(json_encode($data));
            $channel->basic_publish($msg, $exchangeName, $routing_key);
            $channel->close();
            $connection->close();
        } else {
            throw new LoggerException("logging_service_must_be_non_empty_array");
        }
    }


    /**
     * @param array $credentials
     * @throws LoggerException
     */
    public function validateLoggerCredentials(array $credentials)
    {
        //Remove 'type' from credentials array
        if(isset($credentials['type'])){
            unset($credentials['type']);
        }
        foreach ($credentials as $key => $val) {
            if (in_array($key, $this->arrRequiredCredentials) && empty($val)) {
                throw new LoggerException("logger_service_" . strtolower($key) . "_cant_be_empty");
            } elseif (!in_array($key, $this->arrRequiredCredentials)) {
                throw new LoggerException("logger_service_" . strtolower($key) . "_should_be_provided");
            }
        }
        //Initialize logging service parameters
        $this->params = $credentials;
    }
}

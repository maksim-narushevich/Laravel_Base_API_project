<?php
namespace App\Services;

use App\Exceptions\RabbitMQException;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    /**
     * @param array $credentials
     * @throws RabbitMQException
     */
    static public function validateLoggerCredentials(array $credentials)
    {
        $arrRequiredCredentials = ['host', 'port', 'login', 'password'];

        $arrCredentialsKeys=array_keys($credentials);
        foreach ($arrRequiredCredentials as $key) {
            if (!in_array($key, $arrCredentialsKeys)) {
                throw new RabbitMQException("rabbitmq_" . strtolower($key) . "_should_be_provided");
            }elseif (empty($credentials[$key])) {
                throw new RabbitMQException("rabbitmq_" . strtolower($key) . "_cant_be_empty");
            }
        }
    }

    /**
     * @param array $params
     * @return AMQPStreamConnection
     */
    static public function getAMQPStreamConnection(array $params)
    {
        return new AMQPStreamConnection($params['host'], $params['port'], $params['login'], $params['password']);
    }

    /**
     * @param array $data
     * @return AMQPMessage
     */
    static public function getAMQPMessage(array $data)
    {
        return new AMQPMessage(json_encode($data));
    }

}
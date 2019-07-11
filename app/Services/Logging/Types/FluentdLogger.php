<?php

namespace App\Services\Logging\Types;

use App\Services\Logging\LoggerException;
use App\Services\Logging\LoggerInterface;
use Symfony\Component\HttpClient\HttpClient;


class FluentdLogger implements LoggerInterface
{

    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var string */
    private $url;

    /** @var array */
    private $arrRequiredCaredentails = ['user', 'password', 'url'];

    private $httpClient;

    public function __construct($params)
    {
        $this->validateLoggerCredentials($params);
        $this->username = $params['user'];
        $this->password = $params['password'];
        $this->url = $params['url'];
        $this->httpClient = HttpClient::create([
            'headers' => [
                'Content-Type' => 'application/json',
            ]
        ]);
    }

    public function getToken()
    {
        $response = $this->httpClient->request('POST', $this->url . '/login', [
            'json' => ['login' => $this->username, 'password' => $this->password],
        ]);

        if (isset($response->toArray()['token']) && !empty($response->toArray()['token'])) {
            return $response->toArray()['token'];
        } else {
            throw new LoggerException("bad_logging_service_credentials");
        }

    }

    public function sendLog(array $data)
    {
        $response = $this->httpClient->request('POST', $this->url . '/log', [
            'json' => $data,
            'auth_bearer' => $this->getToken(),
        ]);
        return $response->getContent();
    }


    public function validateLoggerCredentials(array $credentials)
    {
        foreach ($credentials as $key => $val) {
            if (in_array($key, $this->arrRequiredCaredentails) && empty($val)) {
                throw new LoggerException("logger_service_" . strtolower($key) . "_cant_be_empty");
            }
        }
    }
}

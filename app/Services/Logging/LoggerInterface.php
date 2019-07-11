<?php

namespace App\Services\Logging;


interface LoggerInterface
{

    public function getToken();

    public function sendLog(array $data);

    public function validateLoggerCredentials(array $credentials);
}
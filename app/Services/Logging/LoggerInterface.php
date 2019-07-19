<?php

namespace App\Services\Logging;


interface LoggerInterface
{
    public function sendLog(array $data);
}

<?php

namespace App\Services\SMS;


interface SMSInterface
{
    public function sendSMS(array $data);
}

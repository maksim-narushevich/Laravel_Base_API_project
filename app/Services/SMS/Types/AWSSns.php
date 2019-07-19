<?php

namespace App\Services\SMS\Types;


use App\Services\SMS\SMSInterface;
use Aws\Laravel\AwsFacade as AWS;


class AWSSns implements SMSInterface
{
    /** @var \Aws\AwsClientInterface  */
    private $sns;

    public function __construct()
    {
        $this->sns=AWS::createClient('sns');
    }

    public function sendSMS(array $data)
    {
        $this->sns->publish([
            'Message' => $data['message'],
            'PhoneNumber' => $data['number'],
            'MessageAttributes' => [
                'AWS.SNS.SMS.SMSType' => [
                    'DataType' => 'String',
                    'StringValue' => 'Transactional',
                ]
            ],
        ]);
        return ["success"=>"SMS successfully sent!"];
    }
}

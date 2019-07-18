<?php
namespace App\Services\SMS;

use App\Services\SMS\Types\AWSSns;
use App\Services\SMS\Types\Microservices\SMSAdapter;

class SMSService
{


    private $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    public function getService()
    {
        switch ($this->params['provider']) {
            case "python-aws-microservice":
                return new SMSAdapter($this->params);
            case "aws":
                return new AWSSns();
            default:
                throw new ServiceException("sms_service_undefined");
        }
    }
}
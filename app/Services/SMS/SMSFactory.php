<?php


namespace App\Services\SMS;


use Aws\Laravel\AwsFacade as AWS;

class SMSFactory
{
    /**
     * @param $provider
     * @return \Aws\AwsClientInterface
     * @throws ServiceException
     */
    static function getService($provider)
    {
        switch ($provider) {
            case "aws":
                return AWS::createClient('sns');
                break;
            default:
                throw new ServiceException("sms_service_undefined");
        }
    }
}
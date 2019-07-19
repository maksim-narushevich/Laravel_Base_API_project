<?php


namespace App\Services\Logging;


use App\Services\Logging\Types\FluentdLogger;

class LoggerService
{

    private $params;

    public function __construct($params)
    {
        $this->params = $params;
    }


    /**
     * Return logger instance
     * @return LoggerInterface
     * @throws LoggerException
     */
    function getService(): LoggerInterface
    {
        switch ($this->params['type']) {
            case "fluentd":
                return new FluentdLogger($this->params);
                break;
            default:
                throw new LoggerException("logging_service_undefined");
        }
    }

}

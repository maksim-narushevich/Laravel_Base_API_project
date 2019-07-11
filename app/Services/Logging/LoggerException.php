<?php
namespace App\Services\Logging;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class LoggerException extends Exception{

    public function __construct($message = null)
    {
        parent::__construct($message, Response::HTTP_BAD_REQUEST);
    }
}
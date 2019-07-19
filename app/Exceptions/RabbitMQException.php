<?php
namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class RabbitMQException extends Exception{

    public function __construct($message = null)
    {
        parent::__construct($message, Response::HTTP_BAD_REQUEST);
    }
}
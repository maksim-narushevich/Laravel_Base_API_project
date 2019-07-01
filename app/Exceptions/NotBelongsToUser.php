<?php

namespace App\Exceptions;

use Exception;

class NotBelongsToUser extends Exception
{
    public function render($request, Exception $exception)
    {

         return ['errors' => $exception->getMessage()];
    }
}

<?php
namespace App\Exceptions;
use Exception;
class ReviewNotBelongsToProduct extends Exception
{
    public function render()
    {
        return ['errors' => 'Review Not Belongs to Product'];
    }
}

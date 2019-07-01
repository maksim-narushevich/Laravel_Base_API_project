<?php


namespace App\Services;


class TokenGenerator
{

    static public function generate(): string
    {
        return bin2hex(random_bytes(32));
    }
}

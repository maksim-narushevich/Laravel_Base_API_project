<?php

function main() : array
{
    return ["token"=>bin2hex(random_bytes(32))];
}

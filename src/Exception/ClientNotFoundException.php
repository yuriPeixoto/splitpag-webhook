<?php

namespace App\Exception;

use Throwable;

class ClientNotFoundException extends ClientException
{
    public function __construct($message = "Client not found", $code = 404, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

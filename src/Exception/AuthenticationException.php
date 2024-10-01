<?php

namespace App\Exception;

use Exception;

class AuthenticationException extends Exception
{
    protected $context;

    public function __construct(string $message = "", int $code = 0, ?Exception $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function __toString(): string
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function getDetailedMessage(): string
    {
        $detailedMessage = $this->getMessage();
        if (!empty($this->context)) {
            $detailedMessage .= "\nContext: " . json_encode($this->context, JSON_PRETTY_PRINT);
        }
        return $detailedMessage;
    }
}

<?php

namespace App\Exceptions\VoucherOrder;

use RuntimeException;
use Throwable;

abstract class VoucherOrderException extends RuntimeException
{
    protected string $userMessage;

    public function __construct(string $message = '', string $userMessage = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->userMessage = $userMessage ?: $message;
    }

    public function getUserMessage(): string
    {
        return $this->userMessage;
    }
}
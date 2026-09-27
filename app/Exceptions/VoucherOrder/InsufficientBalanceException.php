<?php

namespace App\Exceptions\VoucherOrder;

class InsufficientBalanceException extends VoucherOrderException
{
    public function __construct()
    {
        parent::__construct(
            message:     "Insufficient balance",
            userMessage:  "Overdrawn limit exceed."
        );
    }
}
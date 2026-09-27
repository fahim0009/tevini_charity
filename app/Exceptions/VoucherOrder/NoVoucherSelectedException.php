<?php

namespace App\Exceptions\VoucherOrder;

class NoVoucherSelectedException extends VoucherOrderException
{
    public function __construct()
    {
        parent::__construct(
            message:     "No voucher selected",
            userMessage:  "You didn't select any voucher."
        );
    }
}
<?php

namespace App\Exceptions\VoucherOrder;

class StockExceededException extends VoucherOrderException
{
    public function __construct(float $voucherAmount)
    {
        parent::__construct(
            message:      "Stock limit exceeded for voucher £{$voucherAmount}",
            userMessage:  "One or some vouchers stock limit exceeded."
        );
    }
}
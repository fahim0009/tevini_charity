<?php

namespace App\Exceptions\VoucherOrder;

class DeliveryOptionException extends VoucherOrderException
{
    public function __construct()
    {
        parent::__construct(
            message:     "Delivery option not selected",
            userMessage:  "Please select delivery option."
        );
    }
}
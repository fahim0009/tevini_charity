<?php

namespace App\Exceptions\Donation;

class InsufficientBalanceException extends DonationException
{
    public function __construct()
    {
        parent::__construct('Insufficient balance', "You don't have sufficient balance for this donation.");
    }
}
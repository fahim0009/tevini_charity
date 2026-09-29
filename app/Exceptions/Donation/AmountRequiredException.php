<?php

namespace App\Exceptions\Donation;

class AmountRequiredException extends DonationException
{
    public function __construct()
    {
        parent::__construct('Amount is empty', 'Please fill amount field.');
    }
}
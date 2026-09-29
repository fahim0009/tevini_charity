<?php

namespace App\Exceptions\Donation;

class StandingDonationValidationException extends DonationException
{
    public function __construct(string $field = 'field')
    {
        parent::__construct(
            "Missing standing donation field: {$field}",
            "Please fill the {$field} field."
        );
    }
}
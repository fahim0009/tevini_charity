<?php

namespace App\Exceptions\Donation;

class CharityRequiredException extends DonationException
{
    public function __construct()
    {
        parent::__construct('Charity not selected', 'Please select beneficiary field.');
    }
}
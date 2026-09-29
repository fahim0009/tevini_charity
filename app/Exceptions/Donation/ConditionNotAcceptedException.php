<?php

namespace App\Exceptions\Donation;

class ConditionNotAcceptedException extends DonationException
{
    public function __construct()
    {
        parent::__construct('Condition not accepted', 'Please accept the donation condition.');
    }
}
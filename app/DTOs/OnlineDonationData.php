<?php

namespace App\DTOs;

class OnlineDonationData
{
    public function __construct(
        public ?int   $donorId,              // null for guest
        public int    $orderedByUserId,      // admin id, donor id, or 0 for guest
        public string  $source,               // 'admin' | 'web' | 'app' | 'guest'
        public int     $charityId,
        public float   $amount,               // base donation amount
        public bool    $isAnonymous,
        public bool    $isStandingOrder,
        public ?string $charityNote,
        public ?string $myNote,
        public bool    $confirmDonation,
        public string  $paymentMethod = 'balance',   // 'balance' | 'stripe'
        public ?string $stripePaymentIntentId = null,
        // Standing order fields
        public ?string $paymentsType = null,          // '1' = Fixed, '2' = Continuous
        public ?int    $numberPayments = null,
        public ?string $startingDate = null,
        public ?int    $intervalValue = null,
        // Guest donor info
        public ?array  $donorInfo = null,
    ) {}

    public function isGuest(): bool
    {
        return $this->donorId === null;
    }

    public function isStripePayment(): bool
    {
        return $this->paymentMethod === 'stripe';
    }

    public function isFixedPayments(): bool
    {
        return $this->paymentsType === '1';
    }

    // ================================================================
    // Factory: Admin one-time donation
    // ================================================================
    public static function fromAdminOneTime(
        int    $donorId,
        int    $adminId,
        int    $charityId,
        float  $amount,
        bool   $isAnonymous,
        ?string $charityNote,
        ?string $myNote,
        bool   $confirmDonation,
    ): self {
        return new self(
            donorId:         $donorId,
            orderedByUserId: $adminId,
            source:          'admin',
            charityId:       $charityId,
            amount:           $amount,
            isAnonymous:     $isAnonymous,
            isStandingOrder: false,
            charityNote:     $charityNote,
            myNote:           $myNote,
            confirmDonation: $confirmDonation,
            paymentMethod:   'balance',
        );
    }

    // ================================================================
    // Factory: Admin standing donation
    // ================================================================
    public static function fromAdminStanding(
        int    $donorId,
        int    $adminId,
        int    $charityId,
        float  $amount,
        bool   $isAnonymous,
        ?string $charityNote,
        ?string $myNote,
        bool   $confirmDonation,
        string $paymentsType,
        ?int   $numberPayments,
        string $startingDate,
        int    $intervalValue,
    ): self {
        return new self(
            donorId:         $donorId,
            orderedByUserId: $adminId,
            source:          'admin',
            charityId:       $charityId,
            amount:           $amount,
            isAnonymous:     $isAnonymous,
            isStandingOrder: true,
            charityNote:     $charityNote,
            myNote:           $myNote,
            confirmDonation: $confirmDonation,
            paymentMethod:   'balance',
            paymentsType:    $paymentsType,
            numberPayments:  $numberPayments,
            startingDate:    $startingDate,
            intervalValue:   $intervalValue,
        );
    }

    // ================================================================
    // Factory: Donor Web one-time donation
    // ================================================================
    public static function fromDonorWebOneTime(
        int    $donorId,
        int    $charityId,
        float  $amount,
        bool   $isAnonymous,
        ?string $charityNote,
        ?string $myNote,
        bool   $confirmDonation,
    ): self {
        return new self(
            donorId:         $donorId,
            orderedByUserId: $donorId,
            source:          'web',
            charityId:       $charityId,
            amount:           $amount,
            isAnonymous:     $isAnonymous,
            isStandingOrder: false,
            charityNote:     $charityNote,
            myNote:           $myNote,
            confirmDonation: $confirmDonation,
            paymentMethod:   'balance',
        );
    }

    // ================================================================
    // Factory: Donor Web standing donation
    // ================================================================
    public static function fromDonorWebStanding(
        int    $donorId,
        int    $charityId,
        float  $amount,
        bool   $isAnonymous,
        ?string $charityNote,
        ?string $myNote,
        bool   $confirmDonation,
        string $paymentsType,
        ?int   $numberPayments,
        string $startingDate,
        int    $intervalValue,
    ): self {
        return new self(
            donorId:         $donorId,
            orderedByUserId: $donorId,
            source:          'web',
            charityId:       $charityId,
            amount:           $amount,
            isAnonymous:     $isAnonymous,
            isStandingOrder: true,
            charityNote:     $charityNote,
            myNote:           $myNote,
            confirmDonation: $confirmDonation,
            paymentMethod:   'balance',
            paymentsType:    $paymentsType,
            numberPayments:  $numberPayments,
            startingDate:    $startingDate,
            intervalValue:   $intervalValue,
        );
    }

    // ================================================================
    // Factory: Donor App (one-time or standing)
    // ================================================================
    public static function fromDonorApp(
        int    $donorId,
        int    $charityId,
        float  $amount,
        bool   $isAnonymous,
        bool   $isStandingOrder,
        ?string $charityNote,
        ?string $myNote,
        bool   $confirmDonation,
        ?string $paymentsType = null,
        ?int   $numberPayments = null,
        ?string $startingDate = null,
        ?int    $intervalValue = null,
    ): self {
        return new self(
            donorId:         $donorId,
            orderedByUserId: $donorId,
            source:          'app',
            charityId:       $charityId,
            amount:           $amount,
            isAnonymous:     $isAnonymous,
            isStandingOrder: $isStandingOrder,
            charityNote:     $charityNote,
            myNote:           $myNote,
            confirmDonation: $confirmDonation,
            paymentMethod:   'balance',
            paymentsType:    $paymentsType,
            numberPayments:  $numberPayments,
            startingDate:    $startingDate,
            intervalValue:   $intervalValue,
        );
    }

    // ================================================================
    // Factory: Guest donation (Stripe only)
    // ================================================================
    public static function fromGuest(
        int    $charityId,
        float  $amount,
        bool   $isAnonymous,
        ?string $charityNote,
        ?string $myNote,
        bool   $confirmDonation,
        string $paymentMethod = 'stripe',
        ?string $stripePaymentIntentId = null,
        ?array  $donorInfo = null,
        ?int   $donorId = null,
    ): self {
        return new self(
            donorId:              $donorId,
            orderedByUserId:      $donorId ?? 0,
            source:               'guest',
            charityId:            $charityId,
            amount:               $amount,
            isAnonymous:          $isAnonymous,
            isStandingOrder:      false,
            charityNote:          $charityNote,
            myNote:               $myNote,
            confirmDonation:      $confirmDonation,
            paymentMethod:        $paymentMethod,
            stripePaymentIntentId: $stripePaymentIntentId,
            donorInfo:            $donorInfo,
        );
    }
}
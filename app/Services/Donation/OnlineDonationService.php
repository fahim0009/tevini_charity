<?php

namespace App\Services\Donation;

use App\DTOs\OnlineDonationData;
use App\Exceptions\Donation\{
    AmountRequiredException,
    CharityRequiredException,
    ConditionNotAcceptedException,
    DonationException,
    InsufficientBalanceException,
    StandingDonationValidationException
};
use App\Models\{Charity, ContactMail, Donation, PaymentLog, StandingDonation, StandingdonationDetail, User, Usertransaction};
use Illuminate\Support\Facades\{DB, Log, Mail};
use App\Mail\{DonationReport, DonationstandingReport};

class OnlineDonationService
{
    private const FEE_PERCENT = 6.0;

    // ====================================================================
    // PUBLIC: One-Time Donation
    // ====================================================================

    /**
     * Create a one-time donation.
     * Handles: admin, web, app (balance) + guest (stripe)
     */
    public function createOneTimeDonation(OnlineDonationData $data): Donation
    {
        // 1. Validate
        $this->validateOneTimeDonation($data);

        // 2. Calculate fee (6% for Stripe only, 0 for balance)
        $fee = $this->calculateFee($data->amount, $data->paymentMethod);
        $totalChargeable = $data->amount + $fee;

        // 3. Balance check (only for balance payments)
        if ($data->paymentMethod === 'balance' && $data->donorId) {
            $this->ensureSufficientBalance($data->donorId, $totalChargeable);
        }

        // 4. Persist
        $donation = DB::transaction(function () use ($data, $fee, $totalChargeable) {
            return $this->persistOneTimeDonation($data, $fee, $totalChargeable);
        });

        // 5. Create PaymentLog for Stripe
        if ($data->isStripePayment() && $data->stripePaymentIntentId) {
            $this->createPaymentLog($donation, $data->stripePaymentIntentId, $totalChargeable, $data->donorId);
        }

        // 6. Send email
        $this->sendOneTimeDonationEmail($donation, $data->donorId);

        return $donation;
    }

    // ====================================================================
    // PUBLIC: Standing Order Donation
    // ====================================================================

    /**
     * Create a standing order donation.
     * Always uses balance payment (no Stripe for standing orders).
     */
    public function createStandingDonation(OnlineDonationData $data): StandingDonation
    {
        // 1. Validate
        $this->validateStandingDonation($data);

        // 2. Balance check
        if ($data->donorId) {
            $this->ensureSufficientBalance($data->donorId, $data->amount);
        }

        // 3. Persist
        $standingDonation = DB::transaction(function () use ($data) {
            return $this->persistStandingDonation($data);
        });

        // 4. Send email
        $this->sendStandingDonationEmail($standingDonation, $data->donorId);

        return $standingDonation;
    }

    // ====================================================================
    // PUBLIC: Update Standing Donation
    // ====================================================================

    public function updateStandingDonation(int $id, OnlineDonationData $data): StandingDonation
    {
        $standing = StandingDonation::findOrFail($id);

        $standing->amount          = (int) $data->amount;
        $standing->starting        = $data->startingDate;
        $standing->payments        = $data->paymentsType;
        $standing->number_payments = $data->isFixedPayments() ? $data->numberPayments : null;
        $standing->interval        = $data->intervalValue;
        $standing->charitynote     = $data->charityNote;
        $standing->mynote          = $data->myNote;
        $standing->updated_by      = (string) $data->orderedByUserId;
        $standing->save();

        return $standing;
    }

    // ====================================================================
    // PUBLIC: Calculate Donation Total (for PaymentIntent preview)
    // ====================================================================

    public function calculateDonationTotal(float $amount, string $paymentMethod): array
    {
        $fee = $this->calculateFee($amount, $paymentMethod);

        return [
            'base_amount'      => $amount,
            'fee'              => $fee,
            'total'            => $amount + $fee,
            'payment_method'   => $paymentMethod,
        ];
    }

    // ====================================================================
    // PUBLIC: Check Balance (for frontend badge)
    // ====================================================================

    public function checkBalance(int $donorId, float $amount): array
    {
        $user = User::find($donorId);
        if (!$user) {
            return ['has_balance' => false, 'available_limit' => 0];
        }

        $available = $user->getAvailableLimit();

        return [
            'has_balance'      => $available >= $amount && $amount > 0,
            'available_limit'  => $available,
        ];
    }

    // ====================================================================
    // PUBLIC: Create Stripe PaymentIntent
    // ====================================================================

    public function createStripePaymentIntent(OnlineDonationData $data): array
    {
        $fee  = $this->calculateFee($data->amount, 'stripe');
        $total = $data->amount + $fee;

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount'   => (int) round($total * 100),
            'currency' => 'gbp',
            'metadata' => [
                'type'        => 'online_donation',
                'charity_id'  => (string) $data->charityId,
                'base_amount' => $data->amount,
                'fee'         => $fee,
                'user_id'     => $data->donorId ? (string) $data->donorId : 'guest',
                'session_id'  => session()->getId(),
            ],
            'description' => 'Donation to charity ID: ' . $data->charityId,
        ]);

        return [
            'client_secret'  => $paymentIntent->client_secret,
            'total_amount'   => $total,
            'base_amount'     => $data->amount,
            'fee'             => $fee,
            'payment_intent_id' => $paymentIntent->id,
        ];
    }

    // ====================================================================
    // PRIVATE: Validation
    // ====================================================================

    private function validateOneTimeDonation(OnlineDonationData $data): void
    {
        if (empty($data->charityId)) {
            throw new CharityRequiredException();
        }

        if (empty($data->amount) || $data->amount <= 0) {
            throw new AmountRequiredException();
        }

        if (!$data->confirmDonation) {
            throw new ConditionNotAcceptedException();
        }
    }

    private function validateStandingDonation(OnlineDonationData $data): void
    {
        $this->validateOneTimeDonation($data);

        if ($data->isStandingOrder) {
            if (empty($data->paymentsType)) {
                throw new StandingDonationValidationException('payment type');
            }

            if ($data->isFixedPayments() && empty($data->numberPayments)) {
                throw new StandingDonationValidationException('number of payments');
            }

            if (empty($data->startingDate)) {
                throw new StandingDonationValidationException('starting date');
            }

            if (empty($data->intervalValue)) {
                throw new StandingDonationValidationException('interval');
            }
        }
    }

    // ====================================================================
    // PRIVATE: Fee Calculation
    // ====================================================================

    /**
     * 6% fee for Stripe payments only.
     * Balance payments are fee-free.
     */
    private function calculateFee(float $amount, string $paymentMethod): float
    {
        if ($paymentMethod === 'stripe') {
            return round($amount * self::FEE_PERCENT / 100, 2);
        }

        return 0.00;
    }

    // ====================================================================
    // PRIVATE: Balance Check
    // ====================================================================

    private function ensureSufficientBalance(int $donorId, float $amountNeeded): void
    {
        $user = User::find($donorId);
        if (!$user) {
            throw new InsufficientBalanceException();
        }

        $available = $user->getAvailableLimit();

        if ($available < $amountNeeded) {
            throw new InsufficientBalanceException();
        }
    }

    // ====================================================================
    // PRIVATE: Persist One-Time Donation
    // ====================================================================

    private function persistOneTimeDonation(OnlineDonationData $data, float $fee, float $totalChargeable): Donation
    {
        $charity = Charity::findOrFail($data->charityId);

        // Build donation data
        $donationData = [
            'user_id'          => $data->donorId,
            'charity_id'       => $data->charityId,
            'amount'           => $data->amount,
            'admin_charge'     => $fee,
            'stripe_charge'    => 0,
            'currency'         => 'GBP',
            'ano_donation'     => $data->isAnonymous ? 'true' : 'false',
            'standing_order'   => 'false',
            'confirm_donation' => $data->confirmDonation ? 'true' : 'false',
            'charitynote'      => $data->charityNote,
            'mynote'           => $data->myNote,
            'notification'    => 1,
            'status'           => 0,
            'payment_method'   => $data->paymentMethod,
            'created_by'       => (string) $data->orderedByUserId,
            'updated_by'       => (string) $data->orderedByUserId,
        ];

        // Stripe-specific fields
        if ($data->isStripePayment()) {
            $donationData['stripe_payment_id'] = $data->stripePaymentIntentId;
        }

        // Guest donor info
        if ($data->isGuest() && $data->donorInfo) {
            $donationData['guest_first_name'] = $data->donorInfo['first_name'] ?? null;
            $donationData['guest_last_name']  = $data->donorInfo['last_name'] ?? null;
            $donationData['guest_email']      = $data->donorInfo['email'] ?? null;
            $donationData['guest_phone']      = $data->donorInfo['phone'] ?? null;
            $donationData['guest_address_1']   = $data->donorInfo['address_line_1'] ?? null;
            $donationData['guest_address_2']   = $data->donorInfo['address_line_2'] ?? null;
            $donationData['guest_address_3']   = $data->donorInfo['address_line_3'] ?? null;
            $donationData['guest_town']        = $data->donorInfo['town'] ?? null;
            $donationData['guest_postcode']    = $data->donorInfo['postcode'] ?? null;
        }

        $donation = Donation::create($donationData);

        // Create Usertransaction
        $this->createUserTransaction([
            'userId'      => $data->donorId,
            'charityId'   => $data->charityId,
            'donationId'  => $donation->id,
            'amount'      => $totalChargeable,
            'title'       => $data->isStripePayment() ? 'Online Donation (Stripe)' : 'Online Donation',
        ]);

        // Decrement user balance (balance payment only)
        if ($data->paymentMethod === 'balance' && $data->donorId) {
            User::where('id', $data->donorId)->decrement('balance', $totalChargeable);
        }

        // Increment charity balance (always by base amount, NOT including fee)
        $charity->increment('balance', $data->amount);

        Log::info('One-time donation created', [
            'donation_id'    => $donation->id,
            'donor_id'       => $data->donorId,
            'charity_id'     => $data->charityId,
            'base_amount'    => $data->amount,
            'fee'            => $fee,
            'total'           => $totalChargeable,
            'payment_method' => $data->paymentMethod,
            'source'          => $data->source,
        ]);

        return $donation;
    }

    // ====================================================================
    // PRIVATE: Persist Standing Donation
    // ====================================================================

    private function persistStandingDonation(OnlineDonationData $data): StandingDonation
    {
        $charity = Charity::findOrFail($data->charityId);

        // 1. Create StandingDonation
        $standingDonation = StandingDonation::create([
            'user_id'         => $data->donorId,
            'charity_id'      => $data->charityId,
            'amount'          => (int) $data->amount,
            'currency'        => 'GBP',
            'ano_donation'    => $data->isAnonymous ? 'true' : 'false',
            'standing_order'  => 'true',
            'payments'        => $data->paymentsType,
            'number_payments' => $data->isFixedPayments() ? $data->numberPayments : null,
            'payment_made'    => 0,
            'starting'        => $data->startingDate,
            'interval'        => $data->intervalValue,
            'charitynote'     => $data->charityNote,
            'mynote'          => $data->myNote,
            'notification'    => 1,
            'status'          => 1,
            'created_by'      => (string) $data->orderedByUserId,
            'updated_by'      => (string) $data->orderedByUserId,
        ]);

        // 2. Create StandingdonationDetail
        StandingdonationDetail::create([
            'standing_donation_id' => $standingDonation->id,
            'user_id'              => $data->donorId,
            'charity_id'           => $data->charityId,
            'amount'               => (int) $data->amount,
            'instalment_date'      => $data->startingDate,
            'instalment_mode'      => $data->isFixedPayments() ? 'Fixed' : 'continuous',
            'status'               => 0,
            'created_by'           => (string) $data->orderedByUserId,
            'updated_by'           => (string) $data->orderedByUserId,
        ]);

        // 3. Create Usertransaction
        $this->createUserTransaction([
            'userId'       => $data->donorId,
            'charityId'    => $data->charityId,
            'standingId'   => $standingDonation->id,
            'amount'       => $data->amount,
            'title'        => 'Standing order donation',
        ]);

        // 4. Decrement user balance
        if ($data->donorId) {
            User::where('id', $data->donorId)->decrement('balance', $data->amount);
        }

        // 5. Increment charity balance
        $charity->increment('balance', $data->amount);

        Log::info('Standing donation created', [
            'standing_donation_id' => $standingDonation->id,
            'donor_id'             => $data->donorId,
            'charity_id'           => $data->charityId,
            'amount'               => $data->amount,
            'payments_type'        => $data->paymentsType,
            'source'               => $data->source,
        ]);

        return $standingDonation;
    }

    // ====================================================================
    // PRIVATE: Create Usertransaction
    // ====================================================================

    private function createUserTransaction(array $params): void
    {
        $utransaction = new Usertransaction();
        $utransaction->t_id     = 'TXN-' . time() . '-' . rand(1000, 9999);
        $utransaction->user_id   = $params['userId'];
        $utransaction->charity_id = $params['charityId'] ?? null;

        if (isset($params['donationId'])) {
            $utransaction->donation_id = $params['donationId'];
        }

        if (isset($params['standingId'])) {
            $utransaction->standing_donationdetails_id = $params['standingId'];
        }

        $utransaction->t_type   = 'Out';
        $utransaction->amount   = $params['amount'];
        $utransaction->title    = $params['title'];
        $utransaction->status   = 1;
        $utransaction->save();
    }

    // ====================================================================
    // PRIVATE: Create PaymentLog
    // ====================================================================

    private function createPaymentLog(Donation $donation, string $intentId, float $amount, ?int $userId): void
    {
        try {
            PaymentLog::create([
                'user_id'           => $userId,
                'payment_intent_id' => $intentId,
                'amount'            => $amount,
                'currency'          => 'gbp',
                'status'            => 'succeeded',
                'type'              => 'online_donation',
            ]);
        } catch (\Throwable $e) {
            Log::error('Donation PaymentLog failed', [
                'donation_id' => $donation->id,
                'error'       => $e->getMessage(),
            ]);
        }
    }

    // ====================================================================
    // PRIVATE: Send One-Time Donation Email
    // ====================================================================

    private function sendOneTimeDonationEmail(Donation $donation, ?int $donorId): void
    {
        try {
            $email    = null;
            $name     = 'Donor';
            $clientNo = 'N/A';

            if ($donorId) {
                $user = User::find($donorId);
                if (!$user) return;

                $email    = $user->email;
                $name     = $user->name;
                $clientNo = $user->accountno ?? 'N/A';
            } else {
                // Guest — read from donation record
                $email    = $donation->guest_email;
                $name     = trim(($donation->guest_first_name ?? '') . ' ' . ($donation->guest_last_name ?? ''));
                $clientNo = 'Guest';
            }

            if (!$email) {
                Log::warning('Donation email skipped — no email', [
                    'donation_id' => $donation->id,
                    'donor_id'    => $donorId,
                ]);
                return;
            }

            $charity     = Charity::find($donation->charity_id);
            $contact     = ContactMail::find(1);
            $contactmail = $contact?->name ?: 'info@tevini.co.uk';

            $array = [
                'name'           => $name,
                'cc'             => $contactmail,
                'client_no'      => $clientNo,
                'amount'         => $donation->amount,
                'admin_charge'   => $donation->admin_charge ?? 0,
                'stripe_charge'  => $donation->stripe_charge ?? 0,
                'charity_note'   => $donation->charitynote,
                'charity_name'   => $charity?->name ?? 'N/A',
                'payment_method' => $donation->payment_method,
            ];

            Mail::to($email)->cc($contactmail)->send(new DonationReport($array));

        } catch (\Throwable $e) {
            Log::error('Donation email failed', [
                'donation_id' => $donation->id,
                'error'       => $e->getMessage(),
            ]);
        }
    }

    // ====================================================================
    // PRIVATE: Send Standing Donation Email
    // ====================================================================

    private function sendStandingDonationEmail(StandingDonation $donation, ?int $donorId): void
    {
        try {
            if (!$donorId) return;

            $user = User::find($donorId);
            if (!$user) return;

            $charity     = Charity::find($donation->charity_id);
            $contact     = ContactMail::find(1);
            $contactmail = $contact?->name ?: 'info@tevini.co.uk';

            $array = [
                'name'         => $user->name,
                'donation'     => $donation,
                'cc'           => $contactmail,
                'client_no'    => $user->accountno ?? 'N/A',
                'amount'       => $donation->amount,
                'charity_note' => $donation->charitynote,
                'charity_name' => $charity?->name ?? 'N/A',
            ];

            Mail::to($user->email)->cc($contactmail)->send(new DonationstandingReport($array));

        } catch (\Throwable $e) {
            Log::error('Standing donation email failed', [
                'standing_donation_id' => $donation->id,
                'error'                => $e->getMessage(),
            ]);
        }
    }
}
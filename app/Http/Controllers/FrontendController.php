<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\Charity;
use App\Models\User;
use App\Models\Usertransaction;
use App\Models\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Mail\DonationReport;

class FrontendController extends Controller
{

    /* ─── Charge Constants ─────────────────────────────────── */
    const ADMIN_CHARGE_PERCENT = 10;    // 10%
    const STRIPE_FEE_PERCENT   = 1.5;   // 1.5%
    const STRIPE_FEE_FIXED     = 0.20;  // 20p


    /* ─── Charge Helpers ───────────────────────────────────── */

    private function calcAdminCharge($baseAmount)
    {
        return round($baseAmount * self::ADMIN_CHARGE_PERCENT / 100, 2);
    }

    private function calcStripeCharge($subtotal)
    {
        return round(($subtotal * self::STRIPE_FEE_PERCENT / 100) + self::STRIPE_FEE_FIXED, 2);
    }


    // ─────────────────────────────────────────────────────────────
    //  SHOW DONATION FORM
    // ─────────────────────────────────────────────────────────────

    public function onlineDonation($charity_id = null, $amount = null)
    {
        $charityName = null;

        if ($charity_id) {
            $charity = \App\Models\Charity::find($charity_id);
            if ($charity) {
                $charityName = $charity->name;
            }
        }

        return view('frontend.onlineDonation', compact('charity_id', 'amount', 'charityName'));
    }


    // ─────────────────────────────────────────────────────────────
    //  CHECK IF LOGGED-IN USER HAS SUFFICIENT BALANCE
    // ─────────────────────────────────────────────────────────────

    public function onlineDonationCheckBalance(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'has_balance'     => false,
                'is_logged_in'    => false,
                'available_limit' => 0,
            ]);
        }

        $user           = auth()->user();
        $availableLimit = $user->getAvailableLimit();
        $amount         = floatval($request->amount);

        return response()->json([
            'has_balance'     => $availableLimit >= $amount && $amount > 0,
            'is_logged_in'    => true,
            'available_limit' => $availableLimit,
        ]);
    }


    // ─────────────────────────────────────────────────────────────
    //  CREATE STRIPE PAYMENT INTENT
    // ─────────────────────────────────────────────────────────────

    public function onlineDonationCreateIntent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount'       => 'required|numeric|min:0.50|max:999999',
            'charity_id'   => 'required|string',
            'admin_charge' => 'nullable|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 303,
                'message' => '<div class="alert alert-danger">' . implode('<br>', $validator->errors()->all()) . '</div>',
            ]);
        }

        $parts      = explode('|', $request->charity_id);
        $charityId  = $parts[0];
        $charityName = $parts[1] ?? 'Charity';

        // ── Calculate charges ──
        $baseAmount       = floatval($request->amount);
        $includeAdmin     = ($request->admin_charge == '1');
        $adminChargeAmt   = $includeAdmin ? $this->calcAdminCharge($baseAmount) : 0;
        $subtotal         = $baseAmount + $adminChargeAmt;
        $stripeChargeAmt  = $this->calcStripeCharge($subtotal);
        $totalAmount      = round($subtotal + $stripeChargeAmt, 2);

        try {
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $amountInPence = (int) round($totalAmount * 100);

            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount'   => $amountInPence,
                'currency' => 'gbp',
                'metadata' => [
                    'type'           => 'online_donation',
                    'charity_id'     => $charityId,
                    'charity_name'   => $charityName,
                    'anonymous'      => $request->ano_donation ? 'yes' : 'no',
                    'base_amount'    => $baseAmount,
                    'admin_charge'   => $adminChargeAmt,
                    'stripe_charge'  => $stripeChargeAmt,
                ],
                'description' => 'Donation to ' . $charityName,
            ]);

            return response()->json([
                'status'       => 200,
                'client_secret' => $paymentIntent->client_secret,
                'total_amount'  => $totalAmount,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 303,
                'message' => '<div class="alert alert-danger">Payment setup failed: ' . htmlspecialchars($e->getMessage()) . '</div>',
            ]);
        }
    }


    // ─────────────────────────────────────────────────────────────
    //  STORE DONATION  (handles BOTH balance & Stripe paths)
    // ─────────────────────────────────────────────────────────────

    public function onlineDonationStore(Request $request)
    {
        try {

            // ── Parse charity_id ──
            $parts       = explode('|', $request->charity_id);
            $charityId   = $parts[0] ?? null;
            $charityName = $parts[1] ?? '';

            // ── Basic validation ──
            if (empty($charityId)) {
                return response()->json(['status' => 303, 'message' => '<div class="alert alert-danger">Please select a charity.</div>']);
            }

            $baseAmount = floatval($request->amount);
            if (empty($request->amount) || $baseAmount <= 0) {
                return response()->json(['status' => 303, 'message' => '<div class="alert alert-danger">Please enter a valid donation amount.</div>']);
            }

            if (!$request->confirm_donation) {
                return response()->json(['status' => 303, 'message' => '<div class="alert alert-danger">Please accept the donation condition.</div>']);
            }

            // ── Calculate charges (server-side, never trust client) ──
            $includeAdmin    = ($request->admin_charge == '1');
            $adminChargeAmt  = $includeAdmin ? $this->calcAdminCharge($baseAmount) : 0;
            $stripeChargeAmt = 0;
            $totalChargeable = $baseAmount + $adminChargeAmt;

            $paymentMethod = $request->payment_method;

            \Log::info('Donation store — base: ' . $baseAmount . ', admin: ' . $adminChargeAmt . ', method: ' . $paymentMethod);


            // ══════════════════════════════════════════════════════════
            //  PATH 1 — BALANCE PAYMENT
            // ══════════════════════════════════════════════════════════

            if ($paymentMethod === 'balance') {

                \Log::info('=== BALANCE PATH ===');

                if (!auth()->check()) {
                    return response()->json(['status' => 303, 'message' => '<div class="alert alert-danger">You must be logged in to pay from balance.</div>']);
                }

                $userid = auth()->user()->id;

                $userTransactionBalance = Usertransaction::selectRaw('
                        SUM(CASE WHEN t_type = "In" THEN amount ELSE 0 END) -
                        SUM(CASE WHEN t_type = "Out" THEN amount ELSE 0 END) as balance
                    ')
                    ->where([
                        ['user_id', '=', $userid],
                        ['status', '=', '1'],
                    ])->orWhere([
                        ['user_id', '=', $userid],
                        ['pending', '=', '1'],
                    ])
                    ->first();

                $overdraftLimit        = User::where('id', $userid)->first()->overdrawn_amount;
                $donorBalanceWithLimit = $userTransactionBalance->balance + $overdraftLimit;

                \Log::info('User balance: ' . $userTransactionBalance->balance . ' + overdraft: ' . $overdraftLimit . ' = ' . $donorBalanceWithLimit . ' | needed: ' . $totalChargeable);

                if ($donorBalanceWithLimit < $totalChargeable) {
                    return response()->json(['status' => 303, 'message' => '<div class="alert alert-danger">You don\'t have sufficient balance for this donation (including charges).</div>']);
                }

                $donation = new Donation();
                $donation->user_id          = $userid;
                $donation->charity_id       = $charityId;
                $donation->amount           = $baseAmount;
                $donation->admin_charge     = $adminChargeAmt;
                $donation->stripe_charge    = 0;
                $donation->currency         = 'GBP';
                $donation->ano_donation     = $request->ano_donation ? 'true' : 'false';
                $donation->standing_order   = 'false';
                $donation->confirm_donation = 'true';
                $donation->charitynote      = $request->charitynote;
                $donation->mynote           = $request->mynote;
                $donation->notification     = 1;
                $donation->status           = 0;
                $donation->payment_method   = 'balance';

                DB::beginTransaction();
                try {
                    $donation->save();

                    $utransaction            = new Usertransaction();
                    $utransaction->t_id      = time() . '-' . $userid;
                    $utransaction->user_id   = $userid;
                    $utransaction->charity_id = $charityId;
                    $utransaction->donation_id = $donation->id;
                    $utransaction->t_type    = 'Out';
                    $utransaction->amount    = $totalChargeable;  // base + admin
                    $utransaction->title     = 'Online Donation';
                    $utransaction->status    = 1;
                    $utransaction->save();

                    $user = User::find($userid);
                    $user->decrement('balance', $totalChargeable);  // base + admin

                    $charity = Charity::find($charityId);
                    if ($charity) {
                        $charity->increment('balance', $baseAmount);  // charity gets base only
                    }

                    DB::commit();
                    \Log::info('Balance donation committed — ID: ' . $donation->id);

                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::error('Balance donation DB error: ' . $e->getMessage());
                    return response()->json(['status' => 303, 'message' => '<div class="alert alert-danger">Something went wrong. Please try again.</div>']);
                }

                $message = "<div class='alert alert-success'><b>Donation submitted successfully!</b></div>";
                return response()->json(['status' => 300, 'message' => $message]);
            }


            // ══════════════════════════════════════════════════════════
            //  PATH 2 — STRIPE PAYMENT
            // ══════════════════════════════════════════════════════════

            if ($paymentMethod === 'stripe') {

                \Log::info('=== STRIPE PATH ===');

                if (empty($request->payment_intent_id)) {
                    return response()->json(['status' => 303, 'message' => '<div class="alert alert-danger">Payment information is missing.</div>']);
                }

                // ── Calculate stripe charge ──
                $stripeChargeAmt = $this->calcStripeCharge($totalChargeable);
                $totalChargeable = round($totalChargeable + $stripeChargeAmt, 2);

                // ── Verify with Stripe ──
                try {
                    \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                    $paymentIntent = \Stripe\PaymentIntent::retrieve($request->payment_intent_id);

                    \Log::info('PaymentIntent status: ' . $paymentIntent->status . ' | amount: ' . $paymentIntent->amount . 'p | expected: ' . round($totalChargeable * 100) . 'p');

                    if ($paymentIntent->status !== 'succeeded') {
                        return response()->json(['status' => 303, 'message' => '<div class="alert alert-danger">Payment was not completed (status: ' . $paymentIntent->status . '). Please try again.</div>']);
                    }

                    // Verify amount matches (allow 1p rounding difference)
                    $expectedPence  = (int) round($totalChargeable * 100);
                    $actualPence    = (int) $paymentIntent->amount;
                    if (abs($expectedPence - $actualPence) > 1) {
                        \Log::warning('Amount mismatch! Expected: ' . $expectedPence . 'p, Got: ' . $actualPence . 'p');
                        return response()->json(['status' => 303, 'message' => '<div class="alert alert-danger">Payment amount mismatch. Please try again.</div>']);
                    }

                } catch (\Exception $e) {
                    \Log::error('Stripe verify error: ' . $e->getMessage());
                    return response()->json(['status' => 303, 'message' => '<div class="alert alert-danger">Payment verification failed: ' . htmlspecialchars($e->getMessage()) . '</div>']);
                }

                // ── Create donation ──
                $donation = new Donation();
                $donation->charity_id       = $charityId;
                $donation->amount           = $baseAmount;
                $donation->admin_charge     = $adminChargeAmt;
                $donation->stripe_charge    = $stripeChargeAmt;
                $donation->currency         = 'GBP';
                $donation->ano_donation     = $request->ano_donation ? 'true' : 'false';
                $donation->standing_order   = 'false';
                $donation->confirm_donation = 'true';
                $donation->charitynote      = $request->charitynote;
                $donation->mynote           = $request->mynote;
                $donation->notification     = 1;
                $donation->status           = 0;
                $donation->payment_method   = 'stripe';
                $donation->stripe_payment_id = $request->payment_intent_id;

                if (auth()->check()) {
                    $donation->user_id = auth()->user()->id;
                } else {
                    $donation->user_id          = null;
                    $donation->guest_first_name = $request->first_name;
                    $donation->guest_last_name  = $request->last_name;
                    $donation->guest_email      = $request->email;
                    $donation->guest_phone      = $request->phone;
                    $donation->guest_address_1  = $request->address_line_1;
                    $donation->guest_address_2  = $request->address_line_2;
                    $donation->guest_address_3  = $request->address_line_3;
                    $donation->guest_town       = $request->town;
                    $donation->guest_postcode   = $request->postcode;
                }

                DB::beginTransaction();
                try {

                    $donation->save();
                    \Log::info('Stripe donation saved — ID: ' . $donation->id . ' | base: ' . $baseAmount . ' | admin: ' . $adminChargeAmt . ' | stripe: ' . $stripeChargeAmt);

                    $utransaction            = new Usertransaction();
                    $utransaction->t_id      = time() . '-' . ($donation->user_id ?? '000');
                    $utransaction->user_id   = $donation->user_id;
                    $utransaction->charity_id = $charityId;
                    $utransaction->donation_id = $donation->id;
                    $utransaction->t_type    = 'Out';
                    $utransaction->amount    = $totalChargeable;  // base + admin + stripe
                    $utransaction->title     = 'Online Donation (Stripe)';
                    $utransaction->status    = 1;
                    $utransaction->save();

                    $charity = Charity::find($charityId);
                    if ($charity) {
                        $charity->increment('balance', $baseAmount);  // charity gets base only
                    }

                    DB::commit();
                    \Log::info('Stripe donation committed successfully');

                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::error('Stripe donation DB error: ' . $e->getMessage());
                    return response()->json(['status' => 303, 'message' => '<div class="alert alert-danger">Something went wrong while saving. Please contact support.</div>']);
                }

                $message = "<div class='alert alert-success'><b>Donation submitted successfully! Thank you for your generosity.</b></div>";
                return response()->json(['status' => 300, 'message' => $message]);
            }

            return response()->json(['status' => 303, 'message' => '<div class="alert alert-danger">Invalid payment method.</div>']);

        } catch (\Exception $e) {
            \Log::error('UNCAUGHT EXCEPTION in onlineDonationStore: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            return response()->json([
                'status'  => 303,
                'message' => '<div class="alert alert-danger">An unexpected error occurred. Please try again.</div>'
            ]);
        }
    }


    // ─────────────────────────────────────────────────────────────
    //  SHARED EMAIL HELPER
    // ─────────────────────────────────────────────────────────────

    private function sendDonationEmails($donation, $user, $charity, $amount, $charityNote, $guestEmail = null)
    {
        try {
            $contactmail = ContactMail::where('id', 1)->first()->name;

            $recipientEmail = null;
            $recipientName  = 'Donor';

            if ($user) {
                $recipientEmail = $user->email;
                $recipientName  = $user->name;
            } elseif ($guestEmail) {
                $recipientEmail = $guestEmail;
                $recipientName  = $donation->guest_first_name . ' ' . $donation->guest_last_name;
            }

            if ($recipientEmail) {
                $array = [];
                $array['name']           = $recipientName;
                $array['cc']             = $contactmail;
                $array['client_no']      = $user ? $user->accountno : 'N/A';
                $array['amount']         = $amount;
                $array['admin_charge']   = $donation->admin_charge ?? 0;
                $array['stripe_charge']  = $donation->stripe_charge ?? 0;
                $array['charity_note']   = $charityNote;
                $array['charity_name']   = $charity ? $charity->name : 'N/A';
                $array['payment_method'] = $donation->payment_method;

                Mail::to($recipientEmail)
                    ->cc($contactmail)
                    ->send(new DonationReport($array));
            }

        } catch (\Exception $e) {
            \Log::error('Donation email failed: ' . $e->getMessage());
        }
    }
}
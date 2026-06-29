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
// use App\Mail\DonationreportCharity;
// use Barryvdh\DomPDF\Facade as PDF;

class FrontendController extends Controller
{

    // ─────────────────────────────────────────────────────────────
    //  SHOW DONATION FORM
    // ─────────────────────────────────────────────────────────────

    public function onlineDonation($charity_id = null, $amount = null)
    {
        $charityName = null;

        // If a charity_id is in the URL, find its name for the dropdown
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
        // Not logged in → must use Stripe
        if (!auth()->check()) {
            return response()->json([
                'has_balance'    => false,
                'is_logged_in'   => false,
                'available_limit' => 0,
            ]);
        }

        $user           = auth()->user();
        $availableLimit = $user->getAvailableLimit();   // balance + overdraft
        $amount         = floatval($request->amount);

        return response()->json([
            'has_balance'    => $availableLimit >= $amount && $amount > 0,
            'is_logged_in'   => true,
            'available_limit' => $availableLimit,
        ]);
    }


    // ─────────────────────────────────────────────────────────────
    //  CREATE STRIPE PAYMENT INTENT  (returns client_secret)
    // ─────────────────────────────────────────────────────────────

    public function onlineDonationCreateIntent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount'     => 'required|numeric|min:0.50|max:999999',
            'charity_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 303,
                'message' => '<div class="alert alert-danger">' . implode('<br>', $validator->errors()->all()) . '</div>',
            ]);
        }

        // Parse "id|name" format
        $parts    = explode('|', $request->charity_id);
        $charityId   = $parts[0];
        $charityName = $parts[1] ?? 'Charity';

        try {
            // \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            $amountInPence = (int) round(floatval($request->amount) * 100);

            $paymentIntent = \Stripe\PaymentIntent::create([
                'amount'   => $amountInPence,
                'currency' => 'gbp',
                'metadata' => [
                    'type'         => 'online_donation',
                    'charity_id'   => $charityId,
                    'charity_name' => $charityName,
                    'anonymous'    => $request->ano_donation ? 'yes' : 'no',
                ],
                'description' => 'Donation to ' . $charityName,
            ]);

            return response()->json([
                'status'       => 200,
                'client_secret' => $paymentIntent->client_secret,
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
        \Log::info('═══════════════════════════════════════════════');
        \Log::info('onlineDonationStore CALLED');
        \Log::info('payment_method: ' . ($request->payment_method ?? 'NULL'));
        \Log::info('charity_id: ' . ($request->charity_id ?? 'NULL'));
        \Log::info('amount: ' . ($request->amount ?? 'NULL'));
        \Log::info('payment_intent_id: ' . ($request->payment_intent_id ?? 'NULL'));
        \Log::info('confirm_donation: ' . ($request->confirm_donation ?? 'NULL'));
        \Log::info('ano_donation: ' . ($request->ano_donation ?? 'NULL'));
        \Log::info('confirm_donation: ' . ($request->confirm_donation ?? 'NULL'));
        \Log::info('is_auth: ' . (auth()->check() ? 'YES' : 'NO'));

        try {

            // ── Parse charity_id ──
            $parts      = explode('|', $request->charity_id);
            $charityId  = $parts[0] ?? null;
            $charityName = $parts[1] ?? '';

            \Log::info('Parsed charity_id: ' . $charityId);

            // ── Basic validation ──
            if (empty($charityId)) {
                \Log::warning('Validation fail: no charity_id');
                return response()->json(['status' => 303, 'message' => '<div class="alert alert-danger">Please select a charity.</div>']);
            }

            if (empty($request->amount) || floatval($request->amount) <= 0) {
                \Log::warning('Validation fail: invalid amount');
                return response()->json(['status' => 303, 'message' => '<div class="alert alert-danger">Please enter a valid donation amount.</div>']);
            }

            if (!$request->confirm_donation) {
                \Log::warning('Validation fail: condition not accepted');
                return response()->json(['status' => 303, 'message' => '<div class="alert alert-danger">Please accept the donation condition.</div>']);
            }

            $paymentMethod = $request->payment_method;
            $amount        = floatval($request->amount);

            \Log::info('Payment method: ' . $paymentMethod);


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

                \Log::info('User balance: ' . $userTransactionBalance->balance . ' + overdraft: ' . $overdraftLimit . ' = ' . $donorBalanceWithLimit);

                if ($donorBalanceWithLimit < $amount) {
                    return response()->json(['status' => 303, 'message' => '<div class="alert alert-danger">You don\'t have sufficient balance for this donation.</div>']);
                }

                $donation = new Donation();
                $donation->user_id         = $userid;
                $donation->charity_id      = $charityId;
                $donation->amount          = $amount;
                $donation->currency        = 'GBP';
                $donation->ano_donation    = $request->ano_donation ? 'true': 'false';
                $donation->standing_order  = 'false';
                $donation->confirm_donation = 'true';
                $donation->charitynote     = $request->charitynote;
                $donation->mynote          = $request->mynote;
                $donation->notification    = 1;
                $donation->status          = 0;
                $donation->payment_method  = 'balance';

                DB::beginTransaction();
                try {
                    $donation->save();
                    \Log::info('Donation saved, ID: ' . $donation->id);

                    $utransaction            = new Usertransaction();
                    $utransaction->t_id      = time() . '-' . $userid;
                    $utransaction->user_id   = $userid;
                    $utransaction->charity_id = $charityId;
                    $utransaction->donation_id = $donation->id;
                    $utransaction->t_type    = 'Out';
                    $utransaction->amount    = $amount;
                    $utransaction->title     = 'Online Donation';
                    $utransaction->status    = 1;
                    $utransaction->save();

                    $user = User::find($userid);
                    $user->decrement('balance', $amount);

                    $charity = Charity::find($charityId);
                    if ($charity) {
                        $charity->increment('balance', $amount);
                    }

                    DB::commit();
                    \Log::info('Balance donation committed successfully');

                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::error('Balance donation DB error: ' . $e->getMessage());
                    return response()->json(['status' => 303, 'message' => '<div class="alert alert-danger">Something went wrong. Please try again.</div>']);
                }

                // $this->sendDonationEmails($donation, $user, $charity, $amount, $request->charitynote);

                $message = "<div class='alert alert-success'><b>Donation submitted successfully!</b></div>";
                return response()->json(['status' => 300, 'message' => $message]);
            }


            // ══════════════════════════════════════════════════════════
            //  PATH 2 — STRIPE PAYMENT
            // ══════════════════════════════════════════════════════════

            if ($paymentMethod === 'stripe') {

                \Log::info('=== STRIPE PATH ===');

                if (empty($request->payment_intent_id)) {
                    \Log::warning('Stripe path: missing payment_intent_id');
                    return response()->json(['status' => 303, 'message' => '<div class="alert alert-danger">Payment information is missing.</div>']);
                }

                \Log::info('Verifying PaymentIntent: ' . $request->payment_intent_id);

                // ── Verify with Stripe ──
                try {
                    // \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
                    \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                    $paymentIntent = \Stripe\PaymentIntent::retrieve($request->payment_intent_id);

                    \Log::info('PaymentIntent status: ' . $paymentIntent->status);
                    \Log::info('PaymentIntent amount: ' . $paymentIntent->amount);

                    if ($paymentIntent->status !== 'succeeded') {
                        \Log::warning('PaymentIntent not succeeded, status: ' . $paymentIntent->status);
                        return response()->json(['status' => 303, 'message' => '<div class="alert alert-danger">Payment was not completed (status: ' . $paymentIntent->status . '). Please try again.</div>']);
                    }

                } catch (\Exception $e) {
                    \Log::error('Stripe verify error: ' . $e->getMessage());
                    return response()->json(['status' => 303, 'message' => '<div class="alert alert-danger">Payment verification failed: ' . htmlspecialchars($e->getMessage()) . '</div>']);
                }

                // ── Create donation ──
                $donation = new Donation();
                $donation->charity_id       = $charityId;
                $donation->amount           = $amount;
                $donation->currency         = 'GBP';
                $donation->ano_donation     = $request->ano_donation ? 'true': 'false';
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
                    \Log::info('Stripe donation for logged-in user: ' . $donation->user_id);
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
                    \Log::info('Stripe donation for guest: ' . $request->first_name . ' ' . $request->last_name . ' <' . $request->email . '>');
                }



                DB::beginTransaction();
                try {

                    $donation->save();
                    \Log::info('Stripe donation saved, ID: ' . $donation->id);

                    // ── Record transaction for BOTH logged-in and guest users ──
                    $utransaction            = new Usertransaction();
                    $utransaction->t_id      = time() . '-' . ($donation->user_id ?? '000');
                    $utransaction->user_id   = $donation->user_id;  // null for guests
                    $utransaction->charity_id = $charityId;
                    $utransaction->donation_id = $donation->id;
                    $utransaction->t_type    = 'Out';
                    $utransaction->amount    = $amount;
                    $utransaction->title     = 'Online Donation (Stripe)';
                    $utransaction->status    = 1;
                    $utransaction->save();
                    \Log::info('Transaction saved — user_id: ' . ($donation->user_id ?? 'guest'));

                    $charity = Charity::find($charityId);
                    if ($charity) {
                        $charity->increment('balance', $amount);
                        \Log::info('Charity balance incremented by ' . $amount);
                    }

                    DB::commit();
                    \Log::info('Stripe donation committed successfully');

                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::error('Stripe donation DB error: ' . $e->getMessage());
                    \Log::error('Trace: ' . $e->getTraceAsString());
                    return response()->json(['status' => 303, 'message' => '<div class="alert alert-danger">Something went wrong while saving. Please contact support.</div>']);
                }

                // ── Send emails ──
                // $user = auth()->check() ? auth()->user() : null;
                // $this->sendDonationEmails($donation, $user, $charity, $amount, $request->charitynote, $request->email ?? null);

                $message = "<div class='alert alert-success'><b>Donation submitted successfully! Thank you for your generosity.</b></div>";
                \Log::info('Returning success response');
                return response()->json(['status' => 300, 'message' => $message]);
            }

            \Log::warning('Fallback: invalid payment_method: ' . $paymentMethod);
            return response()->json(['status' => 303, 'message' => '<div class="alert alert-danger">Invalid payment method.</div>']);

        } catch (\Exception $e) {
            \Log::error('UNCAUGHT EXCEPTION in onlineDonationStore: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile() . ' Line: ' . $e->getLine());
            \Log::error('Trace: ' . $e->getTraceAsString());
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

            // ── Donor receipt email ──
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
                $array['name']         = $recipientName;
                $array['cc']           = $contactmail;
                $array['client_no']    = $user ? $user->accountno : 'N/A';
                $array['amount']       = $amount;
                $array['charity_note'] = $charityNote;
                $array['charity_name'] = $charity ? $charity->name : 'N/A';
                $array['payment_method'] = $donation->payment_method;

                Mail::to($recipientEmail)
                    ->cc($contactmail)
                    ->send(new DonationReport($array));
            }

            // ── Charity notification email (uncomment when ready) ──
            // if ($charity && $charity->email) {
            //     $pdf = PDF::loadView('invoices.donation_report_charity', compact('user', 'charity', 'donation'));
            //     $output = $pdf->output();
            //     file_put_contents(public_path() . '/invoices/Donation-report-charity#' . $charity->id . '.pdf', $output);
            //
            //     $mailArray['file']      = public_path() . '/invoices/Donation-report-charity#' . $charity->id . '.pdf';
            //     $mailArray['file_name'] = 'Donation-report-charity#' . $charity->id . '.pdf';
            //     $mailArray['cc']        = $contactmail;
            //     $mailArray['charity']   = $charity;
            //     $mailArray['user']      = $user;
            //
            //     Mail::to($charity->email)
            //         ->cc($contactmail)
            //         ->send(new DonationreportCharity($mailArray));
            // }

        } catch (\Exception $e) {
            // Log email error but don't fail the donation
            \Log::error('Donation email failed: ' . $e->getMessage());
        }
    }
}
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
use App\DTOs\OnlineDonationData;
use App\Exceptions\Donation\DonationException;
use App\Services\Donation\OnlineDonationService;
use Illuminate\Support\Facades\Log;

class FrontendController extends Controller
{

    /* ─── Charge Constants ─────────────────────────────────── */
    const FEE_PERCENT = 6;    // 6%


    /* ─── Charge Helpers ───────────────────────────────────── */

    private function calcFee($baseAmount)
    {
        return round($baseAmount * self::FEE_PERCENT / 100, 2);
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


    // ================================================================
    // Check Balance (for frontend badge)
    // ================================================================
    public function onlineDonationCheckBalance(Request $request)
    {
        if (!auth()->check()) {
            return response()->json([
                'has_balance'      => false,
                'is_logged_in'     => false,
                'available_limit'  => 0,
            ]);
        }

        $amount = floatval($request->amount);
        $result = app(OnlineDonationService::class)
            ->checkBalance(auth()->id(), $amount);

        return response()->json([
            'has_balance'      => $result['has_balance'],
            'is_logged_in'     => true,
            'available_limit'  => $result['available_limit'],
        ]);
    }



    // ================================================================
    // Create Stripe Payment Intent
    // ================================================================
    public function onlineDonationCreateIntent(Request $request)
    {
        $request->validate([
            'amount'     => 'required|numeric|min:0.50|max:999999',
            'charity_id' => 'required|string',
        ]);

        $parts     = explode('|', $request->charity_id);
        $charityId = (int) $parts[0];

        try {
            $data = OnlineDonationData::fromGuest(
                charityId:      $charityId,
                amount:         (float) $request->amount,
                isAnonymous:    (bool) $request->ano_donation,
                charityNote:    $request->charitynote,
                myNote:         $request->mynote,
                confirmDonation: true,
                paymentMethod:  'stripe',
                donorId:         auth()->check() ? auth()->id() : null,
                donorInfo:       auth()->check() ? null : [
                    'first_name'     => $request->first_name,
                    'last_name'      => $request->last_name,
                    'email'          => $request->email,
                    'phone'          => $request->phone,
                    'address_line_1' => $request->address_line_1,
                    'address_line_2' => $request->address_line_2,
                    'address_line_3' => $request->address_line_3,
                    'town'           => $request->town,
                    'postcode'       => $request->postcode,
                ],
            );

            $result = app(OnlineDonationService::class)->createStripePaymentIntent($data);

            return response()->json([
                'status'        => 200,
                'client_secret' => $result['client_secret'],
                'total_amount'  => $result['total_amount'],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 303,
                'message' => '<div class="alert alert-danger">Payment setup failed: ' . htmlspecialchars($e->getMessage()) . '</div>',
            ]);
        }
    }

    // ================================================================
    // Store Donation (handles BOTH balance & Stripe)
    // ================================================================
    public function onlineDonationStore(Request $request)
    {
        try {
            $parts     = explode('|', $request->charity_id);
            $charityId = (int) ($parts[0] ?? 0);

            $paymentMethod = $request->payment_method;
            $baseAmount    = (float) $request->amount;

            // Build donor info for guests
            $donorInfo = null;
            if (!auth()->check()) {
                $donorInfo = [
                    'first_name'     => $request->first_name,
                    'last_name'      => $request->last_name,
                    'email'          => $request->email,
                    'phone'          => $request->phone,
                    'address_line_1' => $request->address_line_1,
                    'address_line_2' => $request->address_line_2,
                    'address_line_3' => $request->address_line_3,
                    'town'           => $request->town,
                    'postcode'       => $request->postcode,
                ];
            }

            $data = OnlineDonationData::fromGuest(
                charityId:      $charityId,
                amount:         $baseAmount,
                isAnonymous:    (bool) $request->ano_donation,
                charityNote:    $request->charitynote,
                myNote:         $request->mynote,
                confirmDonation: (bool) $request->confirm_donation,
                paymentMethod:   $paymentMethod,
                stripePaymentIntentId: $request->payment_intent_id,
                donorInfo:       $donorInfo,
                donorId:         auth()->check() ? auth()->id() : null,
            );

            $donation = app(OnlineDonationService::class)->createOneTimeDonation($data);

            $message = "<div class='alert alert-success'><b>Donation submitted successfully!</b></div>";
            return response()->json(['status' => 300, 'message' => $message]);

        } catch (DonationException $e) {
            $message = "<div class='alert alert-danger'><b>{$e->getUserMessage()}</b></div>";
            return response()->json(['status' => 303, 'message' => $message]);
        } catch (\Throwable $e) {
            Log::error('Guest donation failed: ' . $e->getMessage());
            return response()->json([
                'status'  => 303,
                'message' => "<div class='alert alert-danger'><b>An unexpected error occurred.</b></div>"
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
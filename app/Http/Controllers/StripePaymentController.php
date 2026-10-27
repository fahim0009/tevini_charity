<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Donation;
use App\Models\StripeTopup;
use App\Models\Fundraiser;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class StripePaymentController extends Controller
{
    public function stripe()
    {
        return view('frontend.user.strip_topup');
    }

    /**
     * Web topup - creates PaymentIntent and returns client_secret
     */
    public function stripePost(Request $request)
    {
        $amt = $request->amount;

        // FIX: Integer-pence validation (no float arithmetic issues)
        $amountInPence = (int) round($amt * 100);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $paymentIntent = PaymentIntent::create([
            'amount' => $amountInPence, // FIX: Use integer pence
            'currency' => 'GBP',
            'payment_method' => $request->input('payment_method_id'),
            "description" => "Tevini donor topup",
            'confirm' => true,
            'return_url' => url()->current(),
            'automatic_payment_methods' => [
                'enabled' => true,
                'allow_redirects' => 'never',
            ],
            'metadata' => [
                'donor_id' => Auth::user()->id,
                'purpose' => 'wallet_topup',
                'intended_amount_pence' => $amountInPence, // FIX: Store intended amount
            ],
        ]);

        $stripetopup = new StripeTopup();
        $stripetopup->donor_id = Auth::user()->id;
        $stripetopup->amount = $amt;
        $stripetopup->stripe_payment_intent_id = $paymentIntent->id;
        $stripetopup->token = time();
        $stripetopup->description = "Tevini donor topup";
        $stripetopup->notification = "1";
        $stripetopup->status = "0";
        $stripetopup->save();

        return response()->json([
            'client_secret' => $paymentIntent->client_secret,
        ]);
    }

    public function stripetopupstatus(Request $request)
    {
        $data = StripeTopup::find($request->id);
        $data->status = 1;
        if ($data->save()) {
            $message = "<div class='alert alert-success'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><b>Active Successfully.</b></div>";
            return response()->json(['status' => 300, 'message' => $message]);
        }
    }

    public function stripetopUpPost2(Request $request)
    {
        $amt = $request->amount;

        $stripetopup = new StripeTopup();
        $stripetopup->donor_id = $request->donor_id;
        $stripetopup->amount = $amt;
        $stripetopup->token = time();
        $stripetopup->description = "Tevini donor topup";
        $stripetopup->notification = "1";
        $stripetopup->status = "1";
        $stripetopup->save();

        $success['message'] = 'Top up store successfully.';
        $success['data'] = $stripetopup;
        return response()->json(['success' => true, 'response' => $success], 200);
    }

    /**
     * Mobile App topup - verifies payment with Stripe then saves record
     * FIX: Integer-pence validation
     * FIX: Graceful duplicate handling
     * FIX: Use Stripe amount as source of truth
     */
    public function stripetopUpPost(Request $request)
    {
        $user = Auth::user();
        $paymentIntentId = $request->payment_intent_id;
        $amt = $request->amount;

        if (!$paymentIntentId) {
            return response()->json([
                'success' => false,
                'message' => 'Payment intent ID is required.'
            ], 422);
        }

        // FIX: Check for duplicate BEFORE Stripe API call (graceful idempotency)
        $existingTopup = StripeTopup::where('stripe_payment_intent_id', $paymentIntentId)->first();
        if ($existingTopup) {
            Log::info('Topup duplicate request - returning existing record', [
                'payment_intent_id' => $paymentIntentId,
                'existing_topup_id' => $existingTopup->id,
                'user_id' => $user->id,
            ]);
            return response()->json([
                'success' => true,
                'response' => [
                    'message' => 'Top up already processed.',
                    'data' => $existingTopup
                ]
            ], 200);
        }

        // Verify payment with Stripe
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $intent = PaymentIntent::retrieve($paymentIntentId);

            // 1. Check payment status
            if ($intent->status !== 'succeeded') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not completed on Stripe.'
                ], 400);
            }

            // 2. Verify ownership
            if (!isset($intent->metadata['donor_id']) || 
                $intent->metadata['donor_id'] != $user->id) {
                Log::error('Topup ownership mismatch', [
                    'user_id' => $user->id,
                    'intent_donor_id' => $intent->metadata['donor_id'] ?? 'null',
                    'intent_id' => $paymentIntentId,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Payment ownership verification failed.'
                ], 403);
            }

            // 3. Verify purpose
            if (!isset($intent->metadata['purpose']) || 
                $intent->metadata['purpose'] !== 'wallet_topup') {
                Log::error('Topup purpose mismatch', [
                    'user_id' => $user->id,
                    'purpose' => $intent->metadata['purpose'] ?? 'null',
                    'intent_id' => $paymentIntentId,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid payment purpose.'
                ], 403);
            }

            // 4. Verify currency is GBP
            if (strtolower($intent->currency) !== 'gbp') {
                Log::error('Topup currency mismatch', [
                    'user_id' => $user->id,
                    'currency' => $intent->currency,
                    'intent_id' => $paymentIntentId,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid currency. Only GBP is accepted.'
                ], 400);
            }

            // 5. FIX: Integer-pence comparison (no float tolerance)
            $clientAmountInPence = (int) round($amt * 100);
            $stripeAmountInPence = (int) $intent->amount;

            if ($clientAmountInPence !== $stripeAmountInPence) {
                Log::error('Topup amount mismatch', [
                    'user_id' => $user->id,
                    'client_amount_pence' => $clientAmountInPence,
                    'stripe_amount_pence' => $stripeAmountInPence,
                    'intent_id' => $paymentIntentId,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Amount mismatch.'
                ], 400);
            }

            // 6. FIX: Verify against metadata intended amount if present
            if (isset($intent->metadata['intended_amount_pence'])) {
                $intendedAmountInPence = (int) $intent->metadata['intended_amount_pence'];
                if ($stripeAmountInPence !== $intendedAmountInPence) {
                    Log::error('Topup intended amount mismatch', [
                        'user_id' => $user->id,
                        'intended_amount_pence' => $intendedAmountInPence,
                        'stripe_amount_pence' => $stripeAmountInPence,
                        'intent_id' => $paymentIntentId,
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Amount verification failed.'
                    ], 400);
                }
            }
        } catch (\Exception $e) {
            Log::error('Stripe verification failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed.'
            ], 500);
        }

        // Payment verified - save record with Stripe amount as truth
        try {
            $stripetopup = new StripeTopup();
            $stripetopup->donor_id = $user->id;
            // FIX: Use Stripe amount (in pounds) as source of truth, not client amount
            $stripetopup->amount = $stripeAmountInPence / 100;
            $stripetopup->stripe_payment_intent_id = $paymentIntentId;
            $stripetopup->token = time();
            $stripetopup->description = "Tevini donor topup";
            $stripetopup->notification = "1";
            $stripetopup->status = "1";
            $stripetopup->save();
        } catch (\Illuminate\Database\QueryException $e) {
            // FIX: Handle unique constraint violation (race condition)
            if ($e->errorInfo[1] == 1062) {
                Log::info('Topup duplicate caught by unique constraint', [
                    'payment_intent_id' => $paymentIntentId,
                ]);
                $existingTopup = StripeTopup::where('stripe_payment_intent_id', $paymentIntentId)->first();
                return response()->json([
                    'success' => true,
                    'response' => [
                        'message' => 'Top up already processed.',
                        'data' => $existingTopup
                    ]
                ], 200);
            }
            throw $e;
        }

        $success['message'] = 'Top up store successfully.';
        $success['data'] = $stripetopup;
        return response()->json(['success' => true, 'response' => $success], 200);
    }

    /**
     * Create a Payment Intent on the server side.
     * FIX: Only GBP accepted
     * FIX: Integer-pence amount
     */
    public function createPaymentIntent(Request $request)
    {
        // FIX: Only GBP accepted
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|in:GBP',
        ]);

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $user = Auth::user();

            // FIX: Integer-pence with round to avoid float issues
            $amountInPence = (int) round($request->amount * 100);

            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInPence,
                'currency' => 'gbp', // FIX: Always lowercase gbp
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
                'metadata' => [
                    'donor_id' => $user->id,
                    'purpose' => 'wallet_topup',
                    'user_email' => $user->email,
                    'intended_amount_pence' => $amountInPence, // FIX: Store for verification
                ],
            ]);

            Log::info('PaymentIntent created', [
                'user_id' => $user->id,
                'amount_pence' => $amountInPence,
                'intent_id' => $paymentIntent->id,
            ]);

            return response()->json([
                'success' => true,
                'response' => [
                    'client_secret' => $paymentIntent->client_secret,
                    'payment_intent_id' => $paymentIntent->id,
                    'amount' => $request->amount,
                    'currency' => 'GBP',
                ]
            ], 200);
        } catch (\Exception $e) {
            Log::error('PaymentIntent creation failed', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment intent: ' . $e->getMessage()
            ], 500);
        }
    }
}
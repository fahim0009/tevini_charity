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

class StripePaymentController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripe()
    {
        return view('frontend.user.strip_topup');
    }

    /**
     * Web topup - creates PaymentIntent and returns client_secret
     * FIX: Removed hardcoded Stripe key, using env() instead
     */
    public function stripePost(Request $request)
    {
        $amt = $request->amount;

        Stripe::setApiKey(env('STRIPE_SECRET'));

        // FIX: Removed 'confirmation_method' as it conflicts with automatic_payment_methods
        $paymentIntent = PaymentIntent::create([
            'amount' => $amt * 100,
            'currency' => 'GBP',
            'payment_method' => $request->input('payment_method_id'),
            "description" => "Tevini donor topup",
            'confirm' => true,
            'return_url' => url()->current(),
            'automatic_payment_methods' => [
                'enabled' => true,
                'allow_redirects' => 'never',
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

    /**
     * Unused - kept for compatibility
     */
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
     * FIX: Removed hardcoded Stripe key, using env() instead
     * FIX: Get donor_id from Auth, not from request
     * FIX: Verify payment with Stripe API before saving
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

        // FIX: Use env() instead of hardcoded key
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $intent = PaymentIntent::retrieve($paymentIntentId);

            if ($intent->status !== 'succeeded') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not completed on Stripe.'
                ], 400);
            }

            // Verify amount matches
            $paidAmount = $intent->amount / 100; // Convert from pence
            if (abs($paidAmount - $amt) > 0.01) {
                Log::error('Topup amount mismatch', [
                    'user_id' => $user->id,
                    'expected' => $amt,
                    'paid' => $paidAmount,
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Amount mismatch.'
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Stripe verification failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed.'
            ], 500);
        }

        // Payment verified - save record
        $stripetopup = new StripeTopup();
        $stripetopup->donor_id = $user->id;
        $stripetopup->amount = $amt;
        $stripetopup->stripe_payment_intent_id = $paymentIntentId;
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
     * Create a Payment Intent on the server side.
     * Secret key stays on server, never exposed to app.
     */
    public function createPaymentIntent(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|in:GBP,USD,EUR',
        ]);

        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $paymentIntent = PaymentIntent::create([
                'amount' => (int)($request->amount * 100),
                'currency' => strtolower($request->currency),
                // FIX: Use automatic_payment_methods instead of payment_method_types
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never', // Disable redirect-based methods
                ],
            ]);

            Log::info('PaymentIntent created', [
                'user_id' => auth()->id(),
                'amount' => $request->amount,
                'intent_id' => $paymentIntent->id,
            ]);

            return response()->json([
                'success' => true,
                'response' => [
                    'client_secret' => $paymentIntent->client_secret,
                    'payment_intent_id' => $paymentIntent->id,
                    'amount' => $request->amount,
                    'currency' => $request->currency,
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
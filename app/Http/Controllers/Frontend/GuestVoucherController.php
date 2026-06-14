<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Models\VoucherCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Webhook;

class GuestVoucherController extends Controller
{
    /**
     * Show the voucher book order page
     */
    public function orderVoucherBooks()
    {
        if (auth()->check()) {
            $cart = VoucherCart::where('user_id', auth()->user()->id)->get();
        } else {
            $cart = collect(Session::get('guest_voucher_cart', []));
        }

        return view('frontend.voucherBook', compact('cart'));
    }

    /**
     * Add to cart or remove from cart
     * Auth users → VoucherCart DB table
     * Guest users → Session storage
     */
    public function storeCart(Request $request)
    {
        // ==========================================
        // REMOVE ITEM
        // ==========================================
        if ($request->cartid) {
            if (auth()->check()) {
                // Auth: delete from DB
                VoucherCart::destroy($request->cartid);
            } else {
                // Guest: remove from session
                $cart = Session::get('guest_voucher_cart', []);
                $cart = array_filter($cart, function ($item) use ($request) {
                    return $item['id'] != $request->cartid;
                });
                Session::put('guest_voucher_cart', array_values($cart));
            }

            return response()->json([
                'success' => true,
                'message' => '<div class="alert alert-success"><b>Removed from basket.</b></div>'
            ]);
        }

        // ==========================================
        // ADD / UPDATE ITEM
        // ==========================================
        if (auth()->check()) {
            // ------------------------------------------
            // AUTHENTICATED USER → Database
            // ------------------------------------------
            $existing = VoucherCart::where('user_id', auth()->user()->id)
                ->where('voucher_id', $request->voucherID)
                ->first();

            if ($existing) {
                // Increment qty
                $existing->qty += 1;
                $existing->tamount = $existing->qty * $request->v_amount;
                $existing->save();

                $message = "<div class='alert alert-success'><b>Basket updated successfully.</b></div>";
                return response()->json(['status' => 300, 'message' => $message]);
            } else {
                // New item
                $data = new VoucherCart();
                $data->user_id = auth()->user()->id;
                $data->qty = $request->quantity;
                $data->number_voucher = $request->single_amount;
                $data->voucher_id = $request->voucherID;
                $data->amount = $request->v_amount;
                $data->tamount = $request->quantity * $request->v_amount;
                $data->save();

                $message = "<div class='alert alert-success'><b>Added to basket successfully.</b></div>";
                return response()->json(['status' => 300, 'message' => $message, 'newID' => $data->id]);
            }

        } else {
            // ------------------------------------------
            // GUEST USER → Session
            // ------------------------------------------
            $cart = Session::get('guest_voucher_cart', []);

            $existingIndex = null;
            foreach ($cart as $index => $item) {
                if ($item['voucher_id'] == $request->voucherID) {
                    $existingIndex = $index;
                    break;
                }
            }

            if ($existingIndex !== null) {
                // Increment qty
                $cart[$existingIndex]['qty'] += 1;
                $cart[$existingIndex]['tamount'] = $cart[$existingIndex]['qty'] * $request->v_amount;
                Session::put('guest_voucher_cart', $cart);

                $message = "<div class='alert alert-success'><b>Basket updated successfully.</b></div>";
                return response()->json(['status' => 300, 'message' => $message]);
            } else {
                // New item
                $newId = 'guest_' . time() . '_' . rand(100, 999);
                $cart[] = [
                    'id'            => $newId,
                    'voucher_id'    => $request->voucherID,
                    'qty'           => $request->quantity,
                    'number_voucher' => $request->single_amount,
                    'amount'        => $request->v_amount,
                    'tamount'       => $request->quantity * $request->v_amount,
                ];
                Session::put('guest_voucher_cart', $cart);

                $message = "<div class='alert alert-success'><b>Added to basket successfully.</b></div>";
                return response()->json(['status' => 300, 'message' => $message, 'newID' => $newId]);
            }
        }
    }


    public function createPaymentIntent(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $amount = floatval($request->amount);
        $amountPence = (int)round($amount * 100);

        if ($amountPence < 30) { // Stripe minimum is £0.30
            return response()->json([
                'error' => ['message' => 'Minimum order amount is £0.30']
            ], 400);
        }

        $paymentIntent = PaymentIntent::create([
            'amount'   => $amountPence,
            'currency' => 'gbp',
            'metadata' => [
                'type'       => 'voucher_book_order',
                'user_id'    => auth()->check() ? auth()->id() : 'guest',
                'session_id' => session()->getId(),
            ],
        ]);

        return response()->json([
            'client_secret' => $paymentIntent->client_secret,
        ]);
    }


    public function voucherCartHandleWebhook(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
        $payload = $request->getContent();
        $sig = $request->header('Stripe-Signature');

        $event = Webhook::constructEvent($payload, $sig, $endpoint_secret);

        if ($event->type === 'payment_intent.succeeded') {
            $intent = $event->data->object;
            $metadata = $intent->metadata;

            if ($metadata->type === 'voucher_book_order') {
                // Create order in DB
                // Clear cart (DB or session)
                // Send email
            }
        }

        return response()->json(['status' => 'success']);
    }


}
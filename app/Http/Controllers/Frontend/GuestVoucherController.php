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

        /**
     * Temporarily store order data in session so the Webhook can read it after Stripe payment
     */
    public function storePendingOrderData(Request $request)
    {
        Session::put('pending_voucher_order', $request->order_data);
        Session::put('pending_voucher_fee', $request->fee_amount);
        
        return response()->json(['success' => true]);
    }


    /**
     * Stripe Webhook: Creates the order after successful payment
     */
    public function voucherCartHandleWebhook(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');
        $payload = $request->getContent();
        $sig = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sig, $endpoint_secret);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type === 'payment_intent.succeeded') {
            $intent = $event->data->object;
            $metadata = $intent->metadata;

            if (isset($metadata->type) && $metadata->type === 'voucher_book_order') {
                
                // 1. Reconstruct the user's session using the session_id saved in Stripe metadata
                $sessionId = $metadata->session_id;
                Session::setId($sessionId);
                Session::start();

                $orderData = Session::get('pending_voucher_order');
                $feeAmount = Session::get('pending_voucher_fee', 0);

                if (!$orderData) {
                    \Log::error('Voucher Webhook: No pending order data found for session: ' . $sessionId);
                    return response()->json(['status' => 'error']);
                }

                $voucher_ids = $orderData['voucherIds'];
                $qtys = $orderData['qtys'];
                $did = $orderData['did'];
                $delivery = $orderData['delivery'];
                $collection = $orderData['collection'];
                $delivery_charge = $orderData['delivery_charge'];
                $donorInfo = $orderData['donor_info'];

                $prepaid_amount = 0;

                // Validation: Check stock
                foreach($qtys as $key => $qty) {
                    $voucher = \App\Models\Voucher::where('id', $voucher_ids[$key])->first();
                    if ($qty > $voucher->stock) {
                        \Log::error('Voucher Webhook: Stock exceeded for voucher ID ' . $voucher_ids[$key]);
                        return response()->json(['status' => 'error', 'message' => 'Stock exceeded']);
                    }
                }

                // Delivery Option
                if ($delivery == "true") {
                    $delivery_opt = "Delivery";
                } elseif ($collection == "true") {
                    $delivery_opt = "Collection";
                } else {
                    $delivery_opt = null;
                }

                // Calculate prepaid amount
                foreach ($voucher_ids as $key => $id) {
                    $voucher = \App\Models\Voucher::where('id', $id)->first();
                    $prepaid_amount += $voucher->amount * $qtys[$key];
                }

                // Create Order
                $order = new \App\Models\Order();
                $order->user_id = $did;
                $order->order_id = time() . "-" . ($did ?: 'guest');
                $order->amount = $prepaid_amount + $delivery_charge + $feeAmount; // Base + Delivery + 6% Fee
                $order->delivery_charge = $delivery_charge;
                $order->delivery_option = $delivery_opt;
                $order->notification = 1;
                $order->status = 0;
                $order->save();

                // Create Order History & Decrement Stock
                foreach ($voucher_ids as $key => $voucher_id) {
                    if ($qtys[$key] != "0") {
                        $voucherDtl = \App\Models\Voucher::find($voucher_id);
                        $amount = $voucherDtl->amount;

                        if ($qtys[$key] > "1") {
                            for ($x = 0; $x < $qtys[$key]; $x++) {
                                $unique = time() . rand(1, 100);
                                \App\Models\OrderHistory::create([
                                    'order_id' => $order->id,
                                    'voucher_id' => $voucher_id,
                                    'number_voucher' => 1,
                                    'amount' => $amount,
                                    'o_unq' => $unique,
                                    'status' => "0",
                                ]);
                            }
                        } else {
                            $unique = time() . rand(1, 100);
                            \App\Models\OrderHistory::create([
                                'order_id' => $order->id,
                                'voucher_id' => $voucher_id,
                                'number_voucher' => $qtys[$key],
                                'amount' => $qtys[$key] * $amount,
                                'o_unq' => $unique,
                                'status' => "0",
                            ]);
                        }

                        // Decrement stock
                        $voucherDtl->decrement('stock', $qtys[$key]);
                    }
                }

                // Clear Cart
                if ($did) {
                    \App\Models\VoucherCart::where('user_id', $did)->delete();
                } else {
                    Session::forget('guest_voucher_cart');
                }

                // Send Email
                try {
                    $contactmail = \App\Models\ContactMail::where('id', 1)->first()->name;
                    $email = $donorInfo['email'];
                    $name = $donorInfo['first_name'] . ' ' . $donorInfo['last_name'];

                    $array['subject'] = 'Voucher books order confirmation';
                    $array['from'] = 'info@tevini.co.uk';
                    $array['cc'] = $contactmail;
                    $array['name'] = $name;
                    $array['client_no'] = $did ? \App\Models\User::find($did)->accountno ?? 'N/A' : 'Guest';
                    $array['order_id'] = $order->id;
                    $array['orderid'] = $order->order_id;
                    $array['delivery_option'] = $delivery_opt;

                    \Mail::send('mail.order', compact('array'), function ($message) use ($array, $email) {
                        $message->from($array['from'], 'Tevini.co.uk');
                        $message->to($email)->cc($array['cc'])->subject($array['subject']);
                    });
                } catch (\Exception $e) {
                    \Log::error('Voucher Webhook Email Error: ' . $e->getMessage());
                }

                // Cleanup session data
                Session::forget('pending_voucher_order');
                Session::forget('pending_voucher_fee');
                Session::save();
            }
        }

        return response()->json(['status' => 'success']);
    }


}
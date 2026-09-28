<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\PaymentLog; 
use App\DTOs\VoucherBookOrderData;
use App\Exceptions\VoucherOrder\VoucherOrderException;
use App\Services\VoucherBook\VoucherBookOrderService;
use App\Models\{Voucher, VoucherCart, Order};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Log, Session, Mail};
use Stripe\{Stripe, PaymentIntent, Webhook};


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


    // ================================================================
    // PAYMENT INTENT — Calculate amount SERVER-SIDE (security fix)
    // ================================================================
    public function createPaymentIntent(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // 1. Get cart items from session/DB
            $cartItems = $this->getCartItems();

            if (empty($cartItems)) {
                return response()->json(['error' => ['message' => 'Cart is empty']], 400);
            }

            // 2. Build DTO from cart (no amount from frontend!)
            $data = $this->buildDtoFromCart($cartItems, $request);

            // 3. Calculate total SERVER-SIDE (including 6% fee)
            $service = app(VoucherBookOrderService::class);
            $totals  = $service->calculateOrderTotal($data, withFee: true);

            $amountPence = (int) round($totals['total'] * 100);

            if ($amountPence < 30) {
                return response()->json(['error' => ['message' => 'Minimum order amount is £0.30']], 400);
            }

            // 4. Create PaymentIntent with server-calculated amount
            $paymentIntent = PaymentIntent::create([
                'amount'   => $amountPence,
                'currency' => 'gbp',
                'metadata' => [
                    'type'       => 'voucher_book_order',
                    'user_id'    => auth()->check() ? (string) auth()->id() : 'guest',
                    'session_id' => session()->getId(),
                ],
            ]);

            // 5. Store pending order data in session for webhook fallback
            Session::put('pending_voucher_order', [
                'voucherIds'     => array_column($cartItems, 'voucher_id'),
                'qtys'           => array_column($cartItems, 'qty'),
                'did'            => auth()->check() ? auth()->id() : null,
                'delivery'       => $data->isDelivery ? 'true' : 'false',
                'collection'      => $data->isCollection ? 'true' : 'false',
                'donor_info'     => $data->donorInfo,
            ]);
            Session::put('pending_voucher_fee', $totals['fee']);

            return response()->json([
                'client_secret' => $paymentIntent->client_secret,
                'amount'         => $totals['total'],
                'base_amount'    => $totals['base_amount'],
                'fee'            => $totals['fee'],
            ]);

        } catch (VoucherOrderException $e) {
            return response()->json(['error' => ['message' => $e->getUserMessage()]], 400);
        } catch (\Exception $e) {
            Log::error('PaymentIntent creation failed: ' . $e->getMessage());
            return response()->json(['error' => ['message' => 'Could not create payment intent']], 500);
        }
    }

    /**
     * Temporarily store order data in session
     * (Keep your existing implementation)
     */
    public function storePendingOrderData(Request $request)
    {
        Session::put('pending_voucher_order', $request->order_data);
        Session::put('pending_voucher_fee', $request->fee_amount);
        return response()->json(['success' => true]);
    }

    // ================================================================
    // PAYMENT SUCCESS — Alternative to webhook
    // ================================================================

    public function paymentSuccess(Request $request)
    {
        $request->validate(['payment_intent_id' => 'required|string']);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            // 1. Retrieve PaymentIntent from Stripe
            $intent = PaymentIntent::retrieve($request->payment_intent_id);

            if ($intent->status !== 'succeeded') {
                return response()->json(['error' => 'Payment not completed successfully'], 400);
            }

            // 2. Get pending order data from session
            $orderData = Session::get('pending_voucher_order');
            $feeAmount = Session::get('pending_voucher_fee', 0);

            if (!$orderData) {
                return response()->json(['error' => 'Pending order data missing'], 404);
            }

            // 3. Build DTO
            $data = VoucherBookOrderData::fromGuestRequest(
                donorId:      $orderData['did'] ?? null,
                voucherIds:   $orderData['voucherIds'],
                qtys:         $orderData['qtys'],
                isDelivery:   ($orderData['delivery'] ?? 'false') === 'true',
                isCollection: ($orderData['collection'] ?? 'false') === 'true',
                donorInfo:    $orderData['donor_info'] ?? null,
                platformFee:  $feeAmount,
            );

            // 4. Create order via Service (handles idempotency + transaction)
            $paidAmount = $intent->amount / 100;
            $service = app(VoucherBookOrderService::class);
            $order = $service->createOrderFromStripePayment($data, $intent->id, $paidAmount);

            // 5. Clear cart
            $this->clearCart($data->donorId);

            // 6. Cleanup session
            Session::forget('pending_voucher_order');
            Session::forget('pending_voucher_fee');

            return response()->json([
                'success'  => true,
                'message'  => 'Order placed successfully',
                'order_id' => $order->id,
            ]);

        } catch (VoucherOrderException $e) {
            return response()->json(['error' => $e->getUserMessage()], 400);
        } catch (\Exception $e) {
            Log::error('Payment verification failed: ' . $e->getMessage());
            return response()->json(['error' => 'Could not verify payment'], 500);
        }
    }

    // ================================================================
    // STRIPE WEBHOOK — Fallback for payment_intent.succeeded
    // ================================================================

    public function voucherCartHandleWebhook(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');
        $payload = $request->getContent();
        $sig = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sig, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        if ($event->type !== 'payment_intent.succeeded') {
            return response()->json(['status' => 'ignored']);
        }

        $intent = $event->data->object;
        $metadata = $intent->metadata;

        if (!isset($metadata->type) || $metadata->type !== 'voucher_book_order') {
            return response()->json(['status' => 'ignored']);
        }

        // Idempotency: check if order already created by paymentSuccess route
        $existing = Order::where('stripe_payment_intent_id', $intent->id)->first();
        if ($existing) {
            return response()->json(['status' => 'already_processed']);
        }

        // Reconstruct session
        $sessionId = $metadata->session_id;
        Session::setId($sessionId);
        Session::start();

        $orderData = Session::get('pending_voucher_order');
        $feeAmount = Session::get('pending_voucher_fee', 0);

        if (!$orderData) {
            Log::error('Webhook: No pending order data for session: ' . $sessionId);
            return response()->json(['status' => 'error', 'message' => 'No pending data']);
        }

        try {
            $data = VoucherBookOrderData::fromGuestRequest(
                donorId:      $orderData['did'] ?? null,
                voucherIds:   $orderData['voucherIds'],
                qtys:         $orderData['qtys'],
                isDelivery:   ($orderData['delivery'] ?? 'false') === 'true',
                isCollection: ($orderData['collection'] ?? 'false') === 'true',
                donorInfo:    $orderData['donor_info'] ?? null,
                platformFee:  $feeAmount,
            );

            $paidAmount = $intent->amount / 100;
            $service = app(VoucherBookOrderService::class);
            $order = $service->createOrderFromStripePayment($data, $intent->id, $paidAmount);

            // Clear cart
            $this->clearCart($data->donorId);

            // Cleanup session
            Session::forget('pending_voucher_order');
            Session::forget('pending_voucher_fee');
            Session::save();

            return response()->json(['status' => 'success', 'order_id' => $order->id]);

        } catch (VoucherOrderException $e) {
            Log::error('Webhook order creation failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getUserMessage()]);
        } catch (\Exception $e) {
            Log::error('Webhook order creation failed: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // ================================================================
    // PRIVATE HELPERS
    // ================================================================

    /**
     * Get cart items from DB (auth) or Session (guest)
     */
    private function getCartItems(): array
    {
        if (auth()->check()) {
            return VoucherCart::where('user_id', auth()->id())
                ->get()
                ->map(fn($item) => [
                    'voucher_id' => $item->voucher_id,
                    'qty'        => $item->qty,
                    'amount'     => $item->amount,
                    'tamount'    => $item->tamount,
                ])
                ->toArray();
        }

        return collect(Session::get('guest_voucher_cart', []))
            ->map(fn($item) => [
                'voucher_id' => $item['voucher_id'],
                'qty'        => $item['qty'],
                'amount'     => $item['amount'],
                'tamount'    => $item['tamount'],
            ])
            ->toArray();
    }

    /**
     * Build DTO from cart items
     */
    private function buildDtoFromCart(array $cartItems, Request $request): VoucherBookOrderData
    {
        $donorInfo = null;

        if (!auth()->check()) {
            $donorInfo = [
                'first_name'     => $request->input('first_name'),
                'last_name'      => $request->input('last_name'),
                'email'          => $request->input('email'),
                'phone'          => $request->input('phone'),
                'address_line_1' => $request->input('address_line_1'),
                'address_line_2' => $request->input('address_line_2'),
                'town'           => $request->input('town'),
                'postcode'       => $request->input('postcode'),
            ];
        }

        return VoucherBookOrderData::fromGuestRequest(
            donorId:      auth()->check() ? auth()->id() : null,
            voucherIds:   array_column($cartItems, 'voucher_id'),
            qtys:         array_column($cartItems, 'qty'),
            isDelivery:   $request->boolean('delivery'),
            isCollection: $request->boolean('collection'),
            donorInfo:    $donorInfo,
        );
    }

    /**
     * Clear cart (DB for auth, Session for guest)
     */
    private function clearCart(?int $userId): void
    {
        if ($userId) {
            VoucherCart::where('user_id', $userId)->delete();
        } else {
            Session::forget('guest_voucher_cart');
        }
    }




}
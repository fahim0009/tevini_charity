<?php

namespace App\Services\VoucherBook;

use App\DTOs\VoucherBookOrderData;
use App\Exceptions\VoucherOrder\{
    DeliveryOptionException,
    InsufficientBalanceException,
    NoVoucherSelectedException,
    StockExceededException,
    VoucherOrderException
};
use App\Models\{Order, OrderHistory, User, Usertransaction, Voucher, VoucherCart, ContactMail};
use Illuminate\Support\Facades\{DB, Log, Mail};

class VoucherBookOrderService
{
    private const DELIVERY_CHARGE       = 3.50;
    private const FREE_DELIVERY_THRESHOLD = 200.00;

    /**
     * Voucher ID => Mixed leaves mapping
     * 176: booklets with smaller denominations
     * others: standard booklets
     */
    private const MIXED_LEAVES_MAP = [
        176 => ["3", "5", "10", "18"],
    ];
    private const DEFAULT_MIXED_LEAVES = ["20", "25", "36", "50", "72"];

    /**
     * Create a new Voucher Book Order
     *
     * @throws VoucherOrderException on any business rule violation
     */
    public function createOrder(VoucherBookOrderData $data): Order
    {
        // ---------- Phase 1: Validation & Calculation ----------
        $validation = $this->validateAndCalculate($data);

        // ---------- Phase 2: Balance Check ----------
        $this->ensureSufficientBalance($data->donorId, $validation['amount_to_deduct']);

        // ---------- Phase 3: Persist (Transaction) ----------
        $order = DB::transaction(function () use ($data, $validation) {
            return $this->persistOrder($data, $validation);
        });

        // ---------- Phase 4: Side Effects (email) ----------
        $this->sendOrderConfirmationEmail($order, $data->donorId, $validation['delivery_option']);

        return $order;
    }

    /**
     * Update an existing order (admin edit / donor edit)
     */
    public function updateOrder(int $orderId, VoucherBookOrderData $data): Order
    {
        $validation = $this->validateAndCalculate($data);
        $this->ensureSufficientBalance($data->donorId, $validation['amount_to_deduct']);

        return DB::transaction(function () use ($orderId, $data, $validation) {
            $order = Order::findOrFail($orderId);

            // Reverse previous balance deduction
            if ($order->amount > 0) {
                User::where('id', $order->user_id)->increment('balance', $order->amount);
            }

            // Restore previous voucher stock + delete old histories
            foreach ($order->orderhistories as $oh) {
                if ($oh->voucher_id) {
                    Voucher::where('id', $oh->voucher_id)
                        ->increment('stock', $oh->number_voucher);
                }
                $oh->delete();
            }

            // Mark old transactions as cancelled
            Usertransaction::where('order_id', $orderId)->update(['status' => 0]);

            // Persist new state
            return $this->persistOrder($data, $validation, $order);
        });
    }

    // ====================================================================
    // PRIVATE HELPERS
    // ====================================================================

    /**
     * Validate input + Calculate amounts + Load vouchers
     */
    private function validateAndCalculate(VoucherBookOrderData $data): array
    {
        $voucherIds = array_column($data->items, 'voucher_id');
        $vouchers    = Voucher::whereIn('id', $voucherIds)->get()->keyBy('id');

        $prepaidAmount = 0;
        $orderTotal    = 0;
        $allZero       = true;
        $validatedItems = [];

        foreach ($data->items as $item) {
            $voucher = $vouchers->get($item['voucher_id']);
            if (!$voucher) continue;

            $qty = (int)$item['qty'];
            if ($qty <= 0) continue;

            $allZero = false;

            // Stock check
            if ($qty > $voucher->stock) {
                throw new StockExceededException((float)$voucher->amount);
            }

            $lineTotal = $voucher->amount * $qty;
            $orderTotal += $lineTotal;

            if ($voucher->type === 'Prepaid') {
                $prepaidAmount += $lineTotal;
            }

            $validatedItems[] = [
                'voucher' => $voucher,
                'qty'     => $qty,
            ];
        }

        if ($allZero) {
            throw new NoVoucherSelectedException();
        }

        if (!$data->isDelivery && !$data->isCollection) {
            throw new DeliveryOptionException();
        }

        // Delivery option + charge
        $deliveryOption = $data->isDelivery ? 'Delivery' : 'Collection';
        $deliveryCharge = $this->calculateDeliveryCharge(
            deliveryOption:    $deliveryOption,
            prepaidAmount:     $prepaidAmount,
            override:          $data->deliveryChargeOverride,
        );

        return [
            'items'             => $validatedItems,
            'prepaid_amount'    => $prepaidAmount,
            'order_total'       => $orderTotal,
            'delivery_option'   => $deliveryOption,
            'delivery_charge'   => $deliveryCharge,
            'amount_to_deduct'  => $prepaidAmount + $deliveryCharge,
        ];
    }

    /**
     * ✅ FIXED Delivery Charge Logic
     *
     * Business Rule:
     *   - Delivery selected AND prepaid_amount < 200 → £3.50
     *   - This now correctly applies to Mixed-only orders too
     *   - Prepaid ≥ £200 → Free delivery
     */
    private function calculateDeliveryCharge(
        string $deliveryOption,
        float  $prepaidAmount,
        ?float $override = null
    ): float {
        if ($override !== null) {
            return (float)$override;
        }

        if ($deliveryOption === 'Delivery' && $prepaidAmount < self::FREE_DELIVERY_THRESHOLD) {
            return self::DELIVERY_CHARGE;
        }

        return 0.00;
    }

    /**
     * Check if donor has sufficient balance (including overdrawn)
     */
    private function ensureSufficientBalance(int $donorId, float $amountToDeduct): void
    {
        if ($amountToDeduct <= 0) return;

        $user = User::find($donorId);
        if (!$user) {
            throw new InsufficientBalanceException();
        }

        $available = $user->getAvailableLimit(); // getLiveBalance() + overdrawn_amount

        if ($available < $amountToDeduct) {
            throw new InsufficientBalanceException();
        }
    }

    /**
     * Persist order within a transaction
     */
    private function persistOrder(VoucherBookOrderData $data, array $validation, ?Order $existingOrder = null): Order
    {
        $order = $existingOrder ?? new Order();

        $order->user_id         = $data->donorId;
        $order->order_id        = $this->generateOrderId($data->donorId);
        $order->amount          = $validation['amount_to_deduct'];
        $order->delivery_charge = $validation['delivery_charge'];
        $order->delivery_option = $validation['delivery_option'];
        $order->notification    = 1;
        $order->status          = 0;
        $order->created_by      = (string)$data->orderedByUserId;
        $order->updated_by      = (string)$data->orderedByUserId;
        $order->save();

        $ppc = 0; // Prepaid voucher count (legacy compat)

        foreach ($validation['items'] as $item) {
            $voucher = $item['voucher'];
            $qty     = $item['qty'];

            for ($x = 0; $x < $qty; $x++) {
                $uniqueCode = $this->generateUniqueCode();

                if ($voucher->type === 'Mixed') {
                    $this->createMixedOrderHistories($order, $voucher, $uniqueCode);
                } else {
                    OrderHistory::create([
                        'order_id'       => $order->id,
                        'voucher_id'     => $voucher->id,
                        'number_voucher' => 1,
                        'amount'         => $voucher->amount,
                        'o_unq'          => $uniqueCode,
                        'status'         => '0',
                        'created_by'     => (string)$data->orderedByUserId,
                    ]);
                }

                // Prepaid per-unit Usertransaction
                if ($voucher->type === 'Prepaid') {
                    $this->createUserTransaction(
                        userId:    $data->donorId,
                        amount:    $voucher->amount,
                        uniqueCode: $uniqueCode,
                        orderId:   $order->id,
                        title:     'Prepaid Voucher Book'
                    );
                    $ppc++;
                }
            }

            // Decrement voucher stock
            $voucher->decrement('stock', $qty);
        }

        if ($validation['delivery_charge'] > 0) {
            $this->createUserTransaction(
                userId:     $data->donorId,
                amount:     $validation['delivery_charge'],
                uniqueCode: $this->generateUniqueCode(),
                orderId:    $order->id,
                title:      'Delivery Charge'
            );
        }

        
        if ($validation['amount_to_deduct'] > 0) {
            User::where('id', $data->donorId)
                ->decrement('balance', $validation['amount_to_deduct']);
        }

        
        VoucherCart::where('user_id', $data->donorId)->delete();

        Log::info('Voucher book order created', [
            'order_id'         => $order->id,
            'donor_id'         => $data->donorId,
            'ordered_by'       => $data->orderedByUserId,
            'source'           => $data->source,
            'prepaid_amount'   => $validation['prepaid_amount'],
            'delivery_charge'  => $validation['delivery_charge'],
            'amount_to_deduct' => $validation['amount_to_deduct'],
            'ppc'              => $ppc,
        ]);

        return $order;
    }

    /**
     * Create Mixed voucher OrderHistory records (one per leaf)
     */
    private function createMixedOrderHistories(Order $order, Voucher $voucher, string $uniqueCode): void
    {
        $leaves = self::MIXED_LEAVES_MAP[$voucher->id] ?? self::DEFAULT_MIXED_LEAVES;

        foreach ($leaves as $leafValue) {
            OrderHistory::create([
                'order_id'      => $order->id,
                'voucher_id'    => $voucher->id,
                'number_voucher' => 1,
                'amount'        => 0, // Mixed leaves এর amount 0 (book-level amount আলাদা)
                'o_unq'         => $uniqueCode,
                'mixed_value'   => $leafValue,
                'status'        => '0',
            ]);
        }
    }

    /**
     * Create a Usertransaction (Out type)
     */
    private function createUserTransaction(
        int    $userId,
        float  $amount,
        string $uniqueCode,
        int    $orderId,
        string $title
    ): void {
        Usertransaction::create([
            't_id'     => 'TXN-' . time() . '-' . rand(1000, 9999),
            'user_id'  => $userId,
            't_type'   => 'Out',
            'amount'   => $amount,
            't_unq'    => $uniqueCode,
            'order_id' => $orderId,
            'title'    => $title,
            'status'   => 1,
        ]);
    }

    /**
     * Generate unique order ID (more robust than just time())
     */
    private function generateOrderId(int $userId): string
    {
        return time() . '-' . $userId . '-' . rand(100, 999);
    }

    /**
     * Generate unique code for OrderHistory/Usertransaction
     */
    private function generateUniqueCode(): string
    {
        return time() . rand(1000, 9999);
    }

    /**
     * Send order confirmation email
     */
    private function sendOrderConfirmationEmail(Order $order, int $donorId, string $deliveryOption): void
    {
        try {
            $user = User::find($donorId);
            if (!$user) return;

            $contact = ContactMail::find(1);
            $contactmail = $contact?->name ?: 'info@tevini.co.uk';

            $array = [
                'subject'         => 'Voucher books order confirmation',
                'from'            => 'info@tevini.co.uk',
                'cc'              => $contactmail,
                'name'            => $user->name,
                'client_no'       => $user->accountno,
                'order_id'        => $order->id,
                'orderid'         => $order->order_id,
                'delivery_option' => $deliveryOption,
            ];

            Mail::send('mail.order', compact('array'), function ($message) use ($array, $user) {
                $message->from($array['from'], 'Tevini.co.uk')
                        ->to($user->email)
                        ->cc($array['cc'])
                        ->subject($array['subject']);
            });
        } catch (\Throwable $e) {
            Log::error('Voucher order email failed', [
                'order_id' => $order->id,
                'error'    => $e->getMessage(),
            ]);
            // Don't fail the order if email fails
        }
    }
}
<?php

namespace App\Http\Controllers\OneGiv;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\OneGiv\OneGivCard;
use App\Models\OneGiv\OneGivCardOrder;
use App\Models\OneGiv\OneGivTransaction;

class OneGivWebhookController extends Controller
{
    /**
     * OneGiv card print
     */
    public function cardOrderProcessed(Request $request)
    {
        $cards = $request->json()->all();

        foreach ($cards as $card) {
            // Order 
            $order = OneGivCardOrder::where('order_id', $card['id'])->first();

            OneGivCard::updateOrCreate(
                ['serial_number' => $card['serialNumber']],
                [
                    'user_id'        => $order->user_id ?? null,
                    'order_id'       => $card['id'],
                    'order_number'   => $card['orderNumber'],
                    'serial_number'  => $card['serialNumber'],
                    'pin'            => $card['pin'],
                    'display_number' => $card['displayNumber'],
                    'expiry_date'    => $card['expiryDate'],
                    'status'         => 'active',
                ]
            );

            // Order status update 
            if ($order) {
                $order->update(['status' => 'processed']);
            }
        }

        Log::info('OneGiv: Cards processed', ['count' => count($cards)]);

        return response()->json(['status' => 'success']);
    }

    /**
     * Charity Approve
     */
    public function approveCharity(Request $request)
    {
        $data = $request->validate([
            'charityName'      => 'required|string',
            'charityNumber'    => 'required|string',
            'accountNumber'    => 'required|string',
            'sortcode'         => 'required|string',
            'accountReference' => 'required|string',
        ]);

        Log::info('OneGiv: Charity approval request', $data);

        return response()->json(['status' => 'approved']);
    }

    /**
     * Transaction Approve
     */
    public function transactionRequest(Request $request)
    {
        $data = $request->validate([
            'terminalId'          => 'required|string',
            'oneGivTransactionId' => 'required|string',
            'cardSerialNumber'    => 'required|string',
            'amount'              => 'required|integer',
            'reference'           => 'nullable|string',
            'charityNumber'       => 'required|string',
            'accountReference'    => 'nullable|string',
            'accountNumber'       => 'required|string',
            'sortcode'            => 'required|string',
        ]);

        // ✅ STEP 1: Check card exists and is active
        $card = \App\Models\OneGiv\OneGivCard::where('serial_number', $data['cardSerialNumber'])
                                            ->where('status', 'active')
                                            ->first();

        if (!$card) {
            Log::warning('OneGiv Transaction: Card not found or inactive', [
                'serial_number' => $data['cardSerialNumber'],
            ]);
            return response()->json(['status' => 'declined']);
        }

        // ✅ STEP 2: Check charity exists in our system (acc_no = charityNumber)
        $charity = \App\Models\Charity::where('acc_no', $data['charityNumber'])->first();

        if (!$charity) {
            Log::warning('OneGiv Transaction: Charity not found in system', [
                'charity_number' => $data['charityNumber'],
                'card_serial'    => $data['cardSerialNumber'],
            ]);
            return response()->json(['status' => 'declined']);
        }

        Log::info('OneGiv Transaction: Charity verified', [
            'charity_number' => $data['charityNumber'],
            'charity_name'   => $charity->name,
        ]);

        // ✅ STEP 3: Check user exists
        $amountInPounds = $data['amount'] / 100;
        $user = \App\Models\User::find($card->user_id);

        if (!$user) {
            Log::warning('OneGiv Transaction: User not found for card', [
                'serial_number' => $data['cardSerialNumber'],
                'user_id'       => $card->user_id,
            ]);
            return response()->json(['status' => 'declined']);
        }

        // ✅ STEP 4: Check available balance
        $availableBalance = $user->getAvailableLimit();

        if ($availableBalance < $amountInPounds) {
            Log::warning('OneGiv Transaction: Insufficient balance', [
                'user_id'           => $user->id,
                'available_balance' => $availableBalance,
                'requested_amount'  => $amountInPounds,
                'charity_number'    => $data['charityNumber'],
            ]);
            return response()->json(['status' => 'declined']);
        }

        // ✅ STEP 5: Create transaction ID
        $transactionId = 'CI-TXN-' . strtoupper(uniqid());

        // ✅ STEP 6: Save OneGiv transaction
        $onegivTxn = \App\Models\OneGiv\OneGivTransaction::create([
            'terminal_id'                => $data['terminalId'],
            'onegiv_transaction_id'      => $data['oneGivTransactionId'],
            'card_issuer_transaction_id' => $transactionId,
            'card_serial_number'         => $data['cardSerialNumber'],
            'amount'                     => $data['amount'],
            'reference'                  => $data['reference'] ?? null,
            'charity_number'             => $data['charityNumber'],
            'account_number'             => $data['accountNumber'],
            'sortcode'                   => $data['sortcode'],
            'status'                     => 'success',
        ]);

        // ✅ STEP 7: Deduct user balance
        $user->balance = $user->balance - $amountInPounds;
        $user->save();

        // ✅ STEP 8: Save to usertransactions
            $utran                        = new \App\Models\Usertransaction();
            $utran->t_id                  = 'OneGiv-' . time() . '-' . $user->id;
            $utran->user_id               = $user->id;
            $utran->charity_id            = $charity->id;
            $utran->t_type                = 'Out';
            $utran->source                = 'OneGiv Card';
            $utran->amount                = $amountInPounds;
            $utran->title                 = 'OneGiv Card Donation to ' . $charity->name . ' (' . $data['charityNumber'] . ')';
            $utran->onegiv_transaction_id = $onegivTxn->id;
            $utran->status                = 1;
            $utran->save();

            // ✅ STEP 9: Add charity Transaction record (In) — AutoPayment এর জন্য দরকার
            $chtran             = new \App\Models\Transaction();
            $chtran->t_id       = $utran->t_id;
            $chtran->charity_id = $charity->id;
            $chtran->user_id    = $user->id;
            $chtran->t_type     = 'In';
            $chtran->name       = 'OneGiv Card';
            $chtran->amount     = $amountInPounds;
            $chtran->note       = 'OneGiv Card Donation - Serial: ' . $data['cardSerialNumber'];
            $chtran->status     = 1;
            $chtran->save();

            // ✅ STEP 10: Increment charity balance — AutoPayment এ decrement এর জন্য দরকার
            $charity->increment('balance', $amountInPounds);

            Log::info('OneGiv Transaction: Charity balance updated', [
                'charity_id'     => $charity->id,
                'charity_name'   => $charity->name,
                'amount_added'   => $amountInPounds,
                'new_balance'    => $charity->fresh()->balance,
            ]);

        Log::info('OneGiv Transaction: Approved successfully', [
            'transaction_id'    => $transactionId,
            'user_id'           => $user->id,
            'amount_pounds'     => $amountInPounds,
            'balance_before'    => $availableBalance,
            'balance_after'     => $user->balance,
            'charity_number'    => $data['charityNumber'],
            'charity_name'      => $charity->name,
            'card_serial'       => $data['cardSerialNumber'],
        ]);

        return response()->json([
            'status'        => 'success',
            'transactionId' => $transactionId,
        ]);
    }

    /**
     * Refund
     */
    public function refundRequest(Request $request)
    {
        $data = $request->validate([
            'cardIssuerTransactionId' => 'required|string',
        ]);

        $txn = OneGivTransaction::where(
            'card_issuer_transaction_id',
            $data['cardIssuerTransactionId']
        )->first();

        if ($txn) {
            $txn->update(['status' => 'refunded']);
        }

        Log::info('OneGiv: Refund', $data);

        return response()->json(['status' => 'success']);
    }

    /**
     * Notification (invalid PIN etc.)
     */
    public function notify(Request $request)
    {
        $data = $request->validate([
            'reason'           => 'required|string',
            'cardSerialNumber' => 'required|string',
            'data'             => 'required|string',
        ]);

        $payload = json_decode($data['data'], true);

        \App\Models\OneGiv\OneGivNotification::create([
            'reason'            => $data['reason'],
            'card_serial_number'=> $data['cardSerialNumber'],
            'payload'           => $data['data'],
        ]);

        if ($data['reason'] === 'invalid PIN') {
            $attempts = \App\Models\OneGiv\OneGivNotification::where('card_serial_number', $data['cardSerialNumber'])
                            ->where('reason', 'invalid PIN')
                            ->count();

            if ($attempts >= 3) {
                
                \App\Models\OneGiv\OneGivCard::where('serial_number', $data['cardSerialNumber'])
                    ->update(['status' => 'blocked']);

                Log::warning('OneGiv: Card blocked due to invalid PIN attempts', [
                    'card' => $data['cardSerialNumber'],
                ]);
            }
        }

        Log::warning('OneGiv: Notify', [
            'reason'  => $data['reason'],
            'card'    => $data['cardSerialNumber'],
            'payload' => $payload,
        ]);

        return response()->json(null, 200);
    }




}
<?php

namespace App\Http\Controllers\OneGiv;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\OneGiv\OneGivCard;
use App\Models\OneGiv\OneGivCardOrder;
use App\Models\OneGiv\OneGivTransaction;
use Illuminate\Support\Facades\DB;

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
            'charityName'   => 'required|string',
            'charityNumber' => 'required|string',
            'accountNumber' => 'required|string',
            'sortcode'      => 'required|string',
        ]);

        Log::info('OneGiv: Charity approval request received', $data);

        // STEP 1: Charity check 
        $charity = \App\Models\Charity::where('acc_no', $data['charityNumber'])->first();

        if (!$charity) {
            Log::warning('OneGiv: Charity not found in system', [
                'charity_number' => $data['charityNumber'],
                'charity_name'   => $data['charityName'],
            ]);
            return response()->json(['status' => 'accountUnknown']);
        }

        // STEP 2: Bank account number match 
        if ($charity->account_number !== $data['accountNumber']) {
            Log::warning('OneGiv: Charity account number mismatch', [
                'charity_number'    => $data['charityNumber'],
                'expected_account'  => $charity->account_number,
                'received_account'  => $data['accountNumber'],
            ]);
            return response()->json(['status' => 'accountUnknown']);
        }

        // STEP 3: Sort code match 
        $dbSortcode       = preg_replace('/[^0-9]/', '', $charity->account_sortcode ?? '');
        $requestSortcode  = preg_replace('/[^0-9]/', '', $data['sortcode']);

        if ($dbSortcode !== $requestSortcode) {
            Log::warning('OneGiv: Charity sortcode mismatch', [
                'charity_number'   => $data['charityNumber'],
                'expected_sortcode'=> $dbSortcode,
                'received_sortcode'=> $requestSortcode,
            ]);
            return response()->json(['status' => 'accountUnknown']);
        }

        // ✅ All checks passed
        Log::info('OneGiv: Charity approved', [
            'charity_number' => $data['charityNumber'],
            'charity_name'   => $charity->name,
        ]);

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

        // ✅ FIX: Idempotency - check for replay BEFORE anything else
        $existingTxn = \App\Models\OneGiv\OneGivTransaction::where('onegiv_transaction_id', $data['oneGivTransactionId'])->first();
        if ($existingTxn) {
            Log::info('OneGiv Transaction: Duplicate request, returning original decision', [
                'onegiv_transaction_id' => $data['oneGivTransactionId'],
                'existing_card_issuer_transaction_id' => $existingTxn->card_issuer_transaction_id,
            ]);
            return response()->json([
                'status'        => $existingTxn->status === 'success' ? 'success' : 'declined',
                'transactionId' => $existingTxn->card_issuer_transaction_id,
            ]);
        }

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

        // ✅ STEP 2: Check charity exists
        $charity = \App\Models\Charity::where('acc_no', $data['charityNumber'])->first();

        if (!$charity) {
            Log::warning('OneGiv Transaction: Charity not found', [
                'charity_number' => $data['charityNumber'],
            ]);
            return response()->json(['status' => 'declined']);
        }

        // ✅ STEP 3: Check user exists
        $amountInPounds = $data['amount'] / 100;
        $user = \App\Models\User::find($card->user_id);

        if (!$user) {
            return response()->json(['status' => 'declined']);
        }

        $transactionId = 'CI-TXN-' . strtoupper(uniqid());

        // ✅ FIX: Atomic DB Transaction with row locking
        try {
            DB::transaction(function () use ($data, $card, $charity, $user, $amountInPounds, $transactionId) {
                
                // Lock user row for concurrent safety
                $lockedUser = \App\Models\User::where('id', $user->id)->lockForUpdate()->first();
                $availableBalance = $lockedUser->getAvailableLimit();

                if ($availableBalance < $amountInPounds) {
                    Log::warning('OneGiv Transaction: Insufficient balance', [
                        'user_id' => $lockedUser->id,
                        'available' => $availableBalance,
                        'requested' => $amountInPounds,
                    ]);
                    throw new \Exception('Insufficient balance');
                }

                // Save OneGiv transaction (unique constraint will catch concurrent race)
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

                // Deduct user balance
                $lockedUser->balance = $lockedUser->balance - $amountInPounds;
                $lockedUser->save();

                // Save Usertransaction
                $utran = new \App\Models\Usertransaction();
                $utran->t_id                  = 'OneGiv-' . uniqid() . '-' . $lockedUser->id;
                $utran->user_id               = $lockedUser->id;
                $utran->charity_id            = $charity->id;
                $utran->t_type                = 'Out';
                $utran->source                = 'OneGiv Card';
                $utran->amount                = $amountInPounds;
                $utran->title                 = 'OneGiv Card Donation to ' . $charity->name . ' (' . $data['charityNumber'] . ')';
                $utran->onegiv_transaction_id = $onegivTxn->id;
                $utran->status                = 1;
                $utran->save();

                // Charity Transaction (In)
                $chtran = new \App\Models\Transaction();
                $chtran->t_id       = $utran->t_id;
                $chtran->charity_id = $charity->id;
                $chtran->user_id    = $lockedUser->id;
                $chtran->t_type     = 'In';
                $chtran->name       = 'OneGiv Card';
                $chtran->amount     = $amountInPounds;
                $chtran->note       = 'OneGiv Card Donation - Serial: ' . $data['cardSerialNumber'];
                $chtran->status     = 1;
                $chtran->save();

                // Increment charity balance
                $charity->increment('balance', $amountInPounds);
            });
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Insufficient balance') !== false) {
                return response()->json(['status' => 'declined']);
            }
            // Unique constraint violation = concurrent duplicate, fetch existing
            if (strpos($e->getMessage(), 'Duplicate entry') !== false || $e->getCode() === '23000') {
                $existing = \App\Models\OneGiv\OneGivTransaction::where('onegiv_transaction_id', $data['oneGivTransactionId'])->first();
                return response()->json([
                    'status'        => 'success',
                    'transactionId' => $existing->card_issuer_transaction_id,
                ]);
            }
            Log::error('OneGiv Transaction failed: ' . $e->getMessage());
            return response()->json(['status' => 'declined']);
        }

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

        $txn = OneGivTransaction::where('card_issuer_transaction_id', $data['cardIssuerTransactionId'])->first();

        if (!$txn) {
            return response()->json(['status' => 'success']);
        }

        // ✅ FIX: Use status 'refund_pending' instead of 'refunded'
        // Manual refund workflow - admin must approve before money moves
        if (in_array($txn->status, ['refund_pending', 'refunded'])) {
            Log::info('OneGiv Refund: Already requested or completed', [
                'card_issuer_transaction_id' => $data['cardIssuerTransactionId'],
                'current_status' => $txn->status,
            ]);
            return response()->json(['status' => 'success']);
        }

        $amountInPounds = $txn->amount / 100;
        $card = \App\Models\OneGiv\OneGivCard::where('serial_number', $txn->card_serial_number)->first();
        $user = $card ? \App\Models\User::find($card->user_id) : null;
        $charity = \App\Models\Charity::where('acc_no', $txn->charity_number)->first();

        // ✅ FIX: Mark as 'refund_pending' - financial settlement still required
        $txn->update(['status' => 'refund_pending']);

        \App\Models\OneGiv\OneGivRefund::create([
            'card_issuer_transaction_id' => $data['cardIssuerTransactionId'],
            'card_serial_number'         => $txn->card_serial_number,
            'user_id'                    => $user->id ?? null,
            'charity_id'                 => $charity->id ?? null,
            'charity_number'             => $txn->charity_number,
            'amount'                     => $txn->amount,
            'amount_pounds'              => $amountInPounds,
            'onegiv_transaction_id'      => $txn->onegiv_transaction_id,
            'status'                     => 'pending', // ✅ pending, not refunded
            'admin_watch'                => 0,
            'admin_status'               => 0,
        ]);

        Log::info('OneGiv Refund: Refund request created (pending admin approval)', [
            'card_issuer_transaction_id' => $data['cardIssuerTransactionId'],
            'amount_pounds'              => $amountInPounds,
            'user_id'                    => $user->id ?? null,
            'charity_id'                 => $charity->id ?? null,
        ]);

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
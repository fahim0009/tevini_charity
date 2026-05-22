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

            // ✅ STEP 9: Add charity Transaction record (In) — AutoPayment
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

            // ✅ STEP 10: Increment charity balance — AutoPayment  decrement 
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

        // STEP 1: Transaction খুঁজে বের করো
        $txn = OneGivTransaction::where(
            'card_issuer_transaction_id',
            $data['cardIssuerTransactionId']
        )->first();

        if (!$txn) {
            Log::warning('OneGiv Refund: Transaction not found', [
                'card_issuer_transaction_id' => $data['cardIssuerTransactionId'],
            ]);
            return response()->json(['status' => 'success']);
        }

        // STEP 2: Already refunded check
        if ($txn->status === 'refunded') {
            Log::warning('OneGiv Refund: Already refunded', [
                'card_issuer_transaction_id' => $data['cardIssuerTransactionId'],
            ]);
            return response()->json(['status' => 'success']);
        }

        $amountInPounds = $txn->amount / 100;

        // STEP 3: Card → User খুঁজে বের করো
        $card    = \App\Models\OneGiv\OneGivCard::where('serial_number', $txn->card_serial_number)->first();
        $user    = $card ? \App\Models\User::find($card->user_id) : null;

        // STEP 4: Charity খুঁজে বের করো
        $charity = \App\Models\Charity::where('acc_no', $txn->charity_number)->first();

        // STEP 5: OneGivTransaction status update
        $txn->update(['status' => 'refunded']);

        // STEP 6: OneGivRefund table এ save করো
        \App\Models\OneGiv\OneGivRefund::create([
            'card_issuer_transaction_id' => $data['cardIssuerTransactionId'],
            'card_serial_number'         => $txn->card_serial_number,
            'user_id'                    => $user->id ?? null,
            'charity_id'                 => $charity->id ?? null,
            'charity_number'             => $txn->charity_number,
            'amount'                     => $txn->amount,
            'amount_pounds'              => $amountInPounds,
            'onegiv_transaction_id'      => $txn->onegiv_transaction_id,
            'status'                     => 'refunded',
            'admin_watch'                => 0,
            'admin_status'               => 0,
        ]);

        // STEP 7: User balance ফেরত দাও
        // if ($user) {
        //     $user->balance = $user->balance + $amountInPounds;
        //     $user->save();

        //     $utran          = new \App\Models\Usertransaction();
        //     $utran->t_id    = 'OneGiv-Refund-' . time() . '-' . $user->id;
        //     $utran->user_id = $user->id;
        //     $utran->t_type  = 'In';
        //     $utran->source  = 'OneGiv Card Refund';
        //     $utran->amount  = $amountInPounds;
        //     $utran->title   = 'OneGiv Card Donation Refund - ' . $data['cardIssuerTransactionId'];
        //     $utran->status  = 1;
        //     $utran->save();

        //     Log::info('OneGiv Refund: User balance restored', [
        //         'user_id'     => $user->id,
        //         'amount'      => $amountInPounds,
        //         'new_balance' => $user->balance,
        //     ]);
        // }

        // STEP 8: Charity balance কমাও
        // if ($charity) {
        //     $charity->decrement('balance', $amountInPounds);

        //     Log::info('OneGiv Refund: Charity balance deducted', [
        //         'charity_id'   => $charity->id,
        //         'charity_name' => $charity->name,
        //         'amount'       => $amountInPounds,
        //     ]);
        // }

        Log::info('OneGiv Refund: Completed successfully', [
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
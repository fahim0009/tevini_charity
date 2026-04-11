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

        // Card active  check 
        $card = OneGivCard::where('serial_number', $data['cardSerialNumber'])
                          ->where('status', 'active')
                          ->first();

        if (!$card) {
            Log::warning('OneGiv: Card not found or inactive', ['serial' => $data['cardSerialNumber']]);
            return response()->json(['status' => 'declined']);
        }

        $transactionId = 'CI-TXN-' . strtoupper(uniqid());

        OneGivTransaction::create([
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

        Log::info('OneGiv: Transaction approved', ['txn' => $transactionId]);

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

        Log::warning('OneGiv: Notify', [
            'reason'  => $data['reason'],
            'card'    => $data['cardSerialNumber'],
            'payload' => json_decode($data['data'], true),
        ]);

        return response()->json(null, 200);
    }
}
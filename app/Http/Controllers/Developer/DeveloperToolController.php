<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Charity;
use App\Models\StandingdonationDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; 

    use App\Models\Transaction;
use App\Models\CharityPaymentBatch;

class DeveloperToolController extends Controller
{



    public function checkStandingDonation(Request $request)
    {
        // Check if dates exist either from a POST search or a GET redirect after delete
        if ($request->has('fromDate') && $request->has('toDate')) {
            $fromDate = $request->fromDate;
            $toDate = $request->toDate;

            // Eager load user and charity for better performance
            $chktran = StandingdonationDetail::with(['user', 'charity'])
                ->whereDate('created_at', '>=', $fromDate)
                ->whereDate('created_at', '<=', $toDate)
                ->orderBy('id', 'DESC')
                ->get();
                
            // Manually attach transactions because the foreign key in the DB is wrong
            foreach ($chktran as $item) {
                $item->matched_transaction = \App\Models\Usertransaction::where('user_id', $item->user_id)
                    ->where('amount', $item->amount)
                    ->where('title', 'Standing order donation')
                    ->whereBetween('created_at', [
                        \Carbon\Carbon::parse($item->created_at)->subSeconds(5), 
                        \Carbon\Carbon::parse($item->created_at)->addSeconds(5)
                    ])
                    ->first();
            }
            
            return view('admin.devTest.standing_donation_detail', compact('chktran', 'fromDate', 'toDate'));
        } else {
            return view('admin.devTest.standing_donation_detail');
        }
    }

    public function deleteStandingDonation(Request $request, $id)
    {
        try {
            $detail = StandingdonationDetail::findOrFail($id);
            
            // Manually find the transaction
            $transaction = \App\Models\Usertransaction::where('user_id', $detail->user_id)
                ->where('amount', $detail->amount)
                ->where('title', 'Standing order donation')
                ->whereBetween('created_at', [
                    \Carbon\Carbon::parse($detail->created_at)->subSeconds(5), 
                    \Carbon\Carbon::parse($detail->created_at)->addSeconds(5)
                ])
                ->first();

            Log::channel('daily')->warning('Developer Tool: Manual Deletion Initiated', [
                'standing_donation_detail' => $detail->toArray(),
                'user_transaction'         => $transaction ? $transaction->toArray() : 'No matching transaction found',
                'triggered_by'             => auth()->check() ? auth()->id() : 'Guest/System',
            ]);

            if ($transaction) {
                // 1. Update transaction status to 0 instead of deleting
                $transaction->status = 0;
                $transaction->save();
                
                // 2. Reduce the amount from the charity's balance
                $charity = \App\Models\Charity::find($detail->charity_id);
                if ($charity) {
                    $charity->decrement('balance', $detail->amount);
                }
            }
            
            // Delete the standing donation detail record
            $detail->delete();

            // Redirect back to the search page WITH the dates in the URL to show the remaining records
            return redirect()->route('dev.checkStandingDonation', [
                'fromDate' => $request->fromDate,
                'toDate' => $request->toDate
            ])->with('success', 'Detail deleted, transaction status set to 0, and charity balance reduced successfully. Logs have been recorded.');

        } catch (\Exception $e) {
            Log::error('Error deleting standing donation manually', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            
            // Redirect back with dates even if it fails, so the table isn't empty
            return redirect()->route('dev.checkStandingDonation', [
                'fromDate' => $request->fromDate,
                'toDate' => $request->toDate
            ])->with('error', 'Error processing request: ' . $e->getMessage());
        }
    }







    public function searchTransaction(Request $request)
    {
        if ($request->isMethod('post')) {
            $t_id = $request->t_id;
            
            // Search by t_id
            $transaction = Transaction::where('t_id', $t_id)->first();

            if ($transaction) {
                // Eager load charity to show in the view
                $transaction->load('charity');
            }

            return view('admin.devTest.transaction_delete', compact('transaction', 't_id'));
        } else {
            return view('admin.devTest.transaction_delete');
        }
    }

    public function deleteTransaction($id)
    {
        try {
            $transaction = Transaction::findOrFail($id);

            // Log the action before modifying anything
            Log::channel('daily')->warning('Developer Tool: Manual Transaction Deletion Initiated', [
                'transaction' => $transaction->toArray(),
                'triggered_by' => auth()->check() ? auth()->id() : 'Guest/System',
            ]);

            // Reverse the charity balance based on transaction type
            $charity = Charity::find($transaction->charity_id);
            if ($charity) {
                if ($transaction->t_type == 'Out') {
                    // Original script decremented on 'Out', so we increment to reverse
                    $charity->increment('balance', $transaction->amount);
                } else {
                    // If it was an 'In' transaction, decrement to reverse
                    $charity->decrement('balance', $transaction->amount);
                }
            }

            // Delete the associated CharityPaymentBatch record if it exists
            CharityPaymentBatch::where('transaction_id', $transaction->id)->delete();

            // Delete the transaction record
            $transaction->delete();

            return redirect()->back()->with('success', 'Transaction deleted successfully, charity balance reversed, and batch record removed.');
        } catch (\Exception $e) {
            Log::error('Error deleting transaction manually', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Error processing request: ' . $e->getMessage());
        }
    }

    public function updateTransaction(Request $request, $id)
{
    // Validate that the new amount is a positive number
    $request->validate([
        'amount' => 'required|numeric|min:0'
    ]);

    try {
        $transaction = Transaction::findOrFail($id);
        $oldAmount = $transaction->amount;
        $newAmount = $request->amount;

        // If amounts are the same, do nothing
        if ($oldAmount == $newAmount) {
            return redirect()->back()->with('info', 'Amount is the same. No changes made.');
        }

        // Log the action before modifying anything
        Log::channel('daily')->warning('Developer Tool: Manual Transaction Update Initiated', [
            'transaction_id' => $transaction->id,
            't_id' => $transaction->t_id,
            'old_amount' => $oldAmount,
            'new_amount' => $newAmount,
            'triggered_by' => auth()->check() ? auth()->id() : 'Guest/System',
        ]);

        // Calculate the difference
        $difference = $newAmount - $oldAmount;

        // Adjust the charity balance
        $charity = Charity::find($transaction->charity_id);
        if ($charity) {
            if ($transaction->t_type == 'Out') {
                // If 'Out' (payout), original script decremented $oldAmount.
                // To make it the new amount, we need to reverse the difference.
                // If new > old, difference is positive, we decrement balance further.
                $charity->decrement('balance', $difference);
            } else {
                // If 'In', original script incremented $oldAmount.
                // If new > old, difference is positive, we increment balance further.
                $charity->increment('balance', $difference);
            }
        }

        // Update the transaction amount
        $transaction->amount = $newAmount;
        $transaction->save();

        // Also update the associated CharityPaymentBatch record if it exists
        CharityPaymentBatch::where('transaction_id', $transaction->id)->update([
            'amount' => $newAmount
        ]);

        return redirect()->back()->with('success', "Transaction amount updated successfully from £{$oldAmount} to £{$newAmount}. Charity balance adjusted.");

    } catch (\Exception $e) {
        Log::error('Error updating transaction manually', [
            'id' => $id,
            'error' => $e->getMessage()
        ]);
        return redirect()->back()->with('error', 'Error processing request: ' . $e->getMessage());
    }
}

}
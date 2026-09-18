<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Provoucher;
use Illuminate\Http\Request;
use App\Models\Usertransaction;
use App\Models\User;

class TransactionController extends Controller
{
    public function userTransactionShow(Request $request)
    {
        $userId = auth()->id();
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate') ? $request->input('toDate') . ' 23:59:59' : null;
        $hasDateRange = $fromDate && $toDate;

        // ---------------------------------------------------------
        // 1. All Transactions with Running Balance (Exact match with Web)
        // ---------------------------------------------------------
        $query = Usertransaction::with(['charity:id,name', 'standingdonationDetail.standingDonation', 'donation', 'campaign', 'provoucher'])
            ->where('user_id', $userId)
            ->where(function ($q) {
                $q->whereNull('expired')->orWhere('expired', '1');
            })
            ->where(function ($q) use ($hasDateRange, $fromDate, $toDate) {
                if ($hasDateRange) {
                    $q->whereBetween('created_at', [$fromDate, $toDate])->where('status', 1);
                } else {
                    $q->where('status', 1);
                }
            })
            ->orWhere(function ($q) use ($userId, $hasDateRange, $fromDate, $toDate) {
                $q->where('user_id', $userId);
                if ($hasDateRange) {
                    $q->whereBetween('created_at', [$fromDate, $toDate])->where('pending', '0');
                } else {
                    $q->where('pending', '0');
                }
            })
            ->orderBy('created_at', 'asc') // Sort ASC to calculate balance forward
            ->get();

        $currentBalanceTracker = 0;
        $alltransactions = $query->flatMap(function ($data) use (&$currentBalanceTracker) {
            $rows = [];

            $isExpired = (isset($data->expired) && $data->expired == '0') || (isset($data->provoucher) && $data->provoucher->expired == "Yes");
            $isPending = ($data->status != 1);

            // Commission Row
            if ($data->commission != 0) {
                if (!$isExpired && !$isPending) {
                    $currentBalanceTracker -= $data->commission;
                }
                
                $commRow = clone $data;
                $commRow->display_type = 'commission';
                $commRow->calculated_balance = $currentBalanceTracker;
                $rows[] = $commRow;
            }

            // Main Transaction Row
            if (!$isExpired && !$isPending) {
                if ($data->t_type == "In") {
                    $currentBalanceTracker += ($data->commission != 0) ? ($data->amount + $data->commission) : $data->amount;
                } else {
                    $currentBalanceTracker -= $data->amount;
                }
            }

            $currentRow = clone $data;
            $currentRow->display_type = 'main';
            $currentRow->calculated_balance = $currentBalanceTracker;

            $rows[] = $currentRow;
            return $rows;
        })->reverse()->values(); // Reverse to show latest first


        // ---------------------------------------------------------
        // 2. Total Amount (Valid Transactions Only)
        // ---------------------------------------------------------
        $tamount = Usertransaction::where('user_id', $userId)
            ->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('expired')->orWhere('expired', '1');
            })
            ->when($hasDateRange, fn($q) => $q->whereBetween('created_at', [$fromDate, $toDate]))
            ->orderBy('id', 'DESC')
            ->get();


        // ---------------------------------------------------------
        // 3. In Transactions
        // ---------------------------------------------------------
        $intransactions = Usertransaction::where('user_id', $userId)
            ->where('t_type', 'In')
            ->where('status', 1)
            ->where(function ($q) {
                $q->whereNull('expired')->orWhere('expired', '1');
            })
            ->when($hasDateRange, fn($q) => $q->whereBetween('created_at', [$fromDate, $toDate]))
            ->orderBy('id', 'DESC')
            ->get();


        // ---------------------------------------------------------
        // 4. Out Transactions (Including Pending)
        // ---------------------------------------------------------
        // $outtransactions = Usertransaction::where('user_id', $userId)->with('provoucher')
        //     ->where('t_type', 'Out')
        //     ->where(function ($q) {
        //         $q->where('status', 1)->orWhere('pending', '0');
        //     })
        //     ->where(function ($q) {
        //         $q->whereNull('expired')->orWhere('expired', '1');
        //     })
        //     ->when($hasDateRange, fn($q) => $q->whereBetween('created_at', [$fromDate, $toDate]))
        //     ->orderBy('id', 'DESC')
        //     ->get();

        $outTransactionsQuery = Usertransaction::where('t_type', 'Out')->with('provoucher')
            ->where('user_id', $userId)
            ->where(function ($query){
                    $query->where('status', '1');
            })->orWhere(function ($query) {
                $query->where('t_type', 'Out')->where('pending', '0');
            });

        $outtransactions = $outTransactionsQuery->orderByDesc('id')->get();


        // ---------------------------------------------------------
        // 5. Pending Transactions Only
        // ---------------------------------------------------------
        // $pending_transactions = Usertransaction::where('user_id', $userId)
        //     ->where('t_type', 'Out')
        //     ->where('pending', '0')
        //     ->when($hasDateRange, fn($q) => $q->whereBetween('created_at', [$fromDate, $toDate]))
        //     ->orderBy('id', 'DESC')
        //     ->get();
        $pending_transactions = Provoucher::pendingVouchers($userId, $fromDate, $toDate)->get();


        // ---------------------------------------------------------
        // 6. Gift Aid Transactions
        // ---------------------------------------------------------
        $giftAid = Usertransaction::with('user')
            ->where('user_id', $userId)
            ->where('status', 1)
            ->whereNotNull('gift')
            ->where(function ($q) {
                $q->whereNull('expired')->orWhere('expired', '1');
            })
            ->when($hasDateRange, fn($q) => $q->whereBetween('created_at', [$fromDate, $toDate]))
            ->orderBy('id', 'DESC')
            ->get();


        // ---------------------------------------------------------
        // Return Response
        // ---------------------------------------------------------
        $success['alltransactions'] = $alltransactions;
        $success['intransactions'] = $intransactions;
        $success['tamount'] = $tamount;
        $success['giftAid'] = $giftAid;
        $success['outtransactions'] = $outtransactions;
        $success['pending_transactions'] = $pending_transactions;
        
        // Also returning the final live balance for convenience
        $success['live_balance'] = (float) auth()->user()->getLiveBalance();

        return response()->json(['success' => true, 'response' => $success], 200);
    }
}
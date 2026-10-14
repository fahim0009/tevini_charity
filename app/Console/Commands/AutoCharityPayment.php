<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usertransaction;
use App\Models\Transaction;
use App\Models\Charity;
use App\Models\ContactMail;
use App\Models\CompanyDetail;
use App\Models\CharityPaymentBatch;
use Illuminate\Support\Facades\DB;
use App\Mail\CharityDailyReport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class AutoCharityPayment extends Command
{
    protected $signature = 'payments:process-charity';
    protected $description = 'Consolidates daily balances and creates a single payment record per charity';

    public function handle()
    {
        set_time_limit(0);
        
        $companyDetail = CompanyDetail::first();
        
        // 🚨 NEW: Check if payment already ran today
        if ($companyDetail->last_payment_processed_date == Carbon::today()->toDateString()) {
            Log::info("Payment Process: Skipped. Payment already processed today.");
            return; // Stop execution!
        }

        Log::info("Payment Process: Starting charity payout rolling window.");

        $autoPaymentTime = $companyDetail->auto_payment_time ?? '16:30';
        
        $timeParts = explode(':', $autoPaymentTime);
        $hour = (int) $timeParts[0];
        $minute = (int) $timeParts[1];

        $now = now();
        $baseCutoff = $now->copy()->setTime($hour, $minute, 0);

        if ($now->lt($baseCutoff)) {
            $baseCutoff->subDay();
        }

        $dayOfWeek = $baseCutoff->dayOfWeek;

        if (in_array($dayOfWeek, [0, 6])) {
            Log::info("Payment Process: Skipped. Target cutoff is a weekend. Will process on Monday.");
            return;
        }

        $endTime = $baseCutoff->copy()->subSecond();
        $targetBusinessDate = $baseCutoff->toDateString();

        Log::info("Processing Window for Business Date: {$targetBusinessDate}");

        $contactmail = ContactMail::where('id', 1)->first()->name ?? 'info@tevini.co.uk';

        $pendingBalances = Usertransaction::whereNotNull('charity_id')
            ->where('status', 1)
            ->where('business_date', $targetBusinessDate)
            ->whereHas('charity', function ($q) {
                $q->where('auto_payment', 1);
            })
            ->select(['charity_id', DB::raw("SUM(amount) as total")])
            ->groupBy('charity_id')
            ->get();

        Log::info("Payment Process: Found " . $pendingBalances->count() . " charities with transactions.");

        foreach ($pendingBalances as $record) {
            // ... (Your existing loop code stays exactly the same) ...
            $charity = Charity::find($record->charity_id);
            
            if (!$charity || !$charity->email) {
                Log::warning("Payment Process: Charity ID {$record->charity_id} not found or missing email.");
                continue;
            }

            if ($charity->auto_payment != 1) {
                Log::info("Payment Skipped: {$charity->name} has auto_payment disabled.");
                continue;
            }

            $alreadyPaid = Transaction::where('charity_id', $charity->id)->where('status', 1)
                ->where('t_type', 'Out')
                ->where('business_date', $targetBusinessDate)
                ->sum('amount');

            $amountToPayNow = $record->total - $alreadyPaid;

            if ($amountToPayNow > 0.01) {
                try {
                    DB::transaction(function () use ($charity, $amountToPayNow, $endTime, $targetBusinessDate, $contactmail) {
                        
                        $userTransactions = Usertransaction::where('charity_id', $charity->id)
                            ->where('status', 1)
                            ->where('business_date', $targetBusinessDate)
                            ->get();
                            
                        $userTxIds = $userTransactions->pluck('id')->toArray();

                        $transaction = new Transaction();
                        $transaction->t_id = "Out-" . time() . "-" . $charity->id;
                        $transaction->charity_id = $charity->id;
                        $transaction->t_type = "Out";
                        $transaction->name = "Bank";
                        $transaction->amount = $amountToPayNow;
                        $transaction->status = "1"; 
                        $transaction->created_at = $endTime;
                        $transaction->business_date = $targetBusinessDate;
                        $transaction->save();

                        $charity->decrement('balance', $amountToPayNow);

                        CharityPaymentBatch::create([
                            'charity_id'           => $charity->id,
                            'transaction_id'       => $transaction->id,
                            'last_payment_date'    => $endTime,
                            'usertransactions_ids' => $userTxIds,
                            'amount'               => $amountToPayNow,
                        ]);

                        $details = $userTransactions->load(['user', 'donation']);

                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.charity_report', [
                            'charity' => $charity,
                            'details' => $details,
                            'total'   => $amountToPayNow,
                            'date'    => $targetBusinessDate
                        ]);

                        $fileName = 'Statement-' . $charity->id . '-' . $endTime->format('Y-m-d-Hi') . '.pdf';
                        $filePath = public_path('/invoices/' . $fileName);
                        file_put_contents($filePath, $pdf->output());

                        $mailData = [
                            'name'          => $charity->name,
                            'transactionid' => $transaction->t_id,
                            'total'         => number_format($amountToPayNow, 2),
                            'date'          => $targetBusinessDate,
                            'subject'       => 'Daily Statement - ' . $targetBusinessDate,
                            'file'          => $filePath,
                        ];

                        // Mail::to($charity->email)->queue(new CharityDailyReport($mailData));
                        // Mail::to($contactmail)->queue(new CharityDailyReport($mailData));

                        Log::info("Payment Process: Success for {$charity->name}. Emails queued. Batch saved.");
                    });
                } catch (\Exception $e) {
                    Log::error("Payment Process: Failed for Charity {$charity->id}. Error: " . $e->getMessage());
                }
            }
        }

        // --- Revert to default time and LOCK payment for today ---
        $companyDetail->auto_payment_time = '16:30';
        $companyDetail->last_payment_processed_date = Carbon::today()->toDateString(); // <--- LOCK: Payment done for today
        $companyDetail->save();

        DB::table('cutoff_histories')->updateOrInsert(
            ['effective_date' => Carbon::today()->toDateString()],
            [
                'cutoff_time' => '16:30', 
                'updated_at' => now(), 
                'created_at' => now()
            ]
        );
        
        Log::info("Payment Process: Auto payment time reverted to default 16:30.");
        Log::info("Payment Process: Completed for business date " . $targetBusinessDate);
    }
}
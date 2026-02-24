<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usertransaction;
use App\Models\Transaction;
use App\Models\Charity;
use App\Models\ContactMail;
use Illuminate\Support\Facades\DB;
use App\Mail\CharityDailyReport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AutoCharityPayment extends Command
{
    protected $signature = 'payments:process-charity';
    protected $description = 'Consolidates daily balances and creates a single payment record per charity';




    public function handle()
    {
        set_time_limit(0);
        Log::info("Payment Process: Starting charity payout for 16:30 rolling window.");

        // 1. Define the Rolling Time Window
        // The "Base" is today at 16:30:00
        $baseCutoff = now()->setTime(16, 30, 0);

        // If this script runs before 16:30 today, we want to process the window that ended yesterday.
        // This ensures we always target a "completed" window.
        if (now()->lt($baseCutoff)) {
            $baseCutoff->subDay();
        }

        /**
         * Start Time: Exactly 24 hours ago from the base cutoff (e.g., Yesterday 16:30:00)
         * End Time: One second before the base cutoff (e.g., Today 16:29:59)
         * This prevents a transaction at 16:30:00 from being counted in two different days.
         */
        $startTime = (clone $baseCutoff)->subDay(); 
        $endTime   = (clone $baseCutoff)->subSecond(); 

        Log::info("Processing Window: From {$startTime->toDateTimeString()} to {$endTime->toDateTimeString()}");

        $contactmail = ContactMail::where('id', 1)->first()->name;

        // 2. Get transactions within the window

        $pendingBalances = Usertransaction::whereNotNull('charity_id')
            ->where('status', 1)
            ->whereBetween('created_at', [$startTime, $endTime])
            ->whereHas('charity', function ($q) {
                $q->where('auto_payment', 1);
            })
            ->select(['charity_id', DB::raw("SUM(amount) as total")])
            ->groupBy('charity_id')
            ->get();

        Log::info("Payment Process: Found " . $pendingBalances->count() . " charities with transactions.");

        foreach ($pendingBalances as $record) {
            $charity = Charity::find($record->charity_id);
            
            if (!$charity || !$charity->email) {
                Log::warning("Payment Process: Charity ID {$record->charity_id} not found or missing email.");
                continue;
            }

            if ($charity->auto_payment != 1) {
                Log::info("Payment Skipped: {$charity->name} has auto_payment disabled.");
                continue;
            }

            // Check for manual 'Out' transactions in this window
            $alreadyPaid = Transaction::where('charity_id', $charity->id)->where('status', 1)
                ->where('t_type', 'Out')
                ->whereBetween('created_at', [$startTime, $endTime])
                ->sum('amount');

            $amountToPayNow = $record->total - $alreadyPaid;

            if ($amountToPayNow > 0.01) {
                try {
                    DB::transaction(function () use ($charity, $amountToPayNow, $endTime, $startTime, $contactmail) {
                        // Create Payout Record
                        $transaction = new Transaction();
                        $transaction->t_id = "Out-" . time() . "-" . $charity->id;
                        $transaction->charity_id = $charity->id;
                        $transaction->t_type = "Out";
                        $transaction->name = "Bank";
                        $transaction->amount = $amountToPayNow;
                        $transaction->status = "1"; 
                        $transaction->created_at = $endTime;
                        $transaction->save();

                        $charity->decrement('balance', $amountToPayNow);

                        // PDF Generation
                        $details = Usertransaction::where('charity_id', $charity->id)->where('status', 1)
                            ->whereBetween('created_at', [$startTime, $endTime])
                            ->with('user')->get();

                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.charity_report', [
                            'charity' => $charity,
                            'details' => $details,
                            'total'   => $amountToPayNow,
                            'date'    => $endTime->toDateString()
                        ]);

                        $fileName = 'Statement-' . $charity->id . '-' . $endTime->format('Y-m-d-Hi') . '.pdf';
                        $filePath = public_path('/invoices/' . $fileName);
                        file_put_contents($filePath, $pdf->output());

                        $mailData = [
                            'name'          => $charity->name,
                            'transactionid' => $transaction->t_id,
                            'total'         => number_format($amountToPayNow, 2),
                            'date'          => $endTime->toDateString(),
                            'subject'       => 'Daily Statement - ' . $endTime->toDateString(),
                            'file'          => $filePath,
                        ];

                        // 3. Queue the Emails (No more sleep timers!)
                        Mail::to($charity->email)->queue(new CharityDailyReport($mailData));
                        Mail::to($contactmail)->queue(new CharityDailyReport($mailData));

                        Log::info("Payment Process: Success for {$charity->name}. Emails queued.");
                    });
                } catch (\Exception $e) {
                    Log::error("Payment Process: Failed for Charity {$charity->id}. Error: " . $e->getMessage());
                }
            }
        }

        Log::info("Payment Process: Completed for cut-off " . $endTime);
    }


}

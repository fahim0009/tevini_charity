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
        Log::info("Payment Process: Starting charity payout for 16:30 cut-off.");

        // 1. Define the 16:30 cut-off for today
        $endTime = now()->setHour(16)->setMinute(30)->setSecond(0);

        // Define the "Day 1" start time (Midnight of today)
        $startOfToday = (clone $endTime)->startOfDay();

        // Standard 24-hour window start
        $standardStartTime = (clone $endTime)->subDay();

        $launchDate = '2026-02-15'; // Set this to your actual Day 1 date

        if (now()->toDateString() === $launchDate) {
            $startTime = $startOfToday;
            Log::info("Payment Process: Day 1 detected. Setting start time to 00:00.");
        } else {
            $startTime = $standardStartTime;
        }


        
        $contactmail = ContactMail::where('id', 1)->first()->name;

        // 2. Get transactions within the window
        $pendingBalances = Usertransaction::whereNotNull('charity_id')
            ->whereBetween('created_at', [$startTime, $endTime])
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

            // Check for manual 'Out' transactions in this window
            $alreadyPaid = Transaction::where('charity_id', $charity->id)
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
                        $details = Usertransaction::where('charity_id', $charity->id)
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

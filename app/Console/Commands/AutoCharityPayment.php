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
        Log::info("Payment Process: Starting charity payout rolling window.");

        // Fetch the dynamic payment time from CompanyDetail (default to 16:30 if not found)
        $companyDetail = CompanyDetail::first();
        $autoPaymentTime = $companyDetail->auto_payment_time ?? '16:30';
        
        $timeParts = explode(':', $autoPaymentTime);
        $hour = (int) $timeParts[0];
        $minute = (int) $timeParts[1];

        $now = now();
        $baseCutoff = $now->copy()->setTime($hour, $minute, 0);

        // If the script runs before the cutoff time today, we target yesterday's cutoff
        if ($now->lt($baseCutoff)) {
            $baseCutoff->subDay();
        }

        $dayOfWeek = $baseCutoff->dayOfWeek; // 0 = Sunday, 6 = Saturday

        // Weekend Logic: Skip processing if the cutoff lands on Saturday or Sunday.
        // These will be processed together on Monday.
        if (in_array($dayOfWeek, [0, 6])) { // 0=Sunday, 6=Saturday
            Log::info("Payment Process: Skipped. Target cutoff is a weekend. Will process on Monday.");
            return;
        }

        // Calculate Start and End Time based on the Day
        $endTime = $baseCutoff->copy()->subSecond();

        if ($dayOfWeek === Carbon::MONDAY) {
            // If today is Monday, start from Friday's cutoff time to cover Fri, Sat, Sun
            $startTime = $baseCutoff->copy()->subDays(3); 
        } else {
            // For other days, start exactly 24 hours ago
            $startTime = $baseCutoff->copy()->subDay();
        }

        Log::info("Processing Window: From {$startTime->toDateTimeString()} to {$endTime->toDateTimeString()}");

        $contactmail = ContactMail::where('id', 1)->first()->name ?? 'info@tevini.co.uk';

        // Get transactions within the window
        $pendingBalances = Usertransaction::whereNotNull('charity_id')
            ->where('status', 1)
            ->whereBetween('business_date', [$startTime->toDateString(), $endTime->toDateString()]) // business_date
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

            $alreadyPaid = Transaction::where('charity_id', $charity->id)->where('status', 1)
                ->where('t_type', 'Out')
                ->whereBetween('business_date', [$startTime->toDateString(), $endTime->toDateString()])
                ->sum('amount');

            $amountToPayNow = $record->total - $alreadyPaid;

            if ($amountToPayNow > 0.01) {
                try {
                    DB::transaction(function () use ($charity, $amountToPayNow, $endTime, $startTime, $contactmail) {
                        
                        // Fetch the specific Usertransaction IDs for this window to save in the new table
                        $userTransactions = Usertransaction::where('charity_id', $charity->id)
                            ->where('status', 1)
                            ->whereBetween('business_date', [$startTime->toDateString(), $endTime->toDateString()])
                            ->get();
                            
                        $userTxIds = $userTransactions->pluck('id')->toArray();

                        // Create Payout Record
                        $transaction = new Transaction();
                        $transaction->t_id = "Out-" . time() . "-" . $charity->id;
                        $transaction->charity_id = $charity->id;
                        $transaction->t_type = "Out";
                        $transaction->name = "Bank";
                        $transaction->amount = $amountToPayNow;
                        $transaction->status = "1"; 
                        $transaction->created_at = $endTime;
                        $transaction->business_date = $endTime->toDateString();
                        $transaction->save();

                        $charity->decrement('balance', $amountToPayNow);

                        // Save data to the new tracking table
                        CharityPaymentBatch::create([
                            'charity_id'           => $charity->id,
                            'transaction_id'       => $transaction->id,
                            'last_payment_date'    => $endTime,
                            'usertransactions_ids' => $userTxIds, // Saved as JSON automatically by model cast
                            'amount'               => $amountToPayNow,
                        ]);

                        // PDF Generation
                        $details = $userTransactions->load(['user', 'donation']);

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

                        Mail::to($charity->email)->queue(new CharityDailyReport($mailData));
                        Mail::to($contactmail)->queue(new CharityDailyReport($mailData));

                        Log::info("Payment Process: Success for {$charity->name}. Emails queued. Batch saved.");
                    });
                } catch (\Exception $e) {
                    Log::error("Payment Process: Failed for Charity {$charity->id}. Error: " . $e->getMessage());
                }
            }
        }

        Log::info("Payment Process: Completed for cut-off " . $endTime);
    }

}

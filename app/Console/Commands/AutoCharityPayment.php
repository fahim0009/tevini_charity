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
        
        // Prevent multiple runs on the same day
        if ($companyDetail->last_payment_processed_date == Carbon::today()->toDateString()) {
            Log::info("Payment Process: Skipped. Payment already processed today.");
            return;
        }

        Log::info("Payment Process: Starting charity payout rolling window.");

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

        $dayOfWeek = $baseCutoff->dayOfWeek;

        // Weekend Logic: Skip processing if the cutoff lands on Saturday or Sunday.
        if (in_array($dayOfWeek, [0, 6])) {
            Log::info("Payment Process: Skipped. Target cutoff is a weekend. Will process on Monday.");
            return;
        }

        $endTime = $baseCutoff->copy()->subSecond();
        $targetBusinessDate = $baseCutoff->toDateString();

        Log::info("Processing Window for Business Date: {$targetBusinessDate}");

        $contactmail = ContactMail::where('id', 1)->first()->name ?? 'info@tevini.co.uk';

        // ✅ FIX: Use shared query method with business_date for Charity IDs
        $charityIds = $this->getEligibleDonationsQuery($targetBusinessDate)
            ->whereHas('charity', function ($q) {
                $q->where('auto_payment', 1);
            })
            ->distinct()
            ->pluck('charity_id');

        Log::info("Payment Process: Found " . $charityIds->count() . " charities with eligible donations.");

        $allCharitiesSucceeded = true;

        foreach ($charityIds as $charityId) {
            $charity = Charity::find($charityId);
            
            if (!$charity || !$charity->email) {
                Log::warning("Payment Process: Charity ID {$charityId} not found or missing email.");
                $allCharitiesSucceeded = false;
                continue;
            }

            if ($charity->auto_payment != 1) {
                Log::info("Payment Skipped: {$charity->name} has auto_payment disabled.");
                continue;
            }

            try {
                DB::transaction(function () use ($charity, $targetBusinessDate, $endTime, $contactmail, &$allCharitiesSucceeded) {
                    
                    $lockedCharity = Charity::where('id', $charity->id)->lockForUpdate()->first();
                    
                    // ✅ FIX: Shared query for Total Income
                    $totalIncome = $this->getEligibleDonationsQuery($targetBusinessDate, $charity->id)->sum('amount');

                    $alreadyPaid = Transaction::where('charity_id', $charity->id)
                        ->where('status', 1)
                        ->where('t_type', 'Out')
                        ->where('name', 'Bank')
                        ->where('business_date', $targetBusinessDate)
                        ->sum('amount');

                    $amountToPayNow = $totalIncome - $alreadyPaid;

                    Log::info("Payment Process: {$charity->name} - Income: {$totalIncome}, Already Paid: {$alreadyPaid}, To Pay: {$amountToPayNow}");

                    if ($amountToPayNow <= 0.01) {
                        Log::info("Payment Process: {$charity->name} - Nothing to pay. Skipping.");
                        return;
                    }

                    $transaction = new Transaction();
                    $transaction->t_id = "Out-" . uniqid() . "-" . $charity->id;
                    $transaction->charity_id = $charity->id;
                    $transaction->t_type = "Out";
                    $transaction->name = "Bank";
                    $transaction->amount = $amountToPayNow;
                    $transaction->status = "1"; 
                    $transaction->created_at = $endTime;
                    $transaction->business_date = $targetBusinessDate;
                    $transaction->save();

                    $lockedCharity->decrement('balance', $amountToPayNow);

                    // ✅ FIX: Shared query for Batch IDs
                    $incomeTransactionIds = $this->getEligibleDonationsQuery($targetBusinessDate, $charity->id)
                        ->pluck('id')
                        ->toArray();

                    CharityPaymentBatch::create([
                        'charity_id'           => $charity->id,
                        'transaction_id'       => $transaction->id,
                        'last_payment_date'    => $endTime,
                        'usertransactions_ids' => $incomeTransactionIds, 
                        'amount'               => $amountToPayNow,
                    ]);

                    // ✅ FIX: Shared query for PDF Details
                    $details = $this->getEligibleDonationsQuery($targetBusinessDate, $charity->id)
                        ->with(['user', 'donation'])
                        ->get();

                    $mailData = [
                        'name'          => $charity->name,
                        'transactionid' => $transaction->t_id,
                        'total'         => number_format($amountToPayNow, 2),
                        'date'          => $targetBusinessDate,
                        'subject'       => 'Daily Statement - ' . $targetBusinessDate,
                        'charity_email' => $charity->email,
                        'contactmail'   => $contactmail,
                        'charity_id'    => $charity->id,
                        'end_time_str'  => $endTime->format('Y-m-d-Hi'),
                        'details'       => $details,
                    ];

                    DB::afterCommit(function () use ($mailData) {
                        try {
                            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.charity_report', [
                                'charity' => Charity::find($mailData['charity_id']),
                                'details' => $mailData['details'],
                                'total'   => $mailData['total'],
                                'date'    => $mailData['date']
                            ]);

                            $fileName = 'Statement-' . $mailData['charity_id'] . '-' . $mailData['end_time_str'] . '.pdf';
                            $filePath = public_path('/invoices/' . $fileName);
                            file_put_contents($filePath, $pdf->output());

                            $mailData['file'] = $filePath;

                            Mail::to($mailData['charity_email'])->queue(new CharityDailyReport($mailData));
                            Mail::to($mailData['contactmail'])->queue(new CharityDailyReport($mailData));
                        } catch (\Exception $e) {
                            Log::error("Payment Process: PDF/Email failed for {$mailData['name']}: " . $e->getMessage());
                        }
                    });

                    Log::info("Payment Process: Success for {$charity->name}. Payout prepared. Batch saved.");
                });
            } catch (\Exception $e) {
                Log::error("Payment Process: Failed for Charity {$charity->id}. Error: " . $e->getMessage());
                $allCharitiesSucceeded = false;
            }
        }

        if ($allCharitiesSucceeded) {
            $companyDetail->auto_payment_time = '16:30';
            $companyDetail->last_payment_processed_date = Carbon::today()->toDateString();
            $companyDetail->save();
            
            Log::info("Payment Process: Auto payment time reverted to default 16:30.");
            Log::info("Payment Process: Completed for business date " . $targetBusinessDate);
        } else {
            Log::warning("Payment Process: Some charities failed. NOT locking date for retry.");
        }
    }

    /**
     * ✅ FIX: Shared query method to ensure consistent filters across all 4 uses
     * Uses business_date. Excludes expired vouchers, includes all settled (status=1) donations.
     */
    private function getEligibleDonationsQuery(string $businessDate, ?int $charityId = null)
    {
        $query = Usertransaction::whereNotNull('charity_id')
            ->where('status', 1)
            ->where('t_type', 'Out')
            ->where(function($q) {
                $q->where('expired', 1)->orWhereNull('expired');
            })
            ->where('business_date', $businessDate);

        if ($charityId) {
            $query->where('charity_id', $charityId);
        }

        return $query;
    }
}
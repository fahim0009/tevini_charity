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
        
        // ✅ Check if payment already ran today
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

        // ✅ FIX: Use Transaction (charity ledger) instead of Usertransaction
        // Charity income = Transaction where t_type='In' (money received by charity)
        // Already paid = Transaction where t_type='Out' AND name='Bank' (auto-payout)
        // This avoids JSON batch membership entirely!
        
        $charityIds = Transaction::where('status', 1)
            ->where('business_date', $targetBusinessDate)
            ->where('t_type', 'In')
            ->whereNotNull('charity_id')
            ->whereHas('charity', function ($q) {
                $q->where('auto_payment', 1);
            })
            ->distinct()
            ->pluck('charity_id');

        Log::info("Payment Process: Found " . $charityIds->count() . " charities with eligible transactions.");

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
                    
                    // ✅ FIX: Lock charity row
                    $lockedCharity = Charity::where('id', $charity->id)->lockForUpdate()->first();
                    
                    // ✅ FIX: Calculate income from charity Transaction ledger (In type)
                    $totalIncome = Transaction::where('charity_id', $charity->id)
                        ->where('status', 1)
                        ->where('t_type', 'In')
                        ->where('business_date', $targetBusinessDate)
                        ->sum('amount');

                    // ✅ FIX: Calculate already paid (Bank Out transactions)
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

                    // ✅ FIX: Create payout transaction with unique ID
                    $transaction = new Transaction();
                    $transaction->t_id = "Out-" . time() . "-" . $charity->id . "-" . uniqid();
                    $transaction->charity_id = $charity->id;
                    $transaction->t_type = "Out";
                    $transaction->name = "Bank";
                    $transaction->amount = $amountToPayNow;
                    $transaction->status = "1"; 
                    $transaction->created_at = $endTime;
                    $transaction->business_date = $targetBusinessDate;
                    $transaction->save();

                    $lockedCharity->decrement('balance', $amountToPayNow);

                    // ✅ FIX: Store batch record for audit (no JSON query needed)
                    // Pass array directly - Eloquent cast will handle encoding
                    $incomeTransactionIds = Transaction::where('charity_id', $charity->id)
                        ->where('status', 1)
                        ->where('t_type', 'In')
                        ->where('business_date', $targetBusinessDate)
                        ->pluck('id')
                        ->toArray();

                    CharityPaymentBatch::create([
                        'charity_id'           => $charity->id,
                        'transaction_id'       => $transaction->id,
                        'last_payment_date'    => $endTime,
                        'usertransactions_ids' => $incomeTransactionIds, // ✅ Array, not json_encode
                        'amount'               => $amountToPayNow,
                    ]);

                    // Generate PDF and send email
                    $details = Transaction::where('charity_id', $charity->id)
                        ->where('status', 1)
                        ->where('business_date', $targetBusinessDate)
                        ->where('t_type', 'In')
                        ->with(['user', 'donation'])
                        ->get();

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

                    // ✅ FIX: Send emails OUTSIDE transaction (queue after commit)
                    // But we need to prepare it inside, so we use dispatch after commit
                    $emailData = $mailData;
                    $emailData['charity_email'] = $charity->email;
                    $emailData['contactmail'] = $contactmail;
                    
                    DB::afterCommit(function () use ($emailData) {
                        try {
                            Mail::to($emailData['charity_email'])->queue(new CharityDailyReport($emailData));
                            Mail::to($emailData['contactmail'])->queue(new CharityDailyReport($emailData));
                        } catch (\Exception $e) {
                            Log::error("Payment Process: Email failed for {$emailData['name']}: " . $e->getMessage());
                        }
                    });

                    Log::info("Payment Process: Success for {$charity->name}. Payout prepared. Batch saved.");
                });
            } catch (\Exception $e) {
                Log::error("Payment Process: Failed for Charity {$charity->id}. Error: " . $e->getMessage());
                $allCharitiesSucceeded = false;
            }
        }

        // ✅ Only lock payment for today if ALL charities succeeded
        if ($allCharitiesSucceeded) {
            $companyDetail->auto_payment_time = '16:30';
            $companyDetail->last_payment_processed_date = Carbon::today()->toDateString();
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
        } else {
            Log::warning("Payment Process: Some charities failed. NOT locking date for retry.");
        }
    }
}
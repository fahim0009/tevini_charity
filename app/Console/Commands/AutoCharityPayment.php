<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usertransaction;
use App\Models\Transaction;
use App\Models\Charity;
use App\Models\ContactMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AutoCharityPayment extends Command
{
    protected $signature = 'payments:process-charity';
    protected $description = 'Consolidates daily balances and creates a single payment record per charity';




    public function handle()
    {
        // Ensure the script doesn't time out
        set_time_limit(0);

            $today = now()->subDay()->toDateString();
            // $today = now()->toDateString();
        $contactmail = ContactMail::where('id', 1)->first()->name;

        $pendingBalances = Usertransaction::whereNotNull('charity_id')
            ->whereDate('created_at', $today)
            ->select(['charity_id', DB::raw("SUM(amount) as total")])
            ->groupBy('charity_id')->get();

        foreach ($pendingBalances as $record) {
            $charity = Charity::find($record->charity_id);
            if (!$charity || !$charity->email) continue;

            // 1. Calculate how much has ALREADY been paid out today
            $alreadyPaidToday = Transaction::where('charity_id', $charity->id)
                ->where('t_type', 'Out')
                ->whereDate('created_at', $today)
                ->sum('amount');

            // 2. The amount to pay now is the Total Collected minus what was already paid
            $amountToPayNow = $record->total - $alreadyPaidToday;

            // 3. Only proceed if there is a remaining balance higher than 0.01
            if ($amountToPayNow > 0.01) {
                // 1. Database Logic
                $transaction = new Transaction();
                $transaction->t_id = "Out-" . time() . "-" . $charity->id;
                $transaction->charity_id = $charity->id;
                $transaction->t_type = "Out";
                $transaction->name = "Bank";
                $transaction->amount = $amountToPayNow;
                $transaction->status = "0"; 
                $transaction->created_at = $today . ' ' . now()->format('H:i:s');
                $transaction->save();

                $charity->decrement('balance', $amountToPayNow);

                // 2. Generate PDF
                $details = Usertransaction::where('charity_id', $charity->id)
                    ->whereDate('created_at', $today)
                    ->with('user')->get();

                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.charity_report', [
                    'charity' => $charity,
                    'details' => $details,
                    'total'   => $amountToPayNow,
                    'date'    => $today
                ]);

                $fileName = 'Statement-' . $charity->id . '-' . $today . '.pdf';
                $filePath = public_path('/invoices/' . $fileName);
                file_put_contents($filePath, $pdf->output());

                $mailData = [
                    'name'    => $charity->name,
                    'transactionid'    => $transaction->t_id,
                    'total'   => number_format($record->total, 2),
                    'date'    => $today,
                    'subject' => 'Daily Transaction Statement - ' . $today,
                    'file'    => $filePath,
                ];

                // 3. Send to Charity and Wait 10 seconds
                Mail::send('mail.charity_daily_report', $mailData, function($message) use ($charity, $mailData) {
                    $message->to($charity->email)->subject($mailData['subject'])->attach($mailData['file']);
                });
                $this->info("Email sent to charity: " . $charity->email);
                sleep(10); // Pause for 10 seconds

                // 4. Send to Admin and Wait 10 seconds
                Mail::send('mail.charity_daily_report', $mailData, function($message) use ($mailData, $contactmail) {
                    $message->to($contactmail)->subject($mailData['subject'])->attach($mailData['file']);
                });
                $this->info("Copy sent to admin.");
                sleep(10); // Pause for 10 seconds
            }
        }
    }


}

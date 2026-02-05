<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usertransaction;
use App\Models\Transaction;
use App\Models\Charity;
use Illuminate\Support\Facades\DB;

class AutoCharityPayment extends Command
{
    protected $signature = 'payments:process-charity';
    protected $description = 'Consolidates daily balances and creates a single payment record per charity';

    public function handle()
    {
        
        $today = now()->subDay()->toDateString();
        // $today = now()->toDateString();
        $contactmail = DB::table('contact_mails')->where('id', 1)->value('name');

        $pendingBalances = Usertransaction::whereNotNull('charity_id')
            ->whereDate('created_at', $today)
            ->select(['charity_id', DB::raw("SUM(amount) as total")])
            ->groupBy('charity_id')->get();

        foreach ($pendingBalances as $record) {
            $charity = Charity::find($record->charity_id);
            if (!$charity || !$charity->email) continue;

            // Check if already processed
            $exists = Transaction::where('charity_id', $charity->id)
                ->where('t_type', 'Out')->whereDate('created_at', $today)->exists();

            if (!$exists && $record->total > 0.01) {
                // 1. Save the Transaction record
                $transaction = new Transaction();
                $transaction->t_id = "Out-" . time() . "-" . $charity->id;
                $transaction->charity_id = $charity->id;
                $transaction->t_type = "Out";
                $transaction->name = "Bank";
                $transaction->amount = $record->total;
                $transaction->status = "0"; // Pending manual switch
                $transaction->created_at = $today . ' ' . now()->format('H:i:s');
                $transaction->save();

                // 2. Decrement Charity Balance
                $charity->decrement('balance', $record->total);

                // 3. Get all transactions for the PDF
                $details = Usertransaction::where('charity_id', $charity->id)
                    ->whereDate('created_at', $today)
                    ->with('user')
                    ->get();

                // 4. Generate PDF
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.charity_report', [
                    'charity' => $charity,
                    'details' => $details,
                    'total'   => $record->total,
                    'date'    => $today
                ]);

                $fileName = 'Statement-' . $charity->id . '-' . $today . '.pdf';
                $filePath = public_path('/invoices/' . $fileName);
                file_put_contents($filePath, $pdf->output());

                // 5. Prepare and Send Mail
                $mailData = [
                    'name'    => $charity->name,
                    'total'   => number_format($record->total, 2),
                    'date'    => $today,
                    'subject' => 'Daily Transaction Statement - ' . $today,
                    'file'    => $filePath,
                    'file_name' => $fileName,
                    'cc'      => $contactmail
                ];


                \Mail::send('mail.charity_daily_report', $mailData, function($message) use ($charity, $mailData) {
                    $message->to($charity->email)
                            ->subject($mailData['subject'])
                            ->attach($mailData['file']);
                });

                $this->info("Email sent to " . $charity->email);
            }
        }
    }
}

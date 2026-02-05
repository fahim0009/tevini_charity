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
        // $today = now()->subDay()->toDateString();
        $today = now()->toDateString();

        $pendingBalances = Usertransaction::whereNotNull('charity_id')
            ->whereDate('created_at', $today) 
            ->select([
                'charity_id',
                DB::raw("SUM(amount) as total_generated_today")
            ])
            ->groupBy('charity_id')
            ->get();

        foreach ($pendingBalances as $record) {
            $alreadyPaidToday = Transaction::where('charity_id', $record->charity_id)
                ->where('t_type', 'Out')
                ->where('name', 'Bank')
                ->whereDate('created_at', $today)
                ->exists();

            if (!$alreadyPaidToday && $record->total_generated_today > 0.01) {
                $t_id = "Out-" . time() . "-" . $record->charity_id;
                $amountToPay = $record->total_generated_today;

                // 3. Create the payment record
                $transaction = new Transaction();
                $transaction->t_id = $t_id;
                $transaction->charity_id = $record->charity_id;
                $transaction->t_type = "Out";
                $transaction->name = "Bank";
                $transaction->amount = $amountToPay;
                $transaction->note = "Consolidated payment for " . $today;
                $transaction->status = "0";
                $transaction->save();

                // 4. Update Charity Balance
                Charity::where('id', $record->charity_id)->decrement('balance', $amountToPay);

                $this->info("Successfully processed £{$amountToPay} for Charity ID: {$record->charity_id}");
            } else {
                $this->line("Skipping Charity ID: {$record->charity_id} (Already paid or no balance for today)");
            }
        }
    }
}

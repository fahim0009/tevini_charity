<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Usertransaction;
use App\Models\Charity;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SystemController extends Controller
{
    public function dailyPaidTransaction(Request $request)
    {
        $request->validate([
            'fromDate' => 'nullable|date',
            'toDate'   => 'nullable|date|after_or_equal:fromDate',
        ]);
 
        $fromDate    = $request->input('fromDate', '2026-02-08');
        $toDate      = $request->input('toDate', date('Y-m-d'));
        $endDateTime = $toDate ? $toDate . ' 23:59:59' : null;
 
        $query = Transaction::
            where('status', 1)
            ->where('t_type', 'Out')
            ->where('name', '=', 'Bank')
            ->whereRaw('TIME(created_at) BETWEEN ? AND ?', ['17:20:00', '17:39:59']);
            // ->whereTime('created_at', '=', '17:29:00');

            // ->whereRaw('TIME(created_at) BETWEEN ? AND ?', ['17:00:00', '17:59:59'])
 
        if ($fromDate && $toDate) {
            $query->whereBetween('created_at', [$fromDate, $endDateTime]);
        }
 
        $transactions = $query->latest()->get();
 
        $totalAmount = Transaction::where('bank_payment_status', 0)
            ->where('status', 1)
            ->where('t_type', 'Out')
            ->where('name', '=', 'Bank')
            ->sum('amount');

            $count = $query->count();
 
        return view('admin.devTest.daily_paid_transactions', compact('transactions', 'totalAmount', 'fromDate', 'toDate','count'));

    }

    public function updateTransactionDates(Request $request)
    {
        $request->validate([
            'selected_ids'   => 'required|array|min:1',
            'selected_ids.*' => 'integer|exists:transactions,id',
            'new_date'       => 'required|date',
        ]);

        $newDateTime = \Carbon\Carbon::parse($request->new_date)->format('Y-m-d H:i:s');

        Transaction::whereIn('id', $request->selected_ids)
            ->update(['created_at' => $newDateTime]);

        return redirect()->route('dailyPaidTransaction')
            ->with('success', count($request->selected_ids) . ' transaction(s) updated to ' . $newDateTime);
    }



    public function userTransactionDate(Request $request)
    {
        $t_id    = $request->input('t_id');
        $results = collect();

        if ($t_id) {
            $results = Usertransaction::with('charity')
                ->where('t_id', 'LIKE', '%' . $t_id . '%')
                ->latest()
                ->get();
        }

        return view('admin.devTest.user_transaction_date', compact('results', 't_id'));
    }

    public function updateUserTransactionDate(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|integer|exists:usertransactions,id',
            'new_datetime'   => 'required|date',
        ]);

        $newDateTime = \Carbon\Carbon::parse($request->new_datetime)->format('Y-m-d H:i:s');

        Usertransaction::where('id', $request->transaction_id)
            ->update(['created_at' => $newDateTime]);

        return redirect()
            ->route('userTransactionDate', ['t_id' => $request->t_id_ref])
            ->with('success', 'Transaction ' . $request->t_id_ref . ' updated to ' . $newDateTime);
    }



    public function charityPaymentCreate()
    {
        $charities = Charity::orderBy('name')->get();
        return view('admin.devTest.charity_payment', compact('charities'));
    }

    public function charityPaymentStore(Request $request)
    {
        $request->validate([
            'topupid'    => 'required|integer|exists:charities,id',
            'balance'    => 'required|numeric|min:0.01',
            'note'       => 'required|string',
            'source'     => 'required|in:Bank,Cheque,Card',
            'created_at' => 'required|date',
        ]);

        $charity   = Charity::findOrFail($request->topupid);
        $t_id      = time() . "-" . $request->topupid;
        $createdAt = Carbon::parse($request->created_at);

        $transaction             = new Transaction();
        $transaction->t_id       = $t_id;
        $transaction->charity_id = $request->topupid;
        $transaction->t_type     = 'Out';
        $transaction->name       = $request->source;
        $transaction->amount     = $request->balance;
        $transaction->note       = $request->note;
        $transaction->status     = '1';
        $transaction->created_at = $createdAt;
        $transaction->save();

        return back()->with('success', 'Transaction saved successfully. ID: ' . $t_id);
    }



}

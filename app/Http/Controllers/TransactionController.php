<?php

namespace App\Http\Controllers;

use App\Models\Batchprov;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Provoucher;
use App\Models\Charity;
use App\Models\Transaction;
use App\Models\Usertransaction;
use App\Models\Donation;
use App\Models\User;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');

        $dateFilter = function ($query) use ($fromDate, $toDate) {
            if (!empty($fromDate) && !empty($toDate)) {
                $query->whereBetween('created_at', [$fromDate, $toDate . ' 23:59:59']);
            }
        };

        $outtransactions = Transaction::with('charity')
            ->where('t_type', 'Out')
            ->select('id', 't_id', 'created_at', 'charity_id', 'crdAcptID', 'crdAcptLoc', 'name', 'note', 'amount')
            ->where($dateFilter)
            ->orderBy('id', 'DESC')
            ->get();

        $intransactions = Transaction::with('user')
            ->where('t_type', 'In')
            ->select('id', 't_id', 'created_at', 'user_id', 'name', 'amount')
            ->where($dateFilter)
            ->orderBy('id', 'DESC')
            ->get();

        $alltransactions = Transaction::with(['user', 'charity'])
            ->select('id', 't_id', 'created_at', 'user_id', 'charity_id', 'crdAcptID', 'crdAcptLoc', 'name', 'note', 'amount')
            ->where($dateFilter)
            ->orderBy('id', 'DESC')
            ->get();

        return view('transaction.index')
            ->with('alltransactions', $alltransactions)
            ->with('outtransactions', $outtransactions)
            ->with('intransactions', $intransactions);
    }


    public function adminTransactionView()
    {
        return view('transaction.tranview');
    }

    public function remittance_old(Request $request)
    {


        if(!empty($request->input('fromdate')) && !empty($request->input('todate'))&& !empty($request->input('charityid'))){
            $fromDate = $request->input('fromdate');
            $toDate   = $request->input('todate');
            $charityid = $request->input('charityid');
            $charity = Charity::where('id','=',$charityid)->first();
            $remittance = Provoucher::where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59']
            ])->whereIn('status', ['1', '0'])->where('charity_id','=', $charityid)->orderBy('id','DESC')->get();

            $total = Provoucher::where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59'],
                ['status', '=', '1']
            ])->where('charity_id','=', $charityid)->sum('amount');

        }elseif(empty($request->input('fromdate')) && empty($request->input('todate'))&& !empty($request->input('charityid'))){
            $charityid = $request->input('charityid');
            $charity = Charity::where('id','=',$charityid)->first();
            $remittance = Provoucher::where('charity_id','=', $charityid)->whereIn('status', ['1', '0'])->orderBy('id','DESC')->get();
            $total = Provoucher::where([
                ['charity_id','=', $charityid],
                ['status', '=', '1']
                ])->sum('amount');
            $fromDate = "";
            $toDate   = "";
        }else{
            $remittance = Provoucher::whereIn('status', ['1', '0'])->orderBy('id','DESC')->get();
            $total = Provoucher::where('status', '=', '1')->sum('amount');
            $charity = "";
            $fromDate = "";
            $toDate   = "";
        }
        return view('remittance.index',compact('remittance','total','fromDate','toDate','charity'));
    }

    public function remittance(Request $request)
    {
        $fromDate = $request->input('fromdate');
        $toDate = $request->input('todate');
        $charityId = $request->input('charityid');

        $query = Provoucher::query()->whereIn('status', ['1', '0']);

        // Filter by charity if charityid is given
        $charity = null;
        if (!empty($charityId)) {
            $query->where('charity_id', $charityId);
            $charity = Charity::find($charityId);
        }

        // Filter by date range if both dates are provided
        if (!empty($fromDate) && !empty($toDate)) {
            $query->whereBetween('created_at', [$fromDate, $toDate . ' 23:59:59']);
        }

        $remittance = $query->orderBy('id', 'DESC')->get();

        // Calculate total separately (only status = 1)
        $totalQuery = Provoucher::query()->where('status', '1');
        if (!empty($charityId)) {
            $totalQuery->where('charity_id', $charityId);
        }
        if (!empty($fromDate) && !empty($toDate)) {
            $totalQuery->whereBetween('created_at', [$fromDate, $toDate . ' 23:59:59']);
        }
        $total = $totalQuery->sum('amount');

        return view('remittance.index', compact('remittance', 'total', 'fromDate', 'toDate', 'charity'));
    }


    public function userTransactionShow(Request $request)
    {
        $userId = auth()->id();
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate') ? $request->input('toDate') . ' 23:59:59' : null;

        $hasDateRange = $fromDate && $toDate;

        // Total amount transactions
        $tamount = Usertransaction::where('user_id', $userId)
            ->when($hasDateRange, function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->where('status', 1)
            ->orderByDesc('id')
            ->get();

        // All transactions
        $alltransactions = Usertransaction::with([
            'charity:id,name',
            'standingdonationDetail.standingDonation:id,charitynote,mynote',
            'donation:id,charitynote,mynote',
            'campaign:id,campaign_title'
        ])
        ->where('user_id', $userId)
        ->whereNotNull('donation_id')
        ->where(function ($query) use ($hasDateRange, $fromDate, $toDate) {
            $query->where('status', 1)
                ->when($hasDateRange, fn($q) => $q->whereBetween('created_at', [$fromDate, $toDate]));
        })
        ->orWhere(function ($query) use ($userId, $hasDateRange, $fromDate, $toDate) {
            $query->where('user_id', $userId)
                ->where('pending', 1)
                ->when($hasDateRange, fn($q) => $q->whereBetween('created_at', [$fromDate, $toDate]));
        })
        ->orderByDesc('id')
        ->get();


        // In Transactions
        $intransactions = Usertransaction::where('user_id', $userId)
            ->where('t_type', 'In')
            ->where('status', 1)
            ->when($hasDateRange, function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->orderByDesc('id')
            ->get();

        // Out Transactions
        $outtransactions = Usertransaction::where('user_id', $userId)
            ->where('t_type', 'Out')
            ->where(function ($query) {
                $query->where('status', 1);
            })
            ->when($hasDateRange, function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->orWhere(function ($query) use ($userId, $hasDateRange, $fromDate, $toDate) {
                $query->where('user_id', $userId)
                    ->where('t_type', 'Out')
                    ->where('pending', 1);

                if ($hasDateRange) {
                    $query->whereBetween('created_at', [$fromDate, $toDate]);
                }
            })
            ->orderByDesc('id')
            ->get();

        // Pending Transactions
        $pending_transactions = Usertransaction::where('user_id', $userId)
            ->where('t_type', 'Out')
            ->where('pending', 0)
            ->when($hasDateRange, function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->orderByDesc('id')
            ->get();

        // Gift Aid Transactions
        $giftAid = Usertransaction::where('user_id', $userId)
            ->where('status', 1)
            ->whereNotNull('gift')
            ->when($hasDateRange, function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->orderByDesc('id')
            ->get();

        return view('frontend.user.transaction', compact(
            'alltransactions',
            'intransactions',
            'tamount',
            'outtransactions',
            'pending_transactions',
            'giftAid'
        ));
    }

    public function donorTransaction(Request $request, $id)
    {
        $user = User::findOrFail($id); // fail-safe
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate') ? $request->input('toDate') . ' 23:59:59' : null;

        // Base transaction query
        $baseQuery = Usertransaction::where('user_id', $id);

        // Get total amount transactions
        $totalTransactions = $baseQuery->where('status', '1')
                                    ->orderByDesc('id')
                                    ->get();

        // Transaction filters
        $reportQuery = Usertransaction::where('user_id', $id)
            ->where(function ($query) use ($fromDate, $toDate) {
                if ($fromDate && $toDate) {
                    $query->whereBetween('created_at', [$fromDate, $toDate])
                        ->where('status', '1');
                } else {
                    $query->where('status', '1');
                }
            })->orWhere(function ($query) use ($id, $fromDate, $toDate) {
                $query->where('user_id', $id);
                if ($fromDate && $toDate) {
                    $query->whereBetween('created_at', [$fromDate, $toDate])
                        ->where('pending', '0');
                } else {
                    $query->where('pending', '0');
                }
            });

        $report = $reportQuery->orderByDesc('id')->get();

        // In Transactions
        $inTransactions = Usertransaction::where('t_type', 'In')
            ->where('user_id', $id)
            ->when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('created_at', [$fromDate, $toDate]);
            })
            ->where('status', '1')
            ->orderByDesc('id')
            ->get();

        // Out Transactions
        $outTransactionsQuery = Usertransaction::where('t_type', 'Out')
            ->where('user_id', $id)
            ->where(function ($query) use ($fromDate, $toDate) {
                if ($fromDate && $toDate) {
                    $query->whereBetween('created_at', [$fromDate, $toDate])
                        ->where('status', '1');
                } else {
                    $query->where('status', '1');
                }
            })->orWhere(function ($query) use ($id, $fromDate, $toDate) {
                $query->where('t_type', 'Out')
                    ->where('user_id', $id);
                if ($fromDate && $toDate) {
                    $query->whereBetween('created_at', [$fromDate, $toDate]);
                }
                $query->where('pending', '0');
            });

        $outTransactions = $outTransactionsQuery->orderByDesc('id')->get();

        return view('donor.transaction', [
            'intransactions' => $inTransactions,
            'outtransactions' => $outTransactions,
            'report' => $report,
            'fromDate' => $fromDate ?? '',
            'toDate' => $request->input('toDate') ?? '',
            'user' => $user,
            'donor_id' => $id,
            'tamount' => $totalTransactions,
        ]);
    }


    public function charityTransaction(Request $request, $id)
    {

        if(!empty($request->input('fromDate')) && !empty($request->input('toDate'))){
            $fromDate = $request->input('fromDate');
            $toDate   = $request->input('toDate');

            $intransactions = Usertransaction::where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59'],
                ['t_type','=', 'Out'],
                ['charity_id','=', $id],
                ['status','=', '1']
            ])->orderBy('id','DESC')->get();

            $outtransactions = Transaction::where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59'],
                ['t_type','=', 'Out'],
                ['charity_id','=', $id],
                ['status','=', '1']
            ])->orderBy('id','DESC')->get();

            $reports = Batchprov::where([
                ['created_at', '>=', $fromDate],
                ['created_at', '<=', $toDate.' 23:59:59'],
                ['charity_id','=', $id]
            ])->orderby('id','DESC')->get();

        }else{

            $intransactions = Usertransaction::where([
                ['t_type','=', 'Out'],
                ['charity_id','=', $id],
                ['status','=', '1']
            ])->orderBy('id','DESC')->get();

            $outtransactions= Transaction::where([
                ['t_type','=', 'Out'],
                ['charity_id','=', $id],
                ['status','=', '1']
            ])->orderBy('id','DESC')->get();

            $reports = Batchprov::where('charity_id','=', $id)->orderby('id','DESC')->get();

        }


        $totalIN = Usertransaction::where([
            ['t_type','=', 'Out'],
            ['charity_id','=', $id],
            ['status','=', '1']
        ])->orderBy('id','DESC')->sum('amount');

        $totalOUT= Transaction::where([
            ['t_type','=', 'Out'],
            ['charity_id','=', $id],
            ['status','=', '1']
        ])->orderBy('id','DESC')->sum('amount');

        // $ledgers = DB::table('charities')
        // ->join('usertransactions', 'charities.id', '=', 'usertransactions.charity_id')
        // ->join('transactions', 'charities.id', '=', 'transactions.charity_id')
        // ->select('transactions.amount as tranamount','transactions.t_type as trantype','transactions.t_id as trantid','usertransactions.t_id as utrantid','usertransactions.amount as utranamount')
        // ->where('usertransactions.charity_id', $id)->get();

        $pvouchers = Provoucher::where([
            ['charity_id', '=', $id],
            ['waiting', '=', 'No'],
            ['status', '=', '0']
        ])->orderBy('id','DESC')->get();


        return view('charity.transaction')
        ->with('intransactions',$intransactions)
        ->with('id',$id)
        ->with('reports',$reports)
        ->with('totalIN',$totalIN)
        ->with('totalOUT',$totalOUT)
        ->with('pvouchers',$pvouchers)
        ->with('outtransactions',$outtransactions);
    }

}

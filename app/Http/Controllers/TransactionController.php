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
use Illuminate\Support\Carbon;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{


    public function index(Request $request)
    {
        if ($request->ajax()) {
            $type = $request->get('t_type'); // We'll pass this from JS
            $fromDate = $request->get('fromDate');
            $toDate = $request->get('toDate');

            $query = Transaction::query();

            // Apply type filter
            if ($type === 'In' || $type === 'Out') {
                $query->where('t_type', $type);
            }

            // Apply date filter
            if ($fromDate && $toDate) {
                $query->whereBetween('created_at', [$fromDate, $toDate . ' 23:59:59']);
            }

            // Load relationships based on type
            $query->with(['user', 'charity']);

            return DataTables::of($query)
                ->editColumn('created_at', function ($row) {
                    return Carbon::parse($row->created_at)->format('d/m/Y');
                })
                ->addColumn('beneficiary', function ($row) {
                    if ($row->charity_id) return $row->charity->name;
                    if ($row->crdAcptID) return $row->crdAcptLoc;
                    return '';
                })
                ->addColumn('donor', function ($row) {
                    return $row->user ? $row->user->name.' '.$row->user->surname : '';
                })
                ->editColumn('amount', function ($row) {
                    return '£' . number_format($row->amount, 2);
                })
                ->rawColumns(['beneficiary', 'donor'])
                ->make(true);
        }

        return view('transaction.index');
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

    public function remittance2(Request $request)
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

    public function remittance(Request $request)
    {
        $fromDate = $request->input('fromdate');
        $toDate = $request->input('todate');
        $charityId = $request->input('charityid');

        // BUILD MAIN QUERY
        $query = Provoucher::query()
            ->whereIn('status', ['1', '0'])
            ->with('charity')
            ->orderBy('id', 'DESC');

        // FILTERS
        $charity = null;

        if (!empty($charityId)) {
            $query->where('charity_id', $charityId);
            $charity = Charity::find($charityId);
        }

        if (!empty($fromDate) && !empty($toDate)) {
            $query->whereBetween('created_at', [$fromDate, $toDate . ' 23:59:59']);
        }

        // AJAX REQUEST FOR DATATABLE
        if ($request->ajax()) {

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('date', fn($d) => $d->created_at->format('d/m/Y'))
                ->addColumn('description', fn($d) => 'Vouchers')
                ->addColumn('voucher', fn($d) => $d->cheque_no)
                ->addColumn('amount', fn($d) => '£' . number_format($d->amount, 2))
                ->addColumn('balance', function ($d) use ($fromDate, $toDate, $charityId) {

                    $balanceQuery = Provoucher::query()
                        ->where('status', 1);

                    if (!empty($charityId)) {
                        $balanceQuery->where('charity_id', $charityId);
                    }
                    if (!empty($fromDate) && !empty($toDate)) {
                        $balanceQuery->whereBetween('created_at', [$fromDate, $toDate . ' 23:59:59']);
                    }

                    $balance = $balanceQuery->sum('amount');

                    return '£' . number_format($balance, 2);
                })
                ->addColumn('notes', fn($d) => $d->note)
                ->addColumn('status_text', function ($d) {

                    if ($d->status == 1) return "COMPLETE";
                    if ($d->status == 0 && $d->waiting == "Yes") return "AWAITING CONFIRMATION";
                    if ($d->status == 0 && $d->waiting == "No") return "PENDING";
                    if ($d->status == 3) return "CANCEL";

                    return "";
                })
                ->rawColumns(['amount'])
                ->make(true);
        }

        // NORMAL BLADE LOAD
        $remittance = $query->get();

        // TOTAL
        $totalQuery = Provoucher::query()
            ->where('status', '1');

        if (!empty($charityId)) {
            $totalQuery->where('charity_id', $charityId);
        }
        if (!empty($fromDate) && !empty($toDate)) {
            $totalQuery->whereBetween('created_at', [$fromDate, $toDate . ' 23:59:59']);
        }

        $total = $totalQuery->sum('amount');

        return view('remittance.index', compact(
            'remittance',
            'total',
            'fromDate',
            'toDate',
            'charity'
        ));
    }


    public function userTransactionShow(Request $request)
    {
        $userId = auth()->id();
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate') ? $request->input('toDate') . ' 23:59:59' : null;

        $hasDateRange = $fromDate && $toDate;

        // Total amount transactions
        $tamount = Usertransaction::where('user_id', $userId)->with('provoucher')
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

    public function donorTransactionShow(Request $request)
    {
        $userId = auth()->id();
        $fromDate = $request->input('fromDate');
        $toDate = $request->input('toDate') ? $request->input('toDate') . ' 23:59:59' : null;
        $hasDateRange = $fromDate && $toDate;

        // 1. Calculate the initial Total Balance (Running Balance starting point)
        $tamount = Usertransaction::where('user_id', $userId)
            ->where('status', 1)
            ->when($hasDateRange, fn($q) => $q->whereBetween('created_at', [$fromDate, $toDate]))
            ->get();

        $runningBalance = 0;
        foreach ($tamount as $data) {
            if ($data->commission != 0) $runningBalance -= $data->commission;
            if ($data->t_type == "In") {
                $runningBalance += ($data->commission != 0) ? ($data->amount + $data->commission) : $data->amount;
            } else {
                $runningBalance -= $data->amount;
            }
        }

        // 2. Fetch All Transactions
        $query = Usertransaction::with([
                'charity:id,name',
                'standingdonationDetail.standingDonation:id,charitynote,mynote',
                'donation:id,charitynote,mynote',
                'campaign:id,campaign_title',
                'provoucher'
            ])
            ->where('user_id', $userId)
            ->where(function ($q) use ($hasDateRange, $fromDate, $toDate) {
                $q->where('status', 1)
                ->when($hasDateRange, fn($sub) => $sub->whereBetween('created_at', [$fromDate, $toDate]));
            })
            ->orWhere(function ($q) use ($userId, $hasDateRange, $fromDate, $toDate) {
                $q->where('user_id', $userId)
                ->where('pending', 1)
                ->when($hasDateRange, fn($sub) => $sub->whereBetween('created_at', [$fromDate, $toDate]));
            })
            ->orderByDesc('id')
            ->get();

        // 3. Transform data to handle the "Commission Row" logic
        // We use a temporary variable to track balance backward during transformation
        $tempBalance = $runningBalance;
        
        $transformed = $query->flatMap(function ($data) use (&$tempBalance) {
            $rows = [];

            // Main Transaction Row Logic
            $currentRow = clone $data;
            $currentRow->display_type = 'main';
            $currentRow->calculated_balance = $tempBalance;

            // Calculate next balance step
            if ($data->t_type == "In") {
                $tempBalance -= ($data->commission != 0) ? ($data->amount + $data->commission) : $data->amount;
            } elseif ($data->t_type == "Out") {
                if($data->pending != "0" && (!isset($data->provoucher) || $data->provoucher->expired != "Yes")) {
                    $tempBalance += $data->amount; 
                }
            }

            // Commission Row Logic (If exists, it appears ABOVE the transaction in your blade)
            if ($data->commission != 0) {
                $commRow = clone $data;
                $commRow->display_type = 'commission';
                // In your blade, the commission row shows the balance BEFORE the commission is added back
                $commRow->calculated_balance = $currentRow->calculated_balance + $data->commission; 
                $rows[] = $commRow;
            }

            $rows[] = $currentRow;
            return $rows;
        });

        return DataTables::of($transformed)
            ->addIndexColumn()
            ->editColumn('created_at', fn($row) => Carbon::parse($row->created_at)->format('d/m/Y'))
            ->addColumn('description', function($row) {
                if ($row->display_type == 'commission') return 'Commission';
                
                $charityName = $row->charity ? $row->charity->name : '';
                $location = $row->crdAcptID ? $row->crdAcptLoc : '';
                
                // Return the HTML for the Description + Modal Trigger
                return '
                    <div class="d-flex flex-column">
                        <span class="fs-20 txt-secondary fw-bold">'.$charityName.' '.$location.'</span>
                        <span class="fs-16 txt-secondary">
                            '.$row->title.'
                            <a href="#" data-bs-toggle="modal" data-bs-target="#tranModal'.$row->id.'" style="margin-left: 5px;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#18988B" class="bi bi-arrow-up-circle" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/>
                                    <path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.147 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5A.5.5 0 0 0 8 12z"/>
                                </svg>
                            </a>
                        </span>
                    </div>' . view('frontend.user.partials.transaction_modal', ['data' => $row])->render(); 
            })
            ->addColumn('amount', function($row) {
                if ($row->display_type == 'commission') return '-£' . number_format($row->commission, 2);
                
                $amt = number_format($row->amount + ($row->t_type == "In" ? $row->commission : 0), 2);
                
                // Original SVGs from your blade
                $upIcon = '<svg width="11" height="13" viewBox="0 0 11 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.0527 5.89619C9.96315 5.98283 9.84339 6.03126 9.71876 6.03126C9.59413 6.03126 9.47438 5.98283 9.38478 5.89619L5.96876 2.47432V11.656C5.96876 11.7803 5.91938 11.8995 5.83147 11.9874C5.74356 12.0753 5.62433 12.1247 5.50001 12.1247C5.37569 12.1247 5.25646 12.0753 5.16856 11.9874C5.08065 11.8995 5.03126 11.7803 5.03126 11.656V2.47432L1.61525 5.89619C1.52417 5.97094 1.40855 6.00914 1.29087 6.00336C1.17319 5.99758 1.06186 5.94823 0.978549 5.86492C0.895236 5.78161 0.84589 5.67028 0.84011 5.5526C0.834331 5.43492 0.87253 5.3193 0.947278 5.22822L5.16603 1.00947C5.2549 0.92145 5.37493 0.87207 5.50001 0.87207C5.6251 0.87207 5.74512 0.92145 5.834 1.00947L10.0527 5.22822C10.1408 5.31709 10.1901 5.43712 10.1901 5.56221C10.1901 5.68729 10.1408 5.80732 10.0527 5.89619Z" fill="#18988B"></path></svg>';
                $downIcon = '<svg width="11" height="13" viewBox="0 0 11 13" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10.0527 7.18393C9.96315 7.08574 9.84339 7.03085 9.71876 7.03085C9.59413 7.03085 9.47438 7.08574 9.38478 7.18393L5.96876 11.0621V0.656192C5.96876 0.515295 5.91938 0.380169 5.83147 0.28054C5.74356 0.180912 5.62433 0.124942 5.50001 0.124942C5.37569 0.124942 5.25646 0.180912 5.16856 0.28054C5.08065 0.380169 5.03126 0.515295 5.03126 0.656192V11.0621L1.61525 7.18393C1.52417 7.09921 1.40855 7.05592 1.29087 7.06247C1.17319 7.06902 1.06186 7.12494 0.978549 7.21937C0.895236 7.31379 0.84589 7.43995 0.84011 7.57333C0.834331 7.7067 0.87253 7.83774 0.947278 7.94096L5.16603 12.7222C5.2549 12.822 5.37493 12.8779 5.50001 12.8779C5.6251 12.8779 5.74512 12.822 5.834 12.7222L10.0527 7.94096C10.1408 7.84024 10.1901 7.7042 10.1901 7.56244C10.1901 7.42068 10.1408 7.28465 10.0527 7.18393Z" fill="#003057"/></svg>';

                $icon = $row->t_type == "In" ? $upIcon : $downIcon;
                $prefix = $row->t_type == "Out" ? "-£" : "£";
                
                return '<span class="fs-16 txt-secondary">' . $prefix . $amt . ' ' . $icon . '</span>';
            })
            ->addColumn('reference', function($row) {
                if ($row->display_type == 'commission') return $row->t_id;
                return ($row->title == "Voucher") ? $row->cheque_no : $row->t_id;
            })
            ->editColumn('calculated_balance', fn($row) => '£' . number_format($row->calculated_balance, 2))
            ->rawColumns(['description', 'amount'])
            ->make(true);
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
        $reportQuery = Usertransaction::where('user_id', $id)->with('provoucher')
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
        $outTransactionsQuery = Usertransaction::where('t_type', 'Out')->with('provoucher')
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


    public function checkTran(Request $request)
    {
        if ($request->isMethod('post')) {

            $request->validate([
                'tranId' => 'required|string|max:255',
            ]);
    

            $tranId = $request->tranId;
            
            $chktran = Usertransaction::where('t_id', $tranId)->get();



            if ($chktran->count() > 0) {
                
                return view('transaction.delete', compact('chktran','tranId'))->with('success', 'Data found successfully.');
            } else {
                
                return redirect()->back()->with(['error' => 'Data not found.']);
            }
            
        }else{
            return view('transaction.delete');
        }
        
    }

    public function changeTranStatus(Request $request)
    {
        $request->validate([
            'tranId' => 'required|string|max:255',
        ]);

        $tranId = $request->tranId;

        $barcodes = Usertransaction::where('t_id', $tranId);

        if ($barcodes->count() > 0) {
            
            $barcodes->update(['status' => 0]);

            return response()->json([
                'success' => true,
                'message' => 'data deleted successfully.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No data found to delete.'
            ]);
        }
    }





}

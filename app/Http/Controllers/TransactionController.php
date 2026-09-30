<?php

namespace App\Http\Controllers;

use App\Models\Authorisation;
use App\Models\Batchprov;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\Provoucher;
use App\Models\Charity;
use App\Models\CompanyDetail;
use App\Models\Transaction;
use App\Models\Usertransaction;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{





    /**
     * Helper function to calculate dynamic start and end time
     * based on the selected business date and weekend logic.
     */
    private function getBusinessDateWindow($dateStr)
    {
        $companyDetail = CompanyDetail::first();
        $autoPaymentTime = $companyDetail->auto_payment_time ?? '16:30';
        
        $timeParts = explode(':', $autoPaymentTime);
        $hour = (int) $timeParts[0];
        $minute = (int) $timeParts[1] + 1; // +1 minute to match previous 16:31 logic safely

        $businessDate = Carbon::createFromFormat('Y-m-d', $dateStr);

        if ($businessDate->isMonday()) {
            // If business date is Monday, start from Thursday's cutoff time
            // This covers Friday, Saturday, Sunday, and Monday
            $startDateTime = $businessDate->copy()->subDays(4)->setTime($hour, $minute, 0);
        } else {
            // Standard 24 hours window: Yesterday's cutoff time
            $startDateTime = $businessDate->copy()->subDay()->setTime($hour, $minute, 0);
        }

        $endDateTime = $businessDate->copy()->setTime($hour, $minute, 59);

        return [$startDateTime, $endDateTime];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $type = $request->get('t_type');
            $fromDate = $request->get('fromDate');
            $toDate = $request->get('toDate');

            if ($type === 'Summary' || $type === 'PreviousSummary') {

                 // Fetch dynamic cutoff time for Paid Subquery
                $companyDetail = CompanyDetail::first();
                $autoPaymentTime = $companyDetail->auto_payment_time ?? '16:30';
                $cutoffTime = $autoPaymentTime . ':00';

                // Dynamic Business Date Logic for Transactions (Out) table
                $baseDateCalcTx = "
                    CASE 
                        WHEN TIME(transactions.created_at) >= '$cutoffTime'
                        THEN DATE_ADD(transactions.created_at, INTERVAL 1 DAY)
                        ELSE transactions.created_at
                    END
                ";

                $weekendShiftTx = "
                    CASE 
                        WHEN WEEKDAY($baseDateCalcTx) IN (4, 5, 6) 
                        THEN DATE_ADD($baseDateCalcTx, INTERVAL (7 - WEEKDAY($baseDateCalcTx)) DAY)
                        ELSE $baseDateCalcTx
                    END
                ";

                $businessDateRawTx = "DATE($weekendShiftTx)";

                // Paid Subquery
                $paidSubquery = DB::table('transactions')
                    ->select(
                        'business_date as pay_date',
                        'charity_id',
                        DB::raw('SUM(amount) as total_paid'),
                        DB::raw('MAX(bank_payment_status) as current_status')
                    )
                    ->where('status', 1)
                    ->where('t_type', 'Out')
                    ->whereNotNull('business_date')
                    ->groupBy('pay_date', 'charity_id');

                // Main Query using business_date
                $query = Usertransaction::query()
                    ->where('status', 1)
                    ->whereNotNull('usertransactions.charity_id')
                    ->whereNotNull('usertransactions.business_date')
                    ->select([
                        'usertransactions.business_date as date_group',
                        'usertransactions.charity_id',
                        
                        DB::raw("SUM(CASE WHEN donation_id IS NOT NULL THEN amount ELSE 0 END) as online_sum"),
                        DB::raw("SUM(CASE WHEN standing_donationdetails_id IS NOT NULL THEN amount ELSE 0 END) as standing_sum"),
                        DB::raw("SUM(CASE WHEN cheque_no IS NOT NULL THEN amount ELSE 0 END) as voucher_sum"),
                        DB::raw("SUM(CASE WHEN campaign_id IS NOT NULL THEN amount ELSE 0 END) as campaign_sum"),
                        DB::raw("SUM(CASE WHEN onegiv_transaction_id IS NOT NULL THEN amount ELSE 0 END) as card_sum"),
                        
                        DB::raw("IFNULL(MAX(paid_data.total_paid), 0) as paid_sum"),
                        DB::raw("IFNULL(MAX(paid_data.current_status), 0) as payment_status")
                    ])
                    ->leftJoinSub($paidSubquery, 'paid_data', function ($join) {
                        $join->on('usertransactions.business_date', '=', 'paid_data.pay_date')
                            ->on('usertransactions.charity_id', '=', 'paid_data.charity_id');
                    })
                    ->groupBy('date_group', 'usertransactions.charity_id')
                    ->orderByRaw('date_group DESC')
                    ->orderBy('usertransactions.charity_id')
                    ->with('charity');

                if ($type === 'Summary') {
                    // RESTORED: The date filter from your original code
                    $query->where('usertransactions.business_date', '>', '2026-02-07');
                    $query->having('payment_status', '=', 0);
                    $query->whereHas('charity', function($q) {
                        $q->where('auto_payment', 1);
                    });
                } elseif ($type === 'PreviousSummary') {
                    $query->having('payment_status', '=', 1);
                }

                if ($fromDate && $toDate) {
                    $query->whereBetween('usertransactions.business_date', [$fromDate, $toDate]);
                }


                return DataTables::of($query)
                    ->addColumn('date_group', function ($row) {
                        return '<span data-raw="'.$row->date_group.'">'.
                            \Carbon\Carbon::parse($row->date_group)->format('d/m/Y').
                        '</span>';
                    })
                    ->addColumn('charity_name', function ($row) {
                        $charity = $row->charity;
                        $name = $charity->name ?? 'N/A';
                        $balance = $charity->balance ?? '0';
                        $title = '';
                        $style = 'style="color: #28a745; font-weight: bold;"'; 

                        if ($charity && $charity->auto_payment == 0) {
                            $title = ' title="Auto Payment Off"';
                            $style = 'style="color: #dc3545; font-weight: bold;"';
                        }
                        return '<span' . $title . ' ' . $style . '>' . $name . ' (' . $balance . ')</span>';
                    })
                    ->filterColumn('charity_name', function($query, $keyword) {
                        $query->whereHas('charity', function($q) use ($keyword) {
                            $q->where('name', 'like', "%{$keyword}%");
                        });
                    })
                    ->addColumn('balance', function ($row) {
                        $totalGenerated = $row->online_sum + $row->standing_sum + $row->voucher_sum + $row->campaign_sum + $row->card_sum;
                        $balance = $totalGenerated - $row->paid_sum;
                        return '£' . number_format($balance, 2);
                    })
                    ->addColumn('action', function ($row) {
                        $totalGenerated = $row->online_sum + $row->standing_sum + $row->voucher_sum + $row->campaign_sum + $row->card_sum;
                        $isChecked = ($row->payment_status == 1) ? 'checked' : '';

                        return '
                            <div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input status-switch"
                                    type="checkbox"
                                    role="switch"
                                    '.$isChecked.'
                                    data-charity-id="'.$row->charity_id.'"
                                    data-date="'.\Carbon\Carbon::parse($row->date_group)->format('Y-m-d').'"
                                    data-total="'.$totalGenerated.'">
                            </div>';
                    })
                    ->editColumn('paid_sum', function($row) {
                        if ($row->paid_sum <= 0) return '<span class="text-muted">£0.00</span>';
                        return '<a href="javascript:void(0)" class="view-details text-success text-decoration-none fw-bold" 
                                data-type="paid" 
                                data-charity="'.$row->charity_id.'" 
                                data-date="'.\Carbon\Carbon::parse($row->date_group)->format('Y-m-d').'">
                                £' . number_format($row->paid_sum, 2) . '
                                </a>';
                    })
                    ->editColumn('online_sum', function($row) {
                        if ($row->online_sum <= 0) return '<span class="text-muted">£0.00</span>';
                        return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="online" data-charity="'.$row->charity_id.'" data-date="'.\Carbon\Carbon::parse($row->date_group)->format('Y-m-d').'">£' . number_format($row->online_sum, 2) . '</a>';
                    })
                    ->editColumn('standing_sum', function($row) {
                        if ($row->standing_sum <= 0) return '<span class="text-muted">£0.00</span>';
                        return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="standing" data-charity="'.$row->charity_id.'" data-date="'.\Carbon\Carbon::parse($row->date_group)->format('Y-m-d').'">£' . number_format($row->standing_sum, 2) . '</a>';
                    })
                    ->editColumn('voucher_sum', function($row) {
                        if ($row->voucher_sum <= 0) return '<span class="text-muted">£0.00</span>';
                        return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="voucher" data-charity="'.$row->charity_id.'" data-date="'.\Carbon\Carbon::parse($row->date_group)->format('Y-m-d').'">£' . number_format($row->voucher_sum, 2) . '</a>';
                    })
                    ->editColumn('campaign_sum', function($row) {
                        if ($row->campaign_sum <= 0) return '<span class="text-muted">£0.00</span>';
                        return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="campaign" data-charity="'.$row->charity_id.'" data-date="'.\Carbon\Carbon::parse($row->date_group)->format('Y-m-d').'">£' . number_format($row->campaign_sum, 2) . '</a>';
                    })
                    ->editColumn('card_sum', function($row) {
                        if ($row->card_sum <= 0) return '<span class="text-muted">£0.00</span>';
                        return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="card" data-charity="'.$row->charity_id.'" data-date="'.\Carbon\Carbon::parse($row->date_group)->format('Y-m-d').'">£' . number_format($row->card_sum, 2) . '</a>';
                    })
                    ->addColumn('raw_date', function ($row) {
                        return $row->date_group;
                    })
                    ->addColumn('raw_total', function ($row) {
                        return $row->online_sum + $row->standing_sum + $row->voucher_sum + $row->campaign_sum + $row->card_sum;
                    })
                    ->rawColumns([
                        'date_group', 'online_sum', 'standing_sum', 'voucher_sum', 
                        'campaign_sum', 'card_sum', 'paid_sum', 'charity_name', 
                        'action', 'raw_date', 'raw_total' 
                    ])
                    ->make(true);
            }

            $query = Usertransaction::with(['user', 'charity'])->select('usertransactions.*');

            if ($type === 'In' || $type === 'Out') {
                $query->where('usertransactions.t_type', $type);
            }

            if ($type === 'Out') {
                $query->where(function ($query) {
                    $query->whereNull('usertransactions.expired')->orWhere('usertransactions.expired', '1');
                });
            }

            if ($fromDate && $toDate) {
                $query->whereBetween('usertransactions.created_at', [$fromDate, $toDate . ' 23:59:59']);
            }

            return DataTables::of($query)
                ->editColumn('created_at', fn($row) => \Carbon\Carbon::parse($row->created_at)->format('d/m/Y'))
                ->addColumn('beneficiary', function($row) {
                    return $row->charity->name ?? $row->crdAcptLoc ?? 'N/A';
                })
                ->addColumn('donor', function($row) {
                    return $row->user ? $row->user->name.' '.$row->user->surname : 'N/A';
                })
                ->editColumn('amount', fn($row) => '£' . number_format($row->amount, 2))
                ->rawColumns(['beneficiary', 'donor'])
                ->make(true);
        }

        return view('transaction.index');
    }

    public function getDayDetails(Request $request)
    {
        // Use helper to get dynamic window
        [$startDateTime, $endDateTime] = $this->getBusinessDateWindow($request->date);

        if ($request->type == 'paid') {
            $data = Transaction::with('charity')
                ->where('charity_id', $request->charity_id)
                ->where('t_type', 'Out')
                ->where('status', 1)
                ->whereBetween('created_at', [$startDateTime, $endDateTime])
                ->get();

            return response()->json($data->map(function($item) {
                return [
                    'donor'  => $item->charity->name ?? 'N/A',
                    'amount' => '£' . number_format($item->amount, 2),
                    'ref'    => $item->t_id,
                    'status' => $item->status,
                    'date'   => $item->created_at->format('d/m/Y H:i:s')
                ];
            }));
        }

        $query = Usertransaction::with('user')
            ->where('status', 1)
            ->where('charity_id', $request->charity_id)
            ->whereBetween('created_at', [$startDateTime, $endDateTime]);

        if ($request->type == 'online') $query->whereNotNull('donation_id');
        if ($request->type == 'standing') $query->whereNotNull('standing_donationdetails_id');
        if ($request->type == 'voucher') $query->whereNotNull('cheque_no');
        if ($request->type == 'campaign') $query->whereNotNull('campaign_id');
        if ($request->type == 'card') $query->whereNotNull('onegiv_transaction_id');

        $data = $query->get();

        return response()->json($data->map(function($item) {
            return [
                'donor'  => ($item->user->name ?? 'N/A') . ' ' . ($item->user->surname ?? ''),
                'amount' => '£' . number_format($item->amount, 2),
                'ref'    => $item->onegiv_transaction_id ?? $item->cheque_no ?? $item->t_id,
                'status' => $item->status,
                'date'   => $item->created_at->format('d/m/Y H:i')
            ];
        }));
    }

    public function toggleCharityPayment(Request $request)
    {
        $charityId = $request->charity_id;
        $date = $request->date;
        $total = $request->total;
        $status = $request->status === 'true' ? '1' : '0';

        return DB::transaction(function () use ($charityId, $date, $total, $status) {
            // Use helper to get dynamic window
            [$startDateTime, $endDateTime] = $this->getBusinessDateWindow($date);

            $transaction = Transaction::where('charity_id', $charityId)
                ->where('t_type', 'Out')
                ->whereBetween('created_at', [$startDateTime, $endDateTime])
                ->first();

            if ($transaction) {
                $transaction->update(['bank_payment_status' => $status]);
                return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
            } 
            
            return response()->json(['success' => false, 'message' => 'No record found to deactivate.']);
        });
    }

    public function exportSummaryCsv(Request $request)
    {
        $items = $request->get('items', []);
        
        if (empty($items)) {
            return response()->json(['success' => false, 'message' => 'No items selected']);
        }
        
        $filename = 'summary-export-' . date('Y-m-d-His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($items) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Charity Name', 'Account number', 'Sort Code', 'Account type', 'Reference', 'Amount']);
            
            foreach ($items as $item) {
                $charity = Charity::find($item['charity_id']);
                
                // Use helper to get dynamic window
                [$startDateTime, $endDateTime] = $this->getBusinessDateWindow($item['date']);
                
                $paidTransactionIds = Transaction::where('charity_id', $item['charity_id'])
                    ->where('t_type', 'Out')
                    ->where('status', 1)
                    ->whereBetween('created_at', [$startDateTime, $endDateTime])
                    ->pluck('t_id')
                    ->toArray();
                
                $references = !empty($paidTransactionIds) ? implode(', ', $paidTransactionIds) : '';
                
                fputcsv($file, [
                    $charity->name ?? 'N/A',
                    $charity->account_number ?? 'N/A',
                    $charity->account_sortcode ?? 'N/A',
                    'Business',
                    $references,  
                    $item['amount']
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    public function bulkTogglePayment(Request $request)
    {
        $items  = $request->get('items', []);
        $status = $request->status === 'true' ? '1' : '0';

        if (empty($items)) {
            return response()->json([
                'success' => false,
                'message' => 'No items selected.'
            ]);
        }

        return DB::transaction(function () use ($items, $status) {
            $updated  = 0;
            $notFound = 0;
            $notFoundList = [];

            foreach ($items as $item) {
                $charityId = $item['charity_id'] ?? null;
                $date      = $item['date'] ?? null;

                if (!$charityId || !$date) {
                    $notFound++;
                    continue;
                }

                try {
                    // Use helper to get dynamic window
                    [$startDateTime, $endDateTime] = $this->getBusinessDateWindow($date);
                } catch (\Exception $e) {
                    $notFound++;
                    $notFoundList[] = "Invalid date for Charity #{$charityId}";
                    continue;
                }

                $affectedRows = Transaction::where('charity_id', $charityId)
                    ->where('t_type', 'Out')
                    ->whereBetween('created_at', [$startDateTime, $endDateTime])
                    ->update(['bank_payment_status' => $status]);

                if ($affectedRows > 0) {
                    $updated++;
                } else {
                    $notFound++;
                    $notFoundList[] = "Charity #{$charityId} on {$date}";
                }
            }

            $message = "{$updated} record(s) marked as " . ($status == '1' ? 'PAID' : 'UNPAID') . ".";
            if ($notFound > 0) {
                $message .= " {$notFound} record(s) could not be found.";
            }

            return response()->json([
                'success'        => $updated > 0,
                'message'        => $message,
                'updated'        => $updated,
                'not_found'      => $notFound,
                'not_found_list' => $notFoundList,
            ]);
        });
    }




    public function adminTransactionView()
    {
        return view('transaction.tranview');
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
         $outtransactions = Usertransaction::where('t_type', 'Out')
            ->where('user_id', $userId)
            ->where(function ($query) use ($hasDateRange, $fromDate, $toDate) {
                $query->where('status', 1)
                    ->when($hasDateRange, function ($q) use ($fromDate, $toDate) {
                        $q->whereBetween('created_at', [$fromDate, $toDate]);
                    });
            })
            ->orWhere(function ($query) use ($userId, $hasDateRange, $fromDate, $toDate) {
                $query->where('user_id', $userId)
                    ->where('t_type', 'Out')
                    ->where('pending', '0')
                    ->when($hasDateRange, function ($q) use ($fromDate, $toDate) {
                        $q->whereBetween('created_at', [$fromDate, $toDate]);
                    });
            })
            ->orderByDesc('id')
            ->get();

        // Pending Transactions
        $pending_transactions = Provoucher::pendingVouchers($userId, $fromDate, $toDate)->get();


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

        // 1. Calculate the initial Total Balance (This remains the same)


        // 2. Fetch All Transactions - ORDER BY ASC for calculation logic
        // ✅ FIXED QUERY: Matches Admin logic exactly
        $query = Usertransaction::with(['charity:id,name', 'standingdonationDetail.standingDonation', 'donation', 'campaign', 'provoucher'])
            ->where('user_id', $userId)
            ->where(function ($q) {
                $q->whereNull('expired')->orWhere('expired', '1');
            })
            ->where(function ($q) use ($hasDateRange, $fromDate, $toDate) {
                if ($hasDateRange) {
                    $q->whereBetween('created_at', [$fromDate, $toDate])
                        ->where('status', 1);
                } else {
                    $q->where('status', 1);
                }
            })
            ->orWhere(function ($q) use ($userId, $hasDateRange, $fromDate, $toDate) {
                $q->where('user_id', $userId);
                if ($hasDateRange) {
                    $q->whereBetween('created_at', [$fromDate, $toDate])
                        ->where('pending', '0'); // ✅ Changed from 1 to '0'
                } else {
                    $q->where('pending', '0'); // ✅ Changed from 1 to '0'
                }
            })
            ->orderBy('created_at', 'asc') // Sort ASC to calculate balance forward
            ->get();

        // 3. Transform and Calculate - EXACTLY matches Admin Blade logic
        $currentBalanceTracker = 0; 
        $transformed = $query->flatMap(function ($data) use (&$currentBalanceTracker) {
            $rows = [];

            // ✅ FIX: provoucher check বাদ দেওয়া হয়েছে যাতে getLiveBalance() এর সাথে মেলে
            $isExpired = isset($data->expired) && $data->expired == '0';
            
            // ✅ FIX: Pending ট্রানজেকশন চেক করার শর্ত যোগ করা হয়েছে
            $isPending = ($data->pending == "0" || $data->pending === 0);

            // Commission row is processed FIRST
            if ($data->commission != 0) {
                // Expired বা Pending না হলেই ব্যালেন্স থেকে কমবে
                if (!$isExpired && !$isPending) {
                    $currentBalanceTracker -= $data->commission;
                }
                
                $commRow = clone $data;
                $commRow->display_type = 'commission';
                $commRow->calculated_balance = $currentBalanceTracker;
                $rows[] = $commRow;
            }

            // Main transaction row processed SECOND
            if (!$isExpired && !$isPending) {
                if ($data->t_type == "In") {
                    $currentBalanceTracker += ($data->commission != 0) ? ($data->amount + $data->commission) : $data->amount;
                } else {
                    $currentBalanceTracker -= $data->amount;
                }
            }

            $currentRow = clone $data;
            $currentRow->display_type = 'main';
            $currentRow->calculated_balance = $currentBalanceTracker;

            $rows[] = $currentRow;
            return $rows;
        });

        // 4. REVERSE the final collection for Descending View
        $reversedData = $transformed->reverse()->values();

        return DataTables::of($reversedData)
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
        
            ->where(function ($query) {
                $query->whereNull('expired')->orWhere('expired', '1');
            })
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



        $cardTransactions = Usertransaction::where('user_id', $id)->where('source', '=', 'Tevini Card')->whereNotNull('crdAcptLoc')->get();
        
        $authorizations = Authorisation::where('user_id', $id)->where('actionCode', '=', '000')->get();


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
            'cardTransactions' => $cardTransactions,
            'authorizations' => $authorizations,
            'tamount' => $totalTransactions,
        ]);
    }


    public function charityTransaction(Request $request, $id)
    {
        // 1. Optimized Daily Summary (Keep as is, usually small due to grouping)
        $dailySummaryQuery = Usertransaction::query()
            ->selectRaw('DATE(created_at) as trans_date, charity_id, SUM(amount) as total_amount, COUNT(*) as total_entries')
            ->where('charity_id', $id)
            ->where('t_type', 'Out')
            ->where('status', '1');

        if ($request->fromDate && $request->toDate) {
            $endDateTime = $request->toDate . ' 23:59:59';
            $dailySummaryQuery->whereBetween('created_at', [$request->fromDate, $endDateTime]);
        }

        $dailySummary = $dailySummaryQuery->groupBy('trans_date', 'charity_id')->orderBy('trans_date', 'DESC')->with('charity')->get();

        // Calculate Totals efficiently using SQL Sum instead of loading collections
        $totalIN  = Usertransaction::where('charity_id', $id)->where('t_type', 'Out')->where('status', '1')->sum('amount');
        $totalOUT = Transaction::where('charity_id', $id)->where('t_type', 'Out')->where('status', '1')->sum('amount');
        $currentTotalBalance = $totalIN - $totalOUT;

        $paidDates = Transaction::where('charity_id', $id)
            ->where('t_type', 'Out')
            ->where('status', '1')
            ->get()
            ->map(fn($t) => \Carbon\Carbon::parse($t->created_at)->format('Y-m-d'))
            ->toArray();

        return view('charity.transaction', compact(
            'dailySummary', 'totalIN', 'totalOUT', 'currentTotalBalance', 'id', 'paidDates'
        ));
    }


// Transaction In Data
public function getInTransactionsData(Request $request, $id)
{
    $query = Usertransaction::with(['charity', 'user', 'provoucher', 'standingdonationDetail.StandingDonation'])
        ->where('charity_id', $id)
        ->where('t_type', 'Out')
        ->where(function ($query) {
            $query->whereNull('expired')->orWhere('expired', '1');
        })
        ->where('status', '1')
        ->select('usertransactions.*');

    if ($request->has('fromDate') && $request->has('toDate') && $request->fromDate && $request->toDate) {
        $endDateTime = $request->toDate . ' 23:59:59';
        $query->whereBetween('created_at', [$request->fromDate, $endDateTime]);
    }

    return DataTables::of($query)
        ->addColumn('formatted_date', function ($row) {
            return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
        })
        ->addColumn('donor_name', function ($row) {
            return $row->user->name ?? 'N/A';
        })
        ->addColumn('action', function ($row) {
            // Prepare data for the modal
            $charitynote = null;
            if ($row->standing_donationdetails_id && $row->standingdonationDetail && $row->standingdonationDetail->StandingDonation) {
                $charitynote = $row->standingdonationDetail->StandingDonation->charitynote;
            }
            
            $data = [
                'date' => \Carbon\Carbon::parse($row->created_at)->format('d/m/Y'),
                't_id' => $row->t_id,
                'title' => $row->title,
                'charity' => $row->charity->name ?? 'N/A',
                'user' => $row->user->name ?? 'N/A',
                'donation_by' => $row->donation_by,
                'amount' => number_format($row->amount, 2),
                'cheque_no' => $row->cheque_no,
                'note' => $row->note,
                'charitynote' => $charitynote,
                'barcode_image' => $row->barcode_image ? asset($row->barcode_image) : null,
            ];
            
            // Return the View Icon button with data-json attribute
            return '<a href="javascript:void(0)" class="view-tran-btn" data-json=\''.json_encode($data).'\' title="View Details">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="#18988B" class="bi bi-arrow-up-circle" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 1 8 0a8 8 0 0 1 0 16z"/>
                    <path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.147 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5A.5.5 0 0 0 8 12z"/>
                </svg>
            </a>';
        })
        ->rawColumns(['action'])
        ->make(true);
}
// Ledger Data (Using Yajra Collection because of running balance logic)
public function getLedgerData(Request $request, $id)
{
    $userTransactionsledger = Usertransaction::with('charity')
        ->where('charity_id', $id)
        ->where('t_type', 'Out')
        ->where('status', '1')
        ->get();

    $externalTransactionsledger = Transaction::where('charity_id', $id)
        ->where('t_type', 'Out')
        ->where('status', '1')
        ->get();

    $ledgerEntries = collect();

    foreach ($userTransactionsledger as $ut) {
        $descParts = [];
        if ($ut->title) $descParts[] = $ut->title;
        if ($ut->donation_id !== null) $descParts[] = "(Online donation transaction)";
        if ($ut->standing_donationdetails_id !== null) $descParts[] = "(Standing Donation Transaction)";
        if ($ut->cheque_no !== null) $descParts[] = "Voucher No: " . $ut->cheque_no;
        
        $finalDescription = implode(' - ', $descParts) ?: 'User Transfer';

        $ledgerEntries->push([
            'real_id' => $ut->id,
            'date' => $ut->created_at->format('Y-m-d H:i'),
            't_id' => $ut->t_id ?? $ut->id,
            'description' => $finalDescription,
            'debit' => $ut->amount,
            'credit' => 0,
            'ut_status' => $ut->status,
            'type' => 'User'
        ]);
    }

    foreach ($externalTransactionsledger as $et) {
        $ledgerEntries->push([
            'real_id' => $et->id,
            'date' => $et->created_at->format('Y-m-d H:i'),
            't_id' => $et->t_id ?? $et->id,
            'description' => 'Desc: ' . $et->note,
            'debit' => 0,
            'credit' => $et->amount,
            'ut_status' => $et->status,
            'type' => 'External'
        ]);
    }

    $sortedLedger = $ledgerEntries->sortBy('date')->values();

    $runningBalance = 0;
    $ledgerWithBalance = $sortedLedger->map(function ($entry) use (&$runningBalance) {
        $runningBalance += ($entry['debit'] - $entry['credit']);
        $entry['balance'] = $runningBalance;
        return $entry;
    })->reverse()->values(); // Reverse for newest first

    // Return as a Collection to DataTables
    return DataTables::of($ledgerWithBalance)
        ->addColumn('edit_btn', function ($entry) {
            if ($entry['credit'] > 0) {
                return '<a href="javascript:void(0)" class="text-primary ml-2 edit-date-btn" data-id="'.$entry['real_id'].'" data-date="'.$entry['date'].'" title="Edit Date"><i class="fas fa-edit fa-sm"></i></a>';
            }
            return '';
        })
        ->rawColumns(['edit_btn'])
        ->make(true);
}


// Transaction Out Data
public function getOutTransactionsData(Request $request, $id)
{
    $query = Transaction::where('charity_id', $id)
        ->where('t_type', 'Out')
        ->where('status', '1');

    if ($request->has('fromDate') && $request->has('toDate') && $request->fromDate && $request->toDate) {
        $endDateTime = $request->toDate . ' 23:59:59';
        $query->whereBetween('created_at', [$request->fromDate, $endDateTime]);
    }

    return DataTables::of($query)
        ->addColumn('formatted_date', function($row) {
            return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
        })
        ->addColumn('status_switch', function($row) {
            $checked = $row->bank_payment_status ? 'checked' : '';
            return '<div class="form-check form-switch d-flex justify-content-center">
                        <input class="form-check-input status-switch" type="checkbox" role="switch" data-id="'.$row->id.'" '.$checked.'>
                    </div>';
        })
        ->rawColumns(['status_switch'])
        ->make(true);
}

// Reports Data
public function getReportsData(Request $request, $id)
{
    $query = Batchprov::where('charity_id', $id);
    return DataTables::of($query)
        ->addColumn('formatted_date', function($row) {
            return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y H:i');
        })
        ->addColumn('action', function($row) {
            return '<a class="btn btn-sm btn-theme text-white" href="'.route('instreport', $row->id).'">View Report</a>';
        })
        ->rawColumns(['action'])
        ->make(true);
}

// Pending Vouchers Data
public function getPendingVouchersData(Request $request, $id)
{
    $query = Provoucher::with('user')->where('charity_id', $id)
        ->where('waiting', 'No')
        ->where('status', '0');

    return DataTables::of($query)
        ->addColumn('formatted_date', function($row) {
            return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
        })
        ->addColumn('user_name', function($row) {
            return $row->user->name ?? 'N/A';
        })
        ->addColumn('status_badge', function($row) {
            $badge = $row->status == 0 ? 'bg-warning' : ($row->status == 1 ? 'bg-success' : 'bg-danger');
            $text = $row->status == 0 ? 'Pending' : ($row->status == 1 ? 'Complete' : 'Cancelled');
            return '<span class="badge '.$badge.'">'.$text.'</span>';
        })
        ->rawColumns(['status_badge'])
        ->make(true);
}

// Check Trans In Data
public function getCheckTransInData(Request $request, $id)
{
    $query = Usertransaction::where('charity_id', $id)->orderby('id', 'DESC');
    return DataTables::of($query)
        ->addColumn('formatted_date', function($row) {
            return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
        })
        ->addColumn('t_id_html', function($row) {
            if ($row->status == 0) return '<span class="badge bg-danger">'.$row->t_id.'</span>';
            return $row->t_id;
        })
        ->rawColumns(['t_id_html'])
        ->make(true);
}

// Check Trans Out Data
public function getCheckTransOutData(Request $request, $id)
{
    $query = Transaction::where('charity_id', $id)->orderby('id', 'DESC');
    return DataTables::of($query)
        ->addColumn('formatted_date', function($row) {
            return \Carbon\Carbon::parse($row->created_at)->format('d/m/Y');
        })
        ->addColumn('t_id_html', function($row) {
            if ($row->status == 0) return '<span class="badge bg-danger">'.$row->t_id.'</span>';
            return $row->t_id;
        })
        ->rawColumns(['t_id_html'])
        ->make(true);
}












    public function updateDate(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required',
            'new_date' => 'required'
        ]);

        $transaction = Transaction::findOrFail($request->transaction_id);
        $transaction->created_at = $request->new_date;
        // $transaction->bank_payment_status = 0;
        $transaction->save(); 

        return back()->with('success', 'Date updated successfully!');
    }

    public function updatePaymentStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:transactions,id',
            'status' => 'required|in:0,1'
        ]);

        try {
            $transaction = Transaction::findOrFail($request->id);
            $transaction->bank_payment_status = $request->status;
            $transaction->save();

            return response()->json([
                'success' => true,
                'message' => 'Payment status updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status'
            ], 500);
        }
    }



    public function checkTran(Request $request)
    {
        if ($request->isMethod('post')) {

            // $request->validate([
            //     'tranId' => 'required_without:voucher|string|max:255',
            //     'voucher' => 'required_without:tranId|string|max:255',
            // ]);
    
            $tranId = $request->tranId;
            $voucher = $request->voucher;

            $chktran = Usertransaction::where('t_id', $tranId)->get();

            Log::info("Check Transaction Request: tranId={$tranId}, voucher={$voucher}, found=" . $chktran->count());

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
            'tranId' => 'required_without:voucher|string|max:255',
            'voucher' => 'required_without:tranId|string|max:255',
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



    public function allCharityBalances()
    {
        
        // 1. Get all charities with their related transaction sums
        // We use withSum to efficiently get totals without loading every record
        $charities = Charity::withSum(['usertransaction as total_in' => function ($query) {
                $query->where('t_type', 'Out')->where('status', '1');
            }], 'amount')
            ->withSum(['transaction as total_out' => function ($query) {
                $query->where('t_type', 'Out')->where('status', '1');
            }], 'amount')
            ->get();

        // 2. Map the data to calculate the balance for each charity
        $charityData = $charities->map(function ($charity) {
            $in = $charity->total_in ?? 0;
            $out = $charity->total_out ?? 0;
            
            return [
                'name'    => $charity->name,
                'email'   => $charity->email,
                'cbalance'   => $charity->balance,
                'in_amt'  => $in,
                'out_amt' => $out,
                'balance' => $in - $out,
                'id'      => $charity->id
            ];
        });

        return view('charity.charityTest', compact('charityData'));
    }


    public function deleteTransactionUpdate(Request $request)
    {
        // 1. Validate the incoming data
        $request->validate([
            'transactionId' => 'required|exists:usertransactions,id',
            'date' => 'required|date',
        ]);

        try {
            // 2. Find the transaction
            $transaction = UserTransaction::findOrFail($request->transactionId);
            $transaction->voucher_create_date = $transaction->created_at;
            $transaction->voucher_complete_date = date('Y-m-d');
            $transaction->created_at = Carbon::parse($request->date)->setTimeFrom(now());
            $transaction->save();

            return response()->json([
                'success' => true,
                'message' => 'Transaction date updated successfully.'
            ]);

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("Transaction Update Error: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong while updating the record.'
            ], 500);
        }
    }






}

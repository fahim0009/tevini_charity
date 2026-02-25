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


    public function index_old(Request $request)
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
                    if ($row->charity_id) return $row->charity->name.' ('.$row->charity->balance.')';
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

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $type = $request->get('t_type');
            $fromDate = $request->get('fromDate');
            $toDate = $request->get('toDate');

            if ($type === 'Summary') {

                // Define the exact second where the next day starts
                $cutoffTime = '16:31:00';
                $cutoffTime2 = '16:30:00';

                /*
                |--------------------------------------------------------------------------
                | Business Date Logic (Updated)
                |--------------------------------------------------------------------------
                | If time >= 16:30:00 → belongs to NEXT day
                | If time < 16:30:00 → belongs to SAME day
                | This effectively closes the current day at 16:29:59.
                */

                $businessDateRaw = "
                    DATE(
                        CASE 
                            WHEN TIME(usertransactions.created_at) >= '$cutoffTime'
                            THEN DATE_ADD(usertransactions.created_at, INTERVAL 1 DAY)
                            ELSE usertransactions.created_at
                        END
                    )
                ";

                /*
                |--------------------------------------------------------------------------
                | Paid Subquery (Applying same logic to 'Out' transactions)
                |--------------------------------------------------------------------------
                */

                $paidSubquery = DB::table('transactions')
                    ->select(
                        DB::raw("
                            DATE(
                                CASE 
                                    WHEN TIME(created_at) >= '$cutoffTime'
                                    THEN DATE_ADD(created_at, INTERVAL 1 DAY)
                                    ELSE created_at
                                END
                            ) as pay_date
                        "),
                        'charity_id',
                        DB::raw('SUM(amount) as total_paid'),
                        DB::raw('MAX(bank_payment_status) as current_status')
                    )
                    ->where('status', 1)
                    ->where('t_type', 'Out')
                    ->groupBy('pay_date', 'charity_id');

                /*
                |--------------------------------------------------------------------------
                | Main Query
                |--------------------------------------------------------------------------
                */

                $query = Usertransaction::query()
                        ->where('status', 1)
                        ->whereNotNull('usertransactions.charity_id')
                        ->select([
                            DB::raw("$businessDateRaw as date_group"),
                            'usertransactions.charity_id',

                            DB::raw("SUM(CASE WHEN donation_id IS NOT NULL THEN amount ELSE 0 END) as online_sum"),
                            DB::raw("SUM(CASE WHEN standing_donationdetails_id IS NOT NULL THEN amount ELSE 0 END) as standing_sum"),
                            DB::raw("SUM(CASE WHEN cheque_no IS NOT NULL THEN amount ELSE 0 END) as voucher_sum"),
                            DB::raw("SUM(CASE WHEN campaign_id IS NOT NULL THEN amount ELSE 0 END) as campaign_sum"),

                            DB::raw("IFNULL(MAX(paid_data.total_paid), 0) as paid_sum"),
                            DB::raw("IFNULL(MAX(paid_data.current_status), 0) as payment_status")
                        ])
                        ->leftJoinSub($paidSubquery, 'paid_data', function ($join) use ($cutoffTime2) {
                            // Ensure the join uses the same >= 16:30:00 logic
                            $join->on(DB::raw("
                                DATE(
                                    CASE 
                                        WHEN TIME(usertransactions.created_at) >= '$cutoffTime2'
                                        THEN DATE_ADD(usertransactions.created_at, INTERVAL 1 DAY)
                                        ELSE usertransactions.created_at
                                    END
                                )
                            "), '=', 'paid_data.pay_date')
                            ->on('usertransactions.charity_id', '=', 'paid_data.charity_id');
                        })
                        ->groupBy('date_group', 'usertransactions.charity_id')
                        ->orderByRaw('date_group DESC')
                        ->orderBy('usertransactions.charity_id')
                        ->with('charity');

                /*
                |--------------------------------------------------------------------------
                | Date Filter (based on business date)
                |--------------------------------------------------------------------------
                */

                if ($fromDate && $toDate) {

                    $query->whereBetween(
                        DB::raw($businessDateRaw),
                        [$fromDate, $toDate]
                    );
                }

                return DataTables::of($query)

                    // ->editColumn('date_group', function ($row) {
                    //     return \Carbon\Carbon::parse($row->date_group)->format('d/m/Y');
                    // })

                    ->addColumn('date_group', function ($row) {

                        return '<span data-raw="'.$row->date_group.'">'.
                            \Carbon\Carbon::parse($row->date_group)->format('d/m/Y').
                        '</span>';
                    })

                    // ->addColumn('charity_name', function ($row) {
                    //     return ($row->charity->name ?? 'N/A') .
                    //         ' (' . ($row->charity->balance ?? '0') . ')';
                    // })

                    ->addColumn('charity_name', function ($row) {
                        $charity = $row->charity;
                        $name = $charity->name ?? 'N/A';
                        $balance = $charity->balance ?? '0';
                        
                        // Default values (Auto-payment ON)
                        $title = '';
                        $style = 'style="color: #28a745; font-weight: bold;"'; // Green for active

                        // Check if auto_payment is 0 (Comparison ==)
                        if ($charity && $charity->auto_payment == 0) {
                            $title = ' title="Auto Payment Off"';
                            $style = 'style="color: #dc3545; font-weight: bold;"'; // Red for warning
                        }

                        // Return the styled span
                        return '<span' . $title . ' ' . $style . '>' . $name . ' (' . $balance . ')</span>';
                    })

                    ->addColumn('balance', function ($row) {

                        $totalGenerated =
                            $row->online_sum +
                            $row->standing_sum +
                            $row->voucher_sum +
                            $row->campaign_sum;

                        $balance = $totalGenerated - $row->paid_sum;

                        return '£' . number_format($balance, 2);
                    })

                    ->addColumn('action', function ($row) {

                        $totalGenerated =
                            $row->online_sum +
                            $row->standing_sum +
                            $row->voucher_sum +
                            $row->campaign_sum;

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

                    ->rawColumns([
                        'date_group',
                        'online_sum',
                        'standing_sum',
                        'voucher_sum',
                        'campaign_sum',
                        'paid_sum',
                        'charity_name',
                        'action'
                    ])

                    ->make(true);
            }

            $query = Usertransaction::with(['user', 'charity'])->select('usertransactions.*');

            if ($type === 'In' || $type === 'Out') {
                $query->where('usertransactions.t_type', $type);
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

    public function index3(Request $request)
    {
        if ($request->ajax()) {
            $type = $request->get('t_type');
            $fromDate = $request->get('fromDate');
            $toDate = $request->get('toDate');

            if ($type === 'Summary') {
                
                $paidSubquery = DB::table('transactions')
                    ->select(
                        DB::raw('DATE(created_at) as pay_date'),
                        'charity_id',
                        DB::raw('SUM(amount) as total_paid'),
                        DB::raw('MAX(bank_payment_status) as current_status')
                    )
                    ->where('status', 1)
                    ->where('t_type', 'Out')
                    ->groupBy('pay_date', 'charity_id');

                $query = Usertransaction::query()->where('status', 1)
                    ->whereNotNull('usertransactions.charity_id') 
                    ->whereDate('usertransactions.created_at', '<', now()->toDateString())
                    ->select([
                        DB::raw('DATE(usertransactions.created_at) as date_group'),
                        'usertransactions.charity_id',
                        DB::raw("SUM(CASE WHEN donation_id IS NOT NULL THEN amount ELSE 0 END) as online_sum"),
                        DB::raw("SUM(CASE WHEN standing_donationdetails_id IS NOT NULL THEN amount ELSE 0 END) as standing_sum"),
                        DB::raw("SUM(CASE WHEN cheque_no IS NOT NULL THEN amount ELSE 0 END) as voucher_sum"),
                        DB::raw("SUM(CASE WHEN campaign_id IS NOT NULL THEN amount ELSE 0 END) as campaign_sum"),
                        DB::raw("IFNULL(MAX(paid_data.total_paid), 0) as paid_sum"),
                        DB::raw("IFNULL(MAX(paid_data.current_status), 0) as payment_status") 
                    ])
                    ->leftJoinSub($paidSubquery, 'paid_data', function ($join) {
                        $join->on(DB::raw('DATE(usertransactions.created_at)'), '=', 'paid_data.pay_date')
                            ->on('usertransactions.charity_id', '=', 'paid_data.charity_id');
                    })
                    ->groupBy('date_group', 'usertransactions.charity_id') 
                    ->with('charity');

                if ($fromDate && $toDate) {
                    $query->whereBetween('usertransactions.created_at', [$fromDate, $toDate . ' 23:59:59']);
                }

                return DataTables::of($query)
                    ->editColumn('date_group', fn($row) => \Carbon\Carbon::parse($row->date_group)->format('d/m/Y'))
                    ->addColumn('charity_name', fn($row) => ($row->charity->name ?? 'N/A') . ' (' . ($row->charity->balance ?? '0') . ')')
                    ->addColumn('balance', function($row) {
                        $totalGenerated = $row->online_sum + $row->standing_sum + $row->voucher_sum + $row->campaign_sum;
                        $balance = $totalGenerated - $row->paid_sum;
                        return '£' . number_format($balance, 2);
                    })
                    ->addColumn('action', function($row) {
                        $totalGenerated = $row->online_sum + $row->standing_sum + $row->voucher_sum + $row->campaign_sum;
                        
                        // Rule: If status is 1 (Active), switch is ON. If 0 (Deactive) or no record, switch is OFF.
                        $isChecked = ($row->payment_status == 1) ? 'checked' : '';

                        return '
                            <div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input status-switch" type="checkbox" 
                                    role="switch" 
                                    '.$isChecked.'
                                    data-charity-id="'.$row->charity_id.'"
                                    data-date="'.$row->date_group.'"
                                    data-total="'.$totalGenerated.'">
                            </div>';
                    })

                    ->editColumn('paid_sum', function($row) {
                        if ($row->paid_sum <= 0) return '<span class="text-muted">£0.00</span>';
                        
                        return '<a href="javascript:void(0)" class="view-details text-success text-decoration-none fw-bold" 
                                data-type="paid" 
                                data-charity="'.$row->charity_id.'" 
                                data-date="'.$row->date_group.'">
                                £' . number_format($row->paid_sum, 2) . '
                                </a>';
                    })

                    ->editColumn('online_sum', function($row) {
                        if ($row->online_sum <= 0) return '<span class="text-muted">£0.00</span>';
                        return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="online" data-charity="'.$row->charity_id.'" data-date="'.$row->date_group.'">£' . number_format($row->online_sum, 2) . '</a>';
                    })
                    ->editColumn('standing_sum', function($row) {
                        if ($row->standing_sum <= 0) return '<span class="text-muted">£0.00</span>';
                        return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="standing" data-charity="'.$row->charity_id.'" data-date="'.$row->date_group.'">£' . number_format($row->standing_sum, 2) . '</a>';
                    })
                    ->editColumn('voucher_sum', function($row) {
                        if ($row->voucher_sum <= 0) return '<span class="text-muted">£0.00</span>';
                        return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="voucher" data-charity="'.$row->charity_id.'" data-date="'.$row->date_group.'">£' . number_format($row->voucher_sum, 2) . '</a>';
                    })
                    ->editColumn('campaign_sum', function($row) {
                        if ($row->campaign_sum <= 0) return '<span class="text-muted">£0.00</span>';
                        return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="campaign" data-charity="'.$row->charity_id.'" data-date="'.$row->date_group.'">£' . number_format($row->campaign_sum, 2) . '</a>';
                    })
                    ->rawColumns(['action', 'online_sum', 'standing_sum', 'voucher_sum', 'campaign_sum','paid_sum']) 
                    ->make(true);
            }

            $query = Usertransaction::with(['user', 'charity'])->select('usertransactions.*');

            if ($type === 'In' || $type === 'Out') {
                $query->where('usertransactions.t_type', $type);
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

    public function index2(Request $request)
    {
        if ($request->ajax()) {
            $type = $request->get('t_type');
            $fromDate = $request->get('fromDate');
            $toDate = $request->get('toDate');

            if ($type === 'Summary') {
                
                // 1. Define the offset as a clean string for MariaDB
                $timeOffset = "INTERVAL '16:30' HOUR_MINUTE";
                
                // 2. Build the adjusted date logic
                $adjustedDate = "DATE(DATE_SUB(usertransactions.created_at, $timeOffset))";
                $adjustedPaidDate = "DATE(DATE_SUB(transactions.created_at, $timeOffset))";

                $paidSubquery = DB::table('transactions')
                    ->select(
                        DB::raw("$adjustedPaidDate as pay_date"), 
                        'charity_id',
                        DB::raw('SUM(amount) as total_paid'),
                        DB::raw('MAX(bank_payment_status) as current_status')
                    )
                    ->where('t_type', 'Out') 
                    ->groupBy('pay_date', 'charity_id');

                $query = Usertransaction::query()
                    ->whereNotNull('usertransactions.charity_id') 
                    // Ensure now() is formatted as a string for the query
                    ->where('usertransactions.created_at', '<', now()->setHour(16)->setMinute(30)->toDateTimeString())
                    ->select([
                        DB::raw("$adjustedDate as date_group"), 
                        'usertransactions.charity_id',
                        DB::raw("SUM(CASE WHEN donation_id IS NOT NULL THEN amount ELSE 0 END) as online_sum"),
                        DB::raw("SUM(CASE WHEN standing_donationdetails_id IS NOT NULL THEN amount ELSE 0 END) as standing_sum"),
                        DB::raw("SUM(CASE WHEN cheque_no IS NOT NULL THEN amount ELSE 0 END) as voucher_sum"),
                        DB::raw("SUM(CASE WHEN campaign_id IS NOT NULL THEN amount ELSE 0 END) as campaign_sum"),
                        DB::raw("IFNULL(MAX(paid_data.total_paid), 0) as paid_sum"),
                        DB::raw("IFNULL(MAX(paid_data.current_status), 0) as payment_status") 
                    ])
                    ->leftJoinSub($paidSubquery, 'paid_data', function ($join) use ($adjustedDate) {
                        $join->on(DB::raw($adjustedDate), '=', 'paid_data.pay_date')
                            ->on('usertransactions.charity_id', '=', 'paid_data.charity_id');
                    })
                    ->groupBy('date_group', 'usertransactions.charity_id') 
                    ->with('charity');

                if ($fromDate && $toDate) {
                    $query->whereBetween('usertransactions.created_at', [$fromDate, $toDate . ' 23:59:59']);
                }

                return DataTables::of($query)
                    ->editColumn('date_group', fn($row) => \Carbon\Carbon::parse($row->date_group)->format('d/m/Y'))
                    ->addColumn('charity_name', fn($row) => ($row->charity->name ?? 'N/A') . ' (' . ($row->charity->balance ?? '0') . ')')
                    ->addColumn('balance', function($row) {
                        $totalGenerated = $row->online_sum + $row->standing_sum + $row->voucher_sum + $row->campaign_sum;
                        $balance = $totalGenerated - $row->paid_sum;
                        return '£' . number_format($balance, 2);
                    })
                    ->addColumn('action', function($row) {
                        $totalGenerated = $row->online_sum + $row->standing_sum + $row->voucher_sum + $row->campaign_sum;
                        
                        // Rule: If status is 1 (Active), switch is ON. If 0 (Deactive) or no record, switch is OFF.
                        $isChecked = ($row->payment_status == 1) ? 'checked' : '';

                        return '
                            <div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input status-switch" type="checkbox" 
                                    role="switch" 
                                    '.$isChecked.'
                                    data-charity-id="'.$row->charity_id.'"
                                    data-date="'.$row->date_group.'"
                                    data-total="'.$totalGenerated.'">
                            </div>';
                    })

                    ->editColumn('paid_sum', function($row) {
                        if ($row->paid_sum <= 0) return '<span class="text-muted">£0.00</span>';
                        
                        return '<a href="javascript:void(0)" class="view-details text-success text-decoration-none fw-bold" 
                                data-type="paid" 
                                data-charity="'.$row->charity_id.'" 
                                data-date="'.$row->date_group.'">
                                £' . number_format($row->paid_sum, 2) . '
                                </a>';
                    })

                    ->editColumn('online_sum', function($row) {
                        if ($row->online_sum <= 0) return '<span class="text-muted">£0.00</span>';
                        return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="online" data-charity="'.$row->charity_id.'" data-date="'.$row->date_group.'">£' . number_format($row->online_sum, 2) . '</a>';
                    })
                    ->editColumn('standing_sum', function($row) {
                        if ($row->standing_sum <= 0) return '<span class="text-muted">£0.00</span>';
                        return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="standing" data-charity="'.$row->charity_id.'" data-date="'.$row->date_group.'">£' . number_format($row->standing_sum, 2) . '</a>';
                    })
                    ->editColumn('voucher_sum', function($row) {
                        if ($row->voucher_sum <= 0) return '<span class="text-muted">£0.00</span>';
                        return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="voucher" data-charity="'.$row->charity_id.'" data-date="'.$row->date_group.'">£' . number_format($row->voucher_sum, 2) . '</a>';
                    })
                    ->editColumn('campaign_sum', function($row) {
                        if ($row->campaign_sum <= 0) return '<span class="text-muted">£0.00</span>';
                        return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="campaign" data-charity="'.$row->charity_id.'" data-date="'.$row->date_group.'">£' . number_format($row->campaign_sum, 2) . '</a>';
                    })
                    ->rawColumns(['action', 'online_sum', 'standing_sum', 'voucher_sum', 'campaign_sum','paid_sum']) 
                    ->make(true);
            }

            $query = Usertransaction::with(['user', 'charity'])->select('usertransactions.*');

            if ($type === 'In' || $type === 'Out') {
                $query->where('usertransactions.t_type', $type);
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
        $cutoffHour = 16;
        $cutoffMinute = 31;

        // The date selected from the UI (Business Date)
        $businessDate = Carbon::createFromFormat('Y-m-d', $request->date);

        /**
         * START TIME: Previous Day at 16:31:00
         * Logic: (Selected Date - 1 Day) @ 16:31:00
         */
        $startDateTime = $businessDate->copy()
            ->subDay()
            ->setTime($cutoffHour, $cutoffMinute, 0);

        /**
         * END TIME: Today at 16:31:59
         * Logic: Selected Date @ 16:31:59
         */
        $endDateTime = $businessDate->copy()
            ->setTime($cutoffHour, $cutoffMinute, 59);

        /*
        |--------------------------------------------------------------------------
        | Query Implementation
        |--------------------------------------------------------------------------
        */
        if ($request->type == 'paid') {

            $data = Transaction::with('charity')
                ->where('charity_id', $request->charity_id)
                ->where('t_type', 'Out')
                ->where('status', 1)
                // This will capture everything from 16:31:00 yesterday 
                // up to 16:31:59 today (inclusive)
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

        /*
        |--------------------------------------------------------------------------
        | CASE 2: Usertransactions
        |--------------------------------------------------------------------------
        */

        $query = Usertransaction::with('user')
            ->where('status', 1)
            ->where('charity_id', $request->charity_id)
            ->whereBetween('created_at', [$startDateTime, $endDateTime]);

        if ($request->type == 'online')
            $query->whereNotNull('donation_id');

        if ($request->type == 'standing')
            $query->whereNotNull('standing_donationdetails_id');

        if ($request->type == 'voucher')
            $query->whereNotNull('cheque_no');

        if ($request->type == 'campaign')
            $query->whereNotNull('campaign_id');

        $data = $query->get();

        return response()->json($data->map(function($item) {
            return [
                'donor'  => ($item->user->name ?? 'N/A') . ' ' . ($item->user->surname ?? ''),
                'amount' => '£' . number_format($item->amount, 2),
                'ref'    => $item->cheque_no ?? $item->t_id,
                'status' => $item->status,
                'date'   => $item->created_at->format('d/m/Y H:i')
            ];
        }));
    }


    public function getDayDetails2(Request $request)
    {
        $cutoffTime = '16:30:00';

        $businessDate = Carbon::createFromFormat('Y-m-d', $request->date);

        $startDateTime = $businessDate->copy()
            ->subDay()
            ->setTime(16, 30, 0);

        $endDateTime = $businessDate->copy()
            ->setTime(16, 30, 0);

        /*
        |--------------------------------------------------------------------------
        | CASE 1: Paid (transactions table)
        |--------------------------------------------------------------------------
        */

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
                    'date'   => $item->created_at->format('d/m/Y H:i')
                ];
            }));
        }

        /*
        |--------------------------------------------------------------------------
        | CASE 2: Usertransactions
        |--------------------------------------------------------------------------
        */

        $query = Usertransaction::with('user')
            ->where('status', 1)
            ->where('charity_id', $request->charity_id)
            ->whereBetween('created_at', [$startDateTime, $endDateTime]);

        if ($request->type == 'online')
            $query->whereNotNull('donation_id');

        if ($request->type == 'standing')
            $query->whereNotNull('standing_donationdetails_id');

        if ($request->type == 'voucher')
            $query->whereNotNull('cheque_no');

        if ($request->type == 'campaign')
            $query->whereNotNull('campaign_id');

        $data = $query->get();

        return response()->json($data->map(function($item) {
            return [
                'donor'  => ($item->user->name ?? 'N/A') . ' ' . ($item->user->surname ?? ''),
                'amount' => '£' . number_format($item->amount, 2),
                'ref'    => $item->cheque_no ?? $item->t_id,
                'status' => $item->status,
                'date'   => $item->created_at->format('d/m/Y H:i')
            ];
        }));
    }


    public function getDayDetails_old(Request $request)
    {
        // CASE 1: Paid amount clicked (Show the bulk record from transactions table)
        if ($request->type == 'paid') {
            $data = Transaction::with('charity')
                ->where('charity_id', $request->charity_id)
                ->where('t_type', 'Out')->where('status', 1)
                ->whereDate('created_at', $request->date)
                ->get();

            return response()->json($data->map(function($item) {
                return [
                    'donor'  => $item->charity->name ?? 'N/A',
                    'amount' => '£' . number_format($item->amount, 2),
                    'ref'    => $item->t_id,
                    'status'    => $item->status,
                    'date'   => $item->created_at->format('d/m/Y H:i')
                ];
            }));
        }

        // CASE 2: Online/Standing/Voucher/Campaign clicked (Show individual usertransactions)
        $query = Usertransaction::with('user')->where('status', 1)
            ->where('charity_id', $request->charity_id)
            ->whereDate('created_at', $request->date);

        if ($request->type == 'online')   $query->whereNotNull('donation_id');
        if ($request->type == 'standing') $query->whereNotNull('standing_donationdetails_id');
        if ($request->type == 'voucher')  $query->whereNotNull('cheque_no');
        if ($request->type == 'campaign') $query->whereNotNull('campaign_id');

        $data = $query->get();

        return response()->json($data->map(function($item) {
            return [
                'donor'  => ($item->user->name ?? 'N/A') . ' ' . ($item->user->surname ?? ''),
                'amount' => '£' . number_format($item->amount, 2),
                'ref'    => $item->cheque_no ?? $item->t_id,
                'status'    => $item->status,
                'date'   => $item->created_at->format('d/m/Y H:i')
            ];
        }));
    }



public function toggleCharityPayment(Request $request)
{
    $charityId = $request->charity_id;
    $date = $request->date;
    $total = $request->total;
    $status = $request->status === 'true' ? '1' : '0'; // Convert JS boolean to "1" or "0"

    return DB::transaction(function () use ($charityId, $date, $total, $status) {
        // 1. Check if the transaction record already exists for this charity and date
        $transaction = Transaction::where('charity_id', $charityId)
            ->where('t_type', 'Out')
            ->whereDate('created_at', $date)
            ->first();

        if ($transaction) {
            // 2. If it exists, just update the status
            $transaction->update(['bank_payment_status' => $status]);
            
            return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
        } 
        
        return response()->json(['success' => false, 'message' => 'No record found to deactivate.']);
    });
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

        // 1. Calculate the initial Total Balance (This remains the same)
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

        // 2. Fetch All Transactions - ORDER BY ASC for calculation logic
        $query = Usertransaction::with(['charity:id,name', 'standingdonationDetail.standingDonation', 'donation', 'campaign', 'provoucher'])
            ->where(function ($q) use ($userId, $hasDateRange, $fromDate, $toDate) {
                $q->where('user_id', $userId)
                    ->where('status', 1)
                    ->when($hasDateRange, fn($sub) => $sub->whereBetween('created_at', [$fromDate, $toDate]));
            })
            ->orWhere(function ($q) use ($userId, $hasDateRange, $fromDate, $toDate) {
                $q->where('user_id', $userId)
                    ->where('pending', 1)
                    ->when($hasDateRange, fn($sub) => $sub->whereBetween('created_at', [$fromDate, $toDate]));
            })
            ->orderBy('created_at', 'asc') // Sort ASC to calculate balance forward
            ->get();

        // 3. Transform and Calculate
        $currentBalanceTracker = 0; // Start from 0 or your historical starting point
        $transformed = $query->flatMap(function ($data) use (&$currentBalanceTracker) {
            $rows = [];

            // Main Transaction Logic
            if ($data->t_type == "In") {
                $currentBalanceTracker += ($data->commission != 0) ? ($data->amount + $data->commission) : $data->amount;
            } else {
                // Only deduct if not pending or special voucher logic
                if(!($data->pending != "0" && (isset($data->provoucher) && $data->provoucher->expired == "Yes"))) {
                    $currentBalanceTracker -= $data->amount;
                }
            }

            $currentRow = clone $data;
            $currentRow->display_type = 'main';
            $currentRow->calculated_balance = $currentBalanceTracker;

            // If Commission exists, it affects the balance after the main transaction
            if ($data->commission != 0) {
                $currentBalanceTracker -= $data->commission;
                
                $commRow = clone $data;
                $commRow->display_type = 'commission';
                $commRow->calculated_balance = $currentBalanceTracker;
                $rows[] = $commRow;
            }

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
        $request->validate([
            'fromDate' => 'nullable|date',
            'toDate'   => 'nullable|date|after_or_equal:fromDate',
        ]);

        $fromDate = $request->input('fromDate');
        $toDate   = $request->input('toDate');
        $endDateTime = $toDate ? $toDate . ' 23:59:59' : null;

        // --- 1. Optimized Daily Summary (The New Tab Data) ---
        $dailySummaryQuery = Usertransaction::query()
            ->selectRaw('DATE(created_at) as trans_date, charity_id, SUM(amount) as total_amount, COUNT(*) as total_entries')
            ->where('charity_id', $id)
            ->where('t_type', 'Out') // As per your logic, In-transactions are labeled 'Out' in this table
            ->where('status', '1');

        // --- 2. Detailed Transactions (Transaction In Tab) ---
        $userTransQuery = Usertransaction::with('charity')
            ->where('charity_id', $id)
            ->where('t_type', 'Out')
            ->where('status', '1');

        // --- 3. External Transactions (Transaction Out Tab) ---
        $transQuery = Transaction::where('charity_id', $id)
            ->where('t_type', 'Out')
            ->where('status', '1');

        $reportQuery = Batchprov::where('charity_id', $id);

        // Apply Date Filters to all queries
        if ($fromDate && $toDate) {
            $dailySummaryQuery->whereBetween('created_at', [$fromDate, $endDateTime]);
            $userTransQuery->whereBetween('created_at', [$fromDate, $endDateTime]);
            $transQuery->whereBetween('created_at', [$fromDate, $endDateTime]);
            $reportQuery->whereBetween('created_at', [$fromDate, $endDateTime]);
        }

        // Execute Queries
        $dailySummary    = $dailySummaryQuery->groupBy('trans_date', 'charity_id')->orderBy('trans_date', 'DESC')->with('charity')->get();
        $intransactions  = $userTransQuery->orderBy('id', 'DESC')->get();
        $outtransactions = $transQuery->orderBy('id', 'DESC')->get();
        $reports         = $reportQuery->orderBy('id', 'DESC')->get();

        $totalIN  = $intransactions->sum('amount');
        $totalOUT = $outtransactions->sum('amount');

        $pvouchers = Provoucher::with('user')->where('charity_id', $id)
            ->where('waiting', 'No')
            ->where('status', '0')
            ->orderBy('id', 'DESC')
            ->get();

        $paidDates = Transaction::where('charity_id', $id)
            ->where('t_type', 'Out')
            ->where('status', '1')
            ->get()
            ->map(fn($t) => \Carbon\Carbon::parse($t->created_at)->format('Y-m-d'))
            ->toArray();


            // ledger
            // 1. Get the data
            $userTransactionsledger = Usertransaction::with('charity')
                ->where('charity_id', $id)
                ->where('t_type', 'Out')
                ->where('status', '1')
                ->get();

            $externalTransactionsledger = Transaction::where('charity_id', $id)
                ->where('t_type', 'Out')
                ->where('status', '1')
                ->get();
                

            // 2. Normalize and Combine
            $ledgerEntries = collect();

            // Normalize User Transactions (Debits)
            foreach ($userTransactionsledger as $ut) {
                // Start building the dynamic description
                $descParts = [];
                
                // Add the Title first
                if ($ut->title) {
                    $descParts[] = $ut->title;
                }

                // Rule: donation_id not null
                if ($ut->donation_id !== null) {
                    $descParts[] = "(Online donation transaction)";
                }

                // Rule: standing_donationdetails_id not null
                if ($ut->standing_donationdetails_id !== null) {
                    $descParts[] = "(Standing Donation Transaction)";
                }

                // Rule: cheque_no not null (Voucher No)
                if ($ut->cheque_no !== null) {
                    $descParts[] = "Voucher No: " . $ut->cheque_no;
                }

                // Join everything with a space or separator
                $finalDescription = implode(' - ', $descParts);

                $ledgerEntries->push([
                    'date' => $ut->created_at,
                    't_id' => $ut->t_id ?? $ut->id, 
                    'description' => $finalDescription ?: 'User Transfer', // Fallback if empty
                    'debit' => $ut->amount,
                    'credit' => 0,
                    'type' => 'User'
                ]);
            }

            foreach ($externalTransactionsledger as $et) {
                $ledgerEntries->push([
                    'date' => $et->created_at,
                    't_id' => $et->t_id ?? $et->id, // Ensure t_id is captured
                    'description' => 'Desc: ' . $et->note,
                    'debit' => 0,
                    'credit' => $et->amount,
                    'type' => 'External'
                ]);
            }

            // 3. Sort by Date ASCENDING to calculate running balance correctly
            $sortedLedger = $ledgerEntries->sortBy('date');

            $runningBalance = 0;
            $ledgerWithBalance = $sortedLedger->map(function ($entry) use (&$runningBalance) {
                // Standard Ledger: Balance = (Previous Balance + Credit) - Debit
                $runningBalance += ($entry['debit'] - $entry['credit']);
                $entry['balance'] = $runningBalance;
                return $entry;
            });

            // 4. Now reverse it for the View (Descending order: Newest at top)
            $finalLedger = $ledgerWithBalance->reverse();

            // Capture the total current balance to show at the top of the table
            $currentTotalBalance = $runningBalance;
            // ledger

        return view('charity.transaction', compact(
            'dailySummary', 'intransactions', 'outtransactions', 
            'reports', 'totalIN', 'totalOUT', 'pvouchers', 'id', 'paidDates','finalLedger','currentTotalBalance'
        ));
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

            $chktran = Usertransaction::where('t_id', $tranId)->orWhere('cheque_no', $voucher)->get();

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

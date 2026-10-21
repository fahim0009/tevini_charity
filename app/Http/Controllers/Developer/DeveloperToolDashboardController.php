<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usertransaction;
use App\Models\Transaction;
use App\Models\Charity;
use App\Models\User;
use App\Models\Donation;
use App\Models\StandingDonation;
use App\Models\StandingdonationDetail;
use App\Models\Provoucher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

    use Yajra\DataTables\Facades\DataTables;
    use App\Models\AuditTran;
    use App\Models\Authorisation;
use App\Models\Order;
use Illuminate\Support\Facades\Log;


class DeveloperToolDashboardController extends Controller
{
    /**
     * Main Developer Dashboard - Only Summaries & Issues
     */     /**
     * Main Developer Dashboard - Only Summaries & Issues (Lightweight)
     */
    public function dashboard()
    {
        
        $today = Carbon::today();
        $totalTransactions = Transaction::count();
        $totalCharities = Charity::count();
        $totalDonors = User::where('is_type', 'user')->count();

        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $last30Days = Carbon::today()->subDays(30);

        $successfulThisMonth = Usertransaction::where('status', 1)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        $unsuccessfulThisMonth = Usertransaction::where('status', 0)
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        $successfulLast30Days = Usertransaction::where('status', 1)
            ->where('created_at', '>=', $last30Days)
            ->count();

        $unsuccessfulLast30Days = Usertransaction::where('status', 0)
            ->where('created_at', '>=', $last30Days)
            ->count();

        // FIX: Query Usertransaction table directly for accurate daily totals
        $todaysOnlineDonation = Usertransaction::where('status', 1)
            ->where('t_type', 'Out')
            ->whereNotNull('donation_id')
            ->whereDate('created_at', $today)
            ->sum('amount');

        $todaysStandingDonation = Usertransaction::where('status', 1)
            ->where('t_type', 'Out')
            ->whereNotNull('standing_donationdetails_id')
            ->whereDate('created_at', $today)
            ->sum('amount');

        $cutoffHistory = DB::table('cutoff_histories')
            ->orderBy('effective_date', 'desc')
            ->limit(10)
            ->get();

        $stripeSyncIssues = Donation::whereNotNull('stripe_payment_id')
            ->where('status', 0)
            ->with(['user', 'charity'])
            ->latest()
            ->get();

        // Tomorrow & Next 7 Days Calculation (Lightweight - only sum)
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        $next7DaysStart = Carbon::tomorrow()->startOfDay();
        $next7DaysEnd = Carbon::tomorrow()->addDays(6)->endOfDay();

        $intervalMap = [
            'Monthly' => 1, 'Quarterly' => 3, 'Yearly' => 12,
            'Annually' => 12, 'Weekly' => 0, 'Bi-Weekly' => 0,
        ];

        $tomorrowStandingAmount = 0;
        $next7DaysStandingAmount = 0;

        // Use simple select instead of loading relations for speed
        $activeOrders = StandingDonation::where('status', 1)->select(['id', 'starting', 'interval', 'amount'])->get();

        foreach ($activeOrders as $order) {
            $lastDetail = StandingdonationDetail::where('standing_donation_id', $order->id)
                ->latest('id')
                ->value('instalment_date');
            $start_date = $lastDetail ?? $order->starting;
            $start_date_carbon = Carbon::parse($start_date);

            $intervalValue = $order->interval;
            if (is_string($intervalValue) && isset($intervalMap[$intervalValue])) {
                $intervalMonths = $intervalMap[$intervalValue];
            } elseif (is_numeric($intervalValue)) {
                $intervalMonths = (int) $intervalValue;
            } else {
                $intervalMonths = 1;
            }

            if ($intervalMonths === 0) $intervalMonths = 1;

            $instalment_date_carbon = $start_date_carbon->copy()->addMonths($intervalMonths);
            $instalment_date = $instalment_date_carbon->format('Y-m-d');

            if ($instalment_date == $tomorrow) {
                $tomorrowStandingAmount += $order->amount;
            }

            if ($instalment_date_carbon->between($next7DaysStart, $next7DaysEnd)) {
                $next7DaysStandingAmount += $order->amount;
            }
        }



        // ============================================================
        // NEW: 5 Dashboard Summary Cards
        // ============================================================

        // 1. Today's Total Charity IN = All Donations (Out from Usertransaction)
        $todayCharityIN = Usertransaction::where('status', 1)
            ->where('t_type', 'Out')
            ->whereDate('created_at', $today)
            ->sum('amount');

        // 2. Today's Total Charity OUT = AutoCharityPayment generated transactions
        $todayCharityOUT = Transaction::where('t_type', 'Out')
            ->where('status', 1)
            ->whereDate('created_at', $today)
            ->sum('amount');

        // 3. Today's Total Donor IN = Topup amount (In from Usertransaction)
        $todayDonorIN = Usertransaction::where('status', 1)
            ->where('t_type', 'In')
            ->whereDate('created_at', $today)
            ->sum('amount');

        // 4. Today's Total Donor OUT = Donations (Same as Charity IN)
        $todayDonorOUT = $todayCharityIN;

        // 5. Today's Total Commission
        $todayCommission = Usertransaction::where('status', 1)
            ->where('t_type', 'In')
            ->whereDate('created_at', $today)
            ->sum('commission');

        return view('admin.developer.dashboard', compact(
            'totalTransactions', 'totalCharities', 'totalDonors',
            'successfulThisMonth', 'unsuccessfulThisMonth', 'successfulLast30Days', 'unsuccessfulLast30Days',
            'todaysOnlineDonation', 'todaysStandingDonation',
            'cutoffHistory', 'stripeSyncIssues',
            'tomorrowStandingAmount', 'next7DaysStandingAmount',
            'todayCharityIN', 'todayCharityOUT', 'todayDonorIN', 'todayDonorOUT', 'todayCommission'
        ));


    }

    /**
     * AJAX: Get Stuck Standing Donations
     */
    public function getStuckStandingDonations()
    {
        $today = Carbon::today()->format('Y-m-d');
        $intervalMap = [
            'Monthly' => 1, 'Quarterly' => 3, 'Yearly' => 12,
            'Annually' => 12, 'Weekly' => 0, 'Bi-Weekly' => 0,
        ];

        $activeOrders = StandingDonation::where('status', 1)
            ->with(['user', 'charity'])
            ->get();

        $data = [];

        foreach ($activeOrders as $order) {
            $detail = StandingdonationDetail::where('standing_donation_id', $order->id)
                ->latest('id')
                ->first();

            $start_date = $detail ? $detail->instalment_date : $order->starting;
            $start_date_carbon = Carbon::parse($start_date);

            $intervalValue = $order->interval;
            if (is_string($intervalValue) && isset($intervalMap[$intervalValue])) {
                $intervalMonths = $intervalMap[$intervalValue];
            } elseif (is_numeric($intervalValue)) {
                $intervalMonths = (int) $intervalValue;
            } else {
                $intervalMonths = 1;
            }

            if ($intervalMonths === 0) $intervalMonths = 1;

            $instalment_date_carbon = $start_date_carbon->copy()->addMonths($intervalMonths);
            $instalment_date = $instalment_date_carbon->format('Y-m-d');

            // Only stuck ones (overdue)
            if ($instalment_date < $today) {
                $days_overdue = Carbon::parse($instalment_date)->diffInDays(Carbon::today());
                $payments_made = StandingdonationDetail::where('standing_donation_id', $order->id)->where('status', 0)->count();
                $skipped_payments = StandingdonationDetail::where('standing_donation_id', $order->id)->where('status', 2)->count();

                // FIXED: Use null coalescing and avoid HTML in JSON
                $lastPaymentDate = $detail ? Carbon::parse($detail->instalment_date)->format('d M, Y') : null;
                $lastPaymentDisplay = $lastPaymentDate ? $lastPaymentDate : 'Never';

                $data[] = [
                    'id' => $order->id,
                    'donor_name' => $order->user->name ?? 'N/A',
                    'donor_acct' => $order->user->accountno ?? 'N/A',
                    'donor_email' => $order->user->email ?? 'N/A',
                    'charity' => $order->charity->name ?? 'N/A',
                    'amount' => number_format($order->amount, 2),
                    'type' => $order->payments == 1 ? 'Fixed' : 'Continuous',
                    'interval' => $intervalMonths . ' Month(s)',
                    'start_date' => Carbon::parse($order->starting)->format('d M, Y'),
                    'last_payment' => $lastPaymentDisplay,
                    'expected_date' => Carbon::parse($instalment_date)->format('d M, Y'),
                    'days_overdue' => $days_overdue,
                    'payments_made' => $payments_made,
                    'skipped_payments' => $skipped_payments,
                ];
            }
        }

        return response()->json(['data' => $data]);
    }

    /**
     * AJAX: Get Failing Standing Donations (Insufficient Balance)
     */
    public function getFailingStandingDonations()
    {
        $intervalMap = [
            'Monthly' => 1, 'Quarterly' => 3, 'Yearly' => 12,
            'Annually' => 12, 'Weekly' => 0, 'Bi-Weekly' => 0,
        ];

        $activeOrders = StandingDonation::where('status', 1)
            ->with(['user', 'charity'])
            ->get();

        $data = [];

        foreach ($activeOrders as $order) {
            if (!$order->user) continue;

            $availableLimit = $order->user->getAvailableLimit();
            
            if ($availableLimit < $order->amount) {
                $detail = StandingdonationDetail::where('standing_donation_id', $order->id)
                    ->latest('id')
                    ->first();

                $start_date = $detail ? $detail->instalment_date : $order->starting;
                $start_date_carbon = Carbon::parse($start_date);

                $intervalValue = $order->interval;
                if (is_string($intervalValue) && isset($intervalMap[$intervalValue])) {
                    $intervalMonths = $intervalMap[$intervalValue];
                } elseif (is_numeric($intervalValue)) {
                    $intervalMonths = (int) $intervalValue;
                } else {
                    $intervalMonths = 1;
                }

                if ($intervalMonths === 0) $intervalMonths = 1;

                $instalment_date_carbon = $start_date_carbon->copy()->addMonths($intervalMonths);

                $shortfall = $order->amount - $availableLimit;

                $data[] = [
                    'id' => $order->id,
                    'donor_name' => $order->user->name ?? 'N/A',
                    'donor_acct' => $order->user->accountno ?? 'N/A',
                    'donor_email' => $order->user->email ?? 'N/A',
                    'charity' => $order->charity->name ?? 'N/A',
                    'amount' => number_format($order->amount, 2),
                    'available_limit' => number_format($availableLimit, 2),
                    'shortfall' => number_format($shortfall, 2),
                    'type' => $order->payments == 1 ? 'Fixed' : 'Continuous',
                    'next_payment' => $instalment_date_carbon->format('d M, Y'),
                    'interval' => $intervalMonths . ' Month(s)',
                ];
            }
        }

        return response()->json(['data' => $data]);
    }

    /**
     * Fetch TopUp Records via AJAX
     */
    public function getTopupRecords(Request $request)
    {
        $filter = $request->get('filter', 'all');
        $now = Carbon::now();

        $query = Usertransaction::where('t_type', 'In')->with('user');

        switch ($filter) {
            case 'today':
                $query->whereDate('created_at', $now->toDateString());
                break;
            case 'week':
                $query->whereBetween('created_at', [$now->startOfWeek()->toDateTimeString(), $now->endOfWeek()->toDateTimeString()]);
                break;
            case 'month':
                $query->whereMonth('created_at', $now->month)->whereYear('created_at', $now->year);
                break;
            case 'year':
                $query->whereYear('created_at', $now->year);
                break;
            case 'all':
            default:
                // No date filter
                break;
        }

        $topups = $query->latest()->get();

        $totalGross = 0;
        $totalCommission = 0;
        $totalNet = 0;

        $data = $topups->map(function ($t) use (&$totalGross, &$totalCommission, &$totalNet) {
            $gross = $t->amount + $t->commission;
            $totalGross += $gross;
            $totalCommission += $t->commission;
            $totalNet += $t->amount;

            return [
                'id' => $t->id,
                'user_name' => $t->user->name ?? 'N/A',
                'user_email' => $t->user->email ?? 'N/A',
                'gross' => number_format($gross, 2),
                'commission' => number_format($t->commission, 2),
                'net' => number_format($t->amount, 2),
                'source' => $t->source ?? 'N/A',
                'date' => \Carbon\Carbon::parse($t->created_at)->format('d M, Y H:i'),
            ];
        });

        return response()->json([
            'data' => $data,
            'totals' => [
                'gross' => number_format($totalGross, 2),
                'commission' => number_format($totalCommission, 2),
                'net' => number_format($totalNet, 2),
            ]
        ]);
    }






    /**
     * Online Donation Monitoring Page
     */    /**
     * Online Donation Monitoring Page
     */
    public function onlineDonations(Request $request)
    {
        if ($request->ajax()) {
            $filterType = $request->get('filter_type', 'monthly');
            $fromDate = $request->get('fromDate');
            $toDate = $request->get('toDate');
            $today = Carbon::today();

            $query = Donation::with(['user', 'charity'])
                ->select('donations.*', 'usertransactions.t_id as tran_id')
                ->leftJoin('usertransactions', 'donations.id', '=', 'usertransactions.donation_id')
                ->orderBy('donations.created_at', 'DESC');

            // Apply Date Filters
            if ($filterType == 'daily') {
                $query->whereDate('donations.created_at', $today);
            } elseif ($filterType == 'weekly') {
                $query->whereBetween('donations.created_at', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()]);
            } elseif ($filterType == 'monthly') {
                $query->whereMonth('donations.created_at', $today->month)->whereYear('donations.created_at', $today->year);
            } elseif ($filterType == 'last7') {
                $query->whereBetween('donations.created_at', [$today->copy()->subDays(6), $today]);
            } elseif ($filterType == 'custom' && $fromDate && $toDate) {
                $query->whereBetween('donations.created_at', [$fromDate, $toDate . ' 23:59:59']);
            }

            return DataTables::of($query)
                ->addIndexColumn() // For SL No.
                ->addColumn('date', function($row) {
                    return Carbon::parse($row->created_at)->format('d/m/Y H:i');
                })
                ->addColumn('tran_id', function($row) {
                    return '<span class="fw-bold">' . ($row->tran_id ?? 'N/A') . '</span>';
                })
                ->addColumn('donor', function($row) {
                    if ($row->user) {
                        $name = $row->user->name . ' ' . $row->user->surname;
                        $email = $row->user->email;
                        return '<span class="fw-bold text-success">' . $name . '</span><br><small>' . $email . '</small>';
                    } else {
                        $name = trim(($row->guest_first_name ?? '') . ' ' . ($row->guest_last_name ?? '')) ?: 'Guest';
                        $email = $row->guest_email ?? 'N/A';
                        return '<span class="fw-bold text-danger">' . $name . '</span><br><small>' . $email . '</small>';
                    }
                })
                ->addColumn('charity', function($row) {
                    if ($row->charity) {
                        $name = $row->charity->name;
                        $email = $row->charity->email ?? '';
                        return '<span class="fw-bold">' . $name . '</span><br><small>' . $email . '</small>';
                    }
                    return 'N/A';
                })
                ->addColumn('current_balance', function($row) {
                    if ($row->user) {
                        return '£' . number_format($row->user->getAvailableLimit(), 2);
                    }
                    return '<span class="text-muted">N/A</span>';
                })
                ->addColumn('balance_in', function($row) {
                    if ($row->user_id) {
                        $in = Usertransaction::where('user_id', $row->user_id)
                            ->where('status', 1)
                            ->where('t_type', 'In')
                            ->sum('amount');
                        return '£' . number_format($in, 2);
                    }
                    return '<span class="text-muted">N/A</span>';
                })
                ->addColumn('balance_out', function($row) {
                    if ($row->user_id) {
                        $out = Usertransaction::where('user_id', $row->user_id)
                            ->where('status', 1)
                            ->where('t_type', 'Out')
                            ->sum('amount');
                        return '£' . number_format($out, 2);
                    }
                    return '<span class="text-muted">N/A</span>';
                })
                ->editColumn('amount', function($row) {
                    return '<span class="fw-bold">£' . number_format($row->amount, 2) . '</span>';
                })
                ->editColumn('payment_method', function($row) {
                    $badge = $row->payment_method == 'stripe' ? 'bg-primary' : 'bg-success';
                    $text = $row->payment_method == 'stripe' ? 'Stripe' : 'Balance';
                    return '<span class="badge ' . $badge . '">' . $text . '</span>';
                })
                ->filterColumn('donor', function($query, $keyword) {
                    $query->whereHas('user', function($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%")
                          ->orWhere('surname', 'like', "%{$keyword}%")
                          ->orWhere('email', 'like', "%{$keyword}%");
                    })->orWhere('guest_first_name', 'like', "%{$keyword}%")
                      ->orWhere('guest_last_name', 'like', "%{$keyword}%")
                      ->orWhere('guest_email', 'like', "%{$keyword}%");
                })
                ->filterColumn('charity', function($query, $keyword) {
                    $query->whereHas('charity', function($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%")
                          ->orWhere('email', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['tran_id', 'donor', 'charity', 'amount', 'payment_method', 'current_balance', 'balance_in', 'balance_out'])
                ->make(true);
        }

        // --- Summary Cards Data ---
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $todayStats = Donation::whereDate('created_at', $today)
            ->selectRaw("COUNT(*) as total, SUM(amount) as amount, SUM(admin_charge) as fee")->first();
        
        $weekStats = Donation::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->selectRaw("COUNT(*) as total, SUM(amount) as amount, SUM(admin_charge) as fee")->first();
        
        $allStats = Donation::selectRaw("COUNT(*) as total, SUM(amount) as amount, SUM(admin_charge) as fee")->first();

        return view('admin.developer.monitoring.online_donations', [
            'todayCount' => $todayStats->total ?? 0,
            'todayAmount' => $todayStats->amount ?? 0,
            'todayFee' => $todayStats->fee ?? 0,
            'weekCount' => $weekStats->total ?? 0,
            'weekAmount' => $weekStats->amount ?? 0,
            'weekFee' => $weekStats->fee ?? 0,
            'allCount' => $allStats->total ?? 0,
            'allAmount' => $allStats->amount ?? 0,
            'allFee' => $allStats->fee ?? 0,
        ]);
    }

    /**
     * Standing Donation Monitoring Page
     */    /**
     * Standing Donation Monitoring Page
     */
    public function standingDonations(Request $request)
    {
        if ($request->ajax()) {
            $filterType = $request->get('filter_type', 'monthly');
            $fromDate = $request->get('fromDate');
            $toDate = $request->get('toDate');
            $tabType = $request->get('tab_type', 'active'); // active or inactive
            $today = Carbon::today();

            $query = StandingDonation::with(['user', 'charity'])
                ->select('standing_donations.*')
                ->orderBy('standing_donations.created_at', 'DESC');

            // Filter by tab type
            if ($tabType == 'active') {
                $query->where('status', 1);
            } else {
                $query->whereIn('status', [0, 2]); // Inactive or Complete
            }

            // Apply Date Filters (based on starting date)
            if ($filterType == 'daily') {
                $query->whereDate('standing_donations.starting', $today);
            } elseif ($filterType == 'weekly') {
                $query->whereBetween('standing_donations.starting', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()]);
            } elseif ($filterType == 'monthly') {
                $query->whereMonth('standing_donations.starting', $today->month)->whereYear('standing_donations.starting', $today->year);
            } elseif ($filterType == 'last7') {
                $query->whereBetween('standing_donations.starting', [$today->copy()->subDays(6), $today]);
            } elseif ($filterType == 'custom' && $fromDate && $toDate) {
                $query->whereBetween('standing_donations.starting', [$fromDate, $toDate]);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('date', function($row) {
                    return Carbon::parse($row->created_at)->format('d/m/Y');
                })
                ->addColumn('donor', function($row) {
                    if ($row->user) {
                        $name = $row->user->name . ' ' . $row->user->surname;
                        $email = $row->user->email;
                        return '<span class="fw-bold text-success">' . $name . '</span><br><small>' . $email . '</small>';
                    }
                    return '<span class="text-danger">N/A</span>';
                })
                ->addColumn('charity', function($row) {
                    if ($row->charity) {
                        return '<span class="fw-bold">' . $row->charity->name . '</span>';
                    }
                    return 'N/A';
                })
                ->addColumn('current_balance', function($row) {
                    if ($row->user) {
                        return '£' . number_format($row->user->getAvailableLimit(), 2);
                    }
                    return '<span class="text-muted">N/A</span>';
                })
                ->addColumn('balance_in', function($row) {
                    if ($row->user_id) {
                        $in = Usertransaction::where('user_id', $row->user_id)->where('status', 1)->where('t_type', 'In')->sum('amount');
                        return '£' . number_format($in, 2);
                    }
                    return '<span class="text-muted">N/A</span>';
                })
                ->addColumn('balance_out', function($row) {
                    if ($row->user_id) {
                        $out = Usertransaction::where('user_id', $row->user_id)->where('status', 1)->where('t_type', 'Out')->sum('amount');
                        return '£' . number_format($out, 2);
                    }
                    return '<span class="text-muted">N/A</span>';
                })
                ->editColumn('amount', function($row) {
                    return '<span class="fw-bold">£' . number_format($row->amount, 2) . '</span>';
                })
                ->addColumn('payment_type', function($row) {
                    return $row->payments == '1' ? '<span class="badge bg-info">Fixed</span>' : '<span class="badge bg-secondary">Continuous</span>';
                })
                ->addColumn('total_payments', function($row) {
                    return $row->payments == '1' ? ($row->number_payments ?? 0) : '<span class="text-muted">∞</span>';
                })
                ->addColumn('payments_made', function($row) {
                    // Show count of successfully paid instalments + link to modal
                    $made = StandingdonationDetail::where('standing_donation_id', $row->id)->where('status', 0)->count();
                    if ($made > 0) {
                        return '<a href="javascript:void(0)" class="badge bg-success view-payments" data-id="'.$row->id.'">' . $made . ' (View)</a>';
                    }
                    return '<span class="badge bg-light text-muted">0</span>';
                })
                ->addColumn('payments_skipped', function($row) {
                    $skipped = StandingdonationDetail::where('standing_donation_id', $row->id)->where('status', 2)->count();
                    return $skipped > 0 ? '<span class="badge bg-danger">' . $skipped . '</span>' : '<span class="badge bg-light text-muted">0</span>';
                })
                ->addColumn('payments_remaining', function($row) {
                    if ($row->payments == '1') {
                        $made = StandingdonationDetail::where('standing_donation_id', $row->id)->where('status', 0)->count();
                        $skipped = StandingdonationDetail::where('standing_donation_id', $row->id)->where('status', 2)->count();
                        $remaining = ($row->number_payments ?? 0) - $made - $skipped;
                        return '<span class="badge bg-warning text-dark">' . max(0, $remaining) . '</span>';
                    }
                    return '<span class="text-muted">∞</span>';
                })
                ->addColumn('interval_text', function($row) {
                    return $row->interval . ' Month(s)';
                })
                ->addColumn('start_date', function($row) {
                    return Carbon::parse($row->starting)->format('d/m/Y');
                })
                ->addColumn('next_payment', function($row) {
                    $lastDetail = StandingdonationDetail::where('standing_donation_id', $row->id)->latest('id')->first();
                    $baseDate = $lastDetail ? Carbon::parse($lastDetail->instalment_date) : Carbon::parse($row->starting);
                    $interval = is_numeric($row->interval) ? (int)$row->interval : 1;
                    $nextDate = $baseDate->copy()->addMonths($interval);
                    return $nextDate->format('d/m/Y');
                })
                ->addColumn('status', function($row) {
                    if ($row->status == 1) return '<span class="badge bg-success">Active</span>';
                    if ($row->status == 2) return '<span class="badge bg-secondary">Complete</span>';
                    return '<span class="badge bg-danger">Inactive</span>';
                })
                ->filterColumn('donor', function($query, $keyword) {
                    $query->whereHas('user', function($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%")
                          ->orWhere('surname', 'like', "%{$keyword}%")
                          ->orWhere('email', 'like', "%{$keyword}%");
                    });
                })
                ->filterColumn('charity', function($query, $keyword) {
                    $query->whereHas('charity', function($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->rawColumns(['donor', 'charity', 'amount', 'payment_type', 'total_payments', 'payments_made', 'payments_skipped', 'payments_remaining', 'status', 'current_balance', 'balance_in', 'balance_out'])
                ->make(true);
        }

        // --- Stat Cards ---
        $totalStanding = StandingDonation::count();
        $activeStanding = StandingDonation::where('status', 1)->count();
        $inactiveStanding = StandingDonation::whereIn('status', [0, 2])->count();
        $completeStanding = StandingDonation::where('status', 2)->count();

        // Total collected amounts (from successful instalments)
        $totalCollected = StandingdonationDetail::where('status', 0)->sum('amount');

        // --- Today's Standing Order Transactions ---
        $todaysStandingTransactions = Usertransaction::whereNotNull('standing_donationdetails_id')
            ->whereDate('created_at', Carbon::today())
            ->with(['user', 'charity'])
            ->latest()
            ->get();

        $todayCollected = $todaysStandingTransactions->sum('amount');

        // --- This Week's Standing Order Transactions ---
        $weekCollected = Usertransaction::whereNotNull('standing_donationdetails_id')
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->sum('amount');

        // --- Upcoming 7 Days Standing Donations ---
        $intervalMap = ['Monthly' => 1, 'Quarterly' => 3, 'Yearly' => 12, 'Annually' => 12, 'Weekly' => 0, 'Bi-Weekly' => 0];
        $next7DaysStart = Carbon::tomorrow()->startOfDay();
        $next7DaysEnd = Carbon::tomorrow()->addDays(6)->endOfDay();
        $upcoming7DaysDonations = collect();

        $activeOrders = StandingDonation::where('status', 1)
            ->with(['user', 'charity', 'standingdonationDetail' => function($q) { $q->latest('id')->limit(1); }])
            ->get();

        foreach ($activeOrders as $order) {
            $detail = $order->standingdonationDetail->first();
            $start_date = $detail ? $detail->instalment_date : $order->starting;
            $start_date_carbon = Carbon::parse($start_date);
            $intervalValue = $order->interval;
            $intervalMonths = is_numeric($intervalValue) ? (int)$intervalValue : ($intervalMap[$intervalValue] ?? 1);
            if ($intervalMonths === 0) $intervalMonths = 1;

            $instalment_date_carbon = $start_date_carbon->copy()->addMonths($intervalMonths);
            if ($instalment_date_carbon->between($next7DaysStart, $next7DaysEnd)) {
                $order->expected_date = $instalment_date_carbon->format('d M, Y');
                $upcoming7DaysDonations->push($order);
            }
        }
        $upcoming7DaysDonations = $upcoming7DaysDonations->sortBy('expected_date');

        return view('admin.developer.monitoring.standing_donations', compact(
            'totalStanding', 'activeStanding', 'inactiveStanding', 'completeStanding',
            'todaysStandingTransactions', 'upcoming7DaysDonations', 'todayCollected', 'weekCollected', 'totalCollected'
        ));
    }

    /**
     * Get Standing Donation Payment Details (For Modal)
     */
    public function getStandingPaymentDetails($id)
    {
        // Get all instalment records for this standing donation
        $details = StandingdonationDetail::with(['user', 'charity'])
            ->where('standing_donation_id', $id)
            ->orderBy('id', 'DESC')
            ->get();

        return response()->json($details->map(function($item) {
            $statusBadge = '';
            if ($item->status == 0) {
                $statusBadge = '<span class="badge bg-success">Paid</span>';
            } elseif ($item->status == 2) {
                $statusBadge = '<span class="badge bg-danger">Skipped (Insufficient Balance)</span>';
            } else {
                $statusBadge = '<span class="badge bg-warning text-dark">Pending</span>';
            }

            return [
                'donor'   => ($item->user->name ?? 'N/A') . ' ' . ($item->user->surname ?? ''),
                'charity' => $item->charity->name ?? 'N/A',
                'amount'  => '£' . number_format($item->amount, 2),
                'date'    => Carbon::parse($item->instalment_date)->format('d/m/Y'),
                'mode'    => $item->instalment_mode,
                'status'  => $statusBadge
            ];
        }));
    }

    /**
     * Donor Monitoring Page
     */    /**
     * Donor Monitoring Page
     */
    public function donorMonitoring(Request $request)
    {
        if ($request->ajax()) {
            $filterType = $request->get('filter_type', 'monthly');
            $fromDate = $request->get('fromDate');
            $toDate = $request->get('toDate');
            $today = Carbon::today();

            $query = User::where('is_type', 'user')
                ->select('users.*')
                ->orderBy('users.created_at', 'DESC');

            // Apply Date Filters (based on donor registration date)
            if ($filterType == 'daily') {
                $query->whereDate('users.created_at', $today);
            } elseif ($filterType == 'weekly') {
                $query->whereBetween('users.created_at', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()]);
            } elseif ($filterType == 'monthly') {
                $query->whereMonth('users.created_at', $today->month)->whereYear('users.created_at', $today->year);
            } elseif ($filterType == 'last7') {
                $query->whereBetween('users.created_at', [$today->copy()->subDays(6), $today]);
            } elseif ($filterType == 'custom' && $fromDate && $toDate) {
                $query->whereBetween('users.created_at', [$fromDate, $toDate . ' 23:59:59']);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('donor', function($row) {
                    $name = $row->name . ' ' . $row->surname;
                    $email = $row->email;
                    return '<span class="fw-bold text-success">' . $name . '</span><br><small>' . $email . '</small>';
                })
                ->addColumn('accountno', function($row) {
                    return '<span class="badge bg-secondary">' . ($row->accountno ?? 'N/A') . '</span>';
                })
                ->addColumn('balance_in', function($row) {
                    $in = Usertransaction::where('user_id', $row->id)->where('status', 1)->where('t_type', 'In')->sum('amount');
                    return '£' . number_format($in, 2);
                })
                ->addColumn('balance_out', function($row) {
                    $out = Usertransaction::where('user_id', $row->id)->where('status', 1)->where('t_type', 'Out')->sum('amount');
                    return '£' . number_format($out, 2);
                })
                ->addColumn('difference', function($row) {
                    $in = Usertransaction::where('user_id', $row->id)->where('status', 1)->where('t_type', 'In')->sum('amount');
                    $out = Usertransaction::where('user_id', $row->id)->where('status', 1)->where('t_type', 'Out')->sum('amount');
                    $diff = $in - $out;
                    $color = $diff < 0 ? 'text-danger' : 'text-success';
                    return '<span class="fw-bold ' . $color . '">£' . number_format($diff, 2) . '</span>';
                })
                ->addColumn('total_commission', function($row) {
                    $commission = Usertransaction::where('user_id', $row->id)->where('status', 1)->sum('commission');
                    return '£' . number_format($commission, 2);
                })
                ->addColumn('balance', function($row) {
                    $color = $row->balance < 0 ? 'text-danger' : 'text-primary';
                    return '<span class="fw-bold ' . $color . '">£' . number_format($row->balance, 2) . '</span>';
                })
                ->addColumn('limit', function($row) {
                    return '<span class="fw-bold">£' . number_format($row->getAvailableLimit(), 2) . '</span>';
                })
                ->editColumn('expected_gift_aid', function($row) {
                    return '£' . number_format($row->expected_gift_aid ?? 0, 2);
                })
                ->addColumn('status', function($row) {
                    return $row->status == 1 
                        ? '<span class="badge bg-success">Active</span>' 
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->filterColumn('donor', function($query, $keyword) {
                    $query->whereRaw("CONCAT(name, ' ', surname) LIKE ?", ["%{$keyword}%"])
                          ->orWhere('email', 'like', "%{$keyword}%");
                })
                ->rawColumns(['donor', 'accountno', 'balance_in', 'balance_out', 'difference', 'total_commission', 'balance', 'limit', 'status'])
                ->make(true);
        }

        // --- Summary Cards ---
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        // 1. Today's new donors
        $todayNewDonors = User::where('is_type', 'user')->whereDate('created_at', $today)->count();
        
        // 2. This week's new donors
        $weekNewDonors = User::where('is_type', 'user')->whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
        
        // 3. Total active donors
        $activeDonors = User::where('is_type', 'user')->where('status', 1)->count();
        
        // 4. Total inactive donors
        $inactiveDonors = User::where('is_type', 'user')->where('status', 0)->count();
        
        // 5. Donors with no transactions
        $noTransactionDonors = User::where('is_type', 'user')
            ->whereDoesntHave('usertransaction')
            ->count();
        
        // 6. Personal Account
        $personalAccounts = User::where('is_type', 'user')->where('profile_type', 'Personal')->count();
        
        // 7. Company Account
        $companyAccounts = User::where('is_type', 'user')->where('profile_type', 'Company')->count();
        
        // 8. Others Account
        $othersAccounts = User::where('is_type', 'user')
            ->whereNotIn('profile_type', ['Personal', 'Company'])
            ->orWhereNull('profile_type')
            ->count();

        return view('admin.developer.monitoring.donors', compact(
            'todayNewDonors', 'weekNewDonors', 'activeDonors', 'inactiveDonors',
            'noTransactionDonors', 'personalAccounts', 'companyAccounts', 'othersAccounts'
        ));
    }

    /**
     * Charity Monitoring Page
     */    /**
     * Charity Monitoring Page
     */
    public function charityMonitoring(Request $request)
    {
        if ($request->ajax()) {
            $filterType = $request->get('filter_type', 'monthly');
            $fromDate = $request->get('fromDate');
            $toDate = $request->get('toDate');
            $today = Carbon::today();

            $query = Charity::select('charities.*')
                ->orderBy('charities.created_at', 'DESC');

            // Apply Date Filters (based on charity registration date)
            if ($filterType == 'daily') {
                $query->whereDate('charities.created_at', $today);
            } elseif ($filterType == 'weekly') {
                $query->whereBetween('charities.created_at', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()]);
            } elseif ($filterType == 'monthly') {
                $query->whereMonth('charities.created_at', $today->month)->whereYear('charities.created_at', $today->year);
            } elseif ($filterType == 'last7') {
                $query->whereBetween('charities.created_at', [$today->copy()->subDays(6), $today]);
            } elseif ($filterType == 'custom' && $fromDate && $toDate) {
                $query->whereBetween('charities.created_at', [$fromDate, $toDate . ' 23:59:59']);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('charity', function($row) {
                    $name = $row->name;
                    $email = $row->email;
                    return '<span class="fw-bold text-success">' . $name . '</span><br><small>' . $email . '</small>';
                })
                ->addColumn('account_no', function($row) {
                    return '<span class="badge bg-secondary">' . ($row->acc_no ?? 'N/A') . '</span>';
                })
                ->addColumn('balance_in', function($row) {
                    $in = Transaction::where('charity_id', $row->id)->where('status', 1)->where('t_type', 'In')->sum('amount');
                    return '£' . number_format($in, 2);
                })
                ->addColumn('balance_out', function($row) {
                    $out = Transaction::where('charity_id', $row->id)->where('status', 1)->where('t_type', 'Out')->sum('amount');
                    return '£' . number_format($out, 2);
                })
                ->addColumn('difference', function($row) {
                    $in = Transaction::where('charity_id', $row->id)->where('status', 1)->where('t_type', 'In')->sum('amount');
                    $out = Transaction::where('charity_id', $row->id)->where('status', 1)->where('t_type', 'Out')->sum('amount');
                    $diff = $in - $out;
                    $color = $diff < 0 ? 'text-danger' : 'text-success';
                    return '<span class="fw-bold ' . $color . '">£' . number_format($diff, 2) . '</span>';
                })
                ->addColumn('balance', function($row) {
                    $color = $row->balance < 0 ? 'text-danger' : 'text-primary';
                    return '<span class="fw-bold ' . $color . '">£' . number_format($row->balance, 2) . '</span>';
                })
                ->addColumn('auto_payment', function($row) {
                    return $row->auto_payment == 1 
                        ? '<span class="badge bg-success">Enabled</span>' 
                        : '<span class="badge bg-danger">Disabled</span>';
                })
                ->addColumn('status', function($row) {
                    return $row->status == 1 
                        ? '<span class="badge bg-success">Active</span>' 
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->filterColumn('charity', function($query, $keyword) {
                    $query->where('name', 'like', "%{$keyword}%")
                          ->orWhere('email', 'like', "%{$keyword}%");
                })
                ->rawColumns(['charity', 'account_no', 'balance_in', 'balance_out', 'difference', 'balance', 'auto_payment', 'status'])
                ->make(true);
        }

        // --- Summary Cards ---
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        // 1. Today's new charities
        $todayNewCharities = Charity::whereDate('created_at', $today)->count();
        
        // 2. This week's new charities
        $weekNewCharities = Charity::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
        
        // 3. Total active charities
        $activeCharities = Charity::where('status', 1)->count();
        
        // 4. Total inactive charities
        $inactiveCharities = Charity::where('status', 0)->count();
        
        // 5. No transaction charities
        $noTransactionCharities = Charity::whereDoesntHave('transaction')->count();
        
        // 6. Auto Payment Enabled
        $autoPaymentEnabled = Charity::where('auto_payment', 1)->count();
        
        // 7. Auto Payment Disabled
        $autoPaymentDisabled = Charity::where('auto_payment', 0)->count();
        
        // 8. Total Charity Balance
        $charityBalanceSum = Charity::sum('balance');

        return view('admin.developer.monitoring.charities', compact(
            'todayNewCharities', 'weekNewCharities', 'activeCharities', 'inactiveCharities',
            'noTransactionCharities', 'autoPaymentEnabled', 'autoPaymentDisabled', 'charityBalanceSum'
        ));
    }

    /**
     * Process Voucher Monitoring Page - Grouped by Date
     */    /**
     * Process Voucher Monitoring Page - Grouped by Date
     */
    public function processVoucherMonitoring(Request $request)
    {
        if ($request->ajax()) {
            $filterType = $request->get('filter_type', 'monthly');
            $fromDate = $request->get('fromDate');
            $toDate = $request->get('toDate');
            $today = Carbon::today();

            $query = Provoucher::query()
                ->selectRaw("
                    DATE(created_at) as date_group,
                    COUNT(*) as total_processed,
                    SUM(CASE WHEN status = 1 AND waiting = 'No' AND expired = 'No' THEN 1 ELSE 0 END) as complete_count,
                    SUM(CASE WHEN status = 0 AND waiting = 'No' AND expired = 'No' THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN waiting = 'Yes' AND expired = 'No' THEN 1 ELSE 0 END) as waiting_count,
                    SUM(CASE WHEN expired = 'Yes' AND waiting = 'No' THEN 1 ELSE 0 END) as expired_count,
                    SUM(CASE WHEN waiting = 'Yes' AND expired = 'Yes' THEN 1 ELSE 0 END) as multiple_count,
                    SUM(CASE 
                        WHEN NOT (
                            (status = 1 AND waiting = 'No' AND expired = 'No') OR 
                            (status = 0 AND waiting = 'No' AND expired = 'No') OR 
                            (waiting = 'Yes' AND expired = 'No') OR 
                            (expired = 'Yes' AND waiting = 'No') OR 
                            (waiting = 'Yes' AND expired = 'Yes')
                        ) THEN 1 ELSE 0 
                    END) as cancel_count,
                    SUM(amount) as total_amount,
                    '' as voucher_state
                ")
                ->groupBy('date_group')
                ->orderBy('date_group', 'DESC');

            // Apply Date Filters
            if ($filterType == 'daily') {
                $query->whereDate('created_at', $today);
            } elseif ($filterType == 'weekly') {
                $query->whereBetween('created_at', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()]);
            } elseif ($filterType == 'monthly') {
                $query->whereMonth('created_at', $today->month)->whereYear('created_at', $today->year);
            } elseif ($filterType == 'last7') {
                $query->whereBetween('created_at', [$today->copy()->subDays(6), $today]);
            } elseif ($filterType == 'custom' && $fromDate && $toDate) {
                $query->whereBetween('created_at', [$fromDate, $toDate . ' 23:59:59']);
            }

            return DataTables::of($query)
                ->addColumn('date', function($row) {
                    return '<span data-raw="'.$row->date_group.'">'.\Carbon\Carbon::parse($row->date_group)->format('d/m/Y').'</span>';
                })
                ->addColumn('total_processed', function($row) {
                    return '<a href="javascript:void(0)" class="badge bg-primary view-pv-details" data-type="all" data-date="'.$row->date_group.'">'.$row->total_processed.'</a>';
                })
                ->addColumn('complete_count', function($row) {
                    $count = $row->complete_count;
                    return $count > 0 ? '<a href="javascript:void(0)" class="badge bg-success view-pv-details" data-type="complete" data-date="'.$row->date_group.'">'.$count.'</a>' : '<span class="badge bg-light text-muted">0</span>';
                })
                ->addColumn('pending_count', function($row) {
                    $count = $row->pending_count;
                    return $count > 0 ? '<a href="javascript:void(0)" class="badge bg-warning text-dark view-pv-details" data-type="pending" data-date="'.$row->date_group.'">'.$count.'</a>' : '<span class="badge bg-light text-muted">0</span>';
                })
                ->addColumn('waiting_count', function($row) {
                    $count = $row->waiting_count;
                    return $count > 0 ? '<a href="javascript:void(0)" class="badge bg-info text-dark view-pv-details" data-type="waiting" data-date="'.$row->date_group.'">'.$count.'</a>' : '<span class="badge bg-light text-muted">0</span>';
                })
                ->addColumn('expired_count', function($row) {
                    $count = $row->expired_count;
                    return $count > 0 ? '<a href="javascript:void(0)" class="badge bg-danger view-pv-details" data-type="expired" data-date="'.$row->date_group.'">'.$count.'</a>' : '<span class="badge bg-light text-muted">0</span>';
                })
                ->addColumn('multiple_count', function($row) {
                    $count = $row->multiple_count;
                    return $count > 0 ? '<a href="javascript:void(0)" class="badge bg-dark view-pv-details" data-type="multiple" data-date="'.$row->date_group.'">'.$count.'</a>' : '<span class="badge bg-light text-muted">0</span>';
                })
                ->addColumn('cancel_count', function($row) {
                    $count = $row->cancel_count;
                    return $count > 0 ? '<a href="javascript:void(0)" class="badge bg-secondary view-pv-details" data-type="cancel" data-date="'.$row->date_group.'">'.$count.'</a>' : '<span class="badge bg-light text-muted">0</span>';
                })
                ->editColumn('total_amount', function($row) {
                    return '£' . number_format($row->total_amount, 2);
                })
                ->rawColumns(['date', 'total_processed', 'complete_count', 'pending_count', 'waiting_count', 'expired_count', 'multiple_count', 'cancel_count'])
                ->make(true);
        }

        // --- Summary Cards Data ---
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $todayStats = Provoucher::whereDate('created_at', $today)
            ->selectRaw("COUNT(*) as total, SUM(amount) as amount")->first();

        $weekStats = Provoucher::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->selectRaw("COUNT(*) as total, SUM(amount) as amount")->first();

        $allTimeStats = Provoucher::selectRaw("COUNT(*) as total, SUM(amount) as amount")->first();

        return view('admin.developer.monitoring.process_voucher', [
            'todayCount' => $todayStats->total ?? 0,
            'todayAmount' => $todayStats->amount ?? 0,
            'weekCount' => $weekStats->total ?? 0,
            'weekAmount' => $weekStats->amount ?? 0,
            'allCount' => $allTimeStats->total ?? 0,
            'allAmount' => $allTimeStats->amount ?? 0,
        ]);
    }

    /**
     * Get Process Voucher details for a specific date and type
     */    /**
     * Get Process Voucher details for a specific date and type
     */
    public function getProcessVoucherDayDetails(Request $request)
    {
        $date = $request->date;
        $type = $request->type;

        $query = Provoucher::with(['charity', 'user'])
            ->whereDate('created_at', $date);

        if ($type == 'complete') {
            $query->where('status', 1)->where('waiting', 'No')->where('expired', 'No');
        } elseif ($type == 'pending') {
            $query->where('status', 0)->where('waiting', 'No')->where('expired', 'No');
        } elseif ($type == 'waiting') {
            $query->where('waiting', 'Yes')->where('expired', 'No');
        } elseif ($type == 'expired') {
            $query->where('expired', 'Yes')->where('waiting', 'No');
        } elseif ($type == 'multiple') {
            $query->where('waiting', 'Yes')->where('expired', 'Yes');
        } elseif ($type == 'cancel') {
            $query->whereRaw("NOT (
                (status = 1 AND waiting = 'No' AND expired = 'No') OR 
                (status = 0 AND waiting = 'No' AND expired = 'No') OR 
                (waiting = 'Yes' AND expired = 'No') OR 
                (expired = 'Yes' AND waiting = 'No') OR 
                (waiting = 'Yes' AND expired = 'Yes')
            )");
        }

        $data = $query->get();

        return response()->json($data->map(function($item) {
            $isChecked = !is_null($item->checked_by);
            $checkBtn = $isChecked 
                ? '<button class="btn btn-sm btn-success uncheck-voucher" data-id="'.$item->id.'"><i class="fas fa-check"></i> Checked</button>'
                : '<button class="btn btn-sm btn-outline-primary check-voucher" data-id="'.$item->id.'"><i class="fas fa-check"></i> Check</button>';

            // Generate URLs for charity and donor profile links
            $charityUrl = route('charity.tranview', ['id' => $item->charity_id]);
            $donorUrl = route('donor.profile', ['id' => $item->user_id]);

            // --- Charity Info ---
            $charityName = $item->charity->name ?? 'N/A';
            $charityEmail = $item->charity->email ?? 'N/A';
            $charityHtml = '<div style="line-height: 1.4;">';
            $charityHtml .= '<a href="'.$charityUrl.'" target="_blank" class="text-decoration-none fw-bold text-success">'.$charityName.'</a>';
            $charityHtml .= '<br><span class="badge bg-light text-secondary" style="font-size: 0.7rem; font-weight: normal;">'.$charityEmail.'</span>';
            $charityHtml .= '</div>';

            // --- Donor Info ---
            $donorName = trim(($item->user->name ?? '') . ' ' . ($item->user->surname ?? ''));
            if (empty($donorName)) $donorName = 'N/A';
            $donorEmail = $item->user->email ?? 'N/A';
            $donorHtml = '<div style="line-height: 1.4;">';
            $donorHtml .= '<a href="'.$donorUrl.'" target="_blank" class="text-decoration-none fw-bold text-primary">'.$donorName.'</a>';
            $donorHtml .= '<br><span class="badge bg-light text-secondary" style="font-size: 0.7rem; font-weight: normal;">'.$donorEmail.'</span>';
            $donorHtml .= '</div>';

            // --- Donor Balance & Standard Amount ---
            $donorBalance = $item->user ? $item->user->getAvailableLimit() : 0;
            $donorBalanceColor = $donorBalance < 0 ? 'text-danger' : 'text-success';
            $standardAmount = $item->user ? ($item->user->standard_amount ?: 500) : 500;

            // --- Amount Red Logic ---
            $isWarning = ($item->amount > $donorBalance || $item->amount >= $standardAmount);
            $amountClass = $isWarning ? 'text-danger fw-bold' : '';

            // --- Note Input ---
                        // --- Dev Note Input ---
            $noteInput = '<textarea class="form-control form-control-sm pv-note-input" data-id="'.$item->id.'" rows="1" placeholder="Add dev note">'.htmlspecialchars($item->dev_note ?? '').'</textarea>';

            return [
                'charity' => $charityHtml,
                'charity_balance' => '£' . number_format($item->charity->balance ?? 0, 2),
                'donor'   => $donorHtml,
                'donor_balance' => '<span class="'.$donorBalanceColor.' fw-bold">£' . number_format($donorBalance, 2) . '</span>',
                'standard_amount' => '£' . number_format($standardAmount, 2),
                'voucher' => $item->cheque_no,
                'amount'  => '<span class="'.$amountClass.'">£' . number_format($item->amount, 2) . '</span>',
                'status'  => $item->status == 1 ? '<span class="badge bg-success">Complete</span>' : '<span class="badge bg-warning text-dark">Pending</span>',
                'check_btn' => $checkBtn,
                'voucher_state' => $item->voucher_state ?? 'N/A',
                'dev_note_input' => $noteInput
            ];
        }));
    }

    /**
     * Update Voucher Note (AJAX)
     */
        /**
     * Update Voucher Dev Note (AJAX)
     */
    public function updateVoucherNote(Request $request, $id)
    {
        $voucher = Provoucher::find($id);
        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Voucher not found.'], 404);
        }

        $voucher->dev_note = $request->dev_note;
        $voucher->save();

        return response()->json(['success' => true, 'message' => 'Dev note saved successfully.']);
    }

    /**
     * Check Voucher (AJAX)
     */
    public function checkVoucher(Request $request, $id)
    {
        $voucher = Provoucher::find($id);
        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Voucher not found.'], 404);
        }

        $voucher->checked_by = auth()->user()->name ?? 'Developer';
        $voucher->checked_date = now();
        $voucher->save();

        return response()->json(['success' => true, 'message' => 'Voucher checked successfully.']);
    }

    /**
     * Uncheck Voucher (AJAX)
     */
    public function uncheckVoucher(Request $request, $id)
    {
        $voucher = Provoucher::find($id);
        if (!$voucher) {
            return response()->json(['success' => false, 'message' => 'Voucher not found.'], 404);
        }

        $voucher->checked_by = null;
        $voucher->checked_date = null;
        $voucher->save();

        return response()->json(['success' => true, 'message' => 'Voucher unchecked successfully.']);
    }






    /**
     * Voucher Monitoring Page - Shows Voucher Book Orders
     */
    public function voucherMonitoring(Request $request)
    {
        if ($request->ajax()) {
            $filterType = $request->get('filter_type', 'monthly');
            $fromDate = $request->get('fromDate');
            $toDate = $request->get('toDate');
            $today = Carbon::today();

            $orders = Order::with('user')->withCount('barcodes')->latest();

            // Apply Date Filters
            if ($filterType == 'daily') {
                $orders->whereDate('created_at', $today);
            } elseif ($filterType == 'weekly') {
                $startOfWeek = $today->copy()->startOfWeek();
                $endOfWeek = $today->copy()->endOfWeek();
                $orders->whereBetween('created_at', [$startOfWeek, $endOfWeek]);
            } elseif ($filterType == 'monthly') {
                $orders->whereMonth('created_at', $today->month)
                       ->whereYear('created_at', $today->year);
            } elseif ($filterType == 'last7') {
                $startDate = $today->copy()->subDays(6);
                $orders->whereBetween('created_at', [$startDate, $today]);
            } elseif ($filterType == 'custom' && $fromDate && $toDate) {
                $orders->whereBetween('created_at', [$fromDate, $toDate . ' 23:59:59']);
            }

            return DataTables::of($orders)
                ->addColumn('date', function ($row) {
                    return '<span data-raw="'.$row->created_at.'">'.
                        Carbon::parse($row->created_at)->format('d/m/Y H:i').
                    '</span>';
                })
                ->addColumn('donor', function ($row) {
                    if ($row->user) {
                        $name = $row->user->name . ' ' . $row->user->surname;
                        $email = $row->user->email;
                        $style = 'style="color: #28a745; font-weight: bold;"';
                        return '<span ' . $style . '>' . $name . '</span><br><small>' . $email . '</small>';
                    } else {
                        $name = trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? '')) ?: 'Guest';
                        $email = $row->email ?? 'N/A';
                        $style = 'style="color: #dc3545; font-weight: bold;"';
                        return '<span ' . $style . '>' . $name . '</span><br><small>' . $email . '</small>';
                    }
                })
                ->filterColumn('donor', function($query, $keyword) {
                    $query->whereHas('user', function($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%")
                          ->orWhere('surname', 'like', "%{$keyword}%")
                          ->orWhere('email', 'like', "%{$keyword}%");
                    })->orWhere('first_name', 'like', "%{$keyword}%")
                      ->orWhere('last_name', 'like', "%{$keyword}%")
                      ->orWhere('email', 'like', "%{$keyword}%");
                })
                ->addColumn('order_id', function($row) {
                    $html = '<span class="fw-bold">#'.$row->order_id.'</span>';
                    $deliveryBadge = $row->delivery_option == 'Delivery' ? 'bg-info' : 'bg-secondary';
                    $html .= '<br><small><span class="badge '.$deliveryBadge.'">'.$row->delivery_option.'</span> (£'.number_format($row->delivery_charge, 2).')</small>';
                    $paymentBadge = $row->payment_method == 'stripe' ? 'bg-primary' : 'bg-success';
                    $paymentText = $row->payment_method == 'stripe' ? 'Stripe' : 'Balance';
                    $html .= '<br><small><span class="badge '.$paymentBadge.'">'.$paymentText.'</span></small>';
                    return $html;
                })
                ->editColumn('amount', function($row) {
                    return '<span class="fw-bold">£' . number_format($row->amount, 2) . '</span>';
                })
                ->addColumn('total_barcodes', function($row) {
                    $count = $row->barcodes_count;
                    if ($count > 0) {
                        return '<a href="javascript:void(0)" class="badge bg-info text-white view-barcodes" data-id="'.$row->id.'"><i class="fa fa-barcode"></i> '.$count.'</a>';
                    }
                    return '<span class="text-muted">No barcodes</span>';
                })
                ->addColumn('status', function($row) {
                    return $row->status == 1 
                        ? '<span class="badge bg-success">Completed</span>' 
                        : '<span class="badge bg-warning text-dark">Pending</span>';
                })
                ->addColumn('action', function($row) {
                    return '<a href="javascript:void(0)" class="btn btn-sm btn-outline-primary view-order-details" data-id="'.$row->id.'"><i class="fa fa-eye"></i> View</a>';
                })
                ->rawColumns(['date', 'donor', 'order_id', 'amount', 'total_barcodes', 'status', 'action'])
                ->make(true);
        }

                // --- Summary Cards Data ---
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // 1. Barcode Status Stats
        $dailyWith = Order::whereDate('created_at', $today)->has('barcodes')->count();
        $dailyWithout = Order::whereDate('created_at', $today)->doesntHave('barcodes')->count();

        $weeklyWith = Order::whereBetween('created_at', [$startOfWeek, $endOfWeek])->has('barcodes')->count();
        $weeklyWithout = Order::whereBetween('created_at', [$startOfWeek, $endOfWeek])->doesntHave('barcodes')->count();

        $monthlyWith = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])->has('barcodes')->count();
        $monthlyWithout = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])->doesntHave('barcodes')->count();

        $allWith = Order::has('barcodes')->count();
        $allWithout = Order::doesntHave('barcodes')->count();

        // 2. Order Value Stats (For Today, Week, All-Time Cards)
        $todayStats = Order::whereDate('created_at', $today)
            ->selectRaw("COUNT(*) as today_orders, SUM(amount) as today_amount, SUM(delivery_charge) as today_delivery")->first();

        $weekStats = Order::whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->selectRaw("COUNT(*) as week_orders, SUM(amount) as week_amount, SUM(delivery_charge) as week_delivery")->first();

        $allTimeStats = Order::selectRaw("COUNT(*) as total_orders, SUM(amount) as total_amount")->first();

        // 3. Assign to Data Array
        $data = [
            'dailyWith' => $dailyWith, 'dailyWithout' => $dailyWithout,
            'weeklyWith' => $weeklyWith, 'weeklyWithout' => $weeklyWithout,
            'monthlyWith' => $monthlyWith, 'monthlyWithout' => $monthlyWithout,
            'allWith' => $allWith, 'allWithout' => $allWithout,
            
            'todayOrders' => $todayStats->today_orders ?? 0,
            'todayAmount' => $todayStats->today_amount ?? 0,
            'todayDelivery' => $todayStats->today_delivery ?? 0,
            
            'weekOrders' => $weekStats->week_orders ?? 0,
            'weekAmount' => $weekStats->week_amount ?? 0,
            'weekDelivery' => $weekStats->week_delivery ?? 0,

            'totalOrders' => $allTimeStats->total_orders ?? 0,
            'totalAmount' => $allTimeStats->total_amount ?? 0,
        ];

        return view('admin.developer.monitoring.vouchers', $data);
    }

    /**
     * Get Barcodes for a specific order (For Modal)
     */
    public function getOrderBarcodes($id)
    {
        $order = Order::with('orderhistories.barcodes', 'orderhistories.voucher')->find($id);
        
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $barcodes = [];
        foreach ($order->orderhistories as $history) {
            foreach ($history->barcodes as $barcode) {
                $barcodes[] = [
                    'voucher_type' => $history->voucher->type ?? 'N/A',
                    'barcode'      => $barcode->barcode,
                    'amount'       => '£' . number_format($barcode->amount, 2),
                    'status'       => $barcode->status == 1 ? '<span class="badge bg-danger">Cancelled</span>' : '<span class="badge bg-success">Active</span>'
                ];
            }
        }

        return response()->json($barcodes);
    }


    /**
     * Audit donor Page - Shows transaction summary grouped by donor and date
     */
    public function auditDonors(Request $request)
    {
        if ($request->ajax()) {
            // Default to 'monthly' if no filter is selected
            $filterType = $request->get('filter_type', 'monthly');
            $fromDate = $request->get('fromDate');
            $toDate = $request->get('toDate');
            $today = Carbon::today();

            $query = Usertransaction::query()
                ->where('status', 1)
                ->whereNotNull('usertransactions.user_id')
                ->whereNotNull('usertransactions.business_date')
                ->select([
                    'usertransactions.business_date as date_group',
                    'usertransactions.user_id',
                    
                    DB::raw("SUM(CASE WHEN donation_id IS NOT NULL THEN amount ELSE 0 END) as online_sum"),
                    DB::raw("SUM(CASE WHEN standing_donationdetails_id IS NOT NULL THEN amount ELSE 0 END) as standing_sum"),
                    DB::raw("SUM(CASE WHEN cheque_no IS NOT NULL THEN amount ELSE 0 END) as voucher_sum"),
                    DB::raw("SUM(CASE WHEN campaign_id IS NOT NULL THEN amount ELSE 0 END) as campaign_sum"),
                    DB::raw("SUM(CASE WHEN onegiv_transaction_id IS NOT NULL OR title LIKE '%OneGiv Card%' OR source = 'OneGiv Card' THEN CAST(amount AS DECIMAL(10,2)) ELSE 0 END) as card_sum"),
                    
                    // NEW COLUMNS (Before Topup)
                    DB::raw("SUM(CASE WHEN title = 'Prepaid Voucher Book' THEN CAST(amount AS DECIMAL(10,2)) ELSE 0 END) as book_order_sum"),
                    DB::raw("SUM(CASE WHEN title = 'Delivery Charge' THEN CAST(amount AS DECIMAL(10,2)) ELSE 0 END) as delivery_cost_sum"),
                    DB::raw("SUM(CASE WHEN title = 'Transfer to TDF' THEN CAST(amount AS DECIMAL(10,2)) ELSE 0 END) as tdf_sum"),
                    DB::raw("SUM(CASE WHEN gift = 1 THEN (CAST(amount AS DECIMAL(10,2)) + commission) * 0.25 ELSE 0 END) as gift_aid_sum"),
                    
                    // Topup, Commission, Total, Balance
                    DB::raw("SUM(CASE WHEN t_type = 'In' THEN amount ELSE 0 END) as topup_sum"),
                    DB::raw("SUM(CASE WHEN t_type = 'In' THEN commission ELSE 0 END) as commission_sum"),
                    DB::raw("SUM(CASE WHEN t_type = 'Out' THEN amount ELSE 0 END) as total_sum"),
                    DB::raw("(SUM(CASE WHEN t_type = 'In' THEN amount ELSE 0 END) - SUM(CASE WHEN t_type = 'Out' THEN amount ELSE 0 END)) as balance_sum")
                ])
                ->groupBy('date_group', 'usertransactions.user_id')
                ->orderByRaw('date_group DESC')
                ->orderBy('usertransactions.user_id')
                ->with('user');

            // Apply Date Filters
            if ($filterType == 'daily') {
                $query->whereDate('usertransactions.business_date', $today);
            } elseif ($filterType == 'weekly') {
                $startOfWeek = $today->copy()->startOfWeek();
                $endOfWeek = $today->copy()->endOfWeek();
                $query->whereBetween('usertransactions.business_date', [$startOfWeek, $endOfWeek]);
            } elseif ($filterType == 'monthly') {
                $query->whereMonth('usertransactions.business_date', $today->month)
                      ->whereYear('usertransactions.business_date', $today->year);
            } elseif ($filterType == 'last7') {
                $startDate = $today->copy()->subDays(6);
                $query->whereBetween('usertransactions.business_date', [$startDate, $today]);
            } elseif ($filterType == 'custom' && $fromDate && $toDate) {
                $query->whereBetween('usertransactions.business_date', [$fromDate, $toDate]);
            }
            // If 'all', no date filter is applied

            return DataTables::of($query)
                ->addColumn('date_group', function ($row) {
                    return '<span data-raw="'.$row->date_group.'">'.
                        Carbon::parse($row->date_group)->format('d/m/Y').
                    '</span>';
                })
                ->addColumn('donor_name', function ($row) {
                    $user = $row->user;
                    $name = $user ? $user->name.' '.$user->surname : 'N/A';
                    $email = $user->email ?? '';
                    $style = 'style="color: #28a745; font-weight: bold;"'; 
                    return '<span ' . $style . '>' . $name . ' (' . $email . ')</span>';
                })
                ->filterColumn('donor_name', function($query, $keyword) {
                    $query->whereHas('user', function($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%")
                          ->orWhere('surname', 'like', "%{$keyword}%")
                          ->orWhere('email', 'like', "%{$keyword}%");
                    });
                })
                ->editColumn('topup_sum', function($row) {
                    if ($row->topup_sum <= 0) return '<span class="text-muted">£0.00</span>';
                    return '<a href="javascript:void(0)" class="view-details text-info text-decoration-none fw-bold hover-underline" data-type="topup" data-donor="'.$row->user_id.'" data-date="'.Carbon::parse($row->date_group)->format('Y-m-d').'">£' . number_format($row->topup_sum, 2) . '</a>';
                })
                ->editColumn('commission_sum', function($row) {
                    if ($row->commission_sum <= 0) return '<span class="text-muted">£0.00</span>';
                    return '£' . number_format($row->commission_sum, 2);
                })
                ->editColumn('total_sum', function($row) {
                    return '£' . number_format($row->total_sum, 2);
                })
                ->editColumn('balance_sum', function($row) {
                    $balance = $row->balance_sum;
                    $color = $balance < 0 ? 'text-danger' : 'text-success';
                    return '<span class="'.$color.' fw-bold">£' . number_format(abs($balance), 2) . '</span>';
                })
                ->editColumn('online_sum', function($row) {
                    if ($row->online_sum <= 0) return '<span class="text-muted">£0.00</span>';
                    return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="online" data-donor="'.$row->user_id.'" data-date="'.Carbon::parse($row->date_group)->format('Y-m-d').'">£' . number_format($row->online_sum, 2) . '</a>';
                })
                ->editColumn('standing_sum', function($row) {
                    if ($row->standing_sum <= 0) return '<span class="text-muted">£0.00</span>';
                    return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="standing" data-donor="'.$row->user_id.'" data-date="'.Carbon::parse($row->date_group)->format('Y-m-d').'">£' . number_format($row->standing_sum, 2) . '</a>';
                })
                ->editColumn('voucher_sum', function($row) {
                    if ($row->voucher_sum <= 0) return '<span class="text-muted">£0.00</span>';
                    return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="voucher" data-donor="'.$row->user_id.'" data-date="'.Carbon::parse($row->date_group)->format('Y-m-d').'">£' . number_format($row->voucher_sum, 2) . '</a>';
                })
                ->editColumn('campaign_sum', function($row) {
                    if ($row->campaign_sum <= 0) return '<span class="text-muted">£0.00</span>';
                    return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="campaign" data-donor="'.$row->user_id.'" data-date="'.Carbon::parse($row->date_group)->format('Y-m-d').'">£' . number_format($row->campaign_sum, 2) . '</a>';
                })
                ->editColumn('card_sum', function($row) {
                    if ($row->card_sum <= 0) return '<span class="text-muted">£0.00</span>';
                    return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="card" data-donor="'.$row->user_id.'" data-date="'.Carbon::parse($row->date_group)->format('Y-m-d').'">£' . number_format($row->card_sum, 2) . '</a>';
                })

                // --- NEW COLUMNS FORMATTING ---
                ->editColumn('book_order_sum', function($row) {
                    if ($row->book_order_sum <= 0) return '<span class="text-muted">£0.00</span>';
                    return '£' . number_format($row->book_order_sum, 2);
                })
                ->editColumn('delivery_cost_sum', function($row) {
                    if ($row->delivery_cost_sum <= 0) return '<span class="text-muted">£0.00</span>';
                    return '£' . number_format($row->delivery_cost_sum, 2);
                })
                ->editColumn('tdf_sum', function($row) {
                    if ($row->tdf_sum <= 0) return '<span class="text-muted">£0.00</span>';
                    return '£' . number_format($row->tdf_sum, 2);
                })
                ->editColumn('gift_aid_sum', function($row) {
                    if ($row->gift_aid_sum <= 0) return '<span class="text-muted">£0.00</span>';
                    return '<span class="text-success">£' . number_format($row->gift_aid_sum, 2) . '</span>';
                })
                // -------------------------------


                ->rawColumns([
                    'date_group', 'donor_name', 'topup_sum', 'commission_sum', 'total_sum', 'balance_sum', 'online_sum', 
                    'standing_sum', 'voucher_sum', 'campaign_sum', 'card_sum','book_order_sum', 'delivery_cost_sum', 'tdf_sum', 'gift_aid_sum'
                ])
                ->make(true);
        }

        // --- Summary Cards Data (Optimized Queries) ---
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();


        // 1. Today's Stats (Out = Donations, In = Topups/Commissions)
        $todayStats = Usertransaction::where('status', 1)
            ->whereDate('created_at', $today)
            ->selectRaw("
                SUM(CASE WHEN t_type = 'Out' THEN amount ELSE 0 END) as today_donation,
                SUM(CASE WHEN t_type = 'Out' AND donation_id IS NOT NULL THEN amount ELSE 0 END) as today_online,
                SUM(CASE WHEN t_type = 'Out' AND standing_donationdetails_id IS NOT NULL THEN amount ELSE 0 END) as today_standing,
                SUM(CASE WHEN t_type = 'Out' AND cheque_no IS NOT NULL THEN amount ELSE 0 END) as today_voucher,
                SUM(CASE WHEN t_type = 'Out' AND campaign_id IS NOT NULL THEN amount ELSE 0 END) as today_campaign,
                SUM(CASE WHEN t_type = 'Out' AND (onegiv_transaction_id IS NOT NULL OR title LIKE '%OneGiv Card%' OR source = 'OneGiv Card') THEN amount ELSE 0 END) as today_card,
                SUM(CASE WHEN t_type = 'In' THEN amount ELSE 0 END) as today_topup,
                SUM(CASE WHEN t_type = 'In' THEN commission ELSE 0 END) as today_commission
            ")->first();

        // 2. This Week's Stats
        $weekStats = Usertransaction::where('status', 1)
            ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->selectRaw("
                SUM(CASE WHEN t_type = 'Out' THEN amount ELSE 0 END) as week_donation,
                SUM(CASE WHEN t_type = 'Out' AND donation_id IS NOT NULL THEN amount ELSE 0 END) as week_online,
                SUM(CASE WHEN t_type = 'Out' AND standing_donationdetails_id IS NOT NULL THEN amount ELSE 0 END) as week_standing,
                SUM(CASE WHEN t_type = 'Out' AND cheque_no IS NOT NULL THEN amount ELSE 0 END) as week_voucher,
                SUM(CASE WHEN t_type = 'Out' AND campaign_id IS NOT NULL THEN amount ELSE 0 END) as week_campaign,
                SUM(CASE WHEN t_type = 'Out' AND (onegiv_transaction_id IS NOT NULL OR title LIKE '%OneGiv Card%' OR source = 'OneGiv Card') THEN amount ELSE 0 END) as week_card,
                SUM(CASE WHEN t_type = 'In' THEN amount ELSE 0 END) as week_topup,
                SUM(CASE WHEN t_type = 'In' THEN commission ELSE 0 END) as week_commission
            ")->first();

        // 3. All-Time Category Totals
        $allTimeStats = Usertransaction::where('status', 1)->where('t_type', 'Out')
            ->selectRaw("
                SUM(CASE WHEN donation_id IS NOT NULL THEN amount ELSE 0 END) as total_online,
                SUM(CASE WHEN standing_donationdetails_id IS NOT NULL THEN amount ELSE 0 END) as total_standing,
                SUM(CASE WHEN cheque_no IS NOT NULL THEN amount ELSE 0 END) as total_voucher,
                SUM(CASE WHEN campaign_id IS NOT NULL THEN amount ELSE 0 END) as total_campaign,
                SUM(CASE WHEN onegiv_transaction_id IS NOT NULL OR title LIKE '%OneGiv Card%' OR source = 'OneGiv Card' THEN amount ELSE 0 END) as total_card
            ")->first();

        // Assign to variables for easy use in view
        $data = [
            'todayDonation' => $todayStats->today_donation ?? 0,
            'todayOnline' => $todayStats->today_online ?? 0,
            'todayStanding' => $todayStats->today_standing ?? 0,
            'todayVoucher' => $todayStats->today_voucher ?? 0,
            'todayCampaign' => $todayStats->today_campaign ?? 0,
            'todayCard' => $todayStats->today_card ?? 0,
            'todayTopup' => $todayStats->today_topup ?? 0,
            'todayCommission' => $todayStats->today_commission ?? 0,

            'weekDonation' => $weekStats->week_donation ?? 0,
            'weekOnline' => $weekStats->week_online ?? 0,
            'weekStanding' => $weekStats->week_standing ?? 0,
            'weekVoucher' => $weekStats->week_voucher ?? 0,
            'weekCampaign' => $weekStats->week_campaign ?? 0,
            'weekCard' => $weekStats->week_card ?? 0,
            'weekTopup' => $weekStats->week_topup ?? 0,
            'weekCommission' => $weekStats->week_commission ?? 0,

            'totalOnline' => $allTimeStats->total_online ?? 0,
            'totalStanding' => $allTimeStats->total_standing ?? 0,
            'totalVoucher' => $allTimeStats->total_voucher ?? 0,
            'totalCampaign' => $allTimeStats->total_campaign ?? 0,
            'totalCard' => $allTimeStats->total_card ?? 0,
        ];

        return view('admin.developer.monitoring.auditDonors', $data);

    }

    /**
     * Get transaction details for a specific donor on a specific date
     */
    public function getDonorDayDetails(Request $request)
    {
        $date = $request->date;
        $userId = $request->donor_id;

        $query = Usertransaction::with('charity')
            ->where('status', 1)
            ->where('user_id', $userId)
            ->where('business_date', $date);

        if ($request->type == 'online') $query->whereNotNull('donation_id');
        if ($request->type == 'standing') $query->whereNotNull('standing_donationdetails_id');
        if ($request->type == 'voucher') $query->whereNotNull('cheque_no');
        if ($request->type == 'campaign') $query->whereNotNull('campaign_id');
                if ($request->type == 'card') {
            $query->where(function($q) {
                $q->whereNotNull('onegiv_transaction_id')
                  ->orWhere('title', 'LIKE', '%Tevini Card%')
                  ->orWhere('source', 'Tevini Card');
            });
        }
        
        // Added condition for Topup details
        if ($request->type == 'topup') $query->where('t_type', 'In');

        $data = $query->get();

        return response()->json($data->map(function($item) {
            return [
                'charity' => $item->charity->name ?? 'N/A',
                'amount'  => '£' . number_format($item->amount, 2),
                'ref'     => $item->onegiv_transaction_id ?? $item->cheque_no ?? $item->t_id,
                'date'    => $item->created_at->format('d/m/Y H:i')
            ];
        }));
    }









    public function auditBalances(Request $request)
    {
        
        if ($request->ajax()) {
            $filterType = $request->get('filter_type', 'monthly');
            $fromDate = $request->get('fromDate');
            $toDate = $request->get('toDate');
            $today = Carbon::today();

            // Paid Subquery (Using new business_date column)
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

            // Main Query
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
                ->with('charity');

            // Apply Date Filters
            if ($filterType == 'daily') {
                $query->whereDate('usertransactions.business_date', $today);
            } elseif ($filterType == 'weekly') {
                $query->whereBetween('usertransactions.business_date', [$today->copy()->startOfWeek(), $today->copy()->endOfWeek()]);
            } elseif ($filterType == 'monthly') {
                $query->whereMonth('usertransactions.business_date', $today->month)->whereYear('usertransactions.business_date', $today->year);
            } elseif ($filterType == 'last7') {
                $query->whereBetween('usertransactions.business_date', [$today->copy()->subDays(6), $today]);
            } elseif ($filterType == 'custom' && $fromDate && $toDate) {
                $query->whereBetween('usertransactions.business_date', [$fromDate, $toDate]);
            } else {
                // 'all' - default range
                $startDate = $fromDate ?? '2026-02-09';
                $endDate = $toDate ?? Carbon::yesterday()->toDateString();
                $query->whereBetween('usertransactions.business_date', [$startDate, $endDate]);
            }
            
            // Only apply these filters when NOT coming from reconciliation (custom date from URL)
            $isFromReconciliation = request()->has('fromDate') && request()->has('toDate');
            
            if (!$isFromReconciliation) {
                $query->where('usertransactions.business_date', '>', '2026-02-07');
                $query->having('payment_status', '=', 0);
                $query->whereHas('charity', function($q) {
                    $q->where('auto_payment', 1);
                });
            }
            
            $query->where('usertransactions.business_date', '>', '2026-02-07');
            
            // Filter unpaid and auto_payment enabled
            $query->having('payment_status', '=', 0);
            $query->whereHas('charity', function($q) {
                $q->where('auto_payment', 1);
            });

            return DataTables::of($query)
                ->addColumn('date_group', fn($row) => '<span data-raw="'.$row->date_group.'">'.Carbon::parse($row->date_group)->format('d/m/Y').'</span>')
                ->addColumn('charity_name', function ($row) {
                    $charity = $row->charity;
                    $name = $charity->name ?? 'N/A';
                    $balance = $charity->balance ?? '0';
                    $style = ($charity && $charity->auto_payment == 0) ? 'color: #dc3545;' : 'color: #28a745;';
                    return '<span style="'.$style.' font-weight: bold;">'.$name.' ('.$balance.')</span>';
                })
                ->filterColumn('charity_name', function($query, $keyword) {
                    $query->whereHas('charity', function($q) use ($keyword) {
                        $q->where('name', 'like', "%{$keyword}%");
                    });
                })
                ->addColumn('balance', function ($row) {
                    $total = $row->online_sum + $row->standing_sum + $row->voucher_sum + $row->campaign_sum + $row->card_sum;
                    return '£' . number_format($total - $row->paid_sum, 2);
                })
                ->addColumn('action', function ($row) {
                    $total = $row->online_sum + $row->standing_sum + $row->voucher_sum + $row->campaign_sum + $row->card_sum;
                    $isChecked = ($row->payment_status == 1) ? 'checked' : '';
                    return '';
                })
                ->editColumn('paid_sum', function($row) {
                    if ($row->paid_sum <= 0) return '<span class="text-muted">£0.00</span>';
                    return '<a href="javascript:void(0)" class="view-details text-success text-decoration-none fw-bold" 
                            data-type="paid" data-charity="'.$row->charity_id.'" data-date="'.Carbon::parse($row->date_group)->format('Y-m-d').'">
                            £' . number_format($row->paid_sum, 2) . '</a>';
                })
                ->editColumn('online_sum', function($row) {
                    if ($row->online_sum <= 0) return '<span class="text-muted">£0.00</span>';
                    return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="online" data-charity="'.$row->charity_id.'" data-date="'.Carbon::parse($row->date_group)->format('Y-m-d').'">£' . number_format($row->online_sum, 2) . '</a>';
                })
                ->editColumn('standing_sum', function($row) {
                    if ($row->standing_sum <= 0) return '<span class="text-muted">£0.00</span>';
                    return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="standing" data-charity="'.$row->charity_id.'" data-date="'.Carbon::parse($row->date_group)->format('Y-m-d').'">£' . number_format($row->standing_sum, 2) . '</a>';
                })
                ->editColumn('voucher_sum', function($row) {
                    if ($row->voucher_sum <= 0) return '<span class="text-muted">£0.00</span>';
                    return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="voucher" data-charity="'.$row->charity_id.'" data-date="'.Carbon::parse($row->date_group)->format('Y-m-d').'">£' . number_format($row->voucher_sum, 2) . '</a>';
                })
                ->editColumn('campaign_sum', function($row) {
                    if ($row->campaign_sum <= 0) return '<span class="text-muted">£0.00</span>';
                    return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="campaign" data-charity="'.$row->charity_id.'" data-date="'.Carbon::parse($row->date_group)->format('Y-m-d').'">£' . number_format($row->campaign_sum, 2) . '</a>';
                })
                ->editColumn('card_sum', function($row) {
                    if ($row->card_sum <= 0) return '<span class="text-muted">£0.00</span>';
                    return '<a href="javascript:void(0)" class="view-details text-primary text-decoration-none fw-bold hover-underline" data-type="card" data-charity="'.$row->charity_id.'" data-date="'.Carbon::parse($row->date_group)->format('Y-m-d').'">£' . number_format($row->card_sum, 2) . '</a>';
                })
                ->rawColumns(['date_group', 'online_sum', 'standing_sum', 'voucher_sum', 'campaign_sum', 'card_sum', 'paid_sum', 'charity_name', 'action'])
                ->make(true);
        }



        // --- Summary Cards Data ---
        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $todayStats = Usertransaction::where('status', 1)->whereNotNull('charity_id')->whereDate('created_at', $today)
            ->selectRaw("
                SUM(CASE WHEN t_type = 'Out' THEN amount ELSE 0 END) as today_donation,
                SUM(CASE WHEN t_type = 'Out' AND donation_id IS NOT NULL THEN amount ELSE 0 END) as today_online,
                SUM(CASE WHEN t_type = 'Out' AND standing_donationdetails_id IS NOT NULL THEN amount ELSE 0 END) as today_standing,
                SUM(CASE WHEN t_type = 'Out' AND cheque_no IS NOT NULL THEN amount ELSE 0 END) as today_voucher,
                SUM(CASE WHEN t_type = 'Out' AND campaign_id IS NOT NULL THEN amount ELSE 0 END) as today_campaign,
                SUM(CASE WHEN t_type = 'Out' AND onegiv_transaction_id IS NOT NULL THEN amount ELSE 0 END) as today_card,
                SUM(CASE WHEN t_type = 'In' THEN amount ELSE 0 END) as today_topup
            ")->first();

        $weekStats = Usertransaction::where('status', 1)->whereNotNull('charity_id')->whereBetween('created_at', [$startOfWeek, $endOfWeek])
            ->selectRaw("
                SUM(CASE WHEN t_type = 'Out' THEN amount ELSE 0 END) as week_donation,
                SUM(CASE WHEN t_type = 'Out' AND donation_id IS NOT NULL THEN amount ELSE 0 END) as week_online,
                SUM(CASE WHEN t_type = 'Out' AND standing_donationdetails_id IS NOT NULL THEN amount ELSE 0 END) as week_standing,
                SUM(CASE WHEN t_type = 'Out' AND cheque_no IS NOT NULL THEN amount ELSE 0 END) as week_voucher,
                SUM(CASE WHEN t_type = 'Out' AND campaign_id IS NOT NULL THEN amount ELSE 0 END) as week_campaign,
                SUM(CASE WHEN t_type = 'Out' AND onegiv_transaction_id IS NOT NULL THEN amount ELSE 0 END) as week_card
            ")->first();

        $allTimeStats = Usertransaction::where('status', 1)->whereNotNull('charity_id')->where('t_type', 'Out')
            ->selectRaw("
                SUM(CASE WHEN donation_id IS NOT NULL THEN amount ELSE 0 END) as total_online,
                SUM(CASE WHEN standing_donationdetails_id IS NOT NULL THEN amount ELSE 0 END) as total_standing,
                SUM(CASE WHEN cheque_no IS NOT NULL THEN amount ELSE 0 END) as total_voucher,
                SUM(CASE WHEN campaign_id IS NOT NULL THEN amount ELSE 0 END) as total_campaign,
                SUM(CASE WHEN onegiv_transaction_id IS NOT NULL THEN amount ELSE 0 END) as total_card
            ")->first();

        $data = [
            'todayDonation' => $todayStats->today_donation ?? 0,
            'todayOnline' => $todayStats->today_online ?? 0,
            'todayStanding' => $todayStats->today_standing ?? 0,
            'todayVoucher' => $todayStats->today_voucher ?? 0,
            'todayCampaign' => $todayStats->today_campaign ?? 0,
            'todayCard' => $todayStats->today_card ?? 0,
            'todayTopup' => $todayStats->today_topup ?? 0,
            
            'weekDonation' => $weekStats->week_donation ?? 0,
            'weekOnline' => $weekStats->week_online ?? 0,
            'weekStanding' => $weekStats->week_standing ?? 0,
            'weekVoucher' => $weekStats->week_voucher ?? 0,
            'weekCampaign' => $weekStats->week_campaign ?? 0,
            'weekCard' => $weekStats->week_card ?? 0,

            'totalOnline' => $allTimeStats->total_online ?? 0,
            'totalStanding' => $allTimeStats->total_standing ?? 0,
            'totalVoucher' => $allTimeStats->total_voucher ?? 0,
            'totalCampaign' => $allTimeStats->total_campaign ?? 0,
            'totalCard' => $allTimeStats->total_card ?? 0,
        ];

        return view('admin.developer.monitoring.auditCharities', $data);
    }

    public function getDayDetails(Request $request)
    {
        // Updated to use business_date instead of time window
        $date = $request->date;

        if ($request->type == 'paid') {
            $data = Transaction::with('charity')
                ->where('charity_id', $request->charity_id)
                ->where('t_type', 'Out')
                ->where('business_date', $date)
                ->get();

            return response()->json($data->map(function($item) {
                $adjustBtn = '<button class="btn btn-sm btn-warning adjust-transaction" ' .
                 'data-tid="' . $item->id . '" ' .
                 'data-charity="' . $item->charity_id . '" ' .
                 'data-type="paid" ' .
                 'data-donor="' . ($item->user_id ?? '') . '">Adjust</button>';

                return [
                    'donor'  => $item->charity->name ?? 'N/A',
                    'amount' => '£' . number_format($item->amount, 2),
                    'ref'    => $item->t_id,
                    'status' => $item->status,
                    'date'   => $item->created_at->format('d/m/Y H:i:s'),
                    'adjustBtn'   => $adjustBtn
                ];
            }));
        }

        $query = Usertransaction::with('user')
            ->where('charity_id', $request->charity_id)
            ->where('business_date', $date);

        if ($request->type == 'online') $query->whereNotNull('donation_id');
        if ($request->type == 'standing') $query->whereNotNull('standing_donationdetails_id');
        if ($request->type == 'voucher') $query->whereNotNull('cheque_no');
        if ($request->type == 'campaign') $query->whereNotNull('campaign_id');
        if ($request->type == 'card') $query->whereNotNull('onegiv_transaction_id');

        $data = $query->get();

        return response()->json($data->map(function($item) {
            $adjustBtn = '<button class="btn btn-sm btn-warning adjust-transaction" ' .
                     'data-tid="' . $item->id . '" ' .
                     'data-charity="' . $item->charity_id . '" ' .
                     'data-type="receive" ' .
                     'data-donor="' . ($item->user_id ?? '') . '">Adjust</button>';

            return [
                'donor'  => ($item->user->name ?? 'N/A') . ' ' . ($item->user->surname ?? ''),
                'amount' => '£' . number_format($item->amount, 2),
                'ref'    => ($item->cheque_no ?? $item->t_id),
                'status' => $item->status,
                'date'   => $item->created_at->format('d/m/Y H:i'),
                'adjustBtn'   => $adjustBtn
            ];
        }));
    }

    public function adjustTransaction(Request $request)
    {
        Log::info('Adjustment Started', [
            'transaction_id' => $request->transaction_id,
            'table' => $request->table_name,
            'type' => $request->type,
            'tran_type' => $request->tran_type,
            'amount' => $request->amount
        ]);

        $request->validate([
            'transaction_id' => 'required',
            'charity_id'     => 'required',
            'amount'         => 'required|numeric|min:0.01',
            'type'           => 'required|in:increment,decrement',
            'table_name'     => 'required|in:Charity,Transaction'
        ]);

        try {
            return DB::transaction(function () use ($request) {
                if ($request->tran_type === 'paid') {
                    $transaction = Transaction::findOrFail($request->transaction_id);
                } else {
                    $transaction = Usertransaction::findOrFail($request->transaction_id);
                }
                
                $charity = Charity::findOrFail($request->charity_id);

                AuditTran::create([
                    'user_id'    => $transaction->user_id,
                    'charity_id' => $transaction->charity_id,
                    'date'       => $transaction->created_at->format('Y-m-d'),
                    'amount'     => $transaction->amount,
                    'tran_data'  => json_encode([
                        'adjustment_target' => $request->table_name,
                        'transaction' => $transaction->toArray(),
                        'charity_snapshot' => ['balance' => $charity->balance]
                    ]),
                    'status'     => $transaction->status,
                    'updated_by' => auth()->user()->name,
                    'created_by' => "Adj: $request->table_name ($request->type, $request->transaction_id)"
                ]);

                if ($request->table_name === 'Charity') {
                    if ($request->type === 'increment') {
                        $charity->balance += $request->amount;
                    } else {
                        $charity->balance -= $request->amount;
                    }
                    $charity->save();
                }

                if ($request->table_name === 'Transaction') {
                    if ($request->type === 'increment') {
                        $transaction->amount += $request->amount;
                    } else {
                        $transaction->amount -= $request->amount;
                    }
                    $transaction->save();
                }

                return response()->json([
                    'success' => true,
                    'message' => "Successfully updated $request->table_name. Charity Balance: £" . number_format($charity->balance, 2)
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Adjustment failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function changeRealDonor(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $wrongUserId = $request->user_id;
                $realUserId  = $request->real_donor_id;
                $authId      = $request->authorization_id;
                $usertranid  = $request->usertranid;

                $wrongUser = User::findOrFail($wrongUserId);
                $realUser  = User::findOrFail($realUserId);

                $transaction = Usertransaction::where('t_id', 'LIKE', '%' . $usertranid)->first();

                if (!$transaction) {
                    throw new \Exception("Transaction not found");
                }

                $amount = $transaction->amount;

                $wrongUser->increment('balance', $amount);
                $realUser->decrement('balance', $amount);

                $transaction->update(['user_id' => $realUserId]);

                Transaction::where('t_id', $transaction->t_id)
                    ->update(['user_id' => $realUserId]);

                Authorisation::where('Utid', $authId)
                    ->update(['user_id' => $realUserId]);
            });

            return back()->with('success', 'Real donor updated successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }



}
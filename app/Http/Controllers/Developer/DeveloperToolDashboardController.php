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
     */
    public function dashboard()
    {
        $totalTransactions = Transaction::count();
        $totalCharities = Charity::count();
        $totalDonors = User::where('is_type', 'user')->count();

        // User Transaction Stats (This Month & Last 30 Days)
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

        $todaysOnlineDonation = Donation::whereDate('created_at', Carbon::today())->sum('amount');
        $weeklyOnlineDonation = Donation::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('amount');
        $todaysStandingDonation = StandingDonation::whereDate('created_at', Carbon::today())->sum('amount');
        $weeklyStandingDonation = StandingDonation::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('amount');

        $pendingStandingDonations = Usertransaction::where('t_type', 'Standing')
            ->where('status', 'Pending')
            ->count();

        $cutoffHistory = DB::table('cutoff_histories')
            ->orderBy('effective_date', 'desc')
            ->limit(5)
            ->get();

        // Fetch all records for DataTables (removed limit)
        $stripeSyncIssues = Donation::whereNotNull('stripe_payment_id')
            ->where('status', 0)
            ->with(['user', 'charity'])
            ->latest()
            ->get();

        $pendingDonations = Donation::where('status', 0)
            ->with(['user', 'charity'])
            ->latest()
            ->get();

        $negativeBalanceUsers = User::where('balance', '<', 0)
            ->latest()
            ->get();

        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        $intervalMap = [
            'Monthly'   => 1, 'Quarterly' => 3, 'Yearly'    => 12,
            'Annually'  => 12, 'Weekly'    => 0, 'Bi-Weekly' => 0,
        ];

                $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        
        // Next 7 days range
        $next7DaysStart = Carbon::tomorrow()->startOfDay();
        $next7DaysEnd = Carbon::tomorrow()->addDays(6)->endOfDay();

        $intervalMap = [
            'Monthly'   => 1, 'Quarterly' => 3, 'Yearly'    => 12,
            'Annually'  => 12, 'Weekly'    => 0, 'Bi-Weekly' => 0,
        ];

        $tomorrowStandingAmount = 0;
        $next7DaysStandingAmount = 0; // NEW VARIABLE
        $stuckStandingDonations = collect();
        $failingStandingDonations = collect();

        $activeOrders = StandingDonation::where('status', 1)
            ->with(['user', 'charity', 'standingdonationDetail' => function($q) {
                $q->latest('id')->limit(1);
            }])
            ->get();

        foreach ($activeOrders as $order) {
            $detail = $order->standingdonationDetail->first();
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

            // Match cron job fallback: Weekly/Bi-Weekly acts as Monthly
            if ($intervalMonths === 0) {
                $intervalMonths = 1;
            }

            $instalment_date_carbon = $start_date_carbon->copy()->addMonths($intervalMonths);
            $instalment_date = $instalment_date_carbon->format('Y-m-d');

            // Tomorrow's calculation
            if ($instalment_date == $tomorrow) {
                $tomorrowStandingAmount += $order->amount;
            }

            // Next 7 days calculation
            if ($instalment_date_carbon->between($next7DaysStart, $next7DaysEnd)) {
                $next7DaysStandingAmount += $order->amount;
            }

            if ($instalment_date < Carbon::today()->format('Y-m-d') && $order->payment_made == 0 && $order->payments == 1) {
                $stuckStandingDonations->push($order);
            } elseif ($instalment_date < Carbon::today()->format('Y-m-d') && $order->payments == 2) {
                $stuckStandingDonations->push($order);
            }

            if ($order->user && $order->user->getAvailableLimit() < $order->amount) {
                $failingStandingDonations->push($order);
            }
        }

        // Add next7DaysStandingAmount to compact array
        return view('admin.developer.dashboard', compact(
            'totalTransactions', 'totalCharities', 'totalDonors',
            'successfulThisMonth', 'unsuccessfulThisMonth', 'successfulLast30Days', 'unsuccessfulLast30Days',
            'todaysOnlineDonation', 'weeklyOnlineDonation', 'todaysStandingDonation', 'weeklyStandingDonation',
            'pendingStandingDonations', 'cutoffHistory', 'stripeSyncIssues', 'pendingDonations', 
            'negativeBalanceUsers', 'tomorrowStandingAmount', 'next7DaysStandingAmount', 'stuckStandingDonations', 'failingStandingDonations'
        ));
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
     */
    public function onlineDonations()
    {
        $donations = Donation::with(['user', 'charity'])->latest()->paginate(25);
        $stripeSyncIssues = Donation::whereNotNull('stripe_payment_id')->where('status', 0)->with(['user', 'charity'])->latest()->paginate(10);
        
        return view('admin.developer.monitoring.online_donations', compact('donations', 'stripeSyncIssues'));
    }

    /**
     * Standing Donation Monitoring Page
     */
        /**
     * Standing Donation Monitoring Page
     */
    public function standingDonations()
    {
        // 1. Stat Cards Data
        $totalStanding = StandingDonation::count();
        $activeStanding = StandingDonation::where('status', 1)->count();
        $inactiveStanding = StandingDonation::where('status', 0)->count();
        $completeStanding = StandingDonation::where('status', 2)->count();

        // 2. All Standing Donations (Removed pagination for DataTables)
        $standingDonations = StandingDonation::with(['user', 'charity'])->latest()->get();

        // 3. Today's Standing Order Transactions
        // From your cron job, we know 'standing_donationdetails_id' is set for standing donations
        $todaysStandingTransactions = Usertransaction::whereNotNull('standing_donationdetails_id')
            ->whereDate('created_at', Carbon::today())
            ->with(['user', 'charity'])
            ->latest()
            ->get();

        // 4. Upcoming 7 Days Standing Donations (Expected)
        $intervalMap = [
            'Monthly'   => 1, 'Quarterly' => 3, 'Yearly'    => 12,
            'Annually'  => 12, 'Weekly'    => 0, 'Bi-Weekly' => 0,
        ];

        $next7DaysStart = Carbon::tomorrow()->startOfDay();
        $next7DaysEnd = Carbon::tomorrow()->addDays(6)->endOfDay();

        $upcoming7DaysDonations = collect();

        $activeOrders = StandingDonation::where('status', 1)
            ->with(['user', 'charity', 'standingdonationDetail' => function($q) {
                $q->latest('id')->limit(1);
            }])
            ->get();

        foreach ($activeOrders as $order) {
            $detail = $order->standingdonationDetail->first();
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

            // Match cron job fallback: Weekly/Bi-Weekly acts as Monthly
            if ($intervalMonths === 0) {
                $intervalMonths = 1;
            }

            $instalment_date_carbon = $start_date_carbon->copy()->addMonths($intervalMonths);

            if ($instalment_date_carbon->between($next7DaysStart, $next7DaysEnd)) {
                $order->expected_date = $instalment_date_carbon->format('d M, Y');
                $upcoming7DaysDonations->push($order);
            }
        }

        // Sort by expected date
        $upcoming7DaysDonations = $upcoming7DaysDonations->sortBy('expected_date');

        return view('admin.developer.monitoring.standing_donations', compact(
            'totalStanding', 'activeStanding', 'inactiveStanding', 'completeStanding',
            'standingDonations', 'todaysStandingTransactions', 'upcoming7DaysDonations'
        ));
    }

    /**
     * Donor Monitoring Page
     */
    public function donorMonitoring()
    {
        $donors = User::where('is_type', 'user')->latest()->paginate(25);
        $negativeBalanceUsers = User::where('balance', '<', 0)->latest()->paginate(10);
        
        return view('admin.developer.monitoring.donors', compact('donors', 'negativeBalanceUsers'));
    }

    /**
     * Charity Monitoring Page
     */
    public function charityMonitoring()
    {
        $charities = Charity::latest()->paginate(25);
        return view('admin.developer.monitoring.charities', compact('charities'));
    }

    /**
     * Process Voucher Monitoring Page - Grouped by Date
     */
    public function processVoucherMonitoring(Request $request)
    {
        if ($request->ajax()) {
            $query = Provoucher::query()
                ->selectRaw("
                    DATE(created_at) as date_group,
                    COUNT(*) as total_processed,
                    SUM(CASE WHEN status = 1 AND waiting = 'No' AND expired = 'No' THEN 1 ELSE 0 END) as complete_count,
                    SUM(CASE WHEN status = 0 AND waiting = 'No' AND expired = 'No' THEN 1 ELSE 0 END) as pending_count,
                    SUM(CASE WHEN waiting = 'Yes' THEN 1 ELSE 0 END) as waiting_count,
                    SUM(CASE WHEN expired = 'Yes' THEN 1 ELSE 0 END) as expired_count,
                    SUM(amount) as total_amount
                ")
                ->groupBy('date_group')
                ->orderBy('date_group', 'DESC');

            return DataTables::of($query)
                ->addColumn('date', function($row) {
                    return '<span data-raw="'.$row->date_group.'">'.\Carbon\Carbon::parse($row->date_group)->format('d/m/Y').'</span>';
                })
                ->addColumn('total_processed', function($row) {
                    return '<a href="javascript:void(0)" class="fw-bold text-primary view-pv-details" data-type="all" data-date="'.$row->date_group.'">'.$row->total_processed.'</a>';
                })
                ->addColumn('complete_count', function($row) {
                    $count = $row->complete_count;
                    return $count > 0 ? '<a href="javascript:void(0)" class="text-success fw-bold view-pv-details" data-type="complete" data-date="'.$row->date_group.'">'.$count.'</a>' : '<span class="text-muted">0</span>';
                })
                ->addColumn('pending_count', function($row) {
                    $count = $row->pending_count;
                    return $count > 0 ? '<a href="javascript:void(0)" class="text-warning fw-bold view-pv-details" data-type="pending" data-date="'.$row->date_group.'">'.$count.'</a>' : '<span class="text-muted">0</span>';
                })
                ->addColumn('waiting_count', function($row) {
                    $count = $row->waiting_count;
                    return $count > 0 ? '<a href="javascript:void(0)" class="text-info fw-bold view-pv-details" data-type="waiting" data-date="'.$row->date_group.'">'.$count.'</a>' : '<span class="text-muted">0</span>';
                })
                ->addColumn('expired_count', function($row) {
                    $count = $row->expired_count;
                    return $count > 0 ? '<a href="javascript:void(0)" class="text-danger fw-bold view-pv-details" data-type="expired" data-date="'.$row->date_group.'">'.$count.'</a>' : '<span class="text-muted">0</span>';
                })
                ->editColumn('total_amount', function($row) {
                    return '£' . number_format($row->total_amount, 2);
                })
                ->rawColumns(['date', 'total_processed', 'complete_count', 'pending_count', 'waiting_count', 'expired_count'])
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
            $query->where('waiting', 'Yes');
        } elseif ($type == 'expired') {
            $query->where('expired', 'Yes');
        }

        $data = $query->get();

        return response()->json($data->map(function($item) {
            return [
                'charity' => $item->charity->name ?? 'N/A',
                'donor'   => trim(($item->user->name ?? '') . ' ' . ($item->user->surname ?? '')),
                'voucher' => $item->cheque_no,
                'amount'  => '£' . number_format($item->amount, 2),
                'status'  => $item->status == 1 ? '<span class="badge bg-success">Complete</span>' : '<span class="badge bg-warning text-dark">Pending</span>',
            ];
        }));
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
                    DB::raw("SUM(CASE WHEN onegiv_transaction_id IS NOT NULL THEN amount ELSE 0 END) as card_sum"),
                    
                    // New Columns: Topup, Commission, Total, Balance
                    DB::raw("SUM(CASE WHEN t_type = 'In' THEN amount ELSE 0 END) as topup_sum"),
                    DB::raw("SUM(CASE WHEN t_type = 'In' THEN commission ELSE 0 END) as commission_sum"), // Added Commission
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
                ->rawColumns([
                    'date_group', 'donor_name', 'topup_sum', 'commission_sum', 'total_sum', 'balance_sum', 'online_sum', 
                    'standing_sum', 'voucher_sum', 'campaign_sum', 'card_sum'
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
                SUM(CASE WHEN t_type = 'Out' AND onegiv_transaction_id IS NOT NULL THEN amount ELSE 0 END) as today_card,
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
                SUM(CASE WHEN t_type = 'Out' AND onegiv_transaction_id IS NOT NULL THEN amount ELSE 0 END) as week_card,
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
                SUM(CASE WHEN onegiv_transaction_id IS NOT NULL THEN amount ELSE 0 END) as total_card
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
        if ($request->type == 'card') $query->whereNotNull('onegiv_transaction_id');
        
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
            $fromDate = $request->get('fromDate');
            $toDate = $request->get('toDate');

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
                    DB::raw("SUM(CASE WHEN onegiv_transaction_id IS NOT NULL THEN amount ELSE 0 END) as card_sum"), // Added Card Sum
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

            // Default Date Range if not provided
            $startDate = $fromDate ?? '2026-02-09';
            $endDate = $toDate ?? Carbon::yesterday()->toDateString();
            
            $query->whereBetween('usertransactions.business_date', [$startDate, $endDate]);
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
                    return '<div class="form-check form-switch d-flex justify-content-center">
                                <input class="form-check-input status-switch" type="checkbox" role="switch" '.$isChecked.' 
                                data-charity-id="'.$row->charity_id.'" data-date="'.$row->date_group.'" data-total="'.$total.'">
                            </div>';
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
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
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
     * Voucher Book Monitoring Page
     */
    public function voucherBookMonitoring()
    {
        $voucherBooks = \App\Models\Order::whereNotNull('voucher_book_id')->latest()->paginate(25); 
        return view('admin.developer.monitoring.voucher_books', compact('voucherBooks'));
    }

    /**
     * Voucher Monitoring Page
     */
    public function voucherMonitoring()
    {
        $vouchers = \App\Models\Provoucher::latest()->paginate(25); 
        return view('admin.developer.monitoring.vouchers', compact('vouchers'));
    }
}
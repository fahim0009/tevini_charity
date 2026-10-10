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
     * Developer Dashboard
     */
    public function dashboard()
    {
        // Quick stats for developer overview
        $totalTransactions = Transaction::count();
        $totalUserTransactions = Usertransaction::count();
        $totalCharities = Charity::count();
        $totalDonors = User::where('is_type', 'user')->count();

        // Recent transactions for monitoring
        $recentTransactions = Transaction::with(['user', 'charity'])
            ->orderBy('id', 'desc')
            ->limit(10)
            ->get();

        // Last 7 days transaction count (for chart)
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $last7Days[] = [
                'date'  => $date->format('Y-m-d'),
                'label' => $date->format('d M'),
                'count' => Transaction::whereDate('created_at', $date)->count(),
            ];
        }

        // Failed / pending transactions to inspect
        $pendingStandingDonations = Usertransaction::where('t_type', 'Standing')
            ->where('status', 'Pending')
            ->count();

        // Cut-off info
        $cutoffHistory = DB::table('cutoff_histories')
            ->orderBy('effective_date', 'desc')
            ->limit(5)
            ->get();

        /*
        |----------------------------------------------------------------------
        | Donation & Standing Donation Stats Cards
        |----------------------------------------------------------------------
        */
        $todaysOnlineDonation = Donation::whereDate('created_at', Carbon::today())->sum('amount');
        $weeklyOnlineDonation = Donation::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('amount');
        $todaysStandingDonation = StandingDonation::whereDate('created_at', Carbon::today())->sum('amount');
        $weeklyStandingDonation = StandingDonation::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('amount');

        /*
        |----------------------------------------------------------------------
        | Online donation and transaction monitoring for developers
        |----------------------------------------------------------------------
        */

        // 1. Stripe Payment Sync Issues
        $stripeSyncIssues = Donation::whereNotNull('stripe_payment_id')
            ->where('status', 0)
            ->with(['user', 'charity'])
            ->latest()
            ->limit(5)
            ->get();

        // 2. Pending Donations
        $pendingDonations = Donation::where('status', 0)
            ->with(['user', 'charity'])
            ->latest()
            ->limit(5)
            ->get();

        // 3. Negative Balance Users
        $negativeBalanceUsers = User::where('balance', '<', 0)
            ->latest()
            ->limit(5)
            ->get();

        /*
        |----------------------------------------------------------------------
        | Calculate Next Instalment Dates & Fetch Weekly Data
        |----------------------------------------------------------------------
        */
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        $nextWeekStart = Carbon::now()->addWeek()->startOfWeek()->format('Y-m-d');
        $nextWeekEnd = Carbon::now()->addWeek()->endOfWeek()->format('Y-m-d');
        
        $intervalMap = [
            'Monthly'   => 1,
            'Quarterly' => 3,
            'Yearly'    => 12,
            'Annually'  => 12,
            'Weekly'    => 0,
            'Bi-Weekly' => 0,
        ];

        $tomorrowStandingAmount = 0;
        $nextWeekStandingDonations = collect();
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

            $instalment_date_carbon = $start_date_carbon->copy()->addMonths($intervalMonths);
            $instalment_date = $instalment_date_carbon->format('Y-m-d');

            // 4. Tomorrow's Standing Donation Amount
            if ($instalment_date == $tomorrow) {
                $tomorrowStandingAmount += $order->amount;
            }

            // 5. Next Week's Scheduled Standing Donations
            if ($instalment_date >= $nextWeekStart && $instalment_date <= $nextWeekEnd) {
                $nextWeekStandingDonations->push($order);
            }

            // 6. Stuck Standing Donations (Start date passed, payment not made)
            if ($instalment_date < Carbon::today()->format('Y-m-d') && $order->payment_made == 0 && $order->payments == 1) {
                $stuckStandingDonations->push($order);
            } elseif ($instalment_date < Carbon::today()->format('Y-m-d') && $order->payments == 2) {
                // Continuous payments that are overdue
                $stuckStandingDonations->push($order);
            }

            // 7. Will Fail Due to Insufficient Balance
            if ($order->user && $order->user->getAvailableLimit() < $order->amount) {
                $failingStandingDonations->push($order);
            }
        }

        // 8. This Week's Online Donations Data Table
        $weeklyDonationsData = Donation::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->with(['user', 'charity'])
            ->latest()
            ->limit(10)
            ->get();

        // 9. This Week's Created Standing Donations Data Table
        $weeklyStandingData = StandingDonation::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->with(['user', 'charity'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.developer.dashboard', compact(
            'totalTransactions',
            'totalUserTransactions',
            'totalCharities',
            'totalDonors',
            'recentTransactions',
            'last7Days',
            'pendingStandingDonations',
            'cutoffHistory',
            'stripeSyncIssues',
            'pendingDonations',
            'negativeBalanceUsers',
            'todaysOnlineDonation',
            'weeklyOnlineDonation',
            'todaysStandingDonation',
            'weeklyStandingDonation',
            'tomorrowStandingAmount',
            'nextWeekStandingDonations',
            'stuckStandingDonations',
            'failingStandingDonations',
            'weeklyDonationsData',
            'weeklyStandingData'
        ));
    }
}
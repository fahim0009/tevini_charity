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
        $totalUserTransactions = Usertransaction::count();
        $totalCharities = Charity::count();
        $totalDonors = User::where('is_type', 'user')->count();

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

        $stripeSyncIssues = Donation::whereNotNull('stripe_payment_id')
            ->where('status', 0)
            ->with(['user', 'charity'])
            ->latest()
            ->limit(5)
            ->get();

        $pendingDonations = Donation::where('status', 0)
            ->with(['user', 'charity'])
            ->latest()
            ->limit(5)
            ->get();

        $negativeBalanceUsers = User::where('balance', '<', 0)
            ->latest()
            ->limit(5)
            ->get();

        $tomorrow = Carbon::tomorrow()->format('Y-m-d');
        $intervalMap = [
            'Monthly'   => 1, 'Quarterly' => 3, 'Yearly'    => 12,
            'Annually'  => 12, 'Weekly'    => 0, 'Bi-Weekly' => 0,
        ];

        $tomorrowStandingAmount = 0;
        $stuckStandingDonations = collect();
        $failingStandingDonations = collect();

        $activeOrders = StandingDonation::where('status', 1)
            ->with(['user', 'charity', 'standingdonationDetail' => function($q) {
                $q->latest('id')->limit(1);
            }])
            ->limit(100) // Limit to prevent slow load
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

            if ($instalment_date == $tomorrow) {
                $tomorrowStandingAmount += $order->amount;
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

        return view('admin.developer.dashboard', compact(
            'totalTransactions', 'totalUserTransactions', 'totalCharities', 'totalDonors',
            'todaysOnlineDonation', 'weeklyOnlineDonation', 'todaysStandingDonation', 'weeklyStandingDonation',
            'pendingStandingDonations', 'cutoffHistory', 'stripeSyncIssues', 'pendingDonations', 
            'negativeBalanceUsers', 'tomorrowStandingAmount', 'stuckStandingDonations', 'failingStandingDonations'
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
    public function standingDonations()
    {
        $standingDonations = StandingDonation::with(['user', 'charity'])->latest()->paginate(25);
        $stuckStandingDonations = StandingDonation::where('status', 1)->where('payment_made', 0)->whereDate('starting', '<', Carbon::today())->with(['user', 'charity'])->latest()->paginate(10);
        
        return view('admin.developer.monitoring.standing_donations', compact('standingDonations', 'stuckStandingDonations'));
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
        // Assuming you have an Order or VoucherBook model. 
        // Adjust the model according to your project structure.
        $voucherBooks = \App\Models\Order::whereNotNull('voucher_book_id')->latest()->paginate(25); 
        return view('admin.developer.monitoring.voucher_books', compact('voucherBooks'));
    }

    /**
     * Voucher Monitoring Page
     */
    public function voucherMonitoring()
    {
        // Adjust model as needed
        $vouchers = \App\Models\Provoucher::latest()->paginate(25); 
        return view('admin.developer.monitoring.vouchers', compact('vouchers'));
    }
}
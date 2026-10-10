<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usertransaction;
use App\Models\Transaction;
use App\Models\Charity;
use App\Models\User;
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

        return view('admin.developer.dashboard', compact(
            'totalTransactions',
            'totalUserTransactions',
            'totalCharities',
            'totalDonors',
            'recentTransactions',
            'last7Days',
            'pendingStandingDonations',
            'cutoffHistory'
        ));
    }
}

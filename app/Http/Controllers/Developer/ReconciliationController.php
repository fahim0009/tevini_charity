<?php

namespace App\Http\Controllers\Developer;

use App\Http\Controllers\Controller;
use App\Models\Usertransaction;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReconciliationController extends Controller
{
    /**
     * Daily Balance Reconciliation Page
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Step 1: Get daily sums from Usertransactions (only successful)
            $dailyUserTx = Usertransaction::where('status', 1)
                ->selectRaw("
                    DATE(created_at) as date,
                    SUM(CASE WHEN t_type = 'In' THEN CAST(amount AS DECIMAL(10,2)) ELSE 0 END) as daily_in,
                    SUM(CASE WHEN t_type = 'Out' THEN CAST(amount AS DECIMAL(10,2)) ELSE 0 END) as daily_out,
                    SUM(CASE WHEN t_type = 'In' THEN commission ELSE 0 END) as daily_commission
                ")
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date')
                ->get();

            // Step 2: Get daily sums from Transactions (AutoCharityPayment Out)
            $dailyCharityTx = Transaction::where('t_type', 'Out')
                ->where('status', 1)
                ->selectRaw("DATE(created_at) as date, SUM(amount) as daily_charity_out")
                ->groupBy(DB::raw('DATE(created_at)'))
                ->orderBy('date')
                ->get();

            // Step 3: Convert to lookup maps
            $userTxMap = [];
            foreach ($dailyUserTx as $tx) {
                $userTxMap[$tx->date] = $tx;
            }

            $charityTxMap = [];
            foreach ($dailyCharityTx as $tx) {
                $charityTxMap[$tx->date] = $tx;
            }

            // Step 3.5: Get all saved actual balances
            $actualBalances = DB::table('reconciliation_balances')
                ->pluck('actual_balance', 'date')
                ->toArray();

            // Step 4: Merge all unique dates
            $allDates = array_unique(array_merge(array_keys($userTxMap), array_keys($charityTxMap)));
            sort($allDates);

            // Step 5: Calculate cumulative sums (bottom to top)
            $cumIn = 0;
            $cumOut = 0;
            $cumCommission = 0;
            $cumCharityOut = 0;

            $results = [];
            foreach ($allDates as $date) {
                $userTx = $userTxMap[$date] ?? null;
                $charityTx = $charityTxMap[$date] ?? null;

                $dailyIn = (float)($userTx->daily_in ?? 0);
                $dailyOut = (float)($userTx->daily_out ?? 0);
                $dailyCommission = (float)($userTx->daily_commission ?? 0);
                $dailyCharityOut = (float)($charityTx->daily_charity_out ?? 0);

                $cumIn += $dailyIn;
                $cumOut += $dailyOut;
                $cumCommission += $dailyCommission;
                $cumCharityOut += $dailyCharityOut;

                // Charity In = Donor Out (same transaction)
                $dailyCharityIn = $dailyOut;

                $results[] = [
                    'date' => $date,
                    'daily_donor_in' => $dailyIn,
                    'daily_donor_out' => $dailyOut,
                    'donor_balance' => $cumIn - $cumOut,
                    'daily_charity_in' => $dailyCharityIn,
                    'daily_charity_out' => $dailyCharityOut,
                    'charity_balance' => $cumOut - $cumCharityOut,
                    'commission' => $cumCommission,
                    'actual_balance' => $actualBalances[$date] ?? null,
                ];
            }

            // Reverse: newest at top
            $results = array_reverse($results);

            // Step 6: DataTables pagination
            $start = (int)$request->get('start', 0);
            $length = (int)$request->get('length', 25);
            $search = $request->get('search')['value'] ?? '';
            $draw = (int)$request->get('draw', 1);

            // Filter if searching
            if (!empty($search)) {
                $results = array_filter($results, function($item) use ($search) {
                    return stripos($item['date'], $search) !== false
                        || stripos((string)$item['donor_balance'], $search) !== false
                        || stripos((string)$item['charity_balance'], $search) !== false
                        || stripos((string)$item['commission'], $search) !== false;
                });
                $results = array_values($results);
            }

            $total = count($results);
            $pageData = array_slice($results, $start, $length);

            // Format data for DataTables
            $formattedData = [];
            $sl = $start + 1;
            foreach ($pageData as $row) {
                $donorColor = $row['donor_balance'] < 0 ? 'text-danger' : 'text-success';
                $charityColor = $row['charity_balance'] < 0 ? 'text-danger' : 'text-success';

                // Actual Balance Input
                $actualVal = $row['actual_balance'] !== null ? number_format($row['actual_balance'], 2, '.', '') : '';
                $actualBalanceHtml = '<div class="input-group input-group-sm" style="max-width: 140px;">';
                $actualBalanceHtml .= '<input type="number" step="0.01" class="form-control actual-balance-input" data-date="'.$row['date'].'" value="'.$actualVal.'" placeholder="0.00">';
                $actualBalanceHtml .= '<button type="button" class="btn btn-outline-primary save-actual-balance" data-date="'.$row['date'].'"><i class="fas fa-save"></i></button>';
                $actualBalanceHtml .= '</div>';

                $formattedData[] = [
                    'sl' => $sl++,
                    'date' => '<span data-raw="'.$row['date'].'">' . Carbon::parse($row['date'])->format('d M, Y') . '</span>',
                    'daily_donor_in' => '<span class="text-success">£' . number_format($row['daily_donor_in'], 2) . '</span>',
                    'daily_donor_out' => '<span class="text-danger">£' . number_format($row['daily_donor_out'], 2) . '</span>',
                    'donor_balance' => '<span class="'.$donorColor.' fw-bold">£' . number_format($row['donor_balance'], 2) . '</span>',
                    'daily_charity_in' => '<span class="text-success">£' . number_format($row['daily_charity_in'], 2) . '</span>',
                    'daily_charity_out' => '<span class="text-danger">£' . number_format($row['daily_charity_out'], 2) . '</span>',
                    'charity_balance' => '<span class="'.$charityColor.' fw-bold">£' . number_format($row['charity_balance'], 2) . '</span>',
                    'commission' => '<span class="text-warning fw-bold">£' . number_format($row['commission'], 2) . '</span>',
                    'actual_balance' => $actualBalanceHtml,
                ];
            }

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $formattedData
            ]);
        }

        // --- Summary Cards (Latest Values) ---
        $latestDonorIn = Usertransaction::where('status', 1)
            ->where('t_type', 'In')
            ->selectRaw("SUM(CAST(amount AS DECIMAL(10,2))) as total")
            ->value('total') ?? 0;

        $latestDonorOut = Usertransaction::where('status', 1)
            ->where('t_type', 'Out')
            ->selectRaw("SUM(CAST(amount AS DECIMAL(10,2))) as total")
            ->value('total') ?? 0;

        $latestDonorBalance = $latestDonorIn - $latestDonorOut;

        $latestCharityIn = Usertransaction::where('status', 1)
            ->where('t_type', 'Out')
            ->selectRaw("SUM(CAST(amount AS DECIMAL(10,2))) as total")
            ->value('total') ?? 0;

        $latestCharityOut = Transaction::where('t_type', 'Out')
            ->where('status', 1)
            ->sum('amount') ?? 0;

        $latestCharityBalance = $latestCharityIn - $latestCharityOut;

        $latestCommission = Usertransaction::where('status', 1)
            ->where('t_type', 'In')
            ->sum('commission') ?? 0;

        $totalDates = Usertransaction::selectRaw('COUNT(DISTINCT DATE(created_at)) as count')->value('count')
            + Transaction::selectRaw('COUNT(DISTINCT DATE(created_at)) as count')->value('count');

        return view('admin.developer.reconciliation', compact(
            'latestDonorBalance', 'latestCharityBalance', 'latestCommission', 'totalDates'
        ));
    }

    /**
     * Save Actual Balance (AJAX)
     */
    public function updateActualBalance(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'actual_balance' => 'required|numeric'
        ]);

        DB::table('reconciliation_balances')->updateOrInsert(
            ['date' => $request->date],
            [
                'actual_balance' => $request->actual_balance,
                'updated_by' => auth()->user()->name ?? 'Developer',
                'updated_at' => now(),
                'created_at' => now()
            ]
        );

        return response()->json(['success' => true, 'message' => 'Actual balance saved successfully.']);
    }
}
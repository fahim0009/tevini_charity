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
            // Step 1: Get daily sums from Usertransactions
            // Step 1: Get daily sums from Usertransactions (using business_date)
            $dailyUserTx = Usertransaction::where('status', 1)
                ->whereNotNull('business_date')
                ->selectRaw("
                    business_date as date,
                    SUM(CASE WHEN t_type = 'In' THEN CAST(amount AS DECIMAL(10,2)) ELSE 0 END) as daily_in,
                    SUM(CASE WHEN t_type = 'Out' THEN CAST(amount AS DECIMAL(10,2)) ELSE 0 END) as daily_out,
                    SUM(CASE WHEN t_type = 'In' THEN commission ELSE 0 END) as daily_commission
                ")
                ->groupBy('business_date')
                ->orderBy('date')
                ->get();

            // Step 2: Get daily sums from Transactions (using business_date)
            $dailyCharityTx = Transaction::where('t_type', 'Out')
                ->where('status', 1)
                ->whereNotNull('business_date')
                ->selectRaw("business_date as date, SUM(amount) as daily_charity_out")
                ->groupBy('business_date')
                ->orderBy('date')
                ->get();

            $userTxMap = [];
            foreach ($dailyUserTx as $tx) { $userTxMap[$tx->date] = $tx; }

            $charityTxMap = [];
            foreach ($dailyCharityTx as $tx) { $charityTxMap[$tx->date] = $tx; }

            // Step 3: Get saved records (actual balance & check status)
            $savedRecords = DB::table('reconciliation_balances')
                ->get()
                ->keyBy('date');

            // Step 4: Merge all unique dates
            $allDates = array_unique(array_merge(array_keys($userTxMap), array_keys($charityTxMap)));
            sort($allDates);

            // Step 5: Calculate cumulative sums
            $cumIn = 0; $cumOut = 0; $cumCommission = 0; $cumCharityOut = 0;
            $results = [];

            foreach ($allDates as $date) {
                $userTx = $userTxMap[$date] ?? null;
                $charityTx = $charityTxMap[$date] ?? null;

                $dailyIn = (float)($userTx->daily_in ?? 0);
                $dailyOut = (float)($userTx->daily_out ?? 0);
                $dailyCommission = (float)($userTx->daily_commission ?? 0);
                $dailyCharityOut = (float)($charityTx->daily_charity_out ?? 0);
                $dailyCharityIn = $dailyOut;

                $cumIn += $dailyIn;
                $cumOut += $dailyOut;
                $cumCommission += $dailyCommission;
                $cumCharityOut += $dailyCharityOut;

                $savedRecord = $savedRecords[$date] ?? null;

                $results[] = [
                    'date' => $date,
                    'daily_donor_in' => $dailyIn,
                    'daily_donor_out' => $dailyOut,
                    'donor_balance' => $cumIn - $cumOut,
                    'daily_charity_in' => $dailyCharityIn,
                    'daily_charity_out' => $dailyCharityOut,
                    'charity_balance' => $cumOut - $cumCharityOut,
                    'commission' => $cumCommission,
                    'actual_balance' => $savedRecord ? $savedRecord->actual_balance : null,
                    'is_checked' => $savedRecord ? $savedRecord->is_checked : 0,
                ];
            }

            $results = array_reverse($results);

            // Step 6: DataTables pagination
            $start = (int)$request->get('start', 0);
            $length = (int)$request->get('length', 25);
            $search = $request->get('search')['value'] ?? '';
            $draw = (int)$request->get('draw', 1);

            if (!empty($search)) {
                $results = array_filter($results, function($item) use ($search) {
                    return stripos($item['date'], $search) !== false;
                });
                $results = array_values($results);
            }

            $total = count($results);
            $pageData = array_slice($results, $start, $length);

            $formattedData = [];
            $sl = $start + 1;
            
            foreach ($pageData as $row) {
                $donorColor = $row['donor_balance'] < 0 ? 'text-danger' : 'text-success';
                $charityColor = $row['charity_balance'] < 0 ? 'text-danger' : 'text-success';

                // Generate Links for Audit Pages
                $auditDonorUrl = route('dev.auditDonors') . '?filter_type=custom&fromDate=' . $row['date'] . '&toDate=' . $row['date'];
                $auditCharityUrl = route('admin.audit.balances') . '?filter_type=custom&fromDate=' . $row['date'] . '&toDate=' . $row['date'];

                // Check/Undo Button
                $checkedBtn = $row['is_checked'] == 1 
                    ? '<button class="btn btn-sm btn-success toggle-check-btn" data-date="'.$row['date'].'"><i class="fas fa-check-circle"></i> Checked</button>'
                    : '<button class="btn btn-sm btn-outline-primary toggle-check-btn" data-date="'.$row['date'].'"><i class="fas fa-circle"></i> Check</button>';

                // Actual Balance Input
                $actualVal = $row['actual_balance'] !== null ? number_format($row['actual_balance'], 2, '.', '') : '';
                $actualBalanceHtml = '<div class="input-group input-group-sm" style="max-width: 140px;">';
                $actualBalanceHtml .= '<input type="number" step="0.01" class="form-control actual-balance-input" value="'.$actualVal.'" placeholder="0.00">';
                $actualBalanceHtml .= '<button type="button" class="btn btn-outline-primary save-actual-balance" 
                    data-date="'.$row['date'].'" 
                    data-donor-in="'.$row['daily_donor_in'].'" 
                    data-donor-out="'.$row['daily_donor_out'].'" 
                    data-donor-bal="'.$row['donor_balance'].'" 
                    data-charity-in="'.$row['daily_charity_in'].'" 
                    data-charity-out="'.$row['daily_charity_out'].'" 
                    data-charity-bal="'.$row['charity_balance'].'" 
                    data-commission="'.$row['commission'].'">
                    <i class="fas fa-save"></i></button>';
                $actualBalanceHtml .= '</div>';

                $formattedData[] = [
                    'sl' => $sl++,
                    'date' => '<span data-raw="'.$row['date'].'">' . Carbon::parse($row['date'])->format('d M, Y') . '</span>',
                    'daily_donor_in' => '<span class="text-success">£' . number_format($row['daily_donor_in'], 2) . '</span>',
                    'daily_donor_out' => '<span class="text-danger">£' . number_format($row['daily_donor_out'], 2) . '</span>',
                    // Make Donor Balance a Link
                    'donor_balance' => '<a href="'.$auditDonorUrl.'" target="_blank" class="'.$donorColor.' fw-bold text-decoration-none hover-underline">£' . number_format($row['donor_balance'], 2) . '</a>',
                    'daily_charity_in' => '<span class="text-success">£' . number_format($row['daily_charity_in'], 2) . '</span>',
                    'daily_charity_out' => '<span class="text-danger">£' . number_format($row['daily_charity_out'], 2) . '</span>',
                    // Make Charity Balance a Link
                    'charity_balance' => '<a href="'.$auditCharityUrl.'" target="_blank" class="'.$charityColor.' fw-bold text-decoration-none hover-underline">£' . number_format($row['charity_balance'], 2) . '</a>',
                    'commission' => '<span class="text-warning fw-bold">£' . number_format($row['commission'], 2) . '</span>',
                    'actual_balance' => $actualBalanceHtml,
                    'checked_btn' => $checkedBtn
                ];
            }

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'data' => $formattedData
            ]);
        }

        // Summary Cards
        $latestDonorIn = Usertransaction::where('status', 1)->where('t_type', 'In')->selectRaw("SUM(CAST(amount AS DECIMAL(10,2))) as total")->value('total') ?? 0;
        $latestDonorOut = Usertransaction::where('status', 1)->where('t_type', 'Out')->selectRaw("SUM(CAST(amount AS DECIMAL(10,2))) as total")->value('total') ?? 0;
        $latestDonorBalance = $latestDonorIn - $latestDonorOut;

        $latestCharityIn = Usertransaction::where('status', 1)->where('t_type', 'Out')->selectRaw("SUM(CAST(amount AS DECIMAL(10,2))) as total")->value('total') ?? 0;
        $latestCharityOut = Transaction::where('t_type', 'Out')->where('status', 1)->sum('amount') ?? 0;
        $latestCharityBalance = $latestCharityIn - $latestCharityOut;

        $latestCommission = Usertransaction::where('status', 1)->where('t_type', 'In')->sum('commission') ?? 0;
        $totalDates = Usertransaction::selectRaw('COUNT(DISTINCT DATE(created_at)) as count')->value('count') + Transaction::selectRaw('COUNT(DISTINCT DATE(created_at)) as count')->value('count');

        return view('admin.developer.reconciliation', compact(
            'latestDonorBalance', 'latestCharityBalance', 'latestCommission', 'totalDates'
        ));
    }

    /**
     * Save Actual Balance & All Calculations (AJAX)
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
                'all_donor_in' => $request->all_donor_in ?? 0,
                'all_donor_out' => $request->all_donor_out ?? 0,
                'all_donor_total_balance' => $request->all_donor_total_balance ?? 0,
                'all_charity_in' => $request->all_charity_in ?? 0,
                'all_charity_out' => $request->all_charity_out ?? 0,
                'all_charit_total_balance' => $request->all_charity_total_balance ?? 0, // Note: kept your migration typo
                'total_commission' => $request->total_commission ?? 0,
                'updated_by' => auth()->user()->name ?? 'Developer',
                'updated_at' => now(),
                'created_at' => now()
            ]
        );

        return response()->json(['success' => true, 'message' => 'Data saved successfully.']);
    }

    /**
     * Toggle Check Status (AJAX)
     */
    public function toggleReconciliationCheck(Request $request)
    {
        $request->validate(['date' => 'required|date']);

        $record = DB::table('reconciliation_balances')->where('date', $request->date)->first();

        if ($record) {
            $newStatus = $record->is_checked == 1 ? 0 : 1;
            DB::table('reconciliation_balances')->where('date', $request->date)->update([
                'is_checked' => $newStatus,
                'checked_by' => auth()->id(),
                'updated_by' => auth()->user()->name ?? 'Developer',
                'updated_at' => now()
            ]);
        } else {
            // If record doesn't exist, create one just for checking
            DB::table('reconciliation_balances')->insert([
                'date' => $request->date,
                'is_checked' => 1,
                'checked_by' => auth()->id(),
                'updated_by' => auth()->user()->name ?? 'Developer',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $newStatus = 1;
        }

        return response()->json(['success' => true, 'is_checked' => $newStatus]);
    }
}
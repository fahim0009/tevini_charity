<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Admin\SystemController;
use App\Http\Controllers\Developer\DeveloperToolController;
use App\Http\Controllers\Developer\DeveloperToolDashboardController;
use App\Http\Controllers\Developer\ReconciliationController;

/*
|--------------------------------------------------------------------------
| DEVELOPER ROUTES
|--------------------------------------------------------------------------
| All developer routes are protected by 'auth' and 'is_admin' middleware.
| Base URL: http://127.0.0.1:8000/admin/dev-team/
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'admin/dev-team/', 'middleware' => ['auth', 'is_admin']], function () {

    /*
    |----------------------------------------------------------------------
    | DEVELOPER DASHBOARD
    |----------------------------------------------------------------------
    */
    Route::get('dashboard', [DeveloperToolDashboardController::class, 'dashboard'])
        ->name('developer.dashboard');


    Route::get('/get-topup-records', [DeveloperToolDashboardController::class, 'getTopupRecords'])
        ->name('dev.getTopupRecords');
    Route::get('/get-stuck-standing-donations', [DeveloperToolDashboardController::class, 'getStuckStandingDonations'])
        ->name('dev.getStuckStandingDonations');
    Route::get('/get-failing-standing-donations', [DeveloperToolDashboardController::class, 'getFailingStandingDonations'])
        ->name('dev.getFailingStandingDonations');

    /*
    |----------------------------------------------------------------------
    | MONITORING PAGES
    |----------------------------------------------------------------------
    */
    Route::get('online-donations', [DeveloperToolDashboardController::class, 'onlineDonations'])
        ->name('developer.online_donations');

    Route::get('standing-donations', [DeveloperToolDashboardController::class, 'standingDonations'])->name('developer.standing_donations');
    Route::get('standing-donations/{id}/payments', [DeveloperToolDashboardController::class, 'getStandingPaymentDetails'])->name('developer.standing_payments');

    Route::get('donors', [DeveloperToolDashboardController::class, 'donorMonitoring'])
        ->name('developer.donors');

    Route::get('charities', [DeveloperToolDashboardController::class, 'charityMonitoring'])
        ->name('developer.charities');

    Route::get('process-voucher-monitoring', [DeveloperToolDashboardController::class, 'processVoucherMonitoring'])->name('developer.process_voucher_monitoring');
    Route::get('process-voucher-monitoring/details', [DeveloperToolDashboardController::class, 'getProcessVoucherDayDetails'])->name('developer.process_voucher_monitoring.details');
    Route::post('process-voucher-monitoring/check/{id}', [DeveloperToolDashboardController::class, 'checkVoucher'])->name('developer.process_voucher_monitoring.check');
    Route::post('process-voucher-monitoring/uncheck/{id}', [DeveloperToolDashboardController::class, 'uncheckVoucher'])->name('developer.process_voucher_monitoring.uncheck');

    Route::get('vouchers', [DeveloperToolDashboardController::class, 'voucherMonitoring'])
        ->name('developer.vouchers');

    Route::get('vouchers/{id}/barcodes', [DeveloperToolDashboardController::class, 'getOrderBarcodes'])->name('developer.vouchers.barcodes');



    // Donor Audit Routes
    Route::get('/audit-donors', [DeveloperToolDashboardController::class, 'auditDonors'])->name('dev.auditDonors');
    Route::get('/audit-donors/details', [DeveloperToolDashboardController::class, 'getDonorDayDetails'])->name('dev.auditDonors.details');


    /*
    |----------------------------------------------------------------------
    | AUDIT - CHARITIES (Developer Tools)
    |----------------------------------------------------------------------
    */
    Route::get('/audit-charities', [DeveloperToolDashboardController::class, 'auditBalances'])->name('admin.audit.balances');
    Route::get('/audit-transaction/details', [DeveloperToolDashboardController::class, 'getDayDetails'])->name('audit.dailytransaction.details');
    Route::post('/admin/audit/adjust-transaction', [DeveloperToolDashboardController::class, 'adjustTransaction'])->name('admin.audit.adjust');
    Route::post('/change-card-donor', [DeveloperToolDashboardController::class, 'changeRealDonor'])->name('change.real.donor');


    /*
    |----------------------------------------------------------------------    
    | TRANSACTION DELETE/UPDATE (Admin Tools)
    |----------------------------------------------------------------------    
    */
    Route::get('/transaction-delete', [TransactionController::class, 'checkTran'])
        ->name('admin.transactionDelete');
    Route::post('/transaction-delete', [TransactionController::class, 'checkTran'])
        ->name('admin.transactionSearch');
    Route::post('/transaction-status-change', [TransactionController::class, 'changeTranStatus'])
        ->name('admin.transactionChangeStatus');
    Route::post('/transaction-status-update', [TransactionController::class, 'deleteTransactionUpdate'])
        ->name('admin.deleteTransactionUpdate');

    /*
    |----------------------------------------------------------------------    
    | STANDING DONATION CHECK (Developer Tools)
    |----------------------------------------------------------------------    
    */
    Route::match(['get', 'post'], '/standing-donation-check', [DeveloperToolController::class, 'checkStandingDonation'])
        ->name('dev.checkStandingDonation');
    Route::delete('/standing-donation-delete/{id}', [DeveloperToolController::class, 'deleteStandingDonation'])
        ->name('dev.deleteStandingDonation');

    /*
    |----------------------------------------------------------------------    
    | TRANSACTION CHECK / DELETE / UPDATE (Developer Tools)
    |----------------------------------------------------------------------    
    */
    Route::match(['get', 'post'], '/transaction-check', [DeveloperToolController::class, 'searchTransaction'])
        ->name('dev.searchTransaction');
    Route::delete('/transaction-delete/{id}', [DeveloperToolController::class, 'deleteTransaction'])
        ->name('dev.deleteTransaction');
    Route::put('/transaction-update/{id}', [DeveloperToolController::class, 'updateTransaction'])
        ->name('dev.updateTransaction');

    /*
    |----------------------------------------------------------------------    
    | FIX OLD DATES (One-time system fix)
    |----------------------------------------------------------------------    
    */
    Route::get('/fix-old-dates', function () {
        set_time_limit(0);

        $cutoffTime = '16:30:00';

        $baseDateCalc = "
            CASE 
                WHEN TIME(created_at) >= '$cutoffTime'
                THEN DATE_ADD(created_at, INTERVAL 1 DAY)
                ELSE created_at
            END
        ";

        $weekendShift = "
            CASE 
                WHEN WEEKDAY($baseDateCalc) IN (4, 5, 6) 
                THEN DATE_ADD($baseDateCalc, INTERVAL (7 - WEEKDAY($baseDateCalc)) DAY)
                ELSE $baseDateCalc
            END
        ";

        // 1. Reset and set all 'Out' transactions business_date
        DB::table('transactions')
            ->where('t_type', 'Out')
            ->update([
                'business_date' => DB::raw("DATE($weekendShift)")
            ]);

        // 2. Update all usertransactions
        $updatedCount = DB::table('usertransactions')
            ->whereNotNull('charity_id')
            ->update([
                'business_date' => DB::raw("DATE($weekendShift)")
            ]);

        // 3. Ensure history is set to 16:30
        DB::table('cutoff_histories')->updateOrInsert(
            ['effective_date' => '2000-01-01'],
            ['cutoff_time' => '16:30', 'updated_at' => now(), 'created_at' => now()]
        );

        DB::table('cutoff_histories')->updateOrInsert(
            ['effective_date' => \Carbon\Carbon::today()->toDateString()],
            ['cutoff_time' => '16:30', 'updated_at' => now(), 'created_at' => now()]
        );

        return "Success! Recalculated and updated {$updatedCount} user transactions and all Out transactions instantly.";
    })->name('dev.fixOldDates');

    /*
    |----------------------------------------------------------------------    
    | SYSTEM TOOLS (Developer Testing)
    |----------------------------------------------------------------------    
    */
    // Daily Paid Transactions
    Route::get('/daily-paid-transactions', [SystemController::class, 'dailyPaidTransaction'])
        ->name('dailyPaidTransaction');
    Route::post('/daily-paid-transactions/update-dates', [SystemController::class, 'updateTransactionDates'])
        ->name('updateTransactionDates');

    // User Transaction Date
    Route::get('/user-transaction-date', [SystemController::class, 'userTransactionDate'])
        ->name('userTransactionDate');
    Route::post('/user-transaction-date/update', [SystemController::class, 'updateUserTransactionDate'])
        ->name('updateUserTransactionDate');

    // Charity Payment
    Route::get('/charity-payment', [SystemController::class, 'charityPaymentCreate'])
        ->name('charityPaymentCreate');
    Route::post('/charity-payment', [SystemController::class, 'charityPaymentStore'])
        ->name('charityPaymentStore');



    /*
    |----------------------------------------------------------------------    
    | reconciliation
    |----------------------------------------------------------------------    
    */
    Route::get('reconciliation', [\App\Http\Controllers\Developer\ReconciliationController::class, 'index'])->name('developer.reconciliation');
    Route::post('reconciliation/update-actual', [\App\Http\Controllers\Developer\ReconciliationController::class, 'updateActualBalance'])->name('developer.reconciliation.update_actual');
    Route::post('reconciliation/toggle-check', [\App\Http\Controllers\Developer\ReconciliationController::class, 'toggleReconciliationCheck'])->name('developer.reconciliation.toggle_check');



}); // End of Developer Routes
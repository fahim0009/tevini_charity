<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AddUniqueOnegivTransactionIdToOneGivTransactionsTable extends Migration
{
    public function up()
    {
        // ✅ FIX: Don't DELETE duplicates. Mark them as 'duplicate' status.
        // This preserves audit trail and linked transactions.
        // Keep the oldest record (smallest id) as the active one.
        
        // Find all duplicate onegiv_transaction_ids
        $duplicateIds = DB::table('one_giv_transactions')
            ->select('onegiv_transaction_id')
            ->whereNotNull('onegiv_transaction_id')
            ->groupBy('onegiv_transaction_id')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('onegiv_transaction_id');

        foreach ($duplicateIds as $dupId) {
            // Keep the oldest (smallest id), mark others as 'duplicate'
            $keepId = DB::table('one_giv_transactions')
                ->where('onegiv_transaction_id', $dupId)
                ->orderBy('id', 'asc')
                ->value('id');

            // Mark newer duplicates (not delete!)
            DB::table('one_giv_transactions')
                ->where('onegiv_transaction_id', $dupId)
                ->where('id', '!=', $keepId)
                ->update(['status' => 'duplicate']);
            
            // Log for investigation
            Log::warning('OneGiv duplicate marked (not deleted)', [
                'onegiv_transaction_id' => $dupId,
                'kept_id' => $keepId,
                'marked_duplicate_count' => DB::table('one_giv_transactions')
                    ->where('onegiv_transaction_id', $dupId)
                    ->where('id', '!=', $keepId)
                    ->count(),
            ]);
        }

        // Now add unique constraint
        // ✅ FIX: Use partial unique index to exclude 'duplicate' status rows
        // This allows duplicates to coexist with status='duplicate'
        DB::statement("CREATE UNIQUE INDEX onegiv_txn_id_unique ON one_giv_transactions (onegiv_transaction_id) WHERE status != 'duplicate'");
    }

    public function down()
    {
        DB::statement("DROP INDEX IF EXISTS onegiv_txn_id_unique");
    }
}
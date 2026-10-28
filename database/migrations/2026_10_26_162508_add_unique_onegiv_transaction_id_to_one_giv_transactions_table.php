<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUniqueOnegivTransactionIdToOneGivTransactionsTable extends Migration
{
    public function up()
    {
        // ✅ FIX: MySQL compatible. Append ID to make duplicates unique without deleting.
        DB::statement("UPDATE one_giv_transactions n1
                       INNER JOIN (
                           SELECT onegiv_transaction_id, MIN(id) as min_id
                           FROM one_giv_transactions
                           WHERE onegiv_transaction_id IS NOT NULL
                           GROUP BY onegiv_transaction_id
                           HAVING COUNT(*) > 1
                       ) n2 ON n1.onegiv_transaction_id = n2.onegiv_transaction_id AND n1.id > n2.min_id
                       SET n1.onegiv_transaction_id = CONCAT(n1.onegiv_transaction_id, '-DUP-', n1.id),
                           n1.status = 'duplicate'");

        Schema::table('one_giv_transactions', function (Blueprint $table) {
            $table->unique('onegiv_transaction_id', 'onegiv_txn_id_unique');
        });
    }

    public function down()
    {
        Schema::table('one_giv_transactions', function (Blueprint $table) {
            $table->dropUnique('onegiv_txn_id_unique');
        });
    }
}
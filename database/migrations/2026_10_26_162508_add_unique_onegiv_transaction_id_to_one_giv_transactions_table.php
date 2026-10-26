<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUniqueOnegivTransactionIdToOneGivTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("DELETE n1 FROM one_giv_transactions n1, one_giv_transactions n2 
                   WHERE n1.id > n2.id 
                   AND n1.onegiv_transaction_id = n2.onegiv_transaction_id
                   AND n1.onegiv_transaction_id IS NOT NULL");

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

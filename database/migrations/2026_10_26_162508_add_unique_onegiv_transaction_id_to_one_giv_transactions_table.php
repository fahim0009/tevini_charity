<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueOnegivTransactionIdToOneGivTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
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

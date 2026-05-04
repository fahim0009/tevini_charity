<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOnegivTransactionIdToUsertransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usertransactions', function (Blueprint $table) {
            $table->unsignedBigInteger('onegiv_transaction_id')
                ->nullable()
                ->after('campaign_id');
        });
    }

    public function down()
    {
        Schema::table('usertransactions', function (Blueprint $table) {
            $table->dropColumn('onegiv_transaction_id');
        });
    }



}

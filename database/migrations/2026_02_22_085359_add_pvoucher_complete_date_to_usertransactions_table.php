<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPvoucherCompleteDateToUsertransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usertransactions', function (Blueprint $table) {
            $table->string('voucher_complete_date')->nullable()->after('campaign_id');
            $table->string('voucher_create_date')->nullable()->after('campaign_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usertransactions', function (Blueprint $table) {
            //
        });
    }
}

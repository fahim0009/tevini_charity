<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCheckedFieldsToProvouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('provouchers', function (Blueprint $table) {
            $table->string('checked_by')->nullable()->after('voucher_state');
            $table->datetime('checked_date')->nullable()->after('checked_by');
        });
    }

    public function down()
    {
        Schema::table('provouchers', function (Blueprint $table) {
            $table->dropColumn(['checked_by', 'checked_date']);
        });
    }
}

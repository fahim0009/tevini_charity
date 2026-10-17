<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVoucherStateToTables extends Migration
{
    public function up()
    {
        Schema::table('provouchers', function (Blueprint $table) {
            $table->integer('voucher_state')->default(12)->after('status'); // Default Pending
        });

        Schema::table('usertransactions', function (Blueprint $table) {
            $table->integer('voucher_state')->default(12)->after('status'); // Default Pending
        });

        // --- NEW: Determine voucher_state ---
             // Default Pending = 2;
             // Expired = 4;
             // Waiting = 3;
             // Complete = 1;
             // Cancel = 0;
             // Not identify  = 12;
        // ------------------------------------


    }

    public function down()
    {
        Schema::table('provouchers', function (Blueprint $table) {
            $table->dropColumn('voucher_state');
        });

        Schema::table('usertransactions', function (Blueprint $table) {
            $table->dropColumn('voucher_state');
        });
    }
}

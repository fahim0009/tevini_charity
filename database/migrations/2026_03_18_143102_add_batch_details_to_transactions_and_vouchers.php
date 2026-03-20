<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBatchDetailsToTransactionsAndVouchers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        // Adding columns to Usertransaction table
        Schema::table('usertransactions', function (Blueprint $table) {
            $table->foreignId('provoucher_batch_id')->nullable()->after('voucher_complete_date')->constrained('provoucher_batches')->onDelete('set null');
            $table->string('batch_no')->nullable()->after('provoucher_batch_id');
        });

        // Adding columns to Provoucher table
        Schema::table('provouchers', function (Blueprint $table) {
            $table->foreignId('provoucher_batch_id')->nullable()->after('note')->constrained('provoucher_batches')->onDelete('set null');
            $table->string('batch_no')->nullable()->after('provoucher_batch_id');
        });
    }

    public function down(): void
    {
        Schema::table('usertransactions', function (Blueprint $table) {
            $table->dropColumn(['provoucher_batch_id', 'batch_no']);
        });

        Schema::table('provouchers', function (Blueprint $table) {
            $table->dropColumn(['provoucher_batch_id', 'batch_no']);
        });
    }


}

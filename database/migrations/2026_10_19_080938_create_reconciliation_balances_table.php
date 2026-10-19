<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReconciliationBalancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reconciliation_balances', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->decimal('all_donor_in', 10, 2)->default(0);
            $table->decimal('all_donor_out', 10, 2)->default(0);
            $table->decimal('all_donor_total_balance', 10, 2)->default(0);
            $table->decimal('all_charity_in', 10, 2)->default(0);
            $table->decimal('all_charity_out', 10, 2)->default(0);
            $table->decimal('all_charit_total_balance', 10, 2)->default(0);
            $table->decimal('total_commission', 10, 2)->default(0);
            $table->decimal('actual_balance', 10, 2)->default(0);
            $table->integer('is_checked')->default(0)->nullable();
            $table->integer('checked_by')->default(0)->nullable();
            $table->integer('status')->default(0)->nullable();
            $table->string('updated_by')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reconciliation_balances');
    }
}

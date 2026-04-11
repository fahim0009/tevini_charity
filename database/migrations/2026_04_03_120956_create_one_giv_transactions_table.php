<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOneGivTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('one_giv_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('date')->nullable();
            $table->string('terminal_id');
            $table->string('onegiv_transaction_id');
            $table->string('card_issuer_transaction_id')->unique();
            $table->string('card_serial_number');
            $table->integer('amount');
            $table->string('reference')->nullable();
            $table->string('charity_number');
            $table->string('account_number');
            $table->string('sortcode');
            $table->string('extra1')->nullable();
            $table->string('extra2')->nullable();
            $table->string('status')->default('success');
            $table->string('updated_by')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('one_giv_transactions');
    }
}

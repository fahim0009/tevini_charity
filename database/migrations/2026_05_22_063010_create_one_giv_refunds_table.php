<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOneGivRefundsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('one_giv_refunds', function (Blueprint $table) {
            $table->id();
            $table->string('card_issuer_transaction_id');
            $table->string('card_serial_number')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('charity_id')->nullable();
            $table->string('charity_number')->nullable();
            $table->integer('amount');           // pennies
            $table->decimal('amount_pounds', 10, 2); // pounds
            $table->string('onegiv_transaction_id')->nullable();
            $table->string('status')->default('refunded');
            $table->tinyInteger('admin_watch')->default(0);  // 0 = not watched, 1 = watched
            $table->tinyInteger('admin_status')->default(0); // 0 = pending, 1 = approved
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
        Schema::dropIfExists('one_giv_refunds');
    }
}

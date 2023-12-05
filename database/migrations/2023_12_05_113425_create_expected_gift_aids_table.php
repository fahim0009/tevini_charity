<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpectedGiftAidsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expected_gift_aids', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('charity_id')->unsigned()->nullable();
            $table->foreign('charity_id')->references('id')->on('charities')->onDelete('cascade');
            $table->bigInteger('transaction_id')->unsigned()->nullable();
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('cascade');
            $table->bigInteger('usertransaction_id')->unsigned()->nullable();
            $table->foreign('usertransaction_id')->references('id')->on('usertransactions')->onDelete('cascade');
            $table->double('amount',10,2)->nullable();
            $table->double('gift_amount',10,2)->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('expected_gift_aids');
    }
}

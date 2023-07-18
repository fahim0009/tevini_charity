<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettlementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settlements', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('MTID')->nullable();
            $table->string('localDate')->nullable();
            $table->string('localTime')->nullable();
            $table->string('tlogId')->nullable();
            $table->string('orgTlogID')->nullable();
            $table->string('orgItemId')->nullable();
            $table->string('PAN')->nullable();
            $table->string('cardID')->nullable();
            $table->string('txnCode')->nullable();
            $table->string('txnSubCode')->nullable();
            $table->string('amtFee')->nullable();
            $table->string('curTxn')->nullable();
            $table->string('amtTxn')->nullable();
            $table->string('amtCashback')->nullable();
            $table->string('billAmt')->nullable();
            $table->string('curBill')->nullable();
            $table->string('billConvRate')->nullable();
            $table->string('CURSET')->nullable();
            $table->string('AMTSET')->nullable();
            $table->string('RATESET')->nullable();
            $table->string('RATESETECB')->nullable();
            $table->string('approvalCode')->nullable();
            $table->string('crdAcptID')->nullable();
            $table->string('crdAcptLoc')->nullable();
            $table->string('termCode')->nullable();
            $table->string('MCC')->nullable();
            $table->string('ctxLocalDate')->nullable();
            $table->string('ctxLocalTime')->nullable();
            $table->string('PRODCODE')->nullable();
            $table->string('trn')->nullable();
            $table->string('poschp')->nullable();
            $table->string('poscp')->nullable();
            $table->string('poscdim')->nullable();
            $table->string('poscham')->nullable();
            $table->string('settlementActionCode')->nullable();
            $table->string('Utid')->nullable();
            $table->integer('status')->nullable();
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
        Schema::dropIfExists('settlements');
    }
}

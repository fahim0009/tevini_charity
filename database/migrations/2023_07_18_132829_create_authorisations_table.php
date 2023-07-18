<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorisationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authorisations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('Utid')->nullable();
            $table->string('messageID')->nullable();
            $table->string('instCode')->nullable();
            $table->string('txnType')->nullable();
            $table->string('msgType')->nullable();
            $table->string('tlogId')->nullable();
            $table->string('orgTlogID')->nullable();
            $table->string('timeout')->nullable();
            $table->string('repeat')->nullable();
            $table->string('PAN')->nullable();
            $table->string('cardID')->nullable();
            $table->string('accNo')->nullable();
            $table->string('curBill')->nullable();
            $table->string('avlBal')->nullable();
            $table->string('blkAmt')->nullable();
            $table->string('localDate')->nullable();
            $table->string('localTime')->nullable();
            $table->string('amtTxn')->nullable();
            $table->string('curTxn')->nullable();
            $table->string('billAmt')->nullable();
            $table->string('billConvRate')->nullable();
            $table->string('amtCom')->nullable();
            $table->string('amtPad')->nullable();
            $table->string('txnCode')->nullable();
            $table->string('termCode')->nullable();
            $table->string('crdAcptID')->nullable();
            $table->string('crdAcptLoc')->nullable();
            $table->string('MCC')->nullable();
            $table->string('poschp')->nullable();
            $table->string('poscdim')->nullable();
            $table->string('poscham')->nullable();
            $table->string('poscp')->nullable();
            $table->string('approvalCode')->nullable();
            $table->string('sysDate')->nullable();
            $table->string('rev')->nullable();
            $table->string('orgItemId')->nullable();
            $table->string('itemSrc')->nullable();
            $table->string('amtFee')->nullable();
            $table->string('crdproduct')->nullable();
            $table->string('ctxLocalDate')->nullable();
            $table->string('ctxLocalTime')->nullable();
            $table->string('aVSChkRs')->nullable();
            $table->string('threeDSecChkRs')->nullable();
            $table->string('actionCode')->nullable();
            $table->string('amtCashback')->nullable();
            $table->string('trn')->nullable();
            $table->string('txnSubCode')->nullable();
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
        Schema::dropIfExists('authorisations');
    }
}

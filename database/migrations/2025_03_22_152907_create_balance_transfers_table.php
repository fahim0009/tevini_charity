<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBalanceTransfersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('balance_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('date')->nullable();
            $table->string('accountno')->nullable();
            $table->bigInteger('transfer_from')->unsigned()->nullable();
            $table->foreign('transfer_from')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('transfer_to')->unsigned()->nullable();
            $table->foreign('transfer_to')->references('id')->on('users')->onDelete('cascade');
            $table->double('amount', 10,2)->default(0);
            $table->integer('status')->default(0); // pending=0, approved=1, rejected=2
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
        Schema::dropIfExists('balance_transfers');
    }
}

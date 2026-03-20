<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvoucherBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('provoucher_batches', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('charity_id')->unsigned()->nullable();
            $table->foreign('charity_id')->references('id')->on('charities')->onDelete('cascade');
            $table->string('date')->nullable();
            $table->string('batch_no')->nullable();
            $table->double('total_amount',10,2)->nullable();
            $table->integer('status')->default(1)->nullable();
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
        Schema::dropIfExists('provoucher_batches');
    }
}

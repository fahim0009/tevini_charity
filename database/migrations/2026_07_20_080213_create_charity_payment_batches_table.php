<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharityPaymentBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charity_payment_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('charity_id');
            $table->unsignedBigInteger('transaction_id')->nullable(); // The 'Out' transaction ID
            $table->dateTime('last_payment_date');
            $table->json('usertransactions_ids')->nullable(); // JSON array of Usertransaction IDs
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('date')->nullable();
            $table->integer('status')->nullable();
            $table->timestamps();

            $table->foreign('charity_id')->references('id')->on('charities')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('charity_payment_batches');
    }


}

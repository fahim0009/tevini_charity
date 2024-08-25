<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVoucherCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('voucher_carts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('voucher_id')->unsigned()->nullable();
            $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('cascade');
            $table->integer('number_voucher')->nullable();
            $table->integer('qty')->nullable();
            $table->integer('amount')->nullable();
            $table->integer('tamount')->nullable();
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
        Schema::dropIfExists('voucher_carts');
    }
}

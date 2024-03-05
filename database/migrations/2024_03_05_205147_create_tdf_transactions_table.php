<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTdfTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tdf_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('issue_date')->nullable();
            $table->string('payment_date')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->double('tdf_amount', 10,2)->default(0);
            $table->double('current_dollar_amount')->default(0);
            $table->double('payment_dollar_amount')->default(0);
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('tdf_transactions');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donation_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('donor_id')->unsigned()->nullable();
            $table->foreign('donor_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('donation_cal_id')->unsigned()->nullable();
            $table->foreign('donation_cal_id')->references('id')->on('donation_calculators')->onDelete('cascade');
            $table->string('date')->nullable();
            $table->double('donation_amount',10,2)->nullable();
            $table->string('available_for_donation')->nullable();
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
        Schema::dropIfExists('donation_details');
    }
}

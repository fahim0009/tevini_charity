<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDonationCalculatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('donation_calculators', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('donor_id')->unsigned()->nullable();
            $table->foreign('donor_id')->references('id')->on('users')->onDelete('cascade');
            $table->double('income_amount',10,2)->nullable();
            $table->string('income_slot')->nullable();
            $table->string('start_date')->nullable();
            $table->string('donation_percentage')->nullable();
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
        Schema::dropIfExists('donation_calculators');
    }
}

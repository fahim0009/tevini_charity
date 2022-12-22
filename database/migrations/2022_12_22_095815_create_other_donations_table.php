<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtherDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_donations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('donor_id')->unsigned()->nullable();
            $table->foreign('donor_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('donation_date')->nullable();
            $table->double('d_amount',10,2)->nullable();
            $table->string('d_title',255)->nullable();
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
        Schema::dropIfExists('other_donations');
    }
}

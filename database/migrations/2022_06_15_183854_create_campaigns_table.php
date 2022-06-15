<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('charity_id')->unsigned()->nullable();
            $table->foreign('charity_id')->references('id')->on('charities')->onDelete('cascade');
            $table->string('campaign_title')->nullable();
            $table->double('amount',10,2)->nullable();
            $table->double('target_amount',10,2)->nullable();
            $table->double('gain_amount',10,2)->nullable();
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
        Schema::dropIfExists('campaigns');
    }
}

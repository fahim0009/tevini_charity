<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharityLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('charity_links', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('charity_id')->unsigned()->nullable();
            $table->foreign('charity_id')->references('id')->on('charities')->onDelete('cascade');
            $table->double('amount',10,2)->nullable();
            $table->string('email')->nullable();
            $table->string('name')->nullable();
            $table->string('return_url')->nullable();
            $table->boolean('donor_notification')->default(0);
            $table->boolean('status')->default(0);
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
        Schema::dropIfExists('charity_links');
    }
}

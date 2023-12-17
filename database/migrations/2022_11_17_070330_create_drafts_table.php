<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDraftsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drafts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('charity_id')->unsigned()->nullable();
            $table->foreign('charity_id')->references('id')->on('charities')->onDelete('cascade');
            $table->string('donor_acc')->nullable();
            $table->string('donor_name')->nullable();
            $table->string('voucher_number')->nullable();
            $table->double('amount',10,2)->nullable();
            $table->string('note')->nullable();
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
        Schema::dropIfExists('drafts');
    }
}

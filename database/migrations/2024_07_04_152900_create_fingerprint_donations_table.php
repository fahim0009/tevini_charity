<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFingerprintDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fingerprint_donations', function (Blueprint $table) {
            $table->id();
            $table->string('date')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('campaign')->nullable();
            $table->longText('hash')->nullable();
            $table->longText('fingerprint')->nullable();
            $table->longText('hid')->nullable();
            $table->string('FunddAccountNumber')->nullable();
            $table->double('amount', 10,2)->default(0);
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
        Schema::dropIfExists('fingerprint_donations');
    }
}

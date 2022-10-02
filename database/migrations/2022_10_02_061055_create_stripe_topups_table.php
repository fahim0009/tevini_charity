<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStripeTopupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stripe_topups', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('donor_id')->unsigned()->nullable();
            $table->foreign('donor_id')->references('id')->on('users')->onDelete('cascade');
            $table->double('amount',10,2)->nullable();
            $table->string('token')->nullable();
            $table->string('description')->nullable();
            $table->string('notification')->nullable();
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
        Schema::dropIfExists('stripe_topups');
    }
}

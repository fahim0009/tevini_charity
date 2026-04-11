<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOneGivCardOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('one_giv_card_orders', function (Blueprint $table) {
            $table->id();
            $table->string('date')->nullable();
            $table->string('accountno')->nullable();
            $table->unsignedBigInteger('user_id')->nullable(); 
            $table->string('order_id');
            $table->string('order_number')->nullable();
            $table->string('card_holder');
            $table->boolean('fixed_amount')->default(false);
            $table->integer('amount');
            $table->string('pin')->default('1234');
            $table->string('extra1')->nullable();
            $table->string('extra2')->nullable();
            $table->string('status')->default('pending');
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
        Schema::dropIfExists('one_giv_card_orders');
    }
}

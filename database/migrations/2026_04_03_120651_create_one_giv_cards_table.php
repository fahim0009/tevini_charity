<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOneGivCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('one_giv_cards', function (Blueprint $table) {
            $table->id();
            $table->string('date')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('order_id');
            $table->string('order_number');
            $table->string('serial_number')->unique();
            $table->string('pin');
            $table->string('display_number');
            $table->string('expiry_date');
            $table->string('extra1')->nullable();
            $table->string('extra2')->nullable();
            $table->string('status')->default('active');
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
        Schema::dropIfExists('one_giv_cards');
    }
}

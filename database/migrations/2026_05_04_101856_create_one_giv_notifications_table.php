<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOneGivNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('onegiv_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('reason');
            $table->string('card_serial_number');
            $table->text('payload')->nullable(); // JSON data
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
        Schema::dropIfExists('one_giv_notifications');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCutoffHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cutoff_histories', function (Blueprint $table) {
            $table->id();
            $table->date('effective_date'); // The date from which the time is applicable
            $table->string('cutoff_time');  // e.g., '16:30', '14:30'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cutoff_histories');
    }
}

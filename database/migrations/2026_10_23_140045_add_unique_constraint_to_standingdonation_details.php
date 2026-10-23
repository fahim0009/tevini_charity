<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintToStandingdonationDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('standingdonation_details', function (Blueprint $table) {
            $table->unique(['standing_donation_id', 'instalment_date'], 'unique_standing_instalment');
        });
    }

    public function down()
    {
        Schema::table('standingdonation_details', function (Blueprint $table) {
            $table->dropUnique('unique_standing_instalment');
        });
    }
}

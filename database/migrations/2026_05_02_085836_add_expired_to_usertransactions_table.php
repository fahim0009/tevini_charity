<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpiredToUsertransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usertransactions', function (Blueprint $table) {
            $table->integer('expired')->after('pending')->nullable();
        });
    }

    public function down()
    {
        Schema::table('usertransactions', function (Blueprint $table) {
            $table->dropColumn('expired');
        });
    }


}

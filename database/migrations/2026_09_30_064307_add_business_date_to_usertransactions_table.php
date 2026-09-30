<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBusinessDateToUsertransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usertransactions', function (Blueprint $table) {
            $table->date('business_date')->nullable()->after('created_at')->index();
        });
    }

    public function down()
    {
        Schema::table('usertransactions', function (Blueprint $table) {
            $table->dropColumn('business_date');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastPaymentDateToCompanyDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_details', function (Blueprint $table) {
            $table->date('last_payment_processed_date')->nullable()->after('auto_payment_time');
        });
    }

    public function down()
    {
        Schema::table('company_details', function (Blueprint $table) {
            $table->dropColumn('last_payment_processed_date');
        });
    }

    
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAutoPaymentTimeToCompanyDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_details', function (Blueprint $table) {
            // Adding the field with a default of 16:30 (4:30 PM)
            $table->time('auto_payment_time')->default('16:30:00')->after('website'); 
        });
    }

    public function down()
    {
        Schema::table('company_details', function (Blueprint $table) {
            $table->dropColumn('auto_payment_time');
        });
    }
}

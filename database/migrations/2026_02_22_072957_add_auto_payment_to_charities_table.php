<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAutoPaymentToCharitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('charities', function (Blueprint $table) {
            $table->boolean('auto_payment')
                  ->default(1)
                  ->after('pending');
        });
    }

    public function down()
    {
        Schema::table('charities', function (Blueprint $table) {
            $table->dropColumn('auto_payment');
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdminAndStripeChargeToDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->decimal('admin_charge', 10, 2)->default(0)->after('amount');
            $table->decimal('stripe_charge', 10, 2)->default(0)->after('admin_charge');
        });
    }

    public function down()
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn(['admin_charge', 'stripe_charge']);
        });
    }
}

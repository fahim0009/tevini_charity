<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToDonationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->string('payment_method', 20)->default('balance')->after('status')->comment('balance, stripe');
            $table->string('stripe_payment_id')->nullable()->after('payment_method');
            $table->string('guest_first_name')->nullable()->after('stripe_payment_id');
            $table->string('guest_last_name')->nullable()->after('guest_first_name');
            $table->string('guest_email')->nullable()->after('guest_last_name');
            $table->string('guest_phone')->nullable()->after('guest_email');
            $table->string('guest_address_1')->nullable()->after('guest_phone');
            $table->string('guest_address_2')->nullable()->after('guest_address_1');
            $table->string('guest_address_3')->nullable()->after('guest_address_2');
            $table->string('guest_town')->nullable()->after('guest_address_3');
            $table->string('guest_postcode')->nullable()->after('guest_town');
        });
    }

    public function down()
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'stripe_payment_id',
                'guest_first_name',
                'guest_last_name',
                'guest_email',
                'guest_phone',
                'guest_address_1',
                'guest_address_2',
                'guest_address_3',
                'guest_town',
                'guest_postcode',
            ]);
        });
    }
}

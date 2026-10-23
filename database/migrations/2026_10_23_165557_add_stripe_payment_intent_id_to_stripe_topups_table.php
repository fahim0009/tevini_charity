<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStripePaymentIntentIdToStripeTopupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stripe_topups', function (Blueprint $table) {
            $table->string('stripe_payment_intent_id')->nullable()->after('amount');
            $table->unique('stripe_payment_intent_id', 'stripe_topups_intent_unique');
        });
    }

    public function down()
    {
        Schema::table('stripe_topups', function (Blueprint $table) {
            $table->dropUnique('stripe_topups_intent_unique');
            $table->dropColumn('stripe_payment_intent_id');
        });
    }
}

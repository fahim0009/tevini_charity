<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUniqueStripePaymentIntentIdToOrders extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // FIX: Use raw SQL to add unique index (no doctrine/dbal required)
        DB::statement('CREATE UNIQUE INDEX orders_stripe_payment_intent_id_unique ON orders (stripe_payment_intent_id)');
    }

    public function down()
    {
        // Drop the unique index
        DB::statement('DROP INDEX orders_stripe_payment_intent_id_unique ON orders');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUniquePaymentIntentIdToPaymentLogsTable extends Migration
{
    public function up()
    {
        // ✅ FIX: Don't DELETE duplicates. Append '-DUP-id' to make them unique.
        DB::statement("UPDATE payment_logs n1
                       INNER JOIN (
                           SELECT payment_intent_id, MIN(id) as min_id
                           FROM payment_logs
                           WHERE payment_intent_id IS NOT NULL
                           GROUP BY payment_intent_id
                           HAVING COUNT(*) > 1
                       ) n2 ON n1.payment_intent_id = n2.payment_intent_id AND n1.id > n2.min_id
                       SET n1.payment_intent_id = CONCAT(n1.payment_intent_id, '-DUP-', n1.id)");

        // Now add unique constraint safely
        DB::statement('CREATE UNIQUE INDEX payment_logs_intent_unique ON payment_logs (payment_intent_id)');
        DB::statement('CREATE UNIQUE INDEX donations_stripe_payment_id_unique ON donations (stripe_payment_id)');
    }

    public function down()
    {
        DB::statement('DROP INDEX payment_logs_intent_unique ON payment_logs');
        DB::statement('DROP INDEX IF EXISTS donations_stripe_payment_id_unique ON donations');
    }
}
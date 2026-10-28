<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddUniquePaymentIntentIdToPaymentLogsTable extends Migration
{
    public function up()
    {
        // Clean up existing duplicates first
        DB::statement("DELETE n1 FROM payment_logs n1, payment_logs n2 
                       WHERE n1.id > n2.id 
                       AND n1.payment_intent_id = n2.payment_intent_id
                       AND n1.payment_intent_id IS NOT NULL");

        DB::statement('CREATE UNIQUE INDEX payment_logs_intent_unique ON payment_logs (payment_intent_id)');
    }

    public function down()
    {
        DB::statement('DROP INDEX payment_logs_intent_unique ON payment_logs');
    }
}
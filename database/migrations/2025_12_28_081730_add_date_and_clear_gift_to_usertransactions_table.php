<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateAndClearGiftToUsertransactionsTable extends Migration
{
    public function up(): void
    {
        Schema::table('usertransactions', function (Blueprint $table) {
            $table->string('date')->after('gift')->nullable();
            $table->integer('clear_gift')->after('date')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('usertransactions', function (Blueprint $table) {
            $table->dropColumn(['date', 'clear_gift']);
        });
    }
}

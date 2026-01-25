<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGiftAidFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('users', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->decimal('gift_aid_currenction', 8, 2)->nullable()->after('expected_gift_aid');
            $table->decimal('current_yr_gift_aid', 8, 2)->nullable()->after('gift_aid_currenction');
            $table->decimal('prev_yr_gift_aid', 8, 2)->nullable()->after('current_yr_gift_aid');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Illuminate\Database\Schema\Blueprint $table) {
            $table->dropColumn(['gift_aid_currenction', 'current_yr_gift_aid', 'prev_yr_gift_aid']);
        });
    }

}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateStandardAmountDefaultOnUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Existing jader standard_amount 0.00 ache, tader ta 500.00 kore dao
        DB::statement("UPDATE users SET standard_amount = 500.00 WHERE standard_amount = 0.00");

        // 2. Nton user-der jonno default value 500.00 set kore dao
        DB::statement("ALTER TABLE users MODIFY standard_amount DECIMAL(10,2) NOT NULL DEFAULT 500.00");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert back to 0.00 if you ever roll back
        DB::statement("ALTER TABLE users MODIFY standard_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00");
    }
}